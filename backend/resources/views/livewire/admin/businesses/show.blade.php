<div class="space-y-4 font-outfit" x-data="{ paymentModalUrl: null }">
    @unless ($business)
        <div class="py-20 text-center space-y-4">
            <h2 class="text-xl font-bold text-foreground">Business Not Found</h2>
            <p class="text-sm text-muted">The requested business details could not be found or loaded.</p>
            <a href="{{ route('admin.businesses.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white transition-all hover:opacity-90 active:scale-[0.98]">
                <x-icon name="arrow-left" class="h-3.5 w-3.5" /> Back to Businesses
            </a>
        </div>
    @else
        @php
            $statusBadgeColor = [
                'approved' => 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20 backdrop-blur-md',
                'pending' => 'bg-amber-500/10 text-amber-500 border-amber-500/20 backdrop-blur-md',
                'rejected' => 'bg-rose-500/10 text-rose-500 border-rose-500/20 backdrop-blur-md',
            ];
            $addressLine = collect([$business->address, $business->area, $business->state])->filter()->implode(', ');
            $services = collect($business->services ?? [])->map(function ($s) {
                return is_array($s) ? ['title' => $s['title'] ?? '', 'desc' => $s['desc'] ?? ''] : ['title' => (string) $s, 'desc' => ''];
            })->filter(fn ($s) => $s['title'] !== '')->values();
            $mapSrc = parse_google_maps_iframe_src($business->map_iframe);
        @endphp

        {{-- Top action bar --}}
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.businesses.index') }}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all shadow-sm cursor-pointer">
                    <x-icon name="arrow-left" class="h-3.5 w-3.5" />
                </a>
                <div>
                    <h1 class="text-lg font-black text-slate-800 tracking-tight">Business Profile Review</h1>
                    <p class="text-[10px] text-slate-400">Detailed verification and approval workspace</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-1.5">
                @if ($business->status === 'pending')
                    <button wire:click="approve" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 px-3.5 py-2 text-xs font-bold text-white transition-all hover:opacity-95 shadow-sm active:scale-[0.98] cursor-pointer">
                        <x-icon name="check-circle-2" class="h-3.5 w-3.5" /> Approve Submission
                    </button>
                    <button wire:click="openReject" class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-rose-200 bg-rose-50 px-3.5 py-2 text-xs font-bold text-rose-600 transition-all hover:bg-rose-100 active:scale-[0.98] cursor-pointer">
                        <x-icon name="x-circle" class="h-3.5 w-3.5" /> Reject Submission
                    </button>
                @endif
                @if (media_url($business->payment_screenshot))
                    <button x-on:click="paymentModalUrl = {{ Illuminate\Support\Js::from(media_url($business->payment_screenshot)) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-700 bg-amber-50 border border-amber-200/80 rounded-xl px-3.5 py-2 hover:bg-amber-100 transition-all cursor-pointer shadow-sm active:scale-[0.98]">
                        <x-icon name="receipt" class="h-3.5 w-3.5" /> View Payment Receipt
                    </button>
                @endif
                <button wire:click="openDeleteModal" class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-rose-200 bg-rose-50 px-3.5 py-2 text-xs font-bold text-rose-600 transition-all hover:bg-rose-100 active:scale-[0.98] cursor-pointer" title="Delete this business">
                    <x-icon name="trash-2" class="h-3.5 w-3.5" /> Delete Business
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-start">
            {{-- Left column --}}
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white rounded-3xl border border-slate-200/60 overflow-hidden shadow-sm hover:shadow-md transition-all duration-300">
                    <div class="relative w-full aspect-[3.2/1] max-h-[320px] min-h-[180px] bg-slate-950 flex items-center justify-center overflow-hidden">
                        @if (media_url($business->cover_image))
                            <img src="{{ media_url($business->cover_image) }}" alt="{{ $business->name }}" class="w-full h-full object-cover opacity-90" />
                        @else
                            <div class="w-full h-full bg-gradient-to-r from-indigo-950 via-slate-900 to-indigo-950"></div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 via-slate-950/20 to-transparent pointer-events-none"></div>

                        <div class="absolute top-3 right-3 flex items-center gap-2">
                            <span class="px-3 py-0.5 rounded-full text-[9px] font-extrabold border uppercase tracking-widest shadow-lg {{ $statusBadgeColor[$business->status] ?? 'bg-white/10 text-white border-white/20' }}">
                                {{ $business->status }}
                            </span>
                        </div>

                        <div class="absolute -bottom-8 left-6 h-20 w-20 sm:h-24 sm:w-24 rounded-2xl border-4 border-white shadow-xl bg-white overflow-hidden flex items-center justify-center p-1.5">
                            @if (media_url($business->logo))
                                <img src="{{ media_url($business->logo) }}" alt="{{ $business->name }}" class="w-full h-full object-contain" />
                            @else
                                <span class="text-3xl font-black text-indigo-600">{{ $business->name ? mb_substr($business->name, 0, 1) : '?' }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="pt-10 px-5 pb-5 space-y-4">
                        <div>
                            <h2 class="text-xl font-black text-slate-800 leading-tight">{{ $business->name }}</h2>
                            @if ($business->tagline)
                                <p class="text-[11px] text-slate-400 italic mt-1">{{ $business->tagline }}</p>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2.5 border-t border-slate-100/70">
                            <div class="space-y-2.5">
                                <div class="flex items-center gap-2 bg-slate-50/50 p-1.5 rounded-xl border border-slate-100/30">
                                    <div class="flex h-6 w-6 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 shrink-0">
                                        <x-icon name="briefcase" class="h-3 w-3" />
                                    </div>
                                    <div class="text-[11px]">
                                        <p class="text-[9px] text-slate-400 font-semibold uppercase leading-none">Category</p>
                                        <p class="font-extrabold text-slate-800 mt-0.5">{{ $business->category }}</p>
                                    </div>
                                </div>

                                @if ($addressLine || $business->pincode)
                                    <div class="flex items-start gap-2 bg-slate-50/50 p-2 rounded-xl border border-slate-100/30">
                                        <div class="flex h-6 w-6 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 shrink-0 mt-0.5">
                                            <x-icon name="map-pin" class="h-3 w-3" />
                                        </div>
                                        <div class="text-[11px]">
                                            <p class="text-[9px] text-slate-400 font-semibold uppercase leading-none">Address & Location</p>
                                            <p class="font-bold text-slate-800 mt-0.5 leading-snug">
                                                {{ $addressLine }}{{ $business->pincode ? " - {$business->pincode}" : '' }}
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                @if ($business->website)
                                    <div class="flex items-center gap-2 bg-slate-50/50 p-1.5 rounded-xl border border-slate-100/30">
                                        <div class="flex h-6 w-6 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 shrink-0">
                                            <x-icon name="link-2" class="h-3 w-3" />
                                        </div>
                                        <div class="text-[11px] truncate">
                                            <p class="text-[9px] text-slate-400 font-semibold uppercase leading-none">Website</p>
                                            <a href="{{ $business->website }}" target="_blank" rel="noopener noreferrer" class="font-bold text-indigo-600 hover:underline truncate block max-w-[180px] mt-0.5">{{ $business->website }}</a>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="space-y-2.5">
                                @if ($business->business_phone)
                                    <div class="flex items-center gap-2 bg-slate-50/50 p-1.5 rounded-xl border border-slate-100/30">
                                        <div class="flex h-6 w-6 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 shrink-0">
                                            <x-icon name="phone" class="h-3 w-3" />
                                        </div>
                                        <div class="text-[11px]">
                                            <p class="text-[9px] text-slate-400 font-semibold uppercase leading-none">Business Phone</p>
                                            <a href="tel:{{ $business->business_phone }}" class="font-bold text-indigo-600 hover:underline mt-0.5 block">{{ $business->business_phone }}</a>
                                        </div>
                                    </div>
                                @endif

                                @if ($business->business_email)
                                    <div class="flex items-center gap-2 bg-slate-50/50 p-1.5 rounded-xl border border-slate-100/30">
                                        <div class="flex h-6 w-6 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 shrink-0">
                                            <x-icon name="mail" class="h-3 w-3" />
                                        </div>
                                        <div class="text-[11px] truncate">
                                            <p class="text-[9px] text-slate-400 font-semibold uppercase leading-none">Business Email</p>
                                            <a href="mailto:{{ $business->business_email }}" class="font-bold text-indigo-600 hover:underline truncate block max-w-[180px] mt-0.5">{{ $business->business_email }}</a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if ($mapSrc)
                            <div class="pt-3 border-t border-slate-100 space-y-2">
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider flex items-center gap-1">
                                    <x-icon name="map-pin" class="h-3 w-3 text-indigo-600" /> Embedded Location Map
                                </p>
                                <div class="relative rounded-2xl overflow-hidden border border-slate-200 shadow-sm h-52 w-full bg-slate-900">
                                    <iframe src="{{ $mapSrc }}" class="w-full h-full border-0" allowfullscreen loading="lazy"></iframe>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-3xl border border-slate-200/60 p-5 shadow-sm space-y-3">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-widest flex items-center gap-2 border-b border-slate-100 pb-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-indigo-600"></span> Business Description
                    </h3>
                    <p class="text-xs text-slate-600 leading-relaxed whitespace-pre-line bg-slate-50/55 p-3.5 rounded-2xl border border-slate-100/60">
                        {{ $business->description ?: 'No description provided.' }}
                    </p>
                </div>

                @if ($services->isNotEmpty())
                    <div class="bg-white rounded-3xl border border-slate-200/60 p-5 shadow-sm space-y-3">
                        <h3 class="text-xs font-bold text-slate-800 uppercase tracking-widest flex items-center gap-2 border-b border-slate-100 pb-2">
                            <span class="h-1.5 w-1.5 rounded-full bg-indigo-600"></span> Services Offered
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach ($services as $service)
                                <div class="bg-gradient-to-br from-slate-50 to-white border border-slate-200/50 p-3 rounded-2xl space-y-1 hover:border-slate-300 transition-all">
                                    <h4 class="text-xs font-bold text-slate-800 flex items-center gap-2">
                                        <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                                        {{ $service['title'] }}
                                    </h4>
                                    @if ($service['desc'])
                                        <p class="text-[11px] text-slate-500 leading-relaxed pl-4">{{ $service['desc'] }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($business->linkedin || $business->instagram || $business->youtube || $business->twitter || $business->whatsapp)
                    <div class="bg-white rounded-3xl border border-slate-200/60 p-5 shadow-sm space-y-3">
                        <h3 class="text-xs font-bold text-slate-800 uppercase tracking-widest flex items-center gap-2 border-b border-slate-100 pb-2">
                            <span class="h-1.5 w-1.5 rounded-full bg-indigo-600"></span> Social Channels
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            @if ($business->linkedin)
                                <a href="{{ $business->linkedin }}" target="_blank" rel="noopener noreferrer" class="bg-indigo-50/50 hover:bg-indigo-100/70 border border-indigo-100/50 px-3.5 py-2 rounded-2xl text-xs font-bold text-indigo-700 transition-all active:scale-[0.98]">LinkedIn</a>
                            @endif
                            @if ($business->instagram)
                                <a href="{{ $business->instagram }}" target="_blank" rel="noopener noreferrer" class="bg-rose-50/50 hover:bg-rose-100/70 border border-rose-100/50 px-3.5 py-2 rounded-2xl text-xs font-bold text-rose-700 transition-all active:scale-[0.98]">Instagram</a>
                            @endif
                            @if ($business->youtube)
                                <a href="{{ $business->youtube }}" target="_blank" rel="noopener noreferrer" class="bg-red-50/50 hover:bg-red-100/70 border border-red-100/50 px-3.5 py-2 rounded-2xl text-xs font-bold text-red-700 transition-all active:scale-[0.98]">YouTube</a>
                            @endif
                            @if ($business->twitter)
                                <a href="{{ $business->twitter }}" target="_blank" rel="noopener noreferrer" class="bg-sky-50/50 hover:bg-sky-100/70 border border-sky-100/50 px-3.5 py-2 rounded-2xl text-xs font-bold text-sky-700 transition-all active:scale-[0.98]">Twitter</a>
                            @endif
                            @if ($business->whatsapp)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $business->whatsapp) }}" target="_blank" rel="noopener noreferrer" class="bg-emerald-50/50 hover:bg-emerald-100/70 border border-emerald-100/50 px-3.5 py-2 rounded-2xl text-xs font-bold text-emerald-700 transition-all active:scale-[0.98]">WhatsApp</a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            {{-- Right column --}}
            <div class="space-y-4">
                <div class="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 text-white rounded-3xl border border-white/5 p-5 shadow-xl space-y-3 relative overflow-hidden">
                    <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-primary/20 blur-2xl pointer-events-none"></div>
                    <div class="absolute -left-8 -bottom-8 h-24 w-24 rounded-full bg-violet-600/10 blur-2xl pointer-events-none"></div>

                    <h3 class="text-xs font-bold uppercase tracking-widest text-indigo-300 flex items-center gap-2 border-b border-white/10 pb-2.5 relative z-10">
                        <x-icon name="shield-check" class="h-3.5 w-3.5 text-indigo-400" /> Submitted By (Owner)
                    </h3>

                    @if ($business->user)
                        <div class="space-y-3 relative z-10">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-white/10 text-base font-bold text-white border border-white/10 shadow-inner">
                                        {{ $business->user->name ? mb_substr($business->user->name, 0, 1) : '?' }}
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h4 class="text-xs font-black text-white leading-tight">{{ $business->user->name }}</h4>
                                            @if ($business->user->is_blocked)
                                                <span class="rounded-md bg-rose-500/30 border border-rose-400/30 px-2 py-0.5 text-[9px] font-bold text-rose-300 uppercase tracking-wider">Blocked</span>
                                            @endif
                                        </div>
                                        <span class="inline-block rounded-md bg-indigo-500/20 border border-indigo-400/20 px-2 py-0.5 text-[9px] font-bold text-indigo-300 mt-1 uppercase tracking-wider">
                                            {{ $business->user->role ?: 'Member' }}
                                        </span>
                                    </div>
                                </div>

                                @if ($business->user->role !== 'admin')
                                    <button
                                        wire:click="openBlockOwnerModal"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-bold transition-all cursor-pointer border {{ $business->user->is_blocked ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30 hover:bg-emerald-500/30' : 'bg-rose-500/20 text-rose-300 border-rose-500/30 hover:bg-rose-500/30' }}"
                                        title="{{ $business->user->is_blocked ? 'Unblock Owner User' : 'Block Owner User' }}"
                                    >
                                        @if ($business->user->is_blocked)
                                            <x-icon name="user-check" class="h-3 w-3" /> Unblock Owner
                                        @else
                                            <x-icon name="ban" class="h-3 w-3" /> Block Owner
                                        @endif
                                    </button>
                                @endif
                            </div>

                            <div class="space-y-2 text-xs pt-3 border-t border-white/10 text-indigo-200/80">
                                @if ($business->user->email)
                                    <div class="flex items-start gap-2">
                                        <span class="font-bold text-indigo-300 w-16 shrink-0">Email:</span>
                                        <a href="mailto:{{ $business->user->email }}" class="text-white hover:underline truncate block max-w-[160px]">{{ $business->user->email }}</a>
                                    </div>
                                @endif
                                @if ($business->user->phone)
                                    <div class="flex items-start gap-2">
                                        <span class="font-bold text-indigo-300 w-16 shrink-0">Phone:</span>
                                        <a href="tel:{{ $business->user->phone }}" class="text-white hover:underline">{{ $business->user->phone }}</a>
                                    </div>
                                @endif
                                @if ($business->user->native_city)
                                    <div class="flex items-start gap-2">
                                        <span class="font-bold text-indigo-300 w-16 shrink-0">Native:</span>
                                        <span class="text-slate-200">{{ $business->user->native_city }}</span>
                                    </div>
                                @endif
                                @if ($business->user->city)
                                    <div class="flex items-start gap-2">
                                        <span class="font-bold text-indigo-300 w-16 shrink-0">City:</span>
                                        <span class="text-slate-200">{{ $business->user->city }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <p class="text-xs text-indigo-200/60 italic">No owner information registered.</p>
                    @endif
                </div>

                @if ($business->founded || $business->team_size || $business->hours || $business->projects)
                    <div class="bg-white rounded-3xl border border-slate-200/60 p-5 shadow-sm space-y-3">
                        <h3 class="text-xs font-bold uppercase tracking-widest text-slate-800 border-b border-slate-100 pb-2.5">Operational Details</h3>
                        <div class="space-y-2 text-xs">
                            @if ($business->founded)
                                <div class="flex justify-between items-center bg-slate-50/50 p-2 rounded-xl border border-slate-100/30">
                                    <span class="font-semibold text-slate-500 flex items-center gap-1.5"><x-icon name="calendar" class="h-3 w-3 text-slate-400" /> Founded</span>
                                    <span class="font-extrabold text-slate-800">{{ $business->founded }}</span>
                                </div>
                            @endif
                            @if ($business->team_size)
                                <div class="flex justify-between items-center bg-slate-50/50 p-2 rounded-xl border border-slate-100/30">
                                    <span class="font-semibold text-slate-500 flex items-center gap-1.5"><x-icon name="users" class="h-3 w-3 text-slate-400" /> Team Size</span>
                                    <span class="font-extrabold text-slate-800">{{ $business->team_size }}</span>
                                </div>
                            @endif
                            @if ($business->hours)
                                <div class="flex justify-between items-start gap-3 bg-slate-50/50 p-2 rounded-xl border border-slate-100/30">
                                    <span class="font-semibold text-slate-500 flex items-center gap-1.5"><x-icon name="clock" class="h-3 w-3 text-slate-400" /> Hours</span>
                                    <span class="font-extrabold text-slate-800 text-right max-w-[140px]">{{ $business->hours }}</span>
                                </div>
                            @endif
                            @if ($business->projects)
                                <div class="flex justify-between items-center bg-slate-50/50 p-2 rounded-xl border border-slate-100/30">
                                    <span class="font-semibold text-slate-500 flex items-center gap-1.5"><x-icon name="briefcase" class="h-3 w-3 text-slate-400" /> Projects</span>
                                    <span class="font-extrabold text-slate-800">{{ $business->projects }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($business->status === 'rejected' && $business->rejection_reason)
                    <div class="bg-rose-50 border border-rose-200/80 rounded-3xl p-5 text-xs space-y-2 shadow-sm">
                        <h3 class="font-black text-rose-700 flex items-center gap-1.5">
                            <x-icon name="x-circle" class="h-3.5 w-3.5" /> Rejection Notice
                        </h3>
                        <p class="text-rose-600 leading-relaxed bg-white/70 p-3 rounded-2xl border border-rose-100">{{ $business->rejection_reason }}</p>
                    </div>
                @endif
            </div>
        </div>

        <x-admin.prompt-modal
            :show="$rejectModalOpen"
            title="Reject Business Submission"
            :message="'Please enter the reason for rejecting &quot;' . $business->name . '&quot;:'"
            placeholder="Enter details reason for rejection..."
            confirm-label="Reject Business"
            model="rejectReason"
            confirm-action="confirmReject"
            cancel-action="cancelReject"
        />

        <x-admin.confirm-modal
            :show="$deleteModalOpen"
            title="Delete Business Profile"
            :message="'Are you sure you want to delete business &quot;' . $business->name . '&quot;? This action cannot be undone.'"
            confirm-label="Delete Business"
            variant="danger"
            confirm-action="confirmDelete"
            cancel-action="cancelDelete"
        />

        @if ($business->user)
            <x-admin.confirm-modal
                :show="$blockOwnerModalOpen"
                :title="$business->user->is_blocked ? 'Unblock Owner User' : 'Block Owner User'"
                :message="'Are you sure you want to ' . ($business->user->is_blocked ? 'unblock' : 'block') . ' owner &quot;' . $business->user->name . '&quot;?'"
                :confirm-label="$business->user->is_blocked ? 'Unblock Owner' : 'Block Owner'"
                :variant="$business->user->is_blocked ? 'success' : 'danger'"
                confirm-action="confirmToggleBlockOwner"
                cancel-action="cancelBlockOwner"
            />
        @endif

        <div x-show="paymentModalUrl" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/85 backdrop-blur-md" x-on:click="paymentModalUrl = null"></div>
            <button x-on:click="paymentModalUrl = null" class="absolute top-4 right-4 z-50 rounded-full bg-white/10 hover:bg-white/20 text-white p-2 transition-colors cursor-pointer">
                <x-icon name="x" class="h-5 w-5" />
            </button>
            <div class="relative z-40 max-w-2xl w-full flex flex-col items-center gap-4">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl px-4 py-2 text-white text-xs font-semibold flex items-center gap-2">
                    <x-icon name="receipt" class="h-3.5 w-3.5 text-amber-300" /> Payment Screenshot
                </div>
                <img :src="paymentModalUrl" alt="Payment Screenshot" class="max-h-[80vh] max-w-full object-contain rounded-2xl shadow-2xl border border-white/10" />
            </div>
        </div>
    @endunless
</div>
