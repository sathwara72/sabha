@php
    // Indian digit grouping: ₹1,23,456.00
    $inr = function ($value) {
        $negative = $value < 0;
        [$int, $dec] = explode('.', number_format(abs((float) $value), 2, '.', ''));
        if (strlen($int) > 3) {
            $int = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', substr($int, 0, -3)) . ',' . substr($int, -3);
        }

        return ($negative ? '-' : '') . '₹' . $int . '.' . $dec;
    };
    $inputClass = 'w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs font-semibold text-slate-900 outline-none focus:bg-white focus:border-primary';
    $labelClass = 'block text-[11px] font-bold text-slate-700 uppercase mb-1';
@endphp

<div class="space-y-4 font-outfit">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight leading-tight">Financial Report</h1>
            <p class="text-xs text-slate-500 font-medium">Live from Zybra. Every entry added here is saved in Zybra.</p>
        </div>
        @if ($configured && ! $loadError)
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" wire:click="refresh" wire:loading.attr="disabled" wire:target="refresh"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 shadow-2xs">
                    <x-icon name="refresh-cw" class="h-3.5 w-3.5" wire:loading.class="animate-spin" wire:target="refresh" /> Refresh
                </button>
                <button type="button" wire:click="openEntry('income')"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-3 py-2 text-xs font-bold text-white hover:bg-emerald-700 shadow-sm">
                    <x-icon name="plus" class="h-3.5 w-3.5" /> Add Income
                </button>
                <button type="button" wire:click="openEntry('expense')"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-rose-600 px-3 py-2 text-xs font-bold text-white hover:bg-rose-700 shadow-sm">
                    <x-icon name="plus" class="h-3.5 w-3.5" /> Add Expense
                </button>
            </div>
        @endif
    </div>

    @if ($successMsg)
        <div class="flex items-start justify-between gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2.5 text-xs font-semibold text-emerald-800">
            <span class="inline-flex items-center gap-1.5"><x-icon name="check" class="h-3.5 w-3.5" /> {{ $successMsg }}</span>
            <button type="button" wire:click="$set('successMsg', '')" class="text-emerald-600 hover:text-emerald-900"><x-icon name="x" class="h-3.5 w-3.5" /></button>
        </div>
    @endif

    @if (! $configured)
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-xs font-semibold text-amber-800">
            Zybra is not connected. Add <code class="font-mono">ZYBRA_API_KEY</code> to the server's .env file, then run <code class="font-mono">php artisan zybra:check</code>.
        </div>
    @elseif ($loadError)
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-5 space-y-2 text-xs font-semibold text-rose-800">
            <p>Could not load data from Zybra. Nothing is shown so that no wrong figures appear.</p>
            <p class="font-mono text-[11px] text-rose-700 break-words">{{ $loadError }}</p>
            <button type="button" wire:click="refresh" class="rounded-lg border border-rose-300 bg-white px-3 py-1.5 font-bold text-rose-700 hover:bg-rose-100">Try again</button>
        </div>
    @else
        {{-- Summary cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
            <div class="rounded-2xl bg-slate-900 p-4 text-white shadow-sm">
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-300">Balance available now</p>
                <p class="mt-1 text-2xl font-black tabular-nums">{{ $inr($balance) }}</p>
                <div class="mt-2 space-y-0.5">
                    @foreach ($cashAccounts as $account)
                        <div class="flex justify-between gap-2 text-[11px] text-slate-300">
                            <span class="truncate">{{ $account['name'] }}</span>
                            <span class="font-bold tabular-nums text-white">{{ $inr($account['balance']) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="rounded-2xl border border-slate-200/90 bg-white p-4 shadow-2xs">
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Money in</p>
                <p class="mt-1 text-xl font-black text-emerald-700 tabular-nums">{{ $inr($moneyIn) }}</p>
                <p class="text-[11px] text-slate-400 font-medium">in selected period</p>
            </div>
            <div class="rounded-2xl border border-slate-200/90 bg-white p-4 shadow-2xs">
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Money out</p>
                <p class="mt-1 text-xl font-black text-rose-700 tabular-nums">{{ $inr($moneyOut) }}</p>
                <p class="text-[11px] text-slate-400 font-medium">in selected period</p>
            </div>
            <div class="rounded-2xl border border-slate-200/90 bg-white p-4 shadow-2xs">
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Net (in − out)</p>
                <p class="mt-1 text-xl font-black tabular-nums {{ $moneyIn - $moneyOut >= 0 ? 'text-slate-900' : 'text-rose-700' }}">{{ $inr($moneyIn - $moneyOut) }}</p>
                <p class="text-[11px] text-slate-400 font-medium">
                    @if ($rangeFrom || $rangeTo)
                        {{ $rangeFrom ? \Illuminate\Support\Carbon::parse($rangeFrom)->format('d M Y') : 'Start' }} – {{ $rangeTo ? \Illuminate\Support\Carbon::parse($rangeTo)->format('d M Y') : 'Today' }}
                    @else
                        All time
                    @endif
                </p>
            </div>
        </div>

        {{-- Dues --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
            @foreach ([['title' => 'Sabha has to pay', 'hint' => 'Members who paid expenses for Sabha', 'rows' => $owedBySabha, 'tone' => 'rose', 'action' => 'Pay back'], ['title' => 'Sabha has to receive', 'hint' => 'People who owe money to Sabha', 'rows' => $owedToSabha, 'tone' => 'amber', 'action' => 'Received']] as $box)
                <div class="rounded-2xl border border-slate-200/90 bg-white shadow-2xs">
                    <div class="flex items-center justify-between gap-2 border-b border-slate-100 px-4 py-3">
                        <div>
                            <h2 class="text-sm font-black text-slate-900">{{ $box['title'] }}</h2>
                            <p class="text-[11px] text-slate-500 font-medium">{{ $box['hint'] }}</p>
                        </div>
                        <span class="text-sm font-black tabular-nums {{ $box['tone'] === 'rose' ? 'text-rose-700' : 'text-amber-700' }}">{{ $inr(abs($box['rows']->sum('net'))) }}</span>
                    </div>
                    @if ($box['rows']->isEmpty())
                        <p class="px-4 py-6 text-center text-xs italic text-slate-400">No pending dues.</p>
                    @else
                        <ul class="divide-y divide-slate-100 max-h-72 overflow-y-auto">
                            @foreach ($box['rows'] as $party)
                                <li class="flex items-center justify-between gap-3 px-4 py-2" wire:key="due-{{ $party['id'] }}">
                                    <div class="min-w-0">
                                        <p class="truncate text-xs font-bold text-slate-900">{{ $party['name'] }}</p>
                                        @if ($party['mobile'])
                                            <p class="text-[11px] text-slate-500">{{ $party['mobile'] }}</p>
                                        @endif
                                    </div>
                                    <div class="flex shrink-0 items-center gap-2">
                                        <span class="text-xs font-black tabular-nums text-slate-900">{{ $inr(abs($party['net'])) }}</span>
                                        <button type="button" wire:click="openSettle({{ $party['id'] }})"
                                            class="rounded-lg border border-slate-200 bg-white px-2 py-1 text-[11px] font-bold text-slate-700 hover:bg-slate-50">{{ $box['action'] }}</button>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Filters --}}
        <div class="bg-white p-2.5 sm:p-3 rounded-xl border border-slate-200/90 shadow-2xs flex flex-col lg:flex-row lg:items-center gap-2">
            <select wire:model.live="period" class="rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-1.5 text-xs font-bold text-slate-800 outline-none focus:border-primary">
                <option value="month">This month</option>
                <option value="last_month">Last month</option>
                <option value="fy">This financial year (Apr–Mar)</option>
                <option value="all">All time</option>
                <option value="custom">Custom dates</option>
            </select>
            @if ($period === 'custom')
                <div class="flex items-center gap-1.5">
                    <input type="date" wire:model.live="from" class="rounded-xl border border-slate-200 bg-slate-50/50 px-2 py-1.5 text-xs font-semibold outline-none focus:border-primary" />
                    <span class="text-xs text-slate-400">to</span>
                    <input type="date" wire:model.live="to" class="rounded-xl border border-slate-200 bg-slate-50/50 px-2 py-1.5 text-xs font-semibold outline-none focus:border-primary" />
                </div>
            @endif
            <div class="inline-flex rounded-xl border border-slate-200 bg-slate-50 p-0.5">
                @foreach (['all' => 'All', 'in' => 'Money in', 'out' => 'Money out'] as $value => $label)
                    <button type="button" wire:click="$set('direction', '{{ $value }}')"
                        class="rounded-lg px-3 py-1 text-xs font-bold {{ $direction === $value ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-500 hover:text-slate-800' }}">{{ $label }}</button>
                @endforeach
            </div>
            <div class="relative flex-1 lg:max-w-xs lg:ml-auto">
                <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400" />
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search name, number, note..."
                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-1.5 pl-9 pr-3 text-xs font-semibold text-slate-900 outline-none placeholder:text-slate-400 focus:bg-white focus:border-primary" />
            </div>
        </div>

        {{-- Transactions --}}
        <div class="relative rounded-2xl border border-slate-200/90 bg-white shadow-2xs">
            <x-loading-state target="period,from,to,direction,search,gotoPage,nextPage,previousPage,refresh" message="Loading from Zybra..." />

            @if ($transactions->isEmpty())
                <p class="py-16 text-center text-xs italic text-slate-400">No money in or out in this period.</p>
            @else
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="border-b border-slate-100 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-4 py-2.5">Date</th>
                                <th class="px-4 py-2.5">No.</th>
                                <th class="px-4 py-2.5">Details</th>
                                <th class="px-4 py-2.5">Mode / Account</th>
                                <th class="px-4 py-2.5">Added by</th>
                                <th class="px-4 py-2.5 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($transactions as $t)
                                @php $entry = $local->get($t['voucher_type'] . ':' . $t['id']); @endphp
                                <tr wire:key="tx-{{ $t['voucher_type'] }}-{{ $t['id'] }}" class="hover:bg-slate-50/60">
                                    <td class="px-4 py-2.5 whitespace-nowrap font-semibold text-slate-700">{{ \Illuminate\Support\Carbon::parse($t['date'])->format('d M Y') }}</td>
                                    <td class="px-4 py-2.5 whitespace-nowrap font-mono text-[11px] text-slate-500">{{ $t['number'] }}</td>
                                    <td class="px-4 py-2.5">
                                        <p class="font-bold text-slate-900">{{ $t['party'] ?: ($t['account'] ?: '—') }}</p>
                                        @if ($t['notes'] || $t['reference'])
                                            <p class="text-[11px] text-slate-500">{{ $t['notes'] }}{{ $t['notes'] && $t['reference'] ? ' · ' : '' }}{{ $t['reference'] }}</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5 whitespace-nowrap text-slate-600">{{ $t['mode'] ?: '—' }} · {{ $t['cash_account'] ?: '—' }}</td>
                                    <td class="px-4 py-2.5 whitespace-nowrap text-slate-500">{{ $entry?->creator?->name ?? ($entry ? 'Sabha' : 'Zybra app') }}</td>
                                    <td class="px-4 py-2.5 whitespace-nowrap text-right font-black tabular-nums {{ $t['direction'] === 'in' ? 'text-emerald-700' : 'text-rose-700' }}">
                                        {{ $t['direction'] === 'in' ? '+' : '−' }}{{ $inr($t['amount']) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <ul class="md:hidden divide-y divide-slate-100">
                    @foreach ($transactions as $t)
                        @php $entry = $local->get($t['voucher_type'] . ':' . $t['id']); @endphp
                        <li wire:key="txm-{{ $t['voucher_type'] }}-{{ $t['id'] }}" class="flex items-start justify-between gap-3 px-4 py-3">
                            <div class="min-w-0">
                                <p class="truncate text-xs font-bold text-slate-900">{{ $t['party'] ?: ($t['account'] ?: '—') }}</p>
                                <p class="text-[11px] text-slate-500">{{ \Illuminate\Support\Carbon::parse($t['date'])->format('d M Y') }} · {{ $t['number'] }} · {{ $t['mode'] ?: '—' }}</p>
                                @if ($t['notes'])
                                    <p class="truncate text-[11px] text-slate-500">{{ $t['notes'] }}</p>
                                @endif
                                <p class="text-[10px] text-slate-400">Added by {{ $entry?->creator?->name ?? ($entry ? 'Sabha' : 'Zybra app') }}</p>
                            </div>
                            <span class="shrink-0 text-xs font-black tabular-nums {{ $t['direction'] === 'in' ? 'text-emerald-700' : 'text-rose-700' }}">
                                {{ $t['direction'] === 'in' ? '+' : '−' }}{{ $inr($t['amount']) }}
                            </span>
                        </li>
                    @endforeach
                </ul>

                @if ($transactions->hasPages())
                    <div class="px-2 pb-2">
                        <x-pagination :paginator="$transactions" item-label="entries" />
                    </div>
                @endif
            @endif
        </div>

        {{-- Expenses paid by members (no cash moved) --}}
        @if ($memberExpenses->isNotEmpty())
            <div class="rounded-2xl border border-slate-200/90 bg-white shadow-2xs">
                <div class="border-b border-slate-100 px-4 py-3">
                    <h2 class="text-sm font-black text-slate-900">Expenses paid by members</h2>
                    <p class="text-[11px] text-slate-500 font-medium">Not part of money out until Sabha pays the member back (see "Sabha has to pay").</p>
                </div>
                <ul class="divide-y divide-slate-100">
                    @foreach ($memberExpenses as $e)
                        <li wire:key="mx-{{ $e->id }}" class="flex items-start justify-between gap-3 px-4 py-2.5">
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-900">{{ $e->description }}</p>
                                <p class="text-[11px] text-slate-500">
                                    {{ $e->entry_date->format('d M Y') }} · Paid by <span class="font-semibold text-slate-700">{{ $e->party_name }}</span>
                                    · {{ $e->zybra_account_name }} · {{ $e->zybra_voucher_number ?: '#' . $e->zybra_voucher_id }}
                                </p>
                            </div>
                            <span class="shrink-0 text-xs font-black tabular-nums text-slate-900">{{ $inr($e->amount) }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Add income / expense modal --}}
        @if ($showEntryModal)
            <div class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" wire:click="closeEntry"></div>
                <form wire:submit="saveEntry" class="relative z-10 w-full max-w-lg max-h-[calc(100vh-2rem)] overflow-y-auto rounded-2xl border border-slate-200 bg-white p-5 shadow-2xl space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="text-sm font-bold text-slate-900">{{ $entryType === 'income' ? 'Add Income' : 'Add Expense' }}</h3>
                        <button type="button" wire:click="closeEntry" class="text-slate-400 hover:text-slate-700"><x-icon name="x" class="h-4 w-4" /></button>
                    </div>

                    @error('zybra')
                        <p class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-[11px] font-semibold text-rose-700 break-words">{{ $message }}</p>
                    @enderror

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="{{ $labelClass }}">Amount (₹) <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" min="0.01" wire:model="entryAmount" class="{{ $inputClass }}" placeholder="0.00" />
                            @error('entryAmount') <p class="mt-1 text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">Date <span class="text-red-500">*</span></label>
                            <input type="date" wire:model="entryDate" max="{{ app(\App\Services\ZybraService::class)->today()->toDateString() }}" class="{{ $inputClass }}" />
                            @error('entryDate') <p class="mt-1 text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="{{ $labelClass }}">Category <span class="text-red-500">*</span></label>
                        <select wire:model="entryAccountId" class="{{ $inputClass }}">
                            <option value="">Select {{ $entryType === 'income' ? 'income' : 'expense' }} category</option>
                            @foreach ($entryType === 'income' ? $incomeAccounts : $expenseAccounts as $account)
                                <option value="{{ $account['id'] }}">{{ $account['name'] }}</option>
                            @endforeach
                        </select>
                        @error('entryAccountId') <p class="mt-1 text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                    </div>

                    @if ($entryType === 'expense')
                        <div>
                            <label class="{{ $labelClass }}">Paid by</label>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach (['sabha' => 'Sabha (cash / bank)', 'member' => 'A member, from own pocket'] as $value => $label)
                                    <button type="button" wire:click="$set('paidBy', '{{ $value }}')"
                                        class="rounded-xl border px-3 py-2 text-xs font-bold {{ $paidBy === $value ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 text-slate-600 hover:bg-slate-50' }}">{{ $label }}</button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($entryType === 'expense' && $paidBy === 'member')
                        <div>
                            <label class="{{ $labelClass }}">Member who paid <span class="text-red-500">*</span></label>
                            <x-searchable-select wire-model="memberId" :options="array_keys($memberOptions)" :value-map="$memberOptions" :allow-custom="false" placeholder="Search member by name or mobile..." :class="$inputClass" />
                            @error('memberId') <p class="mt-1 text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                            <p class="mt-1 text-[11px] text-slate-500">No cash leaves Sabha now. The amount is added to "Sabha has to pay" for this member until you pay them back.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="{{ $labelClass }}">{{ $entryType === 'income' ? 'Received into' : 'Paid from' }} <span class="text-red-500">*</span></label>
                                <select wire:model="cashAccountId" class="{{ $inputClass }}">
                                    @foreach ($cashAccounts as $account)
                                        <option value="{{ $account['id'] }}">{{ $account['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('cashAccountId') <p class="mt-1 text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="{{ $labelClass }}">Payment mode</label>
                                <select wire:model="paymentModeId" class="{{ $inputClass }}">
                                    <option value="">—</option>
                                    @foreach ($paymentModes as $mode)
                                        <option value="{{ $mode['id'] }}">{{ $mode['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('paymentModeId') <p class="mt-1 text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="{{ $labelClass }}">Description <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="description" maxlength="250" class="{{ $inputClass }}"
                            placeholder="{{ $entryType === 'income' ? 'e.g. Donation from …' : 'e.g. Hotel booking for Navratri event' }}" />
                        @error('description') <p class="mt-1 text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Reference / UTR / Bill no.</label>
                        <input type="text" wire:model="reference" maxlength="100" class="{{ $inputClass }}" />
                        @error('reference') <p class="mt-1 text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" wire:click="closeEntry" class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50">Cancel</button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="saveEntry"
                            class="inline-flex items-center gap-1.5 rounded-xl px-4 py-2 text-xs font-bold text-white shadow-sm disabled:opacity-60 {{ $entryType === 'income' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-rose-600 hover:bg-rose-700' }}">
                            <span wire:loading wire:target="saveEntry" class="h-3 w-3 animate-spin rounded-full border-2 border-white border-t-transparent"></span>
                            Save in Zybra
                        </button>
                    </div>
                </form>
            </div>
        @endif

        {{-- Settle due modal --}}
        @if ($settlePartyId)
            <div class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" wire:click="closeSettle"></div>
                <form wire:submit="saveSettle" class="relative z-10 w-full max-w-md max-h-[calc(100vh-2rem)] overflow-y-auto rounded-2xl border border-slate-200 bg-white p-5 shadow-2xl space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">{{ $settleDirection === 'pay' ? 'Pay back' : 'Record money received' }}</h3>
                            <p class="text-[11px] text-slate-500">{{ $settlePartyName }} · due {{ $inr($settleMax) }}</p>
                        </div>
                        <button type="button" wire:click="closeSettle" class="text-slate-400 hover:text-slate-700"><x-icon name="x" class="h-4 w-4" /></button>
                    </div>

                    @error('zybra')
                        <p class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-[11px] font-semibold text-rose-700 break-words">{{ $message }}</p>
                    @enderror

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="{{ $labelClass }}">Amount (₹) <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" min="0.01" max="{{ $settleMax }}" wire:model="settleAmount" class="{{ $inputClass }}" />
                            @error('settleAmount') <p class="mt-1 text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">Date <span class="text-red-500">*</span></label>
                            <input type="date" wire:model="settleDate" max="{{ app(\App\Services\ZybraService::class)->today()->toDateString() }}" class="{{ $inputClass }}" />
                            @error('settleDate') <p class="mt-1 text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">{{ $settleDirection === 'pay' ? 'Paid from' : 'Received into' }} <span class="text-red-500">*</span></label>
                            <select wire:model="settleCashAccountId" class="{{ $inputClass }}">
                                @foreach ($cashAccounts as $account)
                                    <option value="{{ $account['id'] }}">{{ $account['name'] }}</option>
                                @endforeach
                            </select>
                            @error('settleCashAccountId') <p class="mt-1 text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">Payment mode</label>
                            <select wire:model="settlePaymentModeId" class="{{ $inputClass }}">
                                <option value="">—</option>
                                @foreach ($paymentModes as $mode)
                                    <option value="{{ $mode['id'] }}">{{ $mode['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Description <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="settleDescription" maxlength="250" class="{{ $inputClass }}" />
                        @error('settleDescription') <p class="mt-1 text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Reference / UTR</label>
                        <input type="text" wire:model="settleReference" maxlength="100" class="{{ $inputClass }}" />
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" wire:click="closeSettle" class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50">Cancel</button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="saveSettle"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white hover:bg-slate-800 shadow-sm disabled:opacity-60">
                            <span wire:loading wire:target="saveSettle" class="h-3 w-3 animate-spin rounded-full border-2 border-white border-t-transparent"></span>
                            Save in Zybra
                        </button>
                    </div>
                </form>
            </div>
        @endif
    @endif
</div>
