@php
    $inputClass = 'w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2 text-xs font-semibold text-slate-900 outline-none transition-all placeholder:text-slate-400 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10';
    $labelClass = 'block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1';
@endphp

<div class="space-y-4 font-outfit w-full">
    <form wire:submit="save" class="space-y-4">
        {{-- Top Navigation & Action Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200/80">
            <div class="flex items-center gap-3">
                <a
                    href="{{ route('admin.events.index') }}"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 hover:text-primary hover:bg-slate-50 hover:border-primary/40 transition-all cursor-pointer shadow-2xs shrink-0"
                    title="Back to Events"
                >
                    <x-icon name="arrow-left" class="h-4 w-4" />
                </a>
                <div>
                    <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 leading-tight">
                        {{ $eventId ? 'Edit Event' : 'Create New Event' }}
                    </h1>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center gap-2 self-end sm:self-auto">
                <a
                    href="{{ route('admin.events.index') }}"
                    class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors shadow-2xs cursor-pointer"
                >
                    Cancel
                </a>
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="save"
                    class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-gradient-to-r from-[#00379D] to-[#082e6e] px-4 py-2 text-xs font-bold text-white shadow-sm shadow-primary/20 hover:opacity-95 active:scale-[0.98] disabled:opacity-60 transition-all cursor-pointer"
                >
                    <x-icon name="check-circle-2" class="h-3.5 w-3.5" wire:loading.remove wire:target="save" />
                    <span wire:loading.remove wire:target="save">
                        {{ $eventId ? 'Update & Publish' : 'Publish Event' }}
                    </span>
                    <span wire:loading wire:target="save">Saving Event...</span>
                </button>
            </div>
        </div>

        {{-- Main 2-Column Space-Optimized Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
            {{-- Left Column (8 Columns): Main Content, Agenda, Speakers, Videos --}}
            <div class="lg:col-span-8 space-y-4">
                {{-- Basic Event Info Card --}}
                <div class="rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-2xs space-y-4">
                    <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                        <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-50 text-primary">
                            <x-icon name="calendar" class="h-3.5 w-3.5" />
                        </div>
                        <h2 class="text-sm font-bold text-slate-900">Event Overview</h2>
                    </div>

                    <div class="space-y-3">
                        <div class="space-y-1">
                            <label class="{{ $labelClass }}">
                                Event Title <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                wire:model="title"
                                placeholder="e.g. SABHA Annual Business Conclave & Awards 2026"
                                class="{{ $inputClass }} text-sm font-bold"
                            />
                            @error('title') <p class="text-[11px] text-rose-600 font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="{{ $labelClass }}">
                                Description <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                wire:model="description"
                                rows="4"
                                placeholder="Provide comprehensive details about this event, expected outcomes, who should attend, and special highlights..."
                                class="{{ $inputClass }} leading-relaxed"
                            ></textarea>
                            @error('description') <p class="text-[11px] text-rose-600 font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Agenda Items Card --}}
                <div class="rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-2xs space-y-3.5">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2">
                            <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                                <x-icon name="clock" class="h-3.5 w-3.5" />
                            </div>
                            <h2 class="text-sm font-bold text-slate-900">Program Agenda & Schedule</h2>
                        </div>
                        <button
                            type="button"
                            wire:click="addAgendaItem"
                            class="inline-flex items-center gap-1 text-[11px] font-bold text-primary hover:text-[#082e6e] bg-blue-50/70 border border-blue-200/60 rounded-lg px-2.5 py-1 transition-colors cursor-pointer"
                        >
                            <x-icon name="plus" class="h-3 w-3" />
                            <span>Add Session</span>
                        </button>
                    </div>

                    @if (empty($agenda))
                        <div class="py-6 text-center text-slate-500 text-xs rounded-xl border border-dashed border-slate-200 bg-slate-50/40">
                            <p class="font-bold text-slate-700">No agenda sessions added yet.</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Click "+ Add Session" to create schedule entries.</p>
                        </div>
                    @else
                        <div class="space-y-2">
                            @foreach ($agenda as $idx => $item)
                                <div class="flex items-center gap-2">
                                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-slate-100 border border-slate-200 text-[10px] font-bold text-slate-600">
                                        {{ $idx + 1 }}
                                    </span>
                                    <input
                                        type="text"
                                        wire:model="agenda.{{ $idx }}"
                                        placeholder="e.g. 09:30 AM - Registration & Welcome Networking Tea"
                                        class="{{ $inputClass }}"
                                    />
                                    <button
                                        type="button"
                                        wire:click="removeAgendaItem({{ $idx }})"
                                        class="h-7 w-7 shrink-0 rounded-lg border border-transparent text-slate-400 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-200 flex items-center justify-center transition-colors cursor-pointer"
                                        title="Remove item"
                                    >
                                        <x-icon name="trash-2" class="h-3 w-3" />
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Keynote Speakers Card --}}
                <div class="rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-2xs space-y-3.5">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2">
                            <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-purple-50 text-purple-600">
                                <x-icon name="users" class="h-3.5 w-3.5" />
                            </div>
                            <h2 class="text-sm font-bold text-slate-900">Featured Speakers & Panelists</h2>
                        </div>
                        <button
                            type="button"
                            wire:click="addSpeaker"
                            class="inline-flex items-center gap-1 text-[11px] font-bold text-primary hover:text-[#082e6e] bg-blue-50/70 border border-blue-200/60 rounded-lg px-2.5 py-1 transition-colors cursor-pointer"
                        >
                            <x-icon name="plus" class="h-3 w-3" />
                            <span>Add Speaker</span>
                        </button>
                    </div>

                    @if (empty($speakers))
                        <div class="py-6 text-center text-slate-500 text-xs rounded-xl border border-dashed border-slate-200 bg-slate-50/40">
                            <p class="font-bold text-slate-700">No speakers added yet.</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Click "+ Add Speaker" to list key panelists.</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach ($speakers as $idx => $speaker)
                                <div class="p-3.5 bg-slate-50/60 rounded-xl border border-slate-200 relative space-y-2.5">
                                    <div class="flex items-center justify-between border-b border-slate-200/60 pb-2">
                                        <span class="text-[11px] font-bold text-slate-700">Speaker #{{ $idx + 1 }}</span>
                                        <button
                                            type="button"
                                            wire:click="removeSpeaker({{ $idx }})"
                                            class="text-slate-400 hover:text-rose-600 p-0.5 transition-colors cursor-pointer"
                                            title="Remove speaker"
                                        >
                                            <x-icon name="trash-2" class="h-3.5 w-3.5" />
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                        <div class="space-y-1">
                                            <label class="text-[10px] font-bold text-slate-600 uppercase">Speaker Name</label>
                                            <input
                                                type="text"
                                                wire:model="speakers.{{ $idx }}.name"
                                                placeholder="e.g. Dr. Rajesh Sathwara"
                                                class="{{ $inputClass }}"
                                            />
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-[10px] font-bold text-slate-600 uppercase">Designation / Role</label>
                                            <input
                                                type="text"
                                                wire:model="speakers.{{ $idx }}.role"
                                                placeholder="e.g. Managing Director & Keynote Speaker"
                                                class="{{ $inputClass }}"
                                            />
                                        </div>
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-bold text-slate-600 uppercase">Brief Bio / Credentials</label>
                                        <input
                                            type="text"
                                            wire:model="speakers.{{ $idx }}.bio"
                                            placeholder="e.g. 20+ years expertise in Manufacturing & Industrial Innovation"
                                            class="{{ $inputClass }}"
                                        />
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- YouTube Video Highlights Card --}}
                <div class="rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-2xs space-y-3.5">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2">
                            <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-rose-50 text-rose-600">
                                <x-icon name="play" class="h-3.5 w-3.5" />
                            </div>
                            <h2 class="text-sm font-bold text-slate-900">YouTube Video Highlights</h2>
                        </div>
                        <button
                            type="button"
                            wire:click="addYoutubeUrl"
                            class="inline-flex items-center gap-1 text-[11px] font-bold text-primary hover:text-[#082e6e] bg-blue-50/70 border border-blue-200/60 rounded-lg px-2.5 py-1 transition-colors cursor-pointer"
                        >
                            <x-icon name="plus" class="h-3 w-3" />
                            <span>Add Video Link</span>
                        </button>
                    </div>

                    @if (empty($youtube_urls))
                        <div class="py-6 text-center text-slate-500 text-xs rounded-xl border border-dashed border-slate-200 bg-slate-50/40">
                            <p class="font-bold text-slate-700">No YouTube links added.</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Click "+ Add Video Link" to embed event clips.</p>
                        </div>
                    @else
                        <div class="space-y-2">
                            @foreach ($youtube_urls as $idx => $url)
                                <div class="flex items-center gap-2">
                                    <x-icon name="film" class="h-4 w-4 text-rose-500 shrink-0" />
                                    <input
                                        type="url"
                                        wire:model="youtube_urls.{{ $idx }}"
                                        placeholder="https://www.youtube.com/watch?v=..."
                                        class="{{ $inputClass }}"
                                    />
                                    <button
                                        type="button"
                                        wire:click="removeYoutubeUrl({{ $idx }})"
                                        class="h-7 w-7 shrink-0 rounded-lg text-slate-400 hover:text-rose-600 flex items-center justify-center transition-colors cursor-pointer"
                                        title="Remove video"
                                    >
                                        <x-icon name="trash-2" class="h-3 w-3" />
                                    </button>
                                </div>
                                @error('youtube_urls.' . $idx)
                                    <p class="text-[11px] text-rose-600 font-semibold -mt-1">{{ $message }}</p>
                                @enderror
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Right Column (4 Columns): Schedule, Location, Category, Pricing, Cover Photo --}}
            <div class="lg:col-span-4 space-y-4">
                {{-- Date, Location & Type Settings Card --}}
                <div class="rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-2xs space-y-3.5">
                    <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                        <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                            <x-icon name="tag" class="h-3.5 w-3.5" />
                        </div>
                        <h3 class="text-sm font-bold text-slate-900">Date & Venue</h3>
                    </div>

                    <div class="space-y-3">
                        <div class="space-y-1">
                            <label class="{{ $labelClass }}">
                                Event Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" wire:model="date" class="{{ $inputClass }}" />
                            @error('date') <p class="text-[11px] text-rose-600 font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="{{ $labelClass }}">
                                Location / Venue <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                wire:model="location"
                                placeholder="e.g. YMCA International Club, SG Highway, Ahmedabad"
                                class="{{ $inputClass }}"
                            />
                            @error('location') <p class="text-[11px] text-rose-600 font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="{{ $labelClass }}">
                                Google Maps Embed Iframe / Map URL
                            </label>
                            <textarea
                                rows="2"
                                wire:model.blur="map_iframe"
                                placeholder='Paste Google Maps embed <iframe> code or map share link'
                                class="{{ $inputClass }} font-mono text-[11px] resize-none"
                            ></textarea>
                            @error('map_iframe') <p class="text-[11px] text-rose-600 font-semibold mt-0.5">{{ $message }}</p> @enderror

                            @if (parse_google_maps_iframe_src($map_iframe))
                                <div class="mt-2 rounded-xl border border-slate-200 overflow-hidden bg-slate-900 h-36 w-full shadow-2xs">
                                    <iframe src="{{ parse_google_maps_iframe_src($map_iframe) }}" class="w-full h-full border-0" allowfullscreen loading="lazy"></iframe>
                                </div>
                            @endif
                        </div>

                        {{-- Event Category: Commented Out
                        <div class="space-y-1">
                            <label class="{{ $labelClass }}">Event Category</label>
                            <select wire:model="type" class="{{ $inputClass }}">
                                <option value="Mixer">Mixer & Networking</option>
                                <option value="Workshop">Workshop & Training</option>
                                <option value="Summit">Annual Conclave & Summit</option>
                                <option value="Awards">Awards & Felicitation</option>
                            </select>
                        </div>
                        --}}
                    </div>
                </div>

                {{-- Booking Window / Schedule Card --}}
                <div class="rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-2xs space-y-3.5">
                    <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                        <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                            <x-icon name="calendar" class="h-3.5 w-3.5" />
                        </div>
                        <h3 class="text-sm font-bold text-slate-900">Booking Schedule Dates</h3>
                    </div>

                    <div class="space-y-3">
                        <div class="space-y-1">
                            <label class="{{ $labelClass }}">
                                Booking Start Date
                            </label>
                            <input
                                type="date"
                                wire:model="booking_start_date"
                                class="{{ $inputClass }}"
                            />
                            @error('booking_start_date') <p class="text-[11px] text-rose-600 font-semibold mt-0.5">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="{{ $labelClass }}">
                                Booking End Date
                            </label>
                            <input
                                type="date"
                                wire:model="booking_end_date"
                                class="{{ $inputClass }}"
                            />
                            @error('booking_end_date') <p class="text-[11px] text-rose-600 font-semibold mt-0.5">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Ticket Pricing Card --}}
                <div class="rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-2xs space-y-3.5">
                    <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                        <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                            <x-icon name="receipt" class="h-3.5 w-3.5" />
                        </div>
                        <h3 class="text-sm font-bold text-slate-900">Seat Pricing & Tickets</h3>
                    </div>

                    <div class="space-y-3">
                        <div class="space-y-1">
                            <label class="{{ $labelClass }}">Standard Member Price</label>
                            <input
                                type="text"
                                wire:model="price_normal"
                                placeholder="e.g. ₹2,499 or Free Entry"
                                class="{{ $inputClass }}"
                            />
                            @error('price_normal') <p class="text-[11px] text-rose-600 font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="{{ $labelClass }}">Verified Member Discounted Price</label>
                            <input
                                type="text"
                                wire:model="price_verified"
                                placeholder="e.g. ₹1,499 or Free Entry"
                                class="{{ $inputClass }}"
                            />
                            @error('price_verified') <p class="text-[11px] text-rose-600 font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Event Cover Image Card --}}
                <div class="rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-2xs space-y-3.5">
                    <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                        <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-sky-50 text-primary">
                            <x-icon name="image" class="h-3.5 w-3.5" />
                        </div>
                        <h3 class="text-sm font-bold text-slate-900">Event Banner Photo</h3>
                    </div>

                    <div>
                        @if ($imageFile)
                            <div class="relative w-full h-40 rounded-xl overflow-hidden border border-slate-200 bg-slate-50 group">
                                <img src="{{ $imageFile->temporaryUrl() }}" alt="Preview" class="h-full w-full object-cover" />
                                <button
                                    type="button"
                                    wire:click="removeImage"
                                    class="absolute inset-0 bg-slate-900/60 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-white text-xs font-bold cursor-pointer"
                                >
                                    <x-icon name="trash-2" class="h-4 w-4 mr-1" /> Remove Image
                                </button>
                            </div>
                        @elseif ($imagePreviewUrl)
                            <div class="relative w-full h-40 rounded-xl overflow-hidden border border-slate-200 bg-slate-50 group">
                                <img src="{{ $imagePreviewUrl }}" alt="Preview" class="h-full w-full object-cover" />
                                <button
                                    type="button"
                                    wire:click="removeImage"
                                    class="absolute inset-0 bg-slate-900/60 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-white text-xs font-bold cursor-pointer"
                                >
                                    <x-icon name="trash-2" class="h-4 w-4 mr-1" /> Replace Image
                                </button>
                            </div>
                        @else
                            <label class="flex h-36 w-full cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-200 bg-slate-50/60 hover:bg-slate-100/70 hover:border-primary/40 transition-all p-4 text-center group">
                                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-400 group-hover:text-primary transition-colors shadow-2xs mb-2">
                                    <x-icon name="upload" class="h-4 w-4" />
                                </div>
                                <span class="text-xs font-bold text-slate-700 group-hover:text-primary transition-colors">Click to Upload Event Banner</span>
                                <span class="text-[10px] text-slate-400 mt-0.5">PNG, JPG, WebP up to 10MB (16:9 Recommended)</span>
                                <input type="file" wire:model="imageFile" accept="image/*" class="hidden" />
                            </label>
                        @endif
                        @error('imageFile') <p class="text-[11px] text-rose-600 font-semibold mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Public Notice Card --}}
                <div class="rounded-2xl border border-slate-200/90 bg-white p-4 shadow-2xs">
                    <p class="text-[11px] text-slate-500 text-center font-medium flex items-center justify-center gap-1.5">
                        <x-icon name="info" class="h-3.5 w-3.5 text-primary shrink-0" />
                        <span>Published events appear immediately on SABHA website.</span>
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>
