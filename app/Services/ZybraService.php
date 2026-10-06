<?php

namespace App\Services;

use App\Models\EventRegistration;
use App\Models\FinancialEntry;
use App\Models\User;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Zybra is Sabha's cash book (Zybra API Integration Doc v1). Every rupee in or
 * out is posted to Zybra; Sabha only keeps an audit log (FinancialEntry) of the
 * entries it posted.
 *
 *  - Income (incl. membership fees and event tickets): receipt with an
 *    "account" line against an income ledger.
 *  - Expense paid from Sabha's cash/bank: payment with an "account" line
 *    against an expense ledger.
 *  - Expense paid by a member from their own pocket: journal, debit the
 *    expense ledger and credit the member's party, so Sabha owes the member.
 *  - Settling a due: payment to / receipt from the party ("advance" line).
 */
class ZybraService
{
    protected const REPORT_TTL = 120;

    protected string $baseUrl;
    protected ?string $apiKey;
    protected int $depositAccountId;
    protected string $paymentMode;
    protected float $membershipFee;
    protected int $defaultStateId;
    protected int $membershipIncomeAccountId;
    protected int $eventIncomeAccountId;

    public function __construct()
    {
        $config = config('services.zybra');

        $this->baseUrl = rtrim((string) ($config['base_url'] ?? 'https://api.zybra.in/api/v1'), '/');
        $this->apiKey = $config['api_key'] ?? null;
        $this->depositAccountId = (int) ($config['deposit_account_id'] ?? 0);
        $this->paymentMode = (string) ($config['payment_mode'] ?? 'Cash');
        $this->membershipFee = (float) ($config['membership_fee'] ?? 1000.00);
        $this->defaultStateId = (int) ($config['default_state_id'] ?? 24);
        $this->membershipIncomeAccountId = (int) ($config['membership_income_account_id'] ?? 0);
        $this->eventIncomeAccountId = (int) ($config['event_income_account_id'] ?? 0);
    }

    /**
     * True when a Zybra API key (zbk_ + 48 hex characters) is configured.
     */
    public function isConfigured(): bool
    {
        return is_string($this->apiKey) && preg_match('/^zbk_[0-9a-f]{48}$/i', trim($this->apiKey)) === 1;
    }

    public function defaultCashAccountId(): int
    {
        return $this->depositAccountId;
    }

    public function defaultPaymentModeId(): ?int
    {
        return $this->paymentModeId();
    }

    /* ------------------------------------------------------------------ */
    /* HTTP                                                                */
    /* ------------------------------------------------------------------ */

    /**
     * Base request. Zybra authenticates with the API key as a Bearer token.
     * Only 5xx and connection failures are retried; the Idempotency-Key is
     * kept on retries, so a retried write is not duplicated.
     */
    protected function request(?string $idempotencyKey = null): PendingRequest
    {
        $request = Http::baseUrl($this->baseUrl)
            ->withToken(trim((string) $this->apiKey))
            ->acceptJson()
            ->asJson()
            ->timeout(15)
            ->retry(3, 1000, fn (Exception $e) => $e instanceof ConnectionException
                || ($e instanceof RequestException && $e->response->serverError()), throw: false);

        if ($idempotencyKey) {
            $request->withHeaders(['Idempotency-Key' => $idempotencyKey]);
        }

        return $request;
    }

    /**
     * Turn a Zybra error response ({"error": [{message, code, path}]}) into one line.
     */
    protected function errorMessage(Response $response): string
    {
        $errors = $response->json('error');
        if (is_array($errors) && $errors) {
            return collect($errors)->map(fn ($e) => trim(($e['code'] ?? '') . ' ' . ($e['message'] ?? '') . (isset($e['path']) ? " ({$e['path']})" : '')))->implode('; ');
        }

        return 'HTTP ' . $response->status() . ': ' . Str::limit($response->body(), 300);
    }

    protected function failIfUnsuccessful(Response $response, string $action): void
    {
        if (! $response->successful()) {
            throw new ZybraException("{$action} failed: " . $this->errorMessage($response), $response->status());
        }
    }

    protected function get(string $path, array $query = []): Response
    {
        try {
            $response = $this->request()->get($path, $query);
        } catch (ConnectionException $e) {
            throw new ZybraException("GET {$path} failed: " . $e->getMessage());
        }
        $this->failIfUnsuccessful($response, "GET {$path}");

        return $response;
    }

    /**
     * All rows of a list endpoint ({"data": [...], "pagination": {...}}).
     */
    protected function fetchAll(string $path, array $query = [], int $maxPages = 50): array
    {
        $rows = [];
        $page = 1;

        do {
            $response = $this->get($path, $query + ['page' => $page, 'per_page' => 100]);
            $rows = array_merge($rows, $response->json('data', []));
            $totalPages = (int) ($response->json('pagination.totalPages') ?? 1);
            $page++;
        } while ($page <= $totalPages && $page <= $maxPages);

        return $rows;
    }

    protected function cacheKey(string $key): string
    {
        return 'zybra:' . substr(sha1((string) $this->apiKey), 0, 12) . ':' . $key;
    }

    protected function remember(string $key, int $seconds, callable $callback): mixed
    {
        return Cache::remember($this->cacheKey($key), $seconds, $callback);
    }

    /**
     * Forget cached balances, transactions and dues so the report shows fresh data.
     */
    public function clearReportCache(): void
    {
        foreach (['cash-accounts', 'transactions', 'dues'] as $key) {
            Cache::forget($this->cacheKey($key));
        }
    }

    /* ------------------------------------------------------------------ */
    /* Lookups                                                             */
    /* ------------------------------------------------------------------ */

    /**
     * Cash and bank accounts with their current balance.
     */
    public function cashAccounts(): array
    {
        return $this->remember('cash-accounts', self::REPORT_TTL, fn () => collect($this->get('/bank-accounts', ['per_page' => 100])->json('data', []))
            ->map(fn ($a) => [
                'id' => (int) $a['id'],
                'name' => (string) ($a['name'] ?? ''),
                'type' => (string) ($a['bankType'] ?? ''),
                'balance' => (float) ($a['balance'] ?? 0),
            ])->values()->all());
    }

    /**
     * Ledger accounts of one type (income, expense, ...), cached for a day.
     */
    public function ledgerAccounts(string $type): array
    {
        $all = $this->remember('accounts', 86400, fn () => collect($this->fetchAll('/accounts'))
            ->map(fn ($a) => ['id' => (int) $a['id'], 'name' => (string) ($a['name'] ?? ''), 'type' => (string) ($a['accountType'] ?? '')])
            ->all());

        return collect($all)->where('type', $type)->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)->values()->all();
    }

    public function paymentModes(): array
    {
        return $this->remember('payment-modes', 86400, fn () => collect($this->get('/payment-modes')->json('data', []))
            ->map(fn ($m) => ['id' => (int) $m['id'], 'name' => (string) ($m['name'] ?? '')])
            ->values()->all());
    }

    protected function paymentModeId(): ?int
    {
        $mode = collect($this->paymentModes())->first(fn ($m) => strcasecmp(trim($m['name']), $this->paymentMode) === 0);

        return $mode['id'] ?? null;
    }

    protected function accountName(int $id): ?string
    {
        foreach (['income', 'expense'] as $type) {
            foreach ($this->ledgerAccounts($type) as $account) {
                if ($account['id'] === $id) {
                    return $account['name'];
                }
            }
        }

        return null;
    }

    /**
     * Default numbering series for a voucher type (receipt, payment, journal),
     * so Sabha's entries continue Zybra's own RC- / PY- / JV- numbers.
     */
    protected function seriesId(string $voucherType): int
    {
        return $this->remember('series:' . $voucherType, 86400, function () use ($voucherType) {
            $series = array_values(array_filter($this->get('/voucher-init', ['voucher_type' => $voucherType])->json('voucherAutoSeries', []), 'is_array'));
            if (! $series) {
                throw new ZybraException("Zybra has no numbering series for {$voucherType} vouchers.");
            }

            return (int) (collect($series)->firstWhere('isDefault', true)['id'] ?? $series[0]['id']);
        });
    }

    /**
     * Read-only check of the key and every lookup Sabha depends on.
     * Used by `php artisan zybra:check`.
     */
    public function diagnostics(): array
    {
        $checks = [];
        $run = function (string $label, callable $fn) use (&$checks) {
            try {
                $checks[] = [$label, true, (string) $fn()];
            } catch (Exception $e) {
                $checks[] = [$label, false, $e->getMessage()];
            }
        };

        $run('API key format', fn () => $this->isConfigured() ? 'ok' : throw new ZybraException('ZYBRA_API_KEY must be zbk_ followed by 48 hex characters'));
        if (! $this->isConfigured()) {
            return $checks;
        }

        $run("Payment mode {$this->paymentMode}", fn () => ($id = $this->paymentModeId()) ? "id {$id}" : throw new ZybraException('not found in GET /payment-modes'));
        $run("Deposit account {$this->depositAccountId}", function () {
            $account = collect($this->cashAccounts())->firstWhere('id', $this->depositAccountId);

            return $account ? "{$account['name']} ({$account['type']}), balance ₹" . number_format($account['balance'], 2) : throw new ZybraException('not found in GET /bank-accounts');
        });
        foreach (['Membership income ledger' => $this->membershipIncomeAccountId, 'Event income ledger' => $this->eventIncomeAccountId] as $label => $id) {
            $run("{$label} {$id}", fn () => collect($this->ledgerAccounts('income'))->firstWhere('id', $id)['name'] ?? throw new ZybraException('not an income account in GET /accounts'));
        }
        foreach (['receipt', 'payment', 'journal'] as $type) {
            $run("Numbering series ({$type})", fn () => 'id ' . $this->seriesId($type));
        }

        return $checks;
    }

    /* ------------------------------------------------------------------ */
    /* Report                                                              */
    /* ------------------------------------------------------------------ */

    /**
     * Every receipt (money in) and payment (money out) in Zybra, newest first.
     */
    public function transactions(): array
    {
        return $this->remember('transactions', self::REPORT_TTL, function () {
            $map = fn (array $row, string $direction) => [
                'voucher_type' => $direction === 'in' ? 'receipt' : 'payment',
                'id' => (int) $row['id'],
                'number' => (string) ($row['voucherNumber'] ?? ''),
                'date' => (string) ($row['date'] ?? ''),
                'direction' => $direction,
                'amount' => (float) ($row['totalAmount'] ?? 0),
                'party' => $row['partyNames'] ?? null,
                'account' => $row['accountNames'] ?? null,
                'mode' => $row['paymentModeName'] ?? null,
                'cash_account' => $row[$direction === 'in' ? 'depositToAccountName' : 'paidFromAccountName'] ?? null,
                'reference' => $row['referenceNumber'] ?? null,
                'notes' => $row['notes'] ?? null,
            ];

            $receipts = collect($this->fetchAll('/receipts'))->where('voucherType', 'receipt')->map(fn ($r) => $map($r, 'in'));
            $payments = collect($this->fetchAll('/payments'))->where('voucherType', 'payment')->map(fn ($r) => $map($r, 'out'));

            return $receipts->concat($payments)
                ->sortByDesc(fn ($t) => $t['date'] . sprintf('%020d', $t['id']))
                ->values()->all();
        });
    }

    /**
     * Parties whose balance is not zero. net > 0: the party owes Sabha;
     * net < 0: Sabha owes the party.
     */
    public function partiesWithDues(): array
    {
        return $this->remember('dues', self::REPORT_TTL, fn () => collect($this->fetchAll('/parties', ['status' => 'all']))
            ->map(fn ($p) => [
                'id' => (int) $p['id'],
                'name' => (string) ($p['name'] ?? ''),
                'mobile' => $p['mobile'] ?? null,
                'net' => round((float) ($p['receivable'] ?? 0) - (float) ($p['payable'] ?? 0), 2),
            ])
            ->filter(fn ($p) => abs($p['net']) >= 0.01)
            ->sortByDesc(fn ($p) => abs($p['net']))
            ->values()->all());
    }

    /* ------------------------------------------------------------------ */
    /* Parties                                                             */
    /* ------------------------------------------------------------------ */

    protected function mobile(?string $raw): ?string
    {
        $digits = substr(preg_replace('/\D/', '', (string) $raw), -10);

        return strlen($digits) === 10 ? $digits : null;
    }

    /**
     * Find a Zybra party that is this member: same mobile number, or the exact
     * same name where Zybra has no different mobile on record.
     */
    protected function findExistingParty(User $user): ?int
    {
        $mobile = $this->mobile($user->phone);

        if ($mobile) {
            $match = collect($this->get('/parties', ['search_by' => 'mobile', 'query' => $mobile, 'status' => 'all'])->json('data', []))
                ->first(fn ($p) => $this->mobile($p['mobile'] ?? null) === $mobile);
            if ($match) {
                return (int) $match['id'];
            }
        }

        $name = trim($user->name);
        $match = collect($this->get('/parties', ['search_by' => 'name', 'query' => $name, 'status' => 'all'])->json('data', []))
            ->first(function ($p) use ($name, $mobile) {
                $sameName = strcasecmp(preg_replace('/\s+/', ' ', trim((string) ($p['name'] ?? ''))), preg_replace('/\s+/', ' ', $name)) === 0;
                $partyMobile = $this->mobile($p['mobile'] ?? null);

                return $sameName && (! $partyMobile || ! $mobile || $partyMobile === $mobile);
            });

        return $match ? (int) $match['id'] : null;
    }

    /**
     * Create a customer party. Zybra party names are unique, so on
     * 409 DUPLICATE_CONTACT_NAME (a different person with the same name) the
     * name is retried with a Sabha suffix. The suffixed name is unique to this
     * member, so if it already exists it was created by an earlier sync.
     */
    protected function createParty(array $payload, string $suffix): int
    {
        $response = $this->request()->post('/parties', $payload);

        if ($response->status() === 409) {
            $payload['name'] = Str::limit($payload['name'], 100 - strlen($suffix) - 3, '') . " ({$suffix})";
            $response = $this->request()->post('/parties', $payload);

            if ($response->status() === 409) {
                $existing = collect($this->get('/parties', ['search_by' => 'name', 'query' => $payload['name'], 'status' => 'all'])->json('data', []))
                    ->first(fn ($p) => strcasecmp(trim((string) ($p['name'] ?? '')), $payload['name']) === 0);

                if ($existing) {
                    return (int) $existing['id'];
                }
            }
        }

        $this->failIfUnsuccessful($response, 'POST /parties');

        return (int) $response->json('id');
    }

    /**
     * The member's Zybra party id: linked before, found in Zybra, or created.
     */
    public function syncParty(User $user): int
    {
        if ($user->zybra_party_id) {
            return (int) $user->zybra_party_id;
        }

        $partyId = $this->findExistingParty($user);

        if (! $partyId) {
            $gstin = filled($user->gstin) ? strtoupper(trim($user->gstin)) : null;

            $partyId = $this->createParty(array_filter([
                'name' => Str::limit(trim($user->name), 100, ''),
                'partyType' => 'both',
                'gstType' => $gstin ? 'regular' : 'unregistered',
                'gstn' => $gstin,
                'pan' => filled($user->pan) ? strtoupper(trim($user->pan)) : null,
                'email' => $user->email,
                'mobile' => $this->mobile($user->phone),
                'addresses' => [array_filter([
                    'type' => 'billing',
                    'street' => $user->residence_address ?: null,
                    'city' => $user->city ?: null,
                    'stateId' => $gstin && ctype_digit(substr($gstin, 0, 2)) ? (int) substr($gstin, 0, 2)
                        : (ctype_digit((string) $user->state_code) ? (int) $user->state_code : $this->defaultStateId),
                    'pincode' => preg_match('/^\d{6}$/', (string) $user->pincode) ? $user->pincode : null,
                    'country' => 'India',
                ], fn ($v) => $v !== null)],
            ], fn ($v) => $v !== null), 'SABHA-' . $user->id);
        }

        $user->update(['zybra_party_id' => $partyId]);

        return $partyId;
    }

    /* ------------------------------------------------------------------ */
    /* Writes                                                              */
    /* ------------------------------------------------------------------ */

    /**
     * POST a receipt, payment or journal and return [id, voucherNumber].
     */
    protected function createVoucher(string $voucherType, array $payload, string $idempotencyKey): array
    {
        $path = '/' . $voucherType . 's';

        try {
            $response = $this->request($idempotencyKey)->post($path, $payload);
        } catch (ConnectionException $e) {
            throw new ZybraException("POST {$path} failed: " . $e->getMessage());
        }
        $this->failIfUnsuccessful($response, "POST {$path}");

        $id = (int) ($response->json('id') ?? $response->json('voucherId') ?? $response->json('data.id'));
        $number = $response->json('voucherNumber') ?? $response->json('data.voucherNumber');

        if (! $number && $id) {
            try {
                $number = $this->get("{$path}/{$id}")->json('voucherNumber');
            } catch (ZybraException $e) {
                Log::warning("Zybra {$voucherType} {$id} created but its number could not be read: " . $e->getMessage());
            }
        }

        $this->clearReportCache();

        return [$id, $number ? (string) $number : null];
    }

    protected function log(string $type, string $voucherType, array $voucher, array $data): FinancialEntry
    {
        return FinancialEntry::create($data + [
            'type' => $type,
            'zybra_voucher_type' => $voucherType,
            'zybra_voucher_id' => $voucher[0],
            'zybra_voucher_number' => $voucher[1],
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Money in, booked to an income ledger.
     *
     * @param  array{date: string, amount: float, account_id: int, cash_account_id: int, payment_mode_id: ?int, reference: ?string, description: string}  $d
     */
    public function recordIncome(array $d, string $idempotencyKey, string $type = 'income', ?int $memberId = null): FinancialEntry
    {
        $voucher = $this->createVoucher('receipt', [
            'date' => $d['date'],
            'isAutoNumber' => true,
            'voucherAutoSeriesId' => $this->seriesId('receipt'),
            'depositToAccountId' => $d['cash_account_id'],
            'paymentModeId' => $d['payment_mode_id'],
            'referenceNumber' => $d['reference'] ?: null,
            'notes' => $d['description'],
            'lines' => [[
                'lineType' => 'account',
                'oppositeAccountId' => $d['account_id'],
                'amount' => round($d['amount'], 2),
                'description' => $d['description'],
            ]],
        ], $idempotencyKey);

        return $this->log($type, 'receipt', $voucher, [
            'entry_date' => $d['date'],
            'amount' => $d['amount'],
            'zybra_account_id' => $d['account_id'],
            'zybra_account_name' => $this->accountName($d['account_id']),
            'member_id' => $memberId,
            'reference' => $d['reference'] ?: null,
            'description' => $d['description'],
        ]);
    }

    /**
     * Money out of Sabha's cash/bank, booked to an expense ledger.
     */
    public function recordExpense(array $d, string $idempotencyKey): FinancialEntry
    {
        $voucher = $this->createVoucher('payment', [
            'date' => $d['date'],
            'isAutoNumber' => true,
            'voucherAutoSeriesId' => $this->seriesId('payment'),
            'paidFromAccountId' => $d['cash_account_id'],
            'paymentModeId' => $d['payment_mode_id'],
            'referenceNumber' => $d['reference'] ?: null,
            'notes' => $d['description'],
            'lines' => [[
                'lineType' => 'account',
                'oppositeAccountId' => $d['account_id'],
                'amount' => round($d['amount'], 2),
                'description' => $d['description'],
            ]],
        ], $idempotencyKey);

        return $this->log('expense', 'payment', $voucher, [
            'entry_date' => $d['date'],
            'amount' => $d['amount'],
            'zybra_account_id' => $d['account_id'],
            'zybra_account_name' => $this->accountName($d['account_id']),
            'reference' => $d['reference'] ?: null,
            'description' => $d['description'],
        ]);
    }

    /**
     * An expense a member paid from their own pocket: no cash moves; the
     * expense is booked and Sabha now owes the member (journal entry).
     */
    public function recordExpensePaidByMember(array $d, User $member, string $idempotencyKey): FinancialEntry
    {
        $partyId = $this->syncParty($member);
        $amount = round($d['amount'], 2);

        $voucher = $this->createVoucher('journal', [
            'date' => $d['date'],
            'isAutoNumber' => true,
            'voucherAutoSeriesId' => $this->seriesId('journal'),
            'referenceNumber' => $d['reference'] ?: null,
            'notes' => $d['description'] . ' (paid by ' . $member->name . ')',
            'items' => [
                ['accountId' => $d['account_id'], 'debit' => $amount, 'credit' => 0, 'description' => $d['description']],
                ['accountId' => $partyId, 'debit' => 0, 'credit' => $amount, 'description' => 'Paid by ' . $member->name],
            ],
        ], $idempotencyKey);

        return $this->log('expense_by_member', 'journal', $voucher, [
            'entry_date' => $d['date'],
            'amount' => $d['amount'],
            'zybra_account_id' => $d['account_id'],
            'zybra_account_name' => $this->accountName($d['account_id']),
            'zybra_party_id' => $partyId,
            'party_name' => $member->name,
            'member_id' => $member->id,
            'reference' => $d['reference'] ?: null,
            'description' => $d['description'],
        ]);
    }

    /**
     * The party's unpaid sales invoices, oldest first: [[id, balance], ...].
     */
    protected function unpaidInvoices(string $partyName): array
    {
        // No status filter: "unpaid" leaves out overdue invoices; balance > 0 covers unpaid, partial and overdue.
        return collect($this->fetchAll('/invoices', ['query' => $partyName], 5))
            ->filter(fn ($i) => strcasecmp(trim((string) ($i['partyName'] ?? '')), trim($partyName)) === 0 && (float) ($i['balance'] ?? 0) > 0)
            ->unique('id')
            ->sortBy(fn ($i) => ($i['date'] ?? '') . sprintf('%020d', $i['id']))
            ->map(fn ($i) => ['id' => (int) $i['id'], 'balance' => round((float) $i['balance'], 2)])
            ->values()->all();
    }

    /**
     * Receipt lines for money received from a party: applied to their unpaid
     * invoices oldest first (bill_to_bill), any remainder kept as an advance.
     */
    protected function receiptLinesForParty(int $partyId, string $partyName, float $amount, string $description): array
    {
        $left = round($amount, 2);
        $vouchers = [];

        foreach ($this->unpaidInvoices($partyName) as $invoice) {
            if ($left <= 0) {
                break;
            }
            $apply = min($left, $invoice['balance']);
            $vouchers[] = ['id' => $invoice['id'], 'amount' => $apply];
            $left = round($left - $apply, 2);
        }

        $lines = [];
        if ($vouchers) {
            $lines[] = ['lineType' => 'bill_to_bill', 'partyId' => $partyId, 'amount' => round(array_sum(array_column($vouchers, 'amount')), 2), 'vouchers' => $vouchers, 'description' => $description];
        }
        if ($left > 0) {
            $lines[] = ['lineType' => 'advance', 'partyId' => $partyId, 'amount' => $left, 'description' => $description];
        }

        return $lines;
    }

    /**
     * Settle (part of) a party's due: "pay" = Sabha pays the party (money out),
     * "receive" = the party pays Sabha (money in, applied to their unpaid invoices).
     */
    public function settleDue(string $direction, int $partyId, string $partyName, array $d, string $idempotencyKey): FinancialEntry
    {
        $receive = $direction === 'receive';
        $voucherType = $receive ? 'receipt' : 'payment';

        $voucher = $this->createVoucher($voucherType, [
            'date' => $d['date'],
            'isAutoNumber' => true,
            'voucherAutoSeriesId' => $this->seriesId($voucherType),
            $receive ? 'depositToAccountId' : 'paidFromAccountId' => $d['cash_account_id'],
            'paymentModeId' => $d['payment_mode_id'],
            'referenceNumber' => $d['reference'] ?: null,
            'notes' => $d['description'],
            'lines' => $receive
                ? $this->receiptLinesForParty($partyId, $partyName, $d['amount'], $d['description'])
                : [['lineType' => 'advance', 'partyId' => $partyId, 'amount' => round($d['amount'], 2), 'description' => $d['description']]],
        ], $idempotencyKey);

        return $this->log($receive ? 'settle_receive' : 'settle_pay', $voucherType, $voucher, [
            'entry_date' => $d['date'],
            'amount' => $d['amount'],
            'zybra_party_id' => $partyId,
            'party_name' => $partyName,
            'member_id' => User::where('zybra_party_id', $partyId)->value('id'),
            'reference' => $d['reference'] ?: null,
            'description' => $d['description'],
        ]);
    }

    /* ------------------------------------------------------------------ */
    /* Automatic entries from approvals                                    */
    /* ------------------------------------------------------------------ */

    /**
     * Record a membership fee received (called when an admin approves the payment).
     */
    public function recordMembershipPayment(User $user, ?float $amount = null, ?string $refNo = null): array
    {
        if (! $this->isConfigured()) {
            $user->update(['zybra_sync_status' => 'pending_credentials', 'zybra_error' => 'Zybra API key not configured in .env']);

            return ['success' => false, 'message' => 'Zybra API key not configured. Saved locally.'];
        }

        if ($user->zybra_membership_receipt_no) {
            return ['success' => true, 'message' => 'Already synced.', 'receipt_number' => $user->zybra_membership_receipt_no];
        }

        try {
            $entry = $this->recordIncome([
                'date' => now()->toDateString(),
                'amount' => $amount ?: $this->membershipFee,
                'account_id' => $this->membershipIncomeAccountId,
                'cash_account_id' => $this->depositAccountId,
                'payment_mode_id' => $this->paymentModeId(),
                'reference' => $refNo ?: 'SABHA-MEM-' . $user->id,
                'description' => 'Membership fee - ' . $user->name,
            ], (string) Str::uuid(), 'membership', $user->id);

            $user->update([
                'zybra_membership_receipt_no' => $entry->zybra_voucher_number ?: (string) $entry->zybra_voucher_id,
                'zybra_sync_status' => 'synced',
                'zybra_error' => null,
            ]);

            return ['success' => true, 'receipt_number' => $user->zybra_membership_receipt_no];
        } catch (Exception $e) {
            Log::error("Zybra membership sync failed for User {$user->id}: " . $e->getMessage());
            $user->update(['zybra_sync_status' => 'failed', 'zybra_error' => Str::limit($e->getMessage(), 1000)]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Record an event ticket payment received (called when an admin approves the booking).
     */
    public function recordEventPayment(EventRegistration $registration): array
    {
        if (! $this->isConfigured()) {
            $registration->update(['zybra_sync_status' => 'pending_credentials', 'zybra_error' => 'Zybra API key not configured in .env']);

            return ['success' => false, 'message' => 'Zybra API key not configured. Saved locally.'];
        }

        if ($registration->zybra_receipt_no || $registration->zybra_sync_status === 'synced') {
            return ['success' => true, 'message' => 'Already synced.', 'receipt_number' => $registration->zybra_receipt_no];
        }

        $registration->loadMissing(['user', 'event']);
        $event = $registration->event;

        $price = (float) ($registration->amount_paid ?: ($event?->price_normal ?: 0));
        if ($price <= 0) {
            // Free event passes bring in no money
            $registration->update(['zybra_sync_status' => 'synced', 'zybra_error' => null]);

            return ['success' => true, 'message' => 'Free event pass, nothing to record.'];
        }

        try {
            $entry = $this->recordIncome([
                'date' => now()->toDateString(),
                'amount' => $price,
                'account_id' => $this->eventIncomeAccountId,
                'cash_account_id' => $this->depositAccountId,
                'payment_mode_id' => $this->paymentModeId(),
                'reference' => $registration->ticket_number ?: 'SABHA-TKT-' . $registration->id,
                'description' => 'Event ticket - ' . ($event?->title ?: 'Sabha Event') . ' - ' . ($registration->attendeeName() ?: 'Guest'),
            ], (string) Str::uuid(), 'event', $registration->user_id);

            $registration->update([
                'zybra_receipt_no' => $entry->zybra_voucher_number ?: (string) $entry->zybra_voucher_id,
                'zybra_sync_status' => 'synced',
                'zybra_error' => null,
            ]);

            return ['success' => true, 'receipt_number' => $registration->zybra_receipt_no];
        } catch (Exception $e) {
            Log::error("Zybra event sync failed for Registration {$registration->id}: " . $e->getMessage());
            $registration->update(['zybra_sync_status' => 'failed', 'zybra_error' => Str::limit($e->getMessage(), 1000)]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
