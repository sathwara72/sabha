@php
    $inputClass = 'w-full rounded-xl border border-border bg-white px-3 py-2 text-xs text-foreground outline-none transition-colors placeholder:text-muted-foreground focus:border-primary';
    $labelClass = 'text-xs font-semibold text-foreground';
@endphp

<div class="space-y-5 max-w-lg">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.hero-slider.index') }}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-border bg-white text-muted-foreground hover:text-foreground hover:bg-slate-50 transition-all cursor-pointer shadow-xs">
            <x-icon name="arrow-left" class="h-4 w-4" />
        </a>
        <div>
            <h1 class="text-xl sm:text-2xl font-semibold tracking-tight text-foreground">{{ $heroImageId ? 'Edit Hero Slide' : 'Add Hero Slide' }}</h1>
            <p class="text-xs text-muted">{{ $heroImageId ? 'Update this homepage slider banner' : 'Add a rotating banner to the homepage slider' }}</p>
        </div>
    </div>

    <div class="glass-card p-5">
        @if ($uploadError)
            <div class="rounded-xl bg-red-50 border border-red-100 p-3 text-xs font-semibold text-red-600 flex items-center gap-2 mb-4">
                <x-icon name="alert-circle" class="h-3.5 w-3.5 shrink-0" /><span>{{ $uploadError }}</span>
            </div>
        @endif

        <form wire:submit="save" class="space-y-4">
            <div class="space-y-1.5">
                <label class="{{ $labelClass }}">Slider Image</label>
                @if ($mediaFile)
                    <div class="relative h-32 w-full rounded-xl overflow-hidden border border-border bg-slate-50">
                        <img src="{{ $mediaFile->temporaryUrl() }}" alt="Preview" class="h-full w-full object-cover" />
                    </div>
                @elseif ($imagePreviewUrl)
                    <div class="relative h-32 w-full rounded-xl overflow-hidden border border-border bg-slate-50">
                        <img src="{{ $imagePreviewUrl }}" alt="Preview" class="h-full w-full object-cover" />
                    </div>
                @endif
                <div class="relative border-2 border-dashed border-border rounded-xl p-6 bg-surface/35 hover:bg-surface/65 transition-colors cursor-pointer flex flex-col items-center justify-center mt-2">
                    <input type="file" wire:model="mediaFile" accept="image/*" class="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10" />
                    <x-icon name="upload" class="h-6 w-6 text-primary mb-2" />
                    <span class="text-xs font-semibold text-foreground text-center line-clamp-1 px-2">
                        {{ $mediaFile ? $mediaFile->getClientOriginalName() : ($heroImageId ? 'Click to replace banner image' : 'Click to select banner image') }}
                    </span>
                    <span class="text-[12px] text-muted-foreground mt-1">Recommended aspect ratio: 21:9 or landscape</span>
                </div>
                @error('mediaFile') <p class="text-[12px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-1">
                <label class="{{ $labelClass }}">Title <span class="font-normal text-muted-foreground">(optional)</span></label>
                <input type="text" wire:model="title" placeholder="e.g. Annual Business Summit" class="{{ $inputClass }}" />
                @error('title') <p class="text-[12px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-1">
                <label class="{{ $labelClass }}">Caption <span class="font-normal text-muted-foreground">(optional)</span></label>
                <input type="text" wire:model="caption" placeholder="Short caption shown with the slide" class="{{ $inputClass }}" />
                @error('caption') <p class="text-[12px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-1 border-t border-border pt-3">
                <label class="{{ $labelClass }}">Clicking this slide opens...</label>
                <select wire:model.live="linkType" class="{{ $inputClass }}">
                    <option value="none">Nothing (not clickable)</option>
                    <option value="event">An Event</option>
                    <option value="external">An External Link</option>
                </select>
            </div>

            @if ($linkType === 'event')
                <div class="space-y-1">
                    <label class="{{ $labelClass }}">Select Event</label>
                    <select wire:model="eventId" class="{{ $inputClass }}">
                        <option value="">Choose an event...</option>
                        @foreach ($events as $evt)
                            <option value="{{ $evt->id }}">{{ $evt->title }} — {{ $evt->date->format('M j, Y') }}</option>
                        @endforeach
                    </select>
                    @error('eventId') <p class="text-[12px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
            @endif

            @if ($linkType === 'external')
                <div class="space-y-1">
                    <label class="{{ $labelClass }}">External URL</label>
                    <input type="url" wire:model="externalUrl" placeholder="https://example.com" class="{{ $inputClass }}" />
                    @error('externalUrl') <p class="text-[12px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
            @endif

            <div class="flex justify-end gap-2 border-t border-border pt-4 mt-2">
                <a href="{{ route('admin.hero-slider.index') }}" class="rounded-xl border border-border bg-white px-4 py-2 text-xs font-bold text-foreground hover:bg-slate-50 active:scale-95 transition-colors">
                    Cancel
                </a>
                <button type="submit" wire:loading.attr="disabled" wire:target="save" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white hover:opacity-90 active:scale-[0.98] disabled:opacity-60 cursor-pointer transition-all">
                    <span wire:loading.remove wire:target="save">{{ $heroImageId ? 'Save Changes' : 'Add Slide' }}</span>
                    <span wire:loading wire:target="save">{{ $heroImageId ? 'Saving...' : 'Uploading...' }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
