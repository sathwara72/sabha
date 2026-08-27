<div class="space-y-5 font-outfit">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-foreground">Member Registrations</h1>
            <p class="text-xs text-muted">Review new applications and verify membership payments before members are activated</p>
        </div>
    </div>

    @if ($successMsg)
        <div wire:key="success-{{ md5($successMsg) }}" x-data="{ show: true }" x-init="setTimeout(() => { show = false; $wire.successMsg = '' }, 4000)" x-show="show" x-transition class="rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-xs font-semibold text-emerald-700 flex items-center gap-2">
            <x-icon name="check-circle-2" class="h-3.5 w-3.5 text-emerald-600" /> {{ $successMsg }}
        </div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-1 bg-surface p-1 rounded-xl border border-border self-start">
            <button wire:click="setTab('pending_review')" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg transition-all cursor-pointer {{ $tab === 'pending_review' ? 'bg-white text-foreground shadow-sm' : 'text-muted hover:text-foreground' }}">
                Pending Review
                <span class="px-1.5 py-0.5 text-[12px] rounded-full font-bold {{ $tab === 'pending_review' ? 'bg-primary-soft text-primary' : 'bg-slate-200/60 text-slate-500' }}">{{ $pendingReviewCount }}</span>
            </button>
            <button wire:click="setTab('pending_payment')" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg transition-all cursor-pointer {{ $tab === 'pending_payment' ? 'bg-white text-foreground shadow-sm' : 'text-muted hover:text-foreground' }}">
                Pending Payment Review
                <span class="px-1.5 py-0.5 text-[12px] rounded-full font-bold {{ $tab === 'pending_payment' ? 'bg-primary-soft text-primary' : 'bg-slate-200/60 text-slate-500' }}">{{ $pendingPaymentCount }}</span>
            </button>
        </div>
        <div class="relative max-w-xs w-full">
            <x-icon name="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search name, email, phone..." class="w-full rounded-xl border border-border bg-white py-2 pl-10 pr-4 text-xs font-medium text-slate-900 outline-none focus:border-primary" />
        </div>
    </div>

    @if ($applicants->isEmpty())
        <div class="py-24 text-center text-slate-500 text-xs bg-white rounded-2xl border border-dashed border-border italic shadow-xs">
            No applicants in this queue right now.
        </div>
    @else
        <div class="bg-white rounded-2xl border border-border/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/90 border-b border-border/70">
                            <th class="px-5 py-3.5 text-[12px] font-bold text-slate-500 uppercase tracking-wider">Applicant</th>
                            <th class="px-5 py-3.5 text-[12px] font-bold text-slate-500 uppercase tracking-wider">Phone</th>
                            <th class="px-5 py-3.5 text-[12px] font-bold text-slate-500 uppercase tracking-wider">Applied</th>
                            <th class="px-5 py-3.5 text-[12px] font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/60">
                        @foreach ($applicants as $applicant)
                            <tr class="transition-colors hover:bg-slate-50/70">
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-extrabold text-slate-900">{{ $applicant->name }}</p>
                                    <p class="text-[12px] text-slate-500">{{ $applicant->email }}</p>
                                </td>
                                <td class="px-5 py-3.5 text-xs text-slate-700">{{ $applicant->phone ?: '—' }}</td>
                                <td class="px-5 py-3.5 text-xs text-slate-600">{{ $applicant->created_at->format('M j, Y') }}</td>
                                <td class="px-5 py-3.5 text-right">
                                    <button wire:click="view({{ $applicant->id }})" class="inline-flex items-center gap-1.5 rounded-xl bg-primary-soft px-3 py-1.5 text-[12px] font-bold text-primary hover:opacity-80 cursor-pointer">
                                        <x-icon name="arrow-up-right" class="h-3 w-3" /> Review
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-pagination :paginator="$applicants" item-label="applicants" />
        </div>
    @endif

    {{-- Detail / Review Modal --}}
    @if ($viewingUser)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" wire:click="closeView"></div>
            <div x-data x-show="true" x-transition class="relative w-full max-w-2xl transform rounded-2xl bg-white p-5 shadow-2xl transition-all border border-border max-h-[85vh] overflow-y-auto">
                <div class="flex items-start justify-between border-b border-border pb-4 mb-4">
                    <div>
                        <h3 class="text-base font-bold text-foreground">{{ $viewingUser->name }}</h3>
                        <p class="text-xs text-muted-foreground">{{ $viewingUser->email }} &middot; {{ $viewingUser->phone ?: '—' }}</p>
                    </div>
                    <button wire:click="closeView" class="rounded-lg p-1 text-muted-foreground hover:bg-slate-100 hover:text-foreground transition-colors">
                        <x-icon name="x" class="h-4.5 w-4.5" />
                    </button>
                </div>

                @if ($viewingUser->registration_status === 'pending_review')
                    <div class="space-y-4 text-xs">
                        <div>
                            <p class="text-muted-foreground font-bold uppercase tracking-wider text-[12px] mb-2">References</p>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="rounded-xl border border-border p-3">
                                    <p class="font-bold text-foreground">{{ $viewingUser->ref1_name ?: '—' }}</p>
                                    <p class="text-muted-foreground mt-0.5">{{ $viewingUser->ref1_phone ?: '—' }}</p>
                                </div>
                                <div class="rounded-xl border border-border p-3">
                                    <p class="font-bold text-foreground">{{ $viewingUser->ref2_name ?: '—' }}</p>
                                    <p class="text-muted-foreground mt-0.5">{{ $viewingUser->ref2_phone ?: '—' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-4 mt-4 border-t border-border">
                        <button wire:click="openRejectStep1({{ $viewingUser->id }})" class="inline-flex items-center gap-1.5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-2 text-xs font-bold text-rose-600 hover:bg-rose-100 cursor-pointer">
                            <x-icon name="x-circle" class="h-3.5 w-3.5" /> Reject
                        </button>
                        <button wire:click="approveStep1({{ $viewingUser->id }})" class="inline-flex items-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white hover:opacity-90 cursor-pointer">
                            <x-icon name="check-circle-2" class="h-3.5 w-3.5" /> Approve — Move to Step 2
                        </button>
                    </div>
                @else
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @if (media_url($viewingUser->aadhar_document))
                                <div>
                                    <p class="text-[12px] font-bold text-muted-foreground uppercase mb-1">Aadhar</p>
                                    <x-image-lightbox :src="media_url($viewingUser->aadhar_document)" alt="Aadhar" thumb-class="h-24 w-full object-cover" />
                                </div>
                            @endif
                            @if (media_url($viewingUser->pan_document))
                                <div>
                                    <p class="text-[12px] font-bold text-muted-foreground uppercase mb-1">PAN</p>
                                    <x-image-lightbox :src="media_url($viewingUser->pan_document)" alt="PAN" thumb-class="h-24 w-full object-cover" />
                                </div>
                            @endif
                            @if (media_url($viewingUser->business_document))
                                <div>
                                    <p class="text-[12px] font-bold text-muted-foreground uppercase mb-1">{{ str_replace('_', ' ', $viewingUser->business_document_type ?: 'Business Doc') }}</p>
                                    <x-image-lightbox :src="media_url($viewingUser->business_document)" alt="Business Document" thumb-class="h-24 w-full object-cover" />
                                </div>
                            @endif
                            @if (media_url($viewingUser->membership_payment_screenshot))
                                <div>
                                    <p class="text-[12px] font-bold text-muted-foreground uppercase mb-1">Payment Screenshot</p>
                                    <x-image-lightbox :src="media_url($viewingUser->membership_payment_screenshot)" alt="Payment Screenshot" thumb-class="h-24 w-full object-cover" />
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-4 mt-2 border-t border-border">
                            <button wire:click="openRejectPayment({{ $viewingUser->id }})" class="inline-flex items-center gap-1.5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-2 text-xs font-bold text-rose-600 hover:bg-rose-100 cursor-pointer">
                                <x-icon name="x-circle" class="h-3.5 w-3.5" /> Reject
                            </button>
                            <button wire:click="approvePayment({{ $viewingUser->id }})" class="inline-flex items-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white hover:opacity-90 cursor-pointer">
                                <x-icon name="check-circle-2" class="h-3.5 w-3.5" /> Approve — Activate Membership
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Reject Modal --}}
    @if ($rejectingId !== null)
        <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-slate-900/65 backdrop-blur-sm" wire:click="cancelRejectStep1"></div>
            <div x-data x-show="true" x-transition class="relative z-50 w-full max-w-md bg-white rounded-3xl p-6 shadow-2xl border border-border space-y-4">
                <h3 class="text-base font-extrabold text-slate-900">Reject Application</h3>
                <div>
                    <label class="text-xs font-bold text-slate-700 mb-1 block">Reason {{ $viewingUser?->registration_status === 'pending_payment_review' ? '(shown to the applicant)' : '(optional, included in email)' }}</label>
                    <textarea wire:model="rejectReason" rows="3" class="w-full rounded-xl border border-border bg-white px-3 py-2 text-xs outline-none focus:border-primary resize-none" placeholder="Reason for rejection"></textarea>
                    @error('rejectReason') <p class="mt-1 text-[12px] font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" wire:click="cancelRejectStep1" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 cursor-pointer">Cancel</button>
                    @if ($viewingUser && $viewingUser->registration_status === 'pending_payment_review')
                        <button wire:click="confirmRejectPayment" class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-bold text-white hover:bg-rose-700 cursor-pointer">Confirm Reject</button>
                    @else
                        <button wire:click="confirmRejectStep1" class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-bold text-white hover:bg-rose-700 cursor-pointer">Confirm Reject & Delete</button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
