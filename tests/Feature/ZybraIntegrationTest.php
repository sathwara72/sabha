<?php

namespace Tests\Feature;

use App\Livewire\Admin\Finance\Index as FinanceIndex;
use App\Livewire\Admin\Registrations\Index as RegistrationsIndex;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\FinancialEntry;
use App\Models\User;
use App\Services\EventTicketApprover;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Zybra cash-book integration and the admin Financial Report, against a faked
 * Zybra API (nothing is sent to the real books).
 */
class ZybraIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected const BASE = 'https://zybra.test/api/v1';

    /** POST/PUT requests sent to the fake Zybra, as [method, path, body, headers]. */
    protected array $writes = [];

    /** Parties the fake /parties search returns. */
    protected array $parties = [];

    /** Status code the fake returns for writes (to simulate rejections). */
    protected int $writeStatus = 201;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();

        config()->set('services.zybra', [
            'base_url' => self::BASE,
            'api_key' => 'zbk_' . str_repeat('ab', 24),
            'deposit_account_id' => 3107,
            'payment_mode' => 'Cash',
            'membership_fee' => 1000.00,
            'default_state_id' => 24,
            'membership_income_account_id' => 501,
            'event_income_account_id' => 502,
        ]);

        $this->parties = [
            ['id' => 7001, 'name' => 'Ramesh Patel', 'mobile' => '9800000001', 'receivable' => 1000, 'payable' => 0],
            ['id' => 7002, 'name' => 'Chandreshbhai', 'mobile' => '9800000002', 'receivable' => 0, 'payable' => 5000],
            ['id' => 7003, 'name' => 'Settled Person', 'mobile' => null, 'receivable' => 500, 'payable' => 500],
        ];

        Http::fake(fn (Request $request) => $this->fakeZybra($request));
    }

    protected function fakeZybra(Request $request)
    {
        $path = '/' . ltrim(substr(strtok($request->url(), '?'), strlen(self::BASE)), '/');
        parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);
        $list = fn (array $rows) => Http::response(['data' => $rows, 'pagination' => ['total' => count($rows), 'totalPages' => 1, 'currentPage' => 1]]);

        if ($request->method() !== 'GET') {
            $this->writes[] = [$request->method(), $path, $request->data(), $request->headers()];

            if ($this->writeStatus >= 400) {
                return Http::response(['error' => [['message' => 'Number must be greater than 0', 'code' => 'VALUE_TOO_SMALL', 'path' => 'lines.0.amount']]], $this->writeStatus);
            }

            return match ($path) {
                '/receipts' => Http::response(['id' => 9001], 201),
                '/payments' => Http::response(['id' => 9002, 'voucherNumber' => 'PY-2'], 201),
                '/journals' => Http::response(['id' => 9003, 'voucherNumber' => 'JV-1'], 201),
                '/parties' => Http::response(['id' => 7999, 'name' => $request['name']], 201),
                default => Http::response(['error' => [['code' => 'NOT_FOUND', 'message' => $path]]], 404),
            };
        }

        return match (true) {
            $path === '/payment-modes' => Http::response(['data' => [['id' => 3, 'name' => 'Cash'], ['id' => 4, 'name' => 'UPI']]]),
            $path === '/bank-accounts' => Http::response(['data' => [
                ['id' => 3107, 'name' => 'Cash', 'bankType' => 'cash', 'balance' => '82600.00'],
                ['id' => 3108, 'name' => 'HDFC', 'bankType' => 'bank', 'balance' => '10000.50'],
            ]]),
            $path === '/voucher-init' => Http::response(['voucherAutoSeries' => [['id' => ['receipt' => 11, 'payment' => 12, 'journal' => 13][$query['voucher_type']] ?? 10, 'isDefault' => true]]]),
            $path === '/accounts' => $list([
                ['id' => 501, 'name' => 'Sales', 'accountType' => 'income'],
                ['id' => 502, 'name' => 'General Income', 'accountType' => 'income'],
                ['id' => 601, 'name' => 'Travel Expense', 'accountType' => 'expense'],
                ['id' => 602, 'name' => 'Other Expense', 'accountType' => 'expense'],
            ]),
            $path === '/receipts' => $list([
                ['id' => 1, 'voucherType' => 'receipt', 'voucherNumber' => 'RC-1', 'date' => now()->toDateString(), 'totalAmount' => '1000.00', 'partyNames' => 'Ramesh Patel', 'paymentModeName' => 'Cash', 'depositToAccountName' => 'Cash'],
                ['id' => 2, 'voucherType' => 'journal', 'voucherNumber' => 'JV-9', 'date' => now()->toDateString(), 'totalAmount' => '777.00'],
                ['id' => 3, 'voucherType' => 'receipt', 'voucherNumber' => 'RC-OLD', 'date' => '2001-01-01', 'totalAmount' => '50.00', 'partyNames' => 'Old Donor'],
            ]),
            $path === '/receipts/9001' => Http::response(['id' => 9001, 'voucherNumber' => 'RC-90']),
            $path === '/payments' => $list([
                ['id' => 5, 'voucherType' => 'payment', 'voucherNumber' => 'PY-1', 'date' => now()->toDateString(), 'totalAmount' => '2400.00', 'partyNames' => 'Vidhi Industries', 'paymentModeName' => 'Cash', 'paidFromAccountName' => 'Cash'],
            ]),
            $path === '/invoices' => $list([
                ['id' => 8001, 'voucherNumber' => 'INV-15', 'date' => '2025-10-12', 'partyName' => 'Ramesh Patel', 'balance' => 600, 'status' => 'overdue'],
                ['id' => 8002, 'voucherNumber' => 'INV-16', 'date' => '2025-11-12', 'partyName' => 'Ramesh Patel', 'balance' => 400, 'status' => 'partial'],
                ['id' => 8003, 'voucherNumber' => 'INV-17', 'date' => '2025-11-12', 'partyName' => 'Ramesh Patel Jr', 'balance' => 999],
            ]),
            $path === '/parties' => $list(array_values(array_filter($this->parties, function ($p) use ($query) {
                $q = mb_strtolower($query['query'] ?? '');

                return $q === '' || str_contains(mb_strtolower(($query['search_by'] ?? '') === 'mobile' ? (string) $p['mobile'] : $p['name']), $q);
            }))),
            default => Http::response(['error' => [['code' => 'NOT_FOUND', 'message' => $path]]], 404),
        };
    }

    protected function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    protected function onlyWrite(): array
    {
        $this->assertCount(1, $this->writes, 'Expected exactly one write to Zybra, got: ' . json_encode(array_column($this->writes, 1)));

        return $this->writes[0];
    }

    /* ---------------------------------------------------------------- */
    /* Automatic receipts from approvals                                 */
    /* ---------------------------------------------------------------- */

    public function test_approving_a_membership_payment_posts_a_receipt_to_the_membership_income_ledger(): void
    {
        $member = User::factory()->create(['name' => 'New Member', 'registration_status' => 'pending_payment_review']);

        Livewire::actingAs($this->admin())->test(RegistrationsIndex::class)->call('approvePayment', $member->id);

        [$method, $path, $body, $headers] = $this->onlyWrite();
        $this->assertSame(['POST', '/receipts'], [$method, $path]);
        $this->assertTrue($body['isAutoNumber']);
        $this->assertSame(11, $body['voucherAutoSeriesId']);
        $this->assertSame(3107, $body['depositToAccountId']);
        $this->assertSame(3, $body['paymentModeId']);
        $this->assertSame([['lineType' => 'account', 'oppositeAccountId' => 501, 'amount' => 1000.0, 'description' => 'Membership fee - New Member']], $body['lines']);
        $this->assertNotEmpty($headers['Idempotency-Key'][0]);
        $this->assertSame('Bearer zbk_' . str_repeat('ab', 24), $headers['Authorization'][0]);

        $member->refresh();
        $this->assertSame('active', $member->registration_status);
        $this->assertSame('synced', $member->zybra_sync_status);
        $this->assertSame('RC-90', $member->zybra_membership_receipt_no);
        $this->assertDatabaseHas('financial_entries', ['type' => 'membership', 'member_id' => $member->id, 'zybra_voucher_id' => 9001, 'zybra_voucher_number' => 'RC-90', 'amount' => 1000]);
    }

    public function test_membership_receipt_is_not_posted_twice(): void
    {
        $member = User::factory()->create(['zybra_membership_receipt_no' => 'RC-5']);

        $result = app(\App\Services\ZybraService::class)->recordMembershipPayment($member);

        $this->assertTrue($result['success']);
        $this->assertSame([], $this->writes);
    }

    public function test_zybra_rejection_marks_the_membership_failed_but_still_approves_it(): void
    {
        $this->writeStatus = 400;
        $member = User::factory()->create(['registration_status' => 'pending_payment_review']);

        Livewire::actingAs($this->admin())->test(RegistrationsIndex::class)->call('approvePayment', $member->id);

        $member->refresh();
        $this->assertSame('active', $member->registration_status);
        $this->assertSame('failed', $member->zybra_sync_status);
        $this->assertStringContainsString('VALUE_TOO_SMALL', $member->zybra_error);
        $this->assertSame(0, FinancialEntry::count());
    }

    public function test_approving_a_paid_event_ticket_posts_a_receipt_to_the_event_income_ledger(): void
    {
        // Guest pass without email: the ticket QR email (needs PHP GD) is skipped
        $event = Event::create(['title' => 'Navratri Night', 'date' => now()->addWeek(), 'location' => 'Ahmedabad', 'price_normal' => 500]);
        $registration = EventRegistration::create(['event_id' => $event->id, 'guest_name' => 'Ticket Buyer', 'status' => 'pending', 'amount_paid' => 350]);

        app(EventTicketApprover::class)->approve($registration);

        [, $path, $body] = $this->onlyWrite();
        $this->assertSame('/receipts', $path);
        $this->assertSame(502, $body['lines'][0]['oppositeAccountId']);
        $this->assertSame(350.0, $body['lines'][0]['amount']);
        $this->assertSame('Event ticket - Navratri Night - Ticket Buyer', $body['lines'][0]['description']);
        $this->assertSame($registration->fresh()->ticket_number, $body['referenceNumber']);

        $registration->refresh();
        $this->assertSame('approved', $registration->status);
        $this->assertSame('synced', $registration->zybra_sync_status);
        $this->assertSame('RC-90', $registration->zybra_receipt_no);
    }

    public function test_free_event_ticket_posts_nothing(): void
    {
        $event = Event::create(['title' => 'Free Meetup', 'date' => now()->addWeek(), 'location' => 'Ahmedabad', 'price_normal' => 0]);
        $registration = EventRegistration::create(['event_id' => $event->id, 'guest_name' => 'Guest', 'status' => 'pending']);

        app(EventTicketApprover::class)->approve($registration);

        $this->assertSame([], $this->writes);
        $this->assertSame('synced', $registration->fresh()->zybra_sync_status);
    }

    public function test_without_api_key_nothing_is_sent_and_status_is_pending_credentials(): void
    {
        config()->set('services.zybra.api_key', 'your_key_here');
        $member = User::factory()->create();

        $result = app(\App\Services\ZybraService::class)->recordMembershipPayment($member);

        $this->assertFalse($result['success']);
        $this->assertSame('pending_credentials', $member->fresh()->zybra_sync_status);
        Http::assertNothingSent();
    }

    /* ---------------------------------------------------------------- */
    /* Financial report page                                             */
    /* ---------------------------------------------------------------- */

    public function test_report_page_is_for_the_main_admin_only(): void
    {
        $this->get('/admin/finance')->assertRedirect();
        $this->actingAs(User::factory()->create(['role' => 'sub_admin']))->get('/admin/finance')->assertForbidden();
        $this->actingAs(User::factory()->create(['role' => 'user']))->get('/admin/finance')->assertForbidden();

        $this->actingAs($this->admin())->get('/admin/finance')
            ->assertOk()
            ->assertSee('Financial Report')
            ->assertSee('Balance available now');
    }

    public function test_sub_admin_cannot_use_the_component_directly(): void
    {
        Livewire::actingAs(User::factory()->create(['role' => 'sub_admin']))->test(FinanceIndex::class)->assertForbidden();
    }

    public function test_report_shows_balance_totals_and_only_non_zero_dues(): void
    {
        $component = Livewire::actingAs($this->admin())->test(FinanceIndex::class)->set('period', 'month');

        $component->assertViewHas('balance', 92600.50)
            ->assertViewHas('moneyIn', 1000.0)      // journal row and last-century receipt excluded
            ->assertViewHas('moneyOut', 2400.0)
            ->assertSee('₹92,600.50')
            ->assertSee('RC-1')
            ->assertDontSee('JV-9')
            ->assertDontSee('RC-OLD')
            ->assertViewHas('owedToSabha', fn ($rows) => $rows->pluck('name')->all() === ['Ramesh Patel'])
            ->assertViewHas('owedBySabha', fn ($rows) => $rows->pluck('name')->all() === ['Chandreshbhai'])
            ->assertDontSee('Settled Person');

        $component->set('period', 'all')->assertViewHas('moneyIn', 1050.0)->assertSee('RC-OLD');
        $component->set('direction', 'out')->assertSee('PY-1')->assertDontSee('RC-1');
        $component->set('direction', 'all')->set('search', 'vidhi')->assertSee('PY-1')->assertDontSee('RC-1');
    }

    public function test_indian_number_grouping_for_lakhs(): void
    {
        $this->parties = [['id' => 7004, 'name' => 'Big Donor', 'mobile' => null, 'receivable' => 1234567.5, 'payable' => 0]];

        Livewire::actingAs($this->admin())->test(FinanceIndex::class)->assertSee('₹12,34,567.50');
    }

    public function test_add_income_posts_a_receipt_with_an_account_line(): void
    {
        $admin = $this->admin();

        Livewire::actingAs($admin)->test(FinanceIndex::class)
            ->call('openEntry', 'income')
            ->assertSet('cashAccountId', 3107)
            ->assertSet('paymentModeId', 3)
            ->set('entryAmount', '2500')
            ->set('entryAccountId', '502')
            ->set('paymentModeId', '4')
            ->set('description', 'Donation from Patel family')
            ->set('reference', 'UTR123')
            ->call('saveEntry')
            ->assertHasNoErrors()
            ->assertSet('showEntryModal', false)
            ->assertSet('successMsg', 'Income of ₹2,500.00 saved in Zybra as RC-90.');

        [, $path, $body] = $this->onlyWrite();
        $this->assertSame('/receipts', $path);
        $this->assertSame(4, $body['paymentModeId']);
        $this->assertSame('UTR123', $body['referenceNumber']);
        $this->assertSame([['lineType' => 'account', 'oppositeAccountId' => 502, 'amount' => 2500.0, 'description' => 'Donation from Patel family']], $body['lines']);
        $this->assertDatabaseHas('financial_entries', ['type' => 'income', 'amount' => 2500, 'zybra_account_name' => 'General Income', 'created_by' => $admin->id, 'zybra_voucher_number' => 'RC-90']);
    }

    public function test_add_expense_paid_by_sabha_posts_a_payment(): void
    {
        Livewire::actingAs($this->admin())->test(FinanceIndex::class)
            ->call('openEntry', 'expense')
            ->set('entryAmount', '750.50')
            ->set('entryAccountId', '601')
            ->set('cashAccountId', '3108')
            ->set('description', 'Taxi for guests')
            ->call('saveEntry')
            ->assertHasNoErrors();

        [, $path, $body] = $this->onlyWrite();
        $this->assertSame('/payments', $path);
        $this->assertSame(12, $body['voucherAutoSeriesId']);
        $this->assertSame(3108, $body['paidFromAccountId']);
        $this->assertSame([['lineType' => 'account', 'oppositeAccountId' => 601, 'amount' => 750.5, 'description' => 'Taxi for guests']], $body['lines']);
        $this->assertDatabaseHas('financial_entries', ['type' => 'expense', 'zybra_voucher_number' => 'PY-2']);
    }

    public function test_expense_paid_by_a_member_links_the_existing_party_and_posts_a_journal(): void
    {
        $member = User::factory()->create(['name' => 'Chandreshbhai', 'phone' => '+91 98000 00002']);

        Livewire::actingAs($this->admin())->test(FinanceIndex::class)
            ->call('openEntry', 'expense')
            ->set('paidBy', 'member')
            ->set('memberId', $member->id)
            ->set('entryAmount', '5000')
            ->set('entryAccountId', '601')
            ->set('description', 'Hotel booking')
            ->call('saveEntry')
            ->assertHasNoErrors();

        // Matched by mobile: no new party created
        [, $path, $body] = $this->onlyWrite();
        $this->assertSame('/journals', $path);
        $this->assertSame(13, $body['voucherAutoSeriesId']);
        $this->assertSame(['accountId' => 601, 'debit' => 5000.0, 'credit' => 0], array_intersect_key($body['items'][0], array_flip(['accountId', 'debit', 'credit'])));
        $this->assertSame(['accountId' => 7002, 'debit' => 0, 'credit' => 5000.0], array_intersect_key($body['items'][1], array_flip(['accountId', 'debit', 'credit'])));
        $this->assertSame(7002, $member->fresh()->zybra_party_id);
        $this->assertDatabaseHas('financial_entries', ['type' => 'expense_by_member', 'member_id' => $member->id, 'party_name' => 'Chandreshbhai', 'zybra_voucher_number' => 'JV-1']);
    }

    public function test_member_not_in_zybra_gets_a_new_party_and_same_name_with_other_mobile_is_not_merged(): void
    {
        $member = User::factory()->create(['name' => 'Ramesh Patel', 'phone' => '9811111111']);

        Livewire::actingAs($this->admin())->test(FinanceIndex::class)
            ->call('openEntry', 'expense')
            ->set('paidBy', 'member')
            ->set('memberId', $member->id)
            ->set('entryAmount', '100')
            ->set('entryAccountId', '602')
            ->set('description', 'Flowers')
            ->call('saveEntry')
            ->assertHasNoErrors();

        $this->assertSame(['/parties', '/journals'], array_column($this->writes, 1));
        $this->assertSame('Ramesh Patel', $this->writes[0][2]['name']);
        $this->assertSame('9811111111', $this->writes[0][2]['mobile']);
        $this->assertSame(7999, $this->writes[1][2]['items'][1]['accountId']);
    }

    public function test_paid_by_member_requires_a_member(): void
    {
        Livewire::actingAs($this->admin())->test(FinanceIndex::class)
            ->call('openEntry', 'expense')
            ->set('paidBy', 'member')
            ->set('entryAmount', '100')
            ->set('entryAccountId', '601')
            ->set('description', 'Hotel')
            ->call('saveEntry')
            ->assertHasErrors(['memberId']);

        $this->assertSame([], $this->writes);
    }

    public function test_entry_validation_blocks_bad_input_before_calling_zybra(): void
    {
        Livewire::actingAs($this->admin())->test(FinanceIndex::class)
            ->call('openEntry', 'income')
            ->set('entryAmount', '0')
            ->set('entryAccountId', '601') // an expense ledger is not a valid income category
            ->set('entryDate', now()->addDay()->toDateString())
            ->set('description', '')
            ->call('saveEntry')
            ->assertHasErrors(['entryAmount', 'entryAccountId', 'entryDate', 'description'])
            ->assertSet('showEntryModal', true);

        $this->assertSame([], $this->writes);
    }

    public function test_zybra_rejection_shows_error_saves_nothing_and_uses_a_new_key_next_time(): void
    {
        $this->writeStatus = 400;

        $component = Livewire::actingAs($this->admin())->test(FinanceIndex::class)
            ->call('openEntry', 'income')
            ->set('entryAmount', '10')
            ->set('entryAccountId', '502')
            ->set('description', 'Test');
        $firstKey = $component->get('entryKey');

        $component->call('saveEntry')
            ->assertHasErrors(['zybra'])
            ->assertSee('VALUE_TOO_SMALL')
            ->assertSet('showEntryModal', true);

        $this->assertSame(0, FinancialEntry::count());
        $this->assertNotSame($firstKey, $component->get('entryKey'));
    }

    public function test_server_error_keeps_the_same_key_so_a_resubmit_cannot_duplicate(): void
    {
        $this->writeStatus = 503;

        $component = Livewire::actingAs($this->admin())->test(FinanceIndex::class)
            ->call('openEntry', 'income')
            ->set('entryAmount', '10')
            ->set('entryAccountId', '502')
            ->set('description', 'Test');
        $key = $component->get('entryKey');

        $component->call('saveEntry')->assertHasErrors(['zybra']);

        $this->assertSame($key, $component->get('entryKey'));
        $keysSent = array_unique(array_map(fn ($w) => $w[3]['Idempotency-Key'][0], $this->writes));
        $this->assertSame([$key], array_values($keysSent)); // the automatic retries reused it too
    }

    public function test_paying_back_a_member_posts_a_payment_to_the_party(): void
    {
        Livewire::actingAs($this->admin())->test(FinanceIndex::class)
            ->call('openSettle', 7002)
            ->assertSet('settleDirection', 'pay')
            ->assertSet('settleAmount', '5000.00')
            ->set('settleAmount', '2000')
            ->call('saveSettle')
            ->assertHasNoErrors();

        [, $path, $body] = $this->onlyWrite();
        $this->assertSame('/payments', $path);
        $this->assertSame([['lineType' => 'advance', 'partyId' => 7002, 'amount' => 2000.0, 'description' => 'Due paid to Chandreshbhai']], $body['lines']);
        $this->assertDatabaseHas('financial_entries', ['type' => 'settle_pay', 'zybra_party_id' => 7002, 'amount' => 2000]);
    }

    public function test_receiving_a_due_is_applied_to_unpaid_invoices_oldest_first(): void
    {
        Livewire::actingAs($this->admin())->test(FinanceIndex::class)
            ->call('openSettle', 7001)
            ->assertSet('settleDirection', 'receive')
            ->set('settleAmount', '800')
            ->call('saveSettle')
            ->assertHasNoErrors();

        [, $path, $body] = $this->onlyWrite();
        $this->assertSame('/receipts', $path);
        $this->assertSame([[
            'lineType' => 'bill_to_bill',
            'partyId' => 7001,
            'amount' => 800.0,
            'vouchers' => [['id' => 8001, 'amount' => 600.0], ['id' => 8002, 'amount' => 200.0]],
            'description' => 'Due received from Ramesh Patel',
        ]], $body['lines']);
    }

    public function test_settle_amount_cannot_exceed_the_due(): void
    {
        Livewire::actingAs($this->admin())->test(FinanceIndex::class)
            ->call('openSettle', 7001)
            ->set('settleAmount', '1000.01')
            ->call('saveSettle')
            ->assertHasErrors(['settleAmount']);

        $this->assertSame([], $this->writes);
    }

    public function test_settle_party_cannot_be_swapped_from_the_browser(): void
    {
        $this->expectException(\Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException::class);

        Livewire::actingAs($this->admin())->test(FinanceIndex::class)
            ->call('openSettle', 7002)
            ->set('settlePartyId', 7001);
    }
}
