<div class="space-y-4 font-outfit">
    {{-- Top Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 leading-tight">Member Registrations</h1>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-primary border border-blue-200/60">
                    {{ $pendingReviewCount + $pendingPaymentCount }} Pending
                </span>
            </div>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Review new applications, verify KYC documents & payments, and activate members</p>
        </div>

        {{-- Search Input --}}
        <div class="relative w-full sm:w-72 self-start sm:self-auto">
            <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400 pointer-events-none" />
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search name, email, phone..."
                class="w-full rounded-xl border border-slate-200 bg-white py-1.5 pl-9 pr-8 text-xs font-semibold text-slate-900 outline-none focus:border-primary focus:bg-white transition-colors placeholder:text-slate-400 shadow-2xs"
            />
            @if ($search)
                <button
                    type="button"
                    wire:click="$set('search', '')"
                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 cursor-pointer"
                >
                    <x-icon name="x" class="h-3.5 w-3.5" />
                </button>
            @endif
        </div>
    </div>

    @if ($successMsg)
        <div wire:key="success-{{ md5($successMsg) }}" x-data="{ show: true }"
            x-init="setTimeout(() => { show = false; $wire.successMsg = '' }, 4000)" x-show="show" x-transition
            class="rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-xs font-semibold text-emerald-800 flex items-center gap-2 shadow-2xs">
            <x-icon name="check-circle-2" class="h-4 w-4 text-emerald-600 shrink-0" />
            <span>{{ $successMsg }}</span>
        </div>
    @endif

    {{-- Modern Segmented Tab Bar --}}
    <div class="flex items-center gap-1.5 bg-white p-1 rounded-2xl border border-slate-200/90 shadow-2xs flex-wrap">
        <button
            type="button"
            wire:click="setTab('pending_review')"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-xl transition-all cursor-pointer whitespace-nowrap {{ $tab === 'pending_review' ? 'bg-amber-500 text-white shadow-sm shadow-amber-500/25' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
        >
            <x-icon name="clock" class="h-3.5 w-3.5" />
            <span>Pending Review (Step 1)</span>
            <span class="px-1.5 py-0.5 text-[11px] rounded-full font-black {{ $tab === 'pending_review' ? 'bg-white/25 text-white' : 'bg-amber-100 text-amber-800' }}">
                {{ $pendingReviewCount }}
            </span>
        </button>

        <button
            type="button"
            wire:click="setTab('step1_approved')"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-xl transition-all cursor-pointer whitespace-nowrap {{ $tab === 'step1_approved' ? 'bg-[#00379D] text-white shadow-sm shadow-primary/25' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
        >
            <x-icon name="user-check" class="h-3.5 w-3.5" />
            <span>Step 1 Approved</span>
            <span class="px-1.5 py-0.5 text-[11px] rounded-full font-black {{ $tab === 'step1_approved' ? 'bg-white/25 text-white' : 'bg-blue-100 text-blue-800' }}">
                {{ $step1ApprovedCount }}
            </span>
        </button>

        <button
            type="button"
            wire:click="setTab('pending_payment')"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-xl transition-all cursor-pointer whitespace-nowrap {{ $tab === 'pending_payment' ? 'bg-purple-600 text-white shadow-sm shadow-purple-600/25' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
        >
            <x-icon name="credit-card" class="h-3.5 w-3.5" />
            <span>Payment Review (Step 2)</span>
            <span class="px-1.5 py-0.5 text-[11px] rounded-full font-black {{ $tab === 'pending_payment' ? 'bg-white/25 text-white' : 'bg-purple-100 text-purple-800' }}">
                {{ $pendingPaymentCount }}
            </span>
        </button>

        <button
            type="button"
            wire:click="setTab('active')"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-xl transition-all cursor-pointer whitespace-nowrap {{ $tab === 'active' ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/25' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
        >
            <x-icon name="check-circle-2" class="h-3.5 w-3.5" />
            <span>Active Members</span>
            <span class="px-1.5 py-0.5 text-[11px] rounded-full font-black {{ $tab === 'active' ? 'bg-white/25 text-white' : 'bg-emerald-100 text-emerald-800' }}">
                {{ $activeMembersCount }}
            </span>
        </button>

        <button
            type="button"
            wire:click="setTab('all')"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-xl transition-all cursor-pointer whitespace-nowrap {{ $tab === 'all' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
        >
            <x-icon name="users" class="h-3.5 w-3.5" />
            <span>All Registrations</span>
            <span class="px-1.5 py-0.5 text-[11px] rounded-full font-black {{ $tab === 'all' ? 'bg-white/25 text-white' : 'bg-slate-200 text-slate-700' }}">
                {{ $allCount }}
            </span>
        </button>
    </div>

    @if ($applicants->isEmpty())
        <div
            class="py-20 text-center text-slate-500 text-xs bg-white rounded-2xl border border-dashed border-border italic shadow-xs">
            No applicants found in this category.
        </div>
    @else
        <div class="bg-white rounded-2xl border border-border/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/90 border-b border-border/70">
                            <th class="px-5 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Applicant
                            </th>
                            <th class="px-5 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Phone</th>
                            <th class="px-5 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Status
                            </th>
                            <th class="px-5 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Applied
                                Date</th>
                            <th
                                class="px-5 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/60">
                        @foreach ($applicants as $applicant)
                            <tr class="transition-colors hover:bg-slate-50/70">
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-extrabold text-slate-900">{{ $applicant->name }}</p>
                                    <p class="text-[11px] text-slate-500">{{ $applicant->email }}</p>
                                </td>
                                <td class="px-5 py-3.5 text-xs text-slate-700 font-mono">{{ $applicant->phone ?: '—' }}</td>
                                <td class="px-5 py-3.5">
                                    @if ($applicant->registration_status === 'pending_review')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> Pending Review
                                        </span>
                                    @elseif ($applicant->registration_status === 'step1_approved')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-800 border border-blue-200">
                                            <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span> Step 1 Approved (Waiting
                                            Docs)
                                        </span>
                                    @elseif ($applicant->registration_status === 'pending_payment_review')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-purple-50 text-purple-800 border border-purple-200">
                                            <span class="h-1.5 w-1.5 rounded-full bg-purple-500"></span> Payment Review
                                        </span>
                                    @elseif ($applicant->registration_status === 'payment_rejected')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-800 border border-rose-200">
                                            <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span> Payment Rejected
                                        </span>
                                    @elseif ($applicant->registration_status === 'active')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Active Member
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-xs text-slate-600">{{ $applicant->created_at->format('M j, Y') }}
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <button wire:click="view({{ $applicant->id }})"
                                        class="inline-flex items-center gap-1 rounded-xl bg-primary-soft px-3 py-1.5 text-xs font-bold text-primary hover:opacity-80 transition-all cursor-pointer">
                                        <x-icon name="arrow-up-right" class="h-3 w-3" />
                                        <span>{{ in_array($applicant->registration_status, ['pending_review', 'pending_payment_review']) ? 'Review' : 'View' }}</span>
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

    {{-- Detail / Review Modal Teleported to

    <body> --}}
        @if ($viewingUser)
            <template x-teleport="body">
                <div class="fixed inset-0 z-[99999] overflow-y-auto p-4 sm:p-6 flex min-h-full items-center justify-center font-outfit"
                    x-on:keydown.escape.window="$wire.closeView()">
                    <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity" wire:click="closeView">
                    </div>
                    <div
                        class="relative w-full max-w-lg my-auto transform rounded-3xl bg-white overflow-hidden shadow-2xl transition-all border border-slate-200 z-10 flex flex-col max-h-[90vh]">
                        {{-- Modal Header --}}
                        <div
                            class="flex items-center justify-between px-5 py-4 border-b border-slate-100 bg-slate-50/75 shrink-0">
                            <div class="flex items-center gap-3 min-w-0">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-2xl bg-blue-50 text-primary font-black text-sm shrink-0 shadow-2xs border border-blue-100">
                                    {{ strtoupper(substr($viewingUser->name, 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-sm sm:text-base font-bold text-slate-900 truncate leading-tight">
                                        {{ $viewingUser->name }}</h3>
                                    <p class="text-[11px] text-slate-500 font-medium truncate mt-0.5">
                                        {{ $viewingUser->email }} &middot; {{ $viewingUser->phone ?: '—' }}</p>
                                </div>
                            </div>
                            <button type="button" wire:click="closeView"
                                class="flex h-8 w-8 items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-400 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition-colors cursor-pointer shadow-2xs shrink-0"
                                title="Close">
                                <x-icon name="x" class="h-4 w-4" />
                            </button>
                        </div>

                        {{-- Modal Content --}}
                        <div class="p-5 space-y-4 overflow-y-auto flex-1">
                            {{-- Status Pill in Modal --}}
                            <div
                                class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 border border-slate-200/80">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Current
                                    Status</span>
                                @if ($viewingUser->registration_status === 'pending_review')
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                        <span class="h-2 w-2 rounded-full bg-amber-500"></span> Pending Review (Step 1)
                                    </span>
                                @elseif ($viewingUser->registration_status === 'step1_approved')
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-800 border border-blue-200">
                                        <span class="h-2 w-2 rounded-full bg-blue-500"></span> Step 1 Approved (Waiting for User
                                        Docs)
                                    </span>
                                @elseif ($viewingUser->registration_status === 'pending_payment_review')
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-800 border border-purple-200">
                                        <span class="h-2 w-2 rounded-full bg-purple-500"></span> Payment Review (Step 2)
                                    </span>
                                @elseif ($viewingUser->registration_status === 'active')
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Active Member
                                    </span>
                                @endif
                            </div>

                            @if ($viewingUser->registration_status === 'step1_approved')
                                <div
                                    class="rounded-2xl bg-blue-50/70 border border-blue-200 p-3 text-xs text-blue-900 leading-relaxed space-y-1">
                                    <p class="font-bold">Step 1 was approved.</p>
                                    <p class="text-blue-700 text-[11px]">The applicant has received an approval email and must
                                        log in to upload their KYC documents and membership fee payment receipt. Once submitted,
                                        they will appear under "Payment Review (Step 2)".</p>
                                </div>
                            @endif

                            {{-- References --}}
                            <div class="space-y-2">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Applicant
                                    References</span>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                    <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-3 shadow-2xs">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase">Reference 1</span>
                                        <p class="font-bold text-xs text-slate-900 mt-0.5">
                                            {{ $viewingUser->ref1_name ?: '—' }}</p>
                                        <p class="text-xs text-slate-600 mt-0.5 font-mono">
                                            {{ $viewingUser->ref1_phone ?: '—' }}</p>
                                    </div>
                                    <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-3 shadow-2xs">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase">Reference 2</span>
                                        <p class="font-bold text-xs text-slate-900 mt-0.5">
                                            {{ $viewingUser->ref2_name ?: '—' }}</p>
                                        <p class="text-xs text-slate-600 mt-0.5 font-mono">
                                            {{ $viewingUser->ref2_phone ?: '—' }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Documents (if available) --}}
                            @php
                                $docs = [
                                    ['label' => 'Aadhar Card', 'path' => $viewingUser->aadhar_document, 'alt' => 'Aadhar'],
                                    ['label' => 'PAN Card', 'path' => $viewingUser->pan_document, 'alt' => 'PAN'],
                                    ['label' => str_replace('_', ' ', $viewingUser->business_document_type ?: 'Business Doc'), 'path' => $viewingUser->business_document, 'alt' => 'Business Document'],
                                    ['label' => 'Payment Receipt', 'path' => $viewingUser->membership_payment_screenshot, 'alt' => 'Payment Screenshot'],
                                ];
                                $hasDocs = collect($docs)->filter(fn($d) => !empty($d['path']))->isNotEmpty();
                            @endphp

                            @if ($hasDocs)
                                <div class="space-y-2 pt-2 border-t border-slate-100">
                                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Submitted
                                        Documents & Payment</span>
                                    <div class="grid grid-cols-2 gap-2.5">
                                        @foreach ($docs as $doc)
                                            @if (media_url($doc['path']))
                                                @php
                                                    $url = media_url($doc['path']);
                                                    $isPdf = str_ends_with(strtolower($doc['path']), '.pdf');
                                                @endphp
                                                <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-2.5 shadow-2xs">
                                                    <p class="text-[10px] font-bold text-slate-500 uppercase mb-1.5 truncate">
                                                        {{ $doc['label'] }}</p>
                                                    @if ($isPdf)
                                                        <a href="{{ $url }}" target="_blank"
                                                            class="flex flex-col items-center justify-center h-20 w-full rounded-xl border border-slate-200 bg-white p-2 text-center hover:bg-slate-50 transition-colors group">
                                                            <x-icon name="file-text"
                                                                class="h-5 w-5 text-rose-500 mb-1 group-hover:scale-110 transition-transform" />
                                                            <span class="text-[10px] font-bold text-slate-800">Open PDF ↗</span>
                                                        </a>
                                                    @else
                                                        <x-image-lightbox :src="$url" :alt="$doc['alt']"
                                                            thumb-class="h-20 w-full object-cover rounded-xl border border-slate-200" />
                                                    @endif
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Modal Footer --}}
                        <div
                            class="flex items-center justify-between gap-2.5 px-5 py-3.5 border-t border-slate-100 bg-slate-50/80 shrink-0">
                            <button type="button" wire:click="closeView"
                                class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100 active:scale-[0.98] transition-all shadow-xs cursor-pointer">
                                Close
                            </button>

                            <div class="flex items-center gap-2">
                                @if ($viewingUser->registration_status === 'pending_review')
                                    <button type="button" wire:click="openRejectStep1({{ $viewingUser->id }})"
                                        class="inline-flex items-center gap-1.5 rounded-xl border border-rose-200 bg-rose-50 px-3.5 py-2 text-xs font-bold text-rose-700 hover:bg-rose-100 active:scale-[0.98] transition-all cursor-pointer shadow-2xs">
                                        <x-icon name="x-circle" class="h-3.5 w-3.5 text-rose-600" />
                                        <span>Reject</span>
                                    </button>
                                    <button type="button" wire:click="approveStep1({{ $viewingUser->id }})"
                                        class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-[#00379D] to-[#082e6e] px-4 py-2 text-xs font-bold text-white hover:opacity-95 active:scale-[0.98] transition-all shadow-md shadow-primary/20 cursor-pointer">
                                        <x-icon name="check-circle-2" class="h-3.5 w-3.5" />
                                        <span>Approve (Step 2)</span>
                                    </button>
                                @elseif ($viewingUser->registration_status === 'pending_payment_review')
                                    <button type="button" wire:click="openRejectPayment({{ $viewingUser->id }})"
                                        class="inline-flex items-center gap-1.5 rounded-xl border border-rose-200 bg-rose-50 px-3.5 py-2 text-xs font-bold text-rose-700 hover:bg-rose-100 active:scale-[0.98] transition-all cursor-pointer shadow-2xs">
                                        <x-icon name="x-circle" class="h-3.5 w-3.5 text-rose-600" />
                                        <span>Reject</span>
                                    </button>
                                    <button type="button" wire:click="approvePayment({{ $viewingUser->id }})"
                                        class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-700 px-4 py-2 text-xs font-bold text-white hover:opacity-95 active:scale-[0.98] transition-all shadow-md shadow-emerald-600/20 cursor-pointer">
                                        <x-icon name="check-circle-2" class="h-3.5 w-3.5" />
                                        <span>Activate Membership</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        @endif

        {{-- Reject Modal Teleported to

        <body> --}}
            @if ($rejectingId !== null)
                <template x-teleport="body">
                    <div class="fixed inset-0 z-[99999] overflow-y-auto p-4 sm:p-6 flex min-h-full items-center justify-center font-outfit"
                        x-on:keydown.escape.window="$wire.cancelRejectStep1()">
                        <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity"
                            wire:click="cancelRejectStep1"></div>
                        <div
                            class="relative z-10 w-full max-w-md bg-white rounded-3xl p-5 shadow-2xl border border-slate-200 space-y-4 my-auto">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                <div class="flex items-center gap-2.5">
                                    <div
                                        class="flex h-8 w-8 items-center justify-center rounded-xl bg-rose-50 text-rose-600">
                                        <x-icon name="alert-circle" class="h-4 w-4" />
                                    </div>
                                    <h3 class="text-sm sm:text-base font-bold text-slate-900">Reject Application</h3>
                                </div>
                                <button type="button" wire:click="cancelRejectStep1"
                                    class="text-slate-400 hover:text-slate-600">
                                    <x-icon name="x" class="h-4 w-4" />
                                </button>
                            </div>

                            <div>
                                <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1 block">
                                    Reason
                                    {{ $viewingUser?->registration_status === 'pending_payment_review' ? '(Shown to the applicant)' : '(Optional, sent via email)' }}
                                </label>
                                <textarea wire:model="rejectReason" rows="3"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2 text-xs font-semibold text-slate-900 outline-none focus:border-primary focus:bg-white resize-none"
                                    placeholder="Reason for rejection"></textarea>
                                @error('rejectReason')
                                <p class="mt-1 text-[11px] font-semibold text-rose-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                                <button type="button" wire:click="cancelRejectStep1"
                                    class="px-4 py-2 rounded-xl border border-slate-300 bg-white text-xs font-bold text-slate-700 hover:bg-slate-100 cursor-pointer shadow-xs">
                                    Cancel
                                </button>
                                @if ($viewingUser && $viewingUser->registration_status === 'pending_payment_review')
                                    <button type="button" wire:click="confirmRejectPayment"
                                        class="rounded-xl bg-rose-600 px-4.5 py-2 text-xs font-bold text-white hover:bg-rose-700 shadow-md shadow-rose-600/20 active:scale-95 cursor-pointer">
                                        Confirm Reject
                                    </button>
                                @else
                                    <button type="button" wire:click="confirmRejectStep1"
                                        class="rounded-xl bg-rose-600 px-4.5 py-2 text-xs font-bold text-white hover:bg-rose-700 shadow-md shadow-rose-600/20 active:scale-95 cursor-pointer">
                                        Confirm Reject & Delete
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </template>
            @endif
</div>