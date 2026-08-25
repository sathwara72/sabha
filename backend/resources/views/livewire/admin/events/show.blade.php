<div class="space-y-5 pb-10" x-data="{ lightbox: null }">
    @unless ($event)
        <div class="py-16 text-center space-y-4">
            <h2 class="text-xl font-bold text-foreground">Event Not Found</h2>
            <p class="text-sm text-muted">The requested event could not be found or has been removed.</p>
            <a href="{{ route('admin.events.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white hover:opacity-90">
                <x-icon name="chevron-left" class="h-4 w-4" /> Back to Events List
            </a>
        </div>
    @else
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.events.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-muted hover:text-foreground transition-colors group">
                <x-icon name="chevron-left" class="h-4 w-4 group-hover:-translate-x-0.5 transition-transform" />
                Back to Events List
            </a>
        </div>

        {{-- Event Overview --}}
        <div class="glass-card p-5 rounded-2xl border border-border bg-white shadow-sm flex flex-col md:flex-row gap-5">
            @if (media_url($event->image))
                <div class="w-full md:w-56 h-40 rounded-xl overflow-hidden bg-slate-900 shrink-0 relative">
                    <img src="{{ media_url($event->image) }}" alt="{{ $event->title }}" class="w-full h-full object-cover" />
                    <span class="absolute top-2 left-2 px-2 py-0.5 rounded-full text-[12px] font-bold bg-primary text-white">{{ $event->type ?: 'Event' }}</span>
                </div>
            @endif

            <div class="flex-1 space-y-3">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h1 class="text-xl sm:text-2xl font-bold text-foreground tracking-tight">{{ $event->title }}</h1>
                </div>

                <p class="text-xs text-muted line-clamp-2">{{ $event->description }}</p>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-1 border-t border-border/60">
                    <div class="flex items-center gap-2">
                        <div class="p-2 rounded-lg bg-primary-soft text-primary"><x-icon name="calendar" class="h-3.5 w-3.5" /></div>
                        <div>
                            <p class="text-[12px] font-semibold text-muted">Date</p>
                            <p class="text-xs font-bold text-foreground">{{ $event->date->format('M j, Y') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="p-2 rounded-lg bg-emerald-50 text-emerald-600"><x-icon name="map-pin" class="h-3.5 w-3.5" /></div>
                        <div>
                            <p class="text-[12px] font-semibold text-muted">Location</p>
                            <p class="text-xs font-bold text-foreground truncate max-w-[120px]">{{ $event->location }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="p-2 rounded-lg bg-blue-50 text-blue-600"><x-icon name="users" class="h-3.5 w-3.5" /></div>
                        <div>
                            <p class="text-[12px] font-semibold text-muted">Registrations</p>
                            <p class="text-xs font-bold text-foreground">{{ $registrationsTotal }} Total</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="p-2 rounded-lg bg-amber-50 text-amber-600"><x-icon name="image" class="h-3.5 w-3.5" /></div>
                        <div>
                            <p class="text-[12px] font-semibold text-muted">Event Gallery</p>
                            <p class="text-xs font-bold text-foreground">{{ $galleryTotal }} Media</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="flex items-center gap-2 border-b border-border pb-0">
            <button
                wire:click="setTab('registrations')"
                class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold transition-all border-b-2 {{ $activeTab === 'registrations' ? 'border-primary text-primary bg-primary-soft/30 rounded-t-xl' : 'border-transparent text-muted hover:text-foreground' }}"
            >
                <x-icon name="users" class="h-3.5 w-3.5" /> Registrations & Bookings
                <span class="ml-1 rounded-full bg-slate-100 px-2 py-0.5 text-[12px] font-bold text-foreground">{{ $registrationsTotal }}</span>
            </button>
            <button
                wire:click="setTab('gallery')"
                class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold transition-all border-b-2 {{ $activeTab === 'gallery' ? 'border-primary text-primary bg-primary-soft/30 rounded-t-xl' : 'border-transparent text-muted hover:text-foreground' }}"
            >
                <x-icon name="image" class="h-3.5 w-3.5" /> Event Gallery
                <span class="ml-1 rounded-full bg-slate-100 px-2 py-0.5 text-[12px] font-bold text-foreground">{{ $galleryTotal }}</span>
            </button>
        </div>

        {{-- TAB: Registrations --}}
        @if ($activeTab === 'registrations')
            <div class="space-y-4">
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 bg-white p-3 rounded-xl border border-border shadow-sm">
                    <div class="flex items-center gap-1 overflow-x-auto pb-1 sm:pb-0">
                        @foreach (['all', 'pending', 'approved', 'rejected'] as $filter)
                            <button
                                wire:click="setMemberFilter('{{ $filter }}')"
                                class="px-3 py-1.5 text-xs font-bold rounded-lg capitalize transition-all whitespace-nowrap {{ $memberFilter === $filter ? 'bg-slate-900 text-white shadow-sm' : 'bg-surface text-muted hover:text-foreground hover:bg-slate-100' }}"
                            >
                                {{ $filter }}
                            </button>
                        @endforeach
                    </div>

                    <div class="relative w-full sm:w-64">
                        <x-icon name="search" class="absolute left-3 top-2.5 h-3.5 w-3.5 text-muted" />
                        <input
                            type="text"
                            wire:model.live.debounce.400ms="memberSearch"
                            placeholder="Search member, email, ticket..."
                            class="w-full rounded-xl border border-border bg-surface py-2 pl-9 pr-3 text-xs text-foreground outline-none focus:border-primary"
                        />
                    </div>
                </div>

                @if ($registrations->isEmpty())
                    <div class="glass-card py-16 text-center text-muted border border-dashed border-border rounded-xl">
                        No registrations found matching your filter.
                    </div>
                @else
                    <div class="glass-card p-0 overflow-hidden border border-border rounded-2xl bg-white shadow-sm">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-border bg-slate-50/70 text-[12px] font-bold text-muted uppercase tracking-wider">
                                        <th class="py-3 px-4">Member</th>
                                        <th class="py-3 px-4">Contact</th>
                                        <th class="py-3 px-4">Ticket No.</th>
                                        <th class="py-3 px-4">Status</th>
                                        <th class="py-3 px-4">Check-in</th>
                                        <th class="py-3 px-4 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border text-xs">
                                    @foreach ($registrations as $reg)
                                        @php
                                            $user = $reg->user;
                                            $isPending = $reg->status === 'pending';
                                            $isApproved = $reg->status === 'approved';
                                            $isRejected = $reg->status === 'rejected';
                                        @endphp
                                        <tr class="hover:bg-slate-50/60 transition-colors">
                                            <td class="py-3 px-4 font-semibold text-foreground">
                                                <div class="flex flex-col">
                                                    <span>{{ $user->name ?? 'N/A' }}</span>
                                                    <span class="text-[12px] text-muted font-normal">Member</span>
                                                </div>
                                            </td>
                                            <td class="py-3 px-4 text-muted">
                                                <div class="flex flex-col">
                                                    <span>{{ $user->email ?? 'N/A' }}</span>
                                                    <span class="text-[12px] text-muted">{{ $user->phone ?? '' }}</span>
                                                </div>
                                            </td>
                                            <td class="py-3 px-4 font-mono font-bold text-foreground">{{ $reg->ticket_number ?: '#' . $reg->id }}</td>
                                            <td class="py-3 px-4">
                                                @if ($isApproved)
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[12px] font-bold text-emerald-600 border border-emerald-100"><x-icon name="check-circle-2" class="h-2.5 w-2.5" /> Approved</span>
                                                @elseif ($isPending)
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-0.5 text-[12px] font-bold text-amber-600 border border-amber-100"><x-icon name="clock" class="h-2.5 w-2.5" /> Pending</span>
                                                @elseif ($isRejected)
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2.5 py-0.5 text-[12px] font-bold text-red-600 border border-red-100"><x-icon name="x-circle" class="h-2.5 w-2.5" /> Rejected</span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-4">
                                                <button
                                                    wire:click="toggleAttendance({{ $reg->id }})"
                                                    @disabled(! $isApproved)
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[12px] font-bold transition-all {{ $reg->is_attended ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-muted hover:bg-slate-200' }} {{ ! $isApproved ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer' }}"
                                                >
                                                    @if ($reg->is_attended)
                                                        <x-icon name="check" class="h-2.5 w-2.5" /> Attended
                                                    @else
                                                        Mark Present
                                                    @endif
                                                </button>
                                            </td>
                                            <td class="py-3 px-4 text-right">
                                                <div class="flex items-center justify-end gap-1.5">
                                                    @if ($isPending)
                                                        <button wire:click="approve({{ $reg->id }})" class="px-2.5 py-1 rounded-lg bg-emerald-500 text-white text-[12px] font-bold hover:bg-emerald-600 transition-colors shadow-sm">Approve</button>
                                                        <button wire:click="openReject({{ $reg->id }})" class="px-2.5 py-1 rounded-lg bg-red-50 text-red-600 border border-red-100 text-[12px] font-bold hover:bg-red-600 hover:text-white transition-colors shadow-sm">Reject</button>
                                                    @elseif ($isApproved)
                                                        <button wire:click="openReject({{ $reg->id }})" class="px-2.5 py-1 rounded-lg bg-slate-100 text-muted hover:bg-red-50 hover:text-red-600 text-[12px] font-bold transition-colors">Revoke</button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <x-pagination :paginator="$registrations" item-label="registrations" page-name="regPage" />
                    </div>
                @endif
            </div>
        @endif

        {{-- TAB: Gallery --}}
        @if ($activeTab === 'gallery')
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <div>
                        <h2 class="text-sm font-bold text-foreground">Event Gallery Photos & Videos</h2>
                        <p class="text-xs text-muted">Upload and manage media specifically for "{{ $event->title }}"</p>
                    </div>
                    <button wire:click="openUploadModal" class="inline-flex items-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white transition-all hover:opacity-90 active:scale-95 shadow-sm">
                        <x-icon name="plus" class="h-3.5 w-3.5" /> Add Event Media
                    </button>
                </div>

                @if ($gallery->isEmpty())
                    <div class="glass-card py-16 text-center text-muted border border-dashed border-border rounded-xl">
                        No media uploaded for this event yet. Click "Add Event Media" button to upload photos, videos, or ZIP archives!
                    </div>
                @else
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            @foreach ($gallery as $item)
                                @php $isVideo = is_video_file($item->image_path); @endphp
                                <div
                                    x-on:click="lightbox = { src: {{ Illuminate\Support\Js::from(media_url($item->image_path)) }}, caption: {{ Illuminate\Support\Js::from($item->caption) }}, isVideo: {{ $isVideo ? 'true' : 'false' }} }"
                                    class="glass-card p-0 overflow-hidden flex flex-col group relative cursor-pointer border border-border hover:border-primary/50 transition-all rounded-xl shadow-sm"
                                >
                                    <div class="relative h-40 w-full bg-slate-900 overflow-hidden">
                                        @if ($isVideo)
                                            <video src="{{ media_url($item->image_path) }}" class="h-full w-full object-cover" muted preload="metadata"></video>
                                        @else
                                            <img src="{{ media_url($item->image_path) }}" alt="{{ $item->caption ?: 'Event photo' }}" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300" />
                                        @endif

                                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all duration-300 flex items-center justify-center">
                                            <x-icon name="zoom-in" class="text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200 h-7 w-7" />
                                        </div>

                                        <div class="absolute top-2 left-2 z-10">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[12px] font-bold tracking-wider uppercase shadow-2xs backdrop-blur-md border {{ $isVideo ? 'bg-white/92 text-amber-700 border-amber-200/80' : 'bg-white/92 text-primary border-primary/20' }}">
                                                @if ($isVideo)
                                                    <x-icon name="film" class="h-2.5 w-2.5 text-amber-500" /> Video
                                                @else
                                                    <x-icon name="image" class="h-2.5 w-2.5 text-primary" /> Image
                                                @endif
                                            </span>
                                        </div>

                                        <button
                                            type="button"
                                            x-on:click.stop="$wire.openDeleteMedia({{ $item->id }})"
                                            class="absolute top-2.5 right-2.5 z-20 rounded-xl bg-red-50/90 border border-red-100 p-2 text-red-600 hover:bg-red-600 hover:text-white transition-all shadow-sm md:opacity-0 md:group-hover:opacity-100 duration-200"
                                            title="Delete media"
                                        >
                                            <x-icon name="trash-2" class="h-3 w-3" />
                                        </button>
                                    </div>

                                    @if ($item->caption)
                                        <div class="p-3 border-t border-border/60 bg-surface/30">
                                            <p class="text-xs text-foreground font-medium line-clamp-2 leading-snug">{{ $item->caption }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <x-pagination :paginator="$gallery" item-label="items" page-name="galPage" />
                    </div>
                @endif
            </div>
        @endif

        {{-- Reject Modal --}}
        @if ($rejectModalId !== null)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" wire:click="cancelReject"></div>
                <div x-data x-show="true" x-transition class="relative w-full max-w-sm rounded-2xl bg-white p-5 shadow-2xl border border-border">
                    <h3 class="text-sm font-bold text-foreground mb-2">Reject Registration</h3>
                    <p class="text-xs text-muted mb-3">Please state a reason for rejecting this registration request.</p>
                    <textarea
                        wire:model="rejectReason"
                        rows="3"
                        placeholder="Enter rejection reason..."
                        class="w-full rounded-xl border border-border bg-surface p-2.5 text-xs text-foreground outline-none focus:border-primary resize-none mb-4"
                    ></textarea>
                    <div class="flex justify-end gap-2">
                        <button wire:click="cancelReject" class="px-3 py-1.5 rounded-xl border border-border text-xs font-bold text-foreground hover:bg-slate-100">Cancel</button>
                        <button wire:click="confirmReject" @disabled(trim($rejectReason) === '') class="px-3 py-1.5 rounded-xl bg-red-600 text-white text-xs font-bold hover:bg-red-700 disabled:opacity-50">Confirm Reject</button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Gallery Upload Modal --}}
        @if ($isUploadModalOpen)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" wire:click="closeUploadModal"></div>
                <div x-data x-show="true" x-transition class="relative w-full max-w-md rounded-2xl bg-white p-5 shadow-2xl border border-border">
                    <div class="flex items-start justify-between border-b border-border pb-3 mb-4">
                        <h2 class="text-base font-bold text-foreground flex items-center gap-2">
                            <x-icon name="upload" class="h-4 w-4 text-primary" /> Upload Event Media
                        </h2>
                        <button wire:click="closeUploadModal" class="rounded-lg p-1 text-muted hover:bg-slate-100 hover:text-foreground">
                            <x-icon name="x" class="h-4.5 w-4.5" />
                        </button>
                    </div>

                    @if ($uploadError)
                        <div class="rounded-xl bg-red-50 border border-red-100 p-3 text-xs font-semibold text-red-600 flex items-center gap-2 mb-4">
                            <x-icon name="alert-circle" class="h-3.5 w-3.5 shrink-0" /><span>{{ $uploadError }}</span>
                        </div>
                    @endif

                    <form wire:submit="uploadMedia" class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-foreground flex items-center justify-between">
                                <span>Select Files (Images, Videos or ZIP Archives)</span>
                                <span class="text-[12px] text-primary font-bold bg-primary-soft px-1.5 py-0.5 rounded">Multi-Select Enabled</span>
                            </label>
                            <div class="relative border-2 border-dashed border-border rounded-xl p-6 bg-surface/35 hover:bg-surface/65 transition-colors cursor-pointer flex flex-col items-center justify-center">
                                <input type="file" multiple wire:model="mediaFiles" accept="image/*,video/*,.zip,application/zip,application/x-zip-compressed" class="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10" />
                                <x-icon name="upload" class="h-6 w-6 text-primary mb-2" />
                                <span class="text-xs font-semibold text-foreground text-center line-clamp-1 px-2">
                                    @if (count($mediaFiles) > 0)
                                        {{ count($mediaFiles) }} file{{ count($mediaFiles) > 1 ? 's' : '' }} selected ({{ collect($mediaFiles)->map(fn ($f) => $f->getClientOriginalName())->implode(', ') }})
                                    @else
                                        Click to select single/multiple files or ZIP archive
                                    @endif
                                </span>
                                <span class="text-[12px] text-muted-foreground mt-1">Select multiple images/videos or ZIP archive (up to 100MB)</span>
                            </div>
                        </div>

                        <div class="flex justify-end gap-2 border-t border-border pt-4 mt-2">
                            <button type="button" wire:click="closeUploadModal" class="rounded-xl border border-border bg-white px-4 py-2 text-xs font-bold text-foreground hover:bg-slate-50">Cancel</button>
                            <button type="submit" wire:loading.attr="disabled" wire:target="uploadMedia" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white hover:opacity-90 disabled:opacity-60 cursor-pointer">
                                <span wire:loading.remove wire:target="uploadMedia">Upload <x-icon name="plus" class="h-3.5 w-3.5 inline" /></span>
                                <span wire:loading wire:target="uploadMedia">Uploading...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- Lightbox --}}
        <div x-show="lightbox" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-black/90 backdrop-blur-md" x-on:click="lightbox = null"></div>
            <button x-on:click="lightbox = null" class="absolute top-4 right-4 z-50 rounded-full bg-white/10 hover:bg-white/20 text-white p-2">
                <x-icon name="x" class="h-5 w-5" />
            </button>
            <div class="relative z-40 max-w-5xl w-full mx-auto px-14 flex flex-col items-center gap-4">
                <template x-if="lightbox && !lightbox.isVideo">
                    <img :src="lightbox?.src" :alt="lightbox?.caption" class="max-h-[75vh] max-w-full object-contain rounded-2xl shadow-2xl" />
                </template>
                <template x-if="lightbox && lightbox.isVideo">
                    <video :src="lightbox?.src" controls autoplay class="max-h-[75vh] max-w-full rounded-2xl shadow-2xl"></video>
                </template>
                <p class="text-sm font-medium text-white/80 max-w-xl" x-show="lightbox?.caption" x-text="lightbox?.caption"></p>
            </div>
        </div>

        <x-admin.confirm-modal
            :show="$deleteMediaId !== null"
            title="Delete Event Media"
            message="Are you sure you want to delete this media item from this event? This action cannot be undone."
            confirm-label="Delete"
            variant="danger"
            confirm-action="confirmDeleteMedia"
            cancel-action="cancelDeleteMedia"
        />
    @endunless
</div>
