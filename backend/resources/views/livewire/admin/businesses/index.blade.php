<div x-data="{ paymentModalUrl: null }">
    <div class="space-y-4">
        <div class="flex flex-col">
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-foreground">Business Approvals</h1>
            <p class="text-sm text-muted">Review and approve member businesses</p>
        </div>

        <div class="flex items-center gap-3 rounded-xl bg-primary-soft p-3">
            <x-icon name="info" class="h-5 w-5 shrink-0 text-primary" />
            <p class="text-sm font-semibold text-foreground">
                Review each business carefully before approving it for the community.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="relative flex-1 max-w-md">
                <x-icon name="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                <input
                    type="text"
                    wire:model.live.debounce.400ms="search"
                    placeholder="Search by name, category, or description..."
                    class="w-full rounded-xl border border-border bg-white py-2 pl-10 pr-4 text-xs text-foreground outline-none transition-colors placeholder:text-muted-foreground focus:border-primary"
                />
            </div>
            <div class="flex items-center gap-1 bg-surface p-1 rounded-xl border border-border self-start sm:self-auto justify-center">
                @foreach (['all', 'pending', 'approved', 'rejected'] as $status)
                    <button
                        wire:click="setStatusFilter('{{ $status }}')"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg capitalize transition-all cursor-pointer {{ $statusFilter === $status ? 'bg-white text-foreground shadow-sm' : 'text-muted hover:text-foreground' }}"
                    >
                        {{ $status }}
                        <span class="px-1.5 py-0.5 text-[12px] rounded-full font-bold transition-colors {{ $statusFilter === $status ? 'bg-primary-soft text-primary' : 'bg-slate-200/60 text-slate-500' }}">
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
                    <div class="glass-card p-0 flex flex-col rounded-2xl border border-border">
                        <div class="relative h-28 w-full overflow-hidden rounded-t-2xl shrink-0">
                            @if (media_url($biz->cover_image))
                                <img src="{{ media_url($biz->cover_image) }}" alt="{{ $biz->name }}" class="w-full h-full object-cover" />
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-primary/10 via-primary/5 to-slate-100"></div>
                            @endif
                            <span class="absolute top-2.5 right-2.5 px-2.5 py-0.5 rounded-full text-[12px] font-bold border {{ $statusColor[$biz->status] ?? 'bg-surface text-muted border-border' }}">
                                {{ ucfirst($biz->status) }}
                            </span>
                        </div>

                        <div class="px-4 pb-4 flex flex-col gap-2.5 flex-1">
                            <div class="flex items-center gap-3 -mt-7">
                                <div class="relative z-10 shrink-0 h-14 w-14 rounded-xl border-2 border-white shadow-lg bg-white overflow-hidden flex items-center justify-center p-1">
                                    @if (media_url($biz->logo))
                                        <img src="{{ media_url($biz->logo) }}" alt="{{ $biz->name }}" class="w-full h-full object-contain" />
                                    @else
                                        <span class="text-xl font-bold text-primary">{{ $biz->name ? mb_substr($biz->name, 0, 1) : '?' }}</span>
                                    @endif
                                </div>
                                <div class="flex-1 mt-8">
                                    <h3 class="text-sm font-bold text-foreground leading-tight line-clamp-1">{{ $biz->name }}</h3>
                                    @if ($biz->tagline)
                                        <p class="text-[12px] text-muted italic line-clamp-1 mt-0.5">{{ $biz->tagline }}</p>
                                    @endif
                                </div>
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
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                <x-pagination :paginator="$businesses" item-label="businesses" />
            </div>
        @endif
    </div>

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
