<div class="space-y-4 font-outfit" x-data="{ paymentModalUrl: null }">
    {{-- Top Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 leading-tight">Business Directory & Approvals</h1>
        </div>
    </div>

    {{-- Search & Filter Toolbar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-2.5 sm:p-3 rounded-xl border border-slate-200/90 shadow-2xs">
        <div class="relative flex-1 max-w-md">
            <x-icon name="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400" />
            <input
                type="text"
                wire:model.live.debounce.400ms="search"
                placeholder="Search by name, category, or description..."
                class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-1.5 pl-9 pr-4 text-xs font-semibold text-slate-900 outline-none transition-colors placeholder:text-slate-400 focus:bg-white focus:border-primary shadow-2xs"
            />
        </div>
        <div class="flex items-center gap-1 bg-slate-50 p-1 rounded-xl border border-slate-200 self-start sm:self-auto justify-center">
            @foreach (['all', 'pending', 'approved', 'rejected'] as $status)
                <button
                    wire:click="setStatusFilter('{{ $status }}')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg capitalize transition-all cursor-pointer {{ $statusFilter === $status ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}"
                >
                    {{ $status }}
                    <span class="px-1.5 py-0.2 text-[10px] rounded-full font-bold transition-colors {{ $statusFilter === $status ? 'bg-primary-soft text-primary' : 'bg-slate-200/80 text-slate-600' }}">
                        {{ $counts[$status] ?? 0 }}
                    </span>
                </button>
            @endforeach
        </div>
    </div>

        @if ($businesses->isEmpty())
            <div class="rounded-xl border border-dashed border-border py-20 text-center text-muted">
                {{ $search || $statusFilter !== 'all' ? 'No businesses match your search/filter.' : 'No businesses to review.' }}
            </div>
        @else
            @php
                $statusColor = [
                    'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                    'rejected' => 'bg-red-50 text-red-600 border-red-200',
                ];
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach ($businesses as $biz)
                    <div class="glass-card p-4 flex flex-col gap-2.5 rounded-2xl border border-border">
                        {{-- Top Header: Direct Logo, Name & Status Badge --}}
                        <div class="flex items-start justify-between gap-2.5">
                            <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                <div class="shrink-0 h-11 w-11 flex items-center justify-center">
                                    @if (media_url($biz->logo))
                                        <img src="{{ media_url($biz->logo) }}" alt="{{ $biz->name }}" class="h-11 w-11 object-contain" />
                                    @else
                                        <span class="h-10 w-10 rounded-xl bg-primary-soft text-primary font-bold text-base flex items-center justify-center border border-primary/20">
                                            {{ $biz->name ? mb_substr($biz->name, 0, 1) : '?' }}
                                        </span>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-sm font-bold text-foreground leading-tight truncate" title="{{ $biz->name }}">{{ $biz->name }}</h3>
                                    @if ($biz->tagline)
                                        <p class="text-[11px] text-muted italic truncate mt-0.5">{{ $biz->tagline }}</p>
                                    @endif
                                </div>
                            </div>

                            <span class="shrink-0 px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $statusColor[$biz->status] ?? 'bg-surface text-muted border-border' }}">
                                {{ ucfirst($biz->status) }}
                            </span>
                        </div>

                            <div class="flex flex-wrap gap-x-3 gap-y-1 text-[12px] text-muted font-medium">
                                <span class="flex items-center gap-1 text-primary font-semibold">{{ $biz->category }}</span>
                                @php $addressLine = collect([$biz->area, $biz->state])->filter()->implode(', '); @endphp
                                @if ($addressLine)
                                    <span class="flex items-center gap-1"><x-icon name="map-pin" class="h-2.5 w-2.5 text-primary" /> {{ $addressLine }}</span>
                                @endif
                                @if ($biz->business_phone)
                                    <span class="flex items-center gap-1"><x-icon name="phone" class="h-2.5 w-2.5 text-primary" /> {{ $biz->business_phone }}</span>
                                @endif
                                @if ($biz->website)
                                    <a href="{{ $biz->website }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-1 text-primary hover:underline">
                                        <x-icon name="globe" class="h-2.5 w-2.5" /> Website
                                    </a>
                                @endif
                            </div>

                            <p class="text-[12px] text-muted leading-relaxed line-clamp-2 flex-1">
                                {{ $biz->description ?: 'No description available for this business.' }}
                            </p>

                            @if (media_url($biz->payment_screenshot))
                                <button
                                    x-on:click="paymentModalUrl = {{ Illuminate\Support\Js::from(media_url($biz->payment_screenshot)) }}"
                                    class="flex items-center gap-1.5 text-[12px] font-bold text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-2.5 py-1.5 hover:bg-amber-100 transition-colors w-full justify-center"
                                >
                                    <x-icon name="receipt" class="h-3 w-3" /> View Payment Screenshot
                                </button>
                            @endif

                            <div class="flex items-center gap-2 {{ ! media_url($biz->payment_screenshot) ? 'mt-auto' : '' }}">
                                <a
                                    href="{{ route('admin.businesses.show', $biz->id) }}"
                                    class="flex-1 flex items-center gap-1.5 text-[12px] font-bold text-slate-600 bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5 hover:bg-slate-100 transition-colors justify-center cursor-pointer"
                                >
                                    <x-icon name="eye" class="h-3 w-3" /> View Details
                                </a>
                                <button
                                    wire:click="openDelete({{ $biz->id }}, {{ Illuminate\Support\Js::from($biz->name) }})"
                                    class="flex items-center gap-1.5 text-[12px] font-bold text-rose-600 bg-rose-50 border border-rose-200 rounded-lg px-2.5 py-1.5 hover:bg-rose-100 transition-colors justify-center cursor-pointer"
                                    title="Delete Business"
                                >
                                    <x-icon name="trash-2" class="h-3 w-3" /> Delete
                                </button>
                            </div>

                            @if ($biz->status === 'pending')
                                <div class="flex items-center gap-2 pt-2 border-t border-border">
                                    <button
                                        wire:click="approve({{ $biz->id }})"
                                        class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-3 py-2 text-[12px] font-bold text-white transition-all hover:opacity-90 active:scale-[0.98]"
                                    >
                                        <x-icon name="check-circle-2" class="h-3.5 w-3.5" /> Approve
                                    </button>
                                    <button
                                        wire:click="openReject({{ $biz->id }}, {{ Illuminate\Support\Js::from($biz->name) }})"
                                        class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-xl border border-red-100 bg-red-50 px-3 py-2 text-[12px] font-bold text-red-600 transition-all hover:bg-red-100 active:scale-[0.98]"
                                    >
                                        <x-icon name="x-circle" class="h-3.5 w-3.5" /> Reject
                                    </button>
                                </div>
                            @endif
                        </div>
                @endforeach
            </div>

            <div class="mt-4">
                <x-pagination :paginator="$businesses" item-label="businesses" />
            </div>
        @endif

    <x-admin.prompt-modal
        :show="$rejectingId !== null"
        title="Reject Business Submission"
        :message="$rejectingId !== null ? 'Please enter the reason for rejecting &quot;' . $rejectingName . '&quot;:' : ''"
        placeholder="Enter details reason for rejection..."
        confirm-label="Reject Business"
        model="rejectReason"
        confirm-action="confirmReject"
        cancel-action="cancelReject"
    />

    <x-admin.confirm-modal
        :show="$deletingId !== null"
        title="Delete Business Profile"
        :message="$deletingId !== null ? 'Are you sure you want to delete business &quot;' . $deletingName . '&quot;? This action cannot be undone.' : ''"
        confirm-label="Delete Business"
        variant="danger"
        confirm-action="confirmDelete"
        cancel-action="cancelDelete"
    />

    {{-- Payment Screenshot Lightbox --}}
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
</div>
