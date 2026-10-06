<?php

namespace App\Livewire\Admin\Finance;

use App\Models\FinancialEntry;
use App\Models\User;
use App\Services\ZybraException;
use App\Services\ZybraService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Financial report (main admin only). Balances, money in/out and dues are read
 * live from Zybra; every entry added here is posted to Zybra first and only
 * logged locally once Zybra has accepted it.
 */
class Index extends Component
{
    use WithPagination;

    protected const PER_PAGE = 25;

    // Filters
    public string $period = 'fy';

    public string $from = '';

    public string $to = '';

    public string $direction = 'all';

    public string $search = '';

    public string $successMsg = '';

    // Add income / expense modal
    public bool $showEntryModal = false;

    public string $entryType = 'income';

    public string $entryDate = '';

    public string $entryAmount = '';

    public $entryAccountId = null;

    public string $paidBy = 'sabha';

    public $memberId = null;

    public $cashAccountId = null;

    public $paymentModeId = null;

    public string $reference = '';

    public string $description = '';

    #[Locked]
    public string $entryKey = '';

    // Settle due modal
    #[Locked]
    public ?int $settlePartyId = null;

    #[Locked]
    public string $settlePartyName = '';

    #[Locked]
    public string $settleDirection = '';

    #[Locked]
    public float $settleMax = 0;

    public string $settleAmount = '';

    public string $settleDate = '';

    public $settleCashAccountId = null;

    public $settlePaymentModeId = null;

    public string $settleReference = '';

    public string $settleDescription = '';

    #[Locked]
    public string $settleKey = '';

    public function mount(): void
    {
        $this->ensureMainAdmin();
    }

    protected function ensureMainAdmin(): void
    {
        abort_unless(auth()->check() && auth()->user()->role === 'admin', 403);
    }

    protected function zybra(): ZybraService
    {
        return app(ZybraService::class);
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['period', 'from', 'to', 'direction', 'search'], true)) {
            $this->resetPage();
        }
    }

    public function refresh(): void
    {
        $this->ensureMainAdmin();
        $this->zybra()->clearReportCache();
    }

    /* ------------------------------------------------------------------ */
    /* Add income / expense                                                */
    /* ------------------------------------------------------------------ */

    public function openEntry(string $type): void
    {
        $this->ensureMainAdmin();
        $this->resetErrorBag();
        $this->successMsg = '';

        $this->entryType = $type === 'expense' ? 'expense' : 'income';
        $this->entryDate = now()->toDateString();
        $this->entryAmount = '';
        $this->entryAccountId = null;
        $this->paidBy = 'sabha';
        $this->memberId = null;
        $this->cashAccountId = $this->zybra()->defaultCashAccountId() ?: null;
        $this->paymentModeId = $this->zybra()->defaultPaymentModeId();
        $this->reference = '';
        $this->description = '';
        $this->entryKey = (string) Str::uuid();
        $this->showEntryModal = true;
    }

    public function closeEntry(): void
    {
        $this->showEntryModal = false;
    }

    public function saveEntry(): void
    {
        $this->ensureMainAdmin();

        $zybra = $this->zybra();
        $byMember = $this->entryType === 'expense' && $this->paidBy === 'member';
        $ledgerIds = collect($zybra->ledgerAccounts($this->entryType))->pluck('id')->all();

        $this->validate([
            'entryType' => 'required|in:income,expense',
            'entryDate' => 'required|date|before_or_equal:today',
            'entryAmount' => 'required|numeric|min:0.01|max:99999999',
            'entryAccountId' => ['required', 'integer', 'in:' . implode(',', $ledgerIds)],
            'paidBy' => 'required|in:sabha,member',
            'memberId' => $byMember ? 'required|integer|exists:users,id' : 'nullable',
            'cashAccountId' => $byMember ? 'nullable' : ['required', 'integer', 'in:' . collect($zybra->cashAccounts())->pluck('id')->implode(',')],
            'paymentModeId' => ['nullable', 'integer', 'in:' . collect($zybra->paymentModes())->pluck('id')->implode(',')],
            'reference' => 'nullable|string|max:100',
            'description' => 'required|string|max:250',
        ], [], [
            'entryDate' => 'date',
            'entryAmount' => 'amount',
            'entryAccountId' => 'category',
            'memberId' => 'member',
            'cashAccountId' => 'cash / bank account',
            'paymentModeId' => 'payment mode',
        ]);

        $data = [
            'date' => $this->entryDate,
            'amount' => (float) $this->entryAmount,
            'account_id' => (int) $this->entryAccountId,
            'cash_account_id' => (int) $this->cashAccountId,
            'payment_mode_id' => $this->paymentModeId ? (int) $this->paymentModeId : null,
            'reference' => trim($this->reference),
            'description' => trim($this->description),
        ];

        try {
            if ($this->entryType === 'income') {
                $entry = $zybra->recordIncome($data, $this->entryKey);
            } elseif ($byMember) {
                $entry = $zybra->recordExpensePaidByMember($data, User::findOrFail((int) $this->memberId), $this->entryKey);
            } else {
                $entry = $zybra->recordExpense($data, $this->entryKey);
            }
        } catch (ZybraException $e) {
            if (! $e->isRetryableWithSameKey()) {
                $this->entryKey = (string) Str::uuid();
            }
            $this->addError('zybra', 'Not saved — Zybra rejected the entry: ' . $e->getMessage());

            return;
        }

        $this->showEntryModal = false;
        $this->successMsg = $entry->typeLabel() . ' of ₹' . number_format((float) $entry->amount, 2)
            . ' saved in Zybra as ' . ($entry->zybra_voucher_number ?: '#' . $entry->zybra_voucher_id) . '.';
    }

    /* ------------------------------------------------------------------ */
    /* Settle a due                                                        */
    /* ------------------------------------------------------------------ */

    public function openSettle(int $partyId): void
    {
        $this->ensureMainAdmin();
        $this->resetErrorBag();
        $this->successMsg = '';

        $party = collect($this->zybra()->partiesWithDues())->firstWhere('id', $partyId);
        if (! $party) {
            $this->successMsg = 'That due is already settled.';

            return;
        }

        $this->settlePartyId = $party['id'];
        $this->settlePartyName = $party['name'];
        $this->settleDirection = $party['net'] > 0 ? 'receive' : 'pay';
        $this->settleMax = abs($party['net']);
        $this->settleAmount = number_format(abs($party['net']), 2, '.', '');
        $this->settleDate = now()->toDateString();
        $this->settleCashAccountId = $this->zybra()->defaultCashAccountId() ?: null;
        $this->settlePaymentModeId = $this->zybra()->defaultPaymentModeId();
        $this->settleReference = '';
        $this->settleDescription = ($this->settleDirection === 'receive' ? 'Due received from ' : 'Due paid to ') . $party['name'];
        $this->settleKey = (string) Str::uuid();
    }

    public function closeSettle(): void
    {
        $this->settlePartyId = null;
    }

    public function saveSettle(): void
    {
        $this->ensureMainAdmin();

        $zybra = $this->zybra();

        $this->validate([
            'settleAmount' => 'required|numeric|min:0.01|max:' . $this->settleMax,
            'settleDate' => 'required|date|before_or_equal:today',
            'settleCashAccountId' => ['required', 'integer', 'in:' . collect($zybra->cashAccounts())->pluck('id')->implode(',')],
            'settlePaymentModeId' => ['nullable', 'integer', 'in:' . collect($zybra->paymentModes())->pluck('id')->implode(',')],
            'settleReference' => 'nullable|string|max:100',
            'settleDescription' => 'required|string|max:250',
        ], [
            'settleAmount.max' => 'The amount cannot be more than the due of ₹' . number_format($this->settleMax, 2) . '.',
        ], [
            'settleAmount' => 'amount',
            'settleDate' => 'date',
            'settleCashAccountId' => 'cash / bank account',
            'settlePaymentModeId' => 'payment mode',
            'settleDescription' => 'description',
        ]);

        try {
            $entry = $zybra->settleDue($this->settleDirection, $this->settlePartyId, $this->settlePartyName, [
                'date' => $this->settleDate,
                'amount' => (float) $this->settleAmount,
                'cash_account_id' => (int) $this->settleCashAccountId,
                'payment_mode_id' => $this->settlePaymentModeId ? (int) $this->settlePaymentModeId : null,
                'reference' => trim($this->settleReference),
                'description' => trim($this->settleDescription),
            ], $this->settleKey);
        } catch (ZybraException $e) {
            if (! $e->isRetryableWithSameKey()) {
                $this->settleKey = (string) Str::uuid();
            }
            $this->addError('zybra', 'Not saved — Zybra rejected the entry: ' . $e->getMessage());

            return;
        }

        $this->settlePartyId = null;
        $this->successMsg = $entry->typeLabel() . ' of ₹' . number_format((float) $entry->amount, 2) . ' (' . $entry->party_name . ')'
            . ' saved in Zybra as ' . ($entry->zybra_voucher_number ?: '#' . $entry->zybra_voucher_id) . '.';
    }

    /* ------------------------------------------------------------------ */
    /* Render                                                              */
    /* ------------------------------------------------------------------ */

    /**
     * [from, to] dates (Y-m-d, inclusive) for the selected period; nulls mean open-ended.
     */
    protected function range(): array
    {
        $today = Carbon::today();
        $fyStart = $today->month >= 4 ? $today->copy()->setDate($today->year, 4, 1) : $today->copy()->setDate($today->year - 1, 4, 1);

        return match ($this->period) {
            'month' => [$today->copy()->startOfMonth()->toDateString(), $today->copy()->endOfMonth()->toDateString()],
            'last_month' => [$today->copy()->subMonthNoOverflow()->startOfMonth()->toDateString(), $today->copy()->subMonthNoOverflow()->endOfMonth()->toDateString()],
            'fy' => [$fyStart->toDateString(), $fyStart->copy()->addYear()->subDay()->toDateString()],
            'custom' => [$this->from ?: null, $this->to ?: null],
            default => [null, null],
        };
    }

    public function render()
    {
        $zybra = $this->zybra();
        $data = ['configured' => $zybra->isConfigured(), 'loadError' => null];

        if (! $data['configured']) {
            return view('livewire.admin.finance.index', $data);
        }

        try {
            $cashAccounts = $zybra->cashAccounts();
            $transactions = collect($zybra->transactions());
            $dues = collect($zybra->partiesWithDues());
            $incomeAccounts = $zybra->ledgerAccounts('income');
            $expenseAccounts = $zybra->ledgerAccounts('expense');
            $paymentModes = $zybra->paymentModes();
        } catch (ZybraException $e) {
            return view('livewire.admin.finance.index', $data + ['loadError' => $e->getMessage()]);
        }

        [$from, $to] = $this->range();
        $inPeriod = $transactions->filter(fn ($t) => (! $from || $t['date'] >= $from) && (! $to || $t['date'] <= $to));

        $filtered = $inPeriod
            ->when($this->direction !== 'all', fn ($c) => $c->where('direction', $this->direction))
            ->when(trim($this->search) !== '', function ($c) {
                $s = mb_strtolower(trim($this->search));

                return $c->filter(fn ($t) => str_contains(mb_strtolower(implode(' ', [$t['number'], $t['party'], $t['account'], $t['notes'], $t['reference'], $t['amount']])), $s));
            })
            ->values();

        $page = max(1, min($this->getPage(), (int) ceil(max(1, $filtered->count()) / self::PER_PAGE)));
        $pageItems = $filtered->slice(($page - 1) * self::PER_PAGE, self::PER_PAGE)->values();

        // Who in Sabha added each entry (entries made directly in Zybra have none)
        $local = FinancialEntry::with('creator:id,name')
            ->whereIn('zybra_voucher_id', $pageItems->pluck('id'))
            ->get()
            ->keyBy(fn ($e) => $e->zybra_voucher_type . ':' . $e->zybra_voucher_id);

        // Expenses paid by members move no cash, so they are listed separately
        $memberExpenses = FinancialEntry::with('creator:id,name')
            ->where('type', 'expense_by_member')
            ->when($from, fn ($q) => $q->whereDate('entry_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('entry_date', '<=', $to))
            ->latest('entry_date')->latest('id')
            ->get();

        $members = User::whereNotIn('role', ['admin', 'sub_admin'])->orderBy('name')->get(['id', 'name', 'phone']);
        $memberOptions = [];
        foreach ($members as $m) {
            $label = trim($m->name . ($m->phone ? ' · ' . $m->phone : ''));
            $memberOptions[isset($memberOptions[$label]) ? $label . ' #' . $m->id : $label] = $m->id;
        }

        return view('livewire.admin.finance.index', $data + [
            'cashAccounts' => $cashAccounts,
            'balance' => array_sum(array_column($cashAccounts, 'balance')),
            'moneyIn' => $inPeriod->where('direction', 'in')->sum('amount'),
            'moneyOut' => $inPeriod->where('direction', 'out')->sum('amount'),
            'rangeFrom' => $from,
            'rangeTo' => $to,
            'transactions' => new LengthAwarePaginator($pageItems, $filtered->count(), self::PER_PAGE, $page),
            'local' => $local,
            'memberExpenses' => $memberExpenses,
            'owedToSabha' => $dues->where('net', '>', 0)->values(),
            'owedBySabha' => $dues->where('net', '<', 0)->values(),
            'incomeAccounts' => $incomeAccounts,
            'expenseAccounts' => $expenseAccounts,
            'paymentModes' => $paymentModes,
            'memberOptions' => $memberOptions,
        ]);
    }
}
