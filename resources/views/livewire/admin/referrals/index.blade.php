@php
    $statusColor = [
        'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
        'in_progress' => 'bg-sky-50 text-sky-700 border-sky-200',
        'closed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'rejected' => 'bg-rose-50 text-rose-600 border-rose-200',
    ];
@endphp

<div class="space-y-4 font-outfit">
    {{-- Top Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 leading-tight">Business Referrals</h1>
        </div>
    </div>

    {{-- Search & Status Filter Toolbar Card --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-2.5 sm:p-3 rounded-xl border border-slate-200/90 shadow-2xs">
        <div class="relative flex-1 max-w-md">
            <x-icon name="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400" />
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search by contact, giver, or receiver name..."
                class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-1.5 pl-9 pr-4 text-xs font-semibold text-slate-900 outline-none transition-all placeholder:text-slate-400 focus:bg-white focus:border-primary shadow-2xs"
            />
        </div>
        <div class="flex items-center gap-1 bg-slate-50 p-1 rounded-xl border border-slate-200 self-start sm:self-auto justify-center flex-wrap">
            @foreach (['all', 'pending', 'in_progress', 'closed', 'rejected'] as $status)
                <button
                    wire:click="setStatusFilter('{{ $status }}')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg capitalize transition-all cursor-pointer {{ $statusFilter === $status ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}"
                >
                    {{ str_replace('_', ' ', $status) }}
                    <span class="px-1.5 py-0.2 text-[10px] rounded-full font-bold transition-colors {{ $statusFilter === $status ? 'bg-primary-soft text-primary' : 'bg-slate-200/80 text-slate-600' }}">
                        {{ $counts[$status] ?? 0 }}
                    </span>
                </button>
            @endforeach
        </div>
    </div>

    @if ($referrals->isEmpty())
        <div class="py-24 text-center text-slate-500 text-xs bg-white rounded-2xl border border-dashed border-border italic shadow-xs">
            {{ $search || $statusFilter !== 'all' ? 'No referrals match your search/filter.' : 'No referrals logged yet.' }}
        </div>
    @else
        <div class="bg-white rounded-2xl border border-border/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/90 border-b border-border/70">
                            <th class="px-3.5 py-2 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Giver</th>
                            <th class="px-3.5 py-2 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Receiver</th>
                            <th class="px-3.5 py-2 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Contact</th>
                            <th class="px-3.5 py-2 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-3.5 py-2 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/60">
                        @foreach ($referrals as $ref)
                            <tr class="transition-colors hover:bg-slate-50/70">
                                <td class="px-3.5 py-2 text-xs font-extrabold text-slate-900">{{ $ref->giver?->name ?? '—' }}</td>
                                <td class="px-3.5 py-2 text-xs font-semibold text-slate-700">{{ $ref->receiver?->name ?? '—' }}</td>
                                <td class="px-3.5 py-2 text-xs text-slate-600">{{ $ref->contact_name }}</td>
                                <td class="px-3.5 py-2">
                                    <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide {{ $statusColor[$ref->status] ?? $statusColor['pending'] }}">
                                        {{ str_replace('_', ' ', $ref->status) }}
                                    </span>
                                    @if ($ref->status === 'closed' && $ref->amount)
                                        <span class="block mt-0.5 text-[11px] font-bold text-emerald-700">₹{{ number_format((float) $ref->amount) }}</span>
                                    @endif
                                </td>
                                <td class="px-3.5 py-2 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button
                                            wire:click="view({{ $ref->id }})"
                                            class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition-all hover:bg-slate-50 hover:text-slate-900 active:scale-95 cursor-pointer shadow-2xs"
                                            title="View Referral Details"
                                        >
                                            <x-icon name="arrow-up-right" class="h-3 w-3" />
                                        </button>
                                        <button
                                            wire:click="openDelete({{ $ref->id }})"
                                            class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 text-rose-600 transition-all hover:bg-rose-100 active:scale-95 cursor-pointer shadow-2xs"
                                            title="Delete Referral"
                                        >
                                            <x-icon name="trash-2" class="h-3 w-3" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-pagination :paginator="$referrals" item-label="referrals" />
        </div>
    @endif

    {{-- View Referral Modal --}}
    @if ($selectedReferral)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" wire:click="closeView"></div>
            <div x-data x-show="true" x-transition class="relative w-full max-w-lg transform rounded-2xl bg-white p-5 shadow-2xl transition-all border border-border max-h-[85vh] overflow-y-auto">
                <div class="flex items-start justify-between border-b border-border pb-4 mb-4">
                    <div>
                        <h3 class="text-base font-bold text-foreground">Referral Details</h3>
                        <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 mt-1 text-[12px] font-bold uppercase tracking-wide {{ $statusColor[$selectedReferral->status] ?? $statusColor['pending'] }}">
                            {{ str_replace('_', ' ', $selectedReferral->status) }}
                        </span>
                    </div>
                    <button wire:click="closeView" class="rounded-lg p-1 text-muted-foreground hover:bg-slate-100 hover:text-foreground transition-colors">
                        <x-icon name="x" class="h-4.5 w-4.5" />
                    </button>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="grid grid-cols-2 gap-3">
                        <div><span class="text-muted-foreground block mb-0.5">Giver</span><span class="font-semibold text-foreground">{{ $selectedReferral->giver?->name ?? '—' }}</span></div>
                        <div><span class="text-muted-foreground block mb-0.5">Receiver</span><span class="font-semibold text-foreground">{{ $selectedReferral->receiver?->name ?? '—' }}</span></div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div><span class="text-muted-foreground block mb-0.5">Contact Name</span><span class="font-semibold text-foreground">{{ $selectedReferral->contact_name }}</span></div>
                        <div><span class="text-muted-foreground block mb-0.5">Contact Number</span><span class="font-semibold text-foreground">{{ $selectedReferral->contact_number }}</span></div>
                    </div>
                    @if ($selectedReferral->company_details)
                        <div><span class="text-muted-foreground block mb-0.5">Company Details</span><span class="font-semibold text-foreground">{{ $selectedReferral->company_details }}</span></div>
                    @endif
                    <div><span class="text-muted-foreground block mb-0.5">Business Requirement</span><span class="font-semibold text-foreground">{{ $selectedReferral->business_requirement }}</span></div>
                    <div><span class="text-muted-foreground block mb-0.5">Lead Rating</span><span class="font-semibold text-foreground capitalize">{{ $selectedReferral->lead_rating }}</span></div>
                    @if ($selectedReferral->giver_comments)
                        <div><span class="text-muted-foreground block mb-0.5">Giver Comments</span><span class="font-semibold text-foreground">{{ $selectedReferral->giver_comments }}</span></div>
                    @endif
                    <div><span class="text-muted-foreground block mb-0.5">Contact Status</span><span class="font-semibold text-foreground">{{ $selectedReferral->contact_status === 'connected' ? 'Connected' : 'Not yet contacted' }}</span></div>
                    @if ($selectedReferral->receiver_comments)
                        <div><span class="text-muted-foreground block mb-0.5">Receiver Comments</span><span class="font-semibold text-foreground">{{ $selectedReferral->receiver_comments }}</span></div>
                    @endif
                    @if ($selectedReferral->status === 'closed')
                        <div class="grid grid-cols-2 gap-3">
                            <div><span class="text-muted-foreground block mb-0.5">Amount Closed</span><span class="font-semibold text-emerald-700">{{ $selectedReferral->amount ? '₹' . number_format((float) $selectedReferral->amount) : '—' }}</span></div>
                            <div><span class="text-muted-foreground block mb-0.5">Testimonial Shown</span><span class="font-semibold text-foreground">{{ $selectedReferral->display_testimonial ? 'Yes' : 'No' }}</span></div>
                        </div>
                        @if ($selectedReferral->testimonial)
                            <div><span class="text-muted-foreground block mb-0.5">Testimonial</span><span class="font-semibold text-foreground italic">"{{ $selectedReferral->testimonial }}"</span></div>
                        @endif
                    @endif
                </div>

                <div class="flex justify-end pt-4 mt-4 border-t border-border">
                    <button wire:click="closeView" class="rounded-xl border border-border bg-white px-4 py-2 text-xs font-bold text-foreground transition-colors hover:bg-slate-50 active:scale-95">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif

    <x-admin.confirm-modal
        :show="$deletingId !== null"
        title="Delete Referral"
        message="Are you sure you want to delete this referral? This cannot be undone."
        confirm-label="Delete"
        variant="danger"
        confirm-action="confirmDelete"
        cancel-action="cancelDelete"
    />
</div>
