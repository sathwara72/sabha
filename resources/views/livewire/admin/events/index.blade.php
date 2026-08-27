<div class="space-y-3 w-full">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-foreground">Events</h1>
            <p class="text-xs text-muted">Manage and monitor community events</p>
        </div>
        <button
            wire:click="openCreateModal"
            class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white transition-all hover:opacity-90 active:scale-[0.98] cursor-pointer"
        >
            <x-icon name="plus-circle" class="h-4 w-4" />
            Create Event
        </button>
    </div>

    <div class="space-y-3 pt-1">
        <div class="glass-card overflow-hidden p-0">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface border-b border-border">
                        <th class="px-4 py-2.5 text-xs font-bold text-muted">Event Details</th>
                        <th class="px-4 py-2.5 text-xs font-bold text-muted">Date & Location</th>
                        <th class="px-4 py-2.5 text-xs font-bold text-muted">Normal Price</th>
                        <th class="px-4 py-2.5 text-xs font-bold text-muted">Verified Price</th>
                        <th class="px-4 py-2.5 text-xs font-bold text-muted text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($events as $evt)
                        <tr class="transition-colors hover:bg-surface/50">
                            <td class="px-4 py-2.5">
                                <div>
                                    <p class="text-xs font-bold text-foreground">{{ $evt->title }}</p>
                                    <span class="inline-block rounded-full bg-primary-soft px-2 py-0.5 text-[12px] font-bold text-primary mt-0.5">{{ $evt->type }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-2.5">
                                <p class="text-xs font-semibold text-foreground">{{ $evt->date->format('M j, Y') }}</p>
                                <p class="text-[12px] text-muted-foreground mt-0.5">{{ $evt->location }}</p>
                            </td>
                            <td class="px-4 py-2.5 font-bold text-xs text-foreground">{{ $evt->price_normal ?: 'N/A' }}</td>
                            <td class="px-4 py-2.5 font-bold text-xs text-primary">{{ $evt->price_verified ?: 'N/A' }}</td>
                            <td class="px-4 py-2.5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a
                                        href="{{ route('admin.events.show', $evt->id) }}"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-primary-soft text-primary hover:bg-primary hover:text-white transition-all cursor-pointer shadow-xs active:scale-95"
                                        title="View Event Details & Members"
                                    >
                                        <x-icon name="eye" class="h-3.5 w-3.5" />
                                    </a>
                                    <button
                                        wire:click="editEvent({{ $evt->id }})"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-amber-50 border border-amber-200/60 text-amber-700 hover:bg-amber-500 hover:text-white transition-all cursor-pointer shadow-xs active:scale-95"
                                        title="Edit Event"
                                    >
                                        <x-icon name="pencil" class="h-3.5 w-3.5" />
                                    </button>
                                    <button
                                        wire:click="openDelete({{ $evt->id }}, {{ Illuminate\Support\Js::from($evt->title) }})"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-rose-50 border border-rose-200/60 text-rose-600 hover:bg-rose-600 hover:text-white transition-all cursor-pointer shadow-xs active:scale-95"
                                        title="Delete Event"
                                    >
                                        <x-icon name="trash-2" class="h-3.5 w-3.5" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-xs text-muted font-medium">
                                No registered events found. Create one above to get started.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination :paginator="$events" item-label="events" />
    </div>

    {{-- Create/Edit Event Modal --}}
    @if ($isCreateModalOpen)
        @php
            $inputClass = 'w-full rounded-xl border border-border bg-white px-3 py-2 text-xs text-foreground outline-none transition-colors placeholder:text-muted-foreground focus:border-primary';
            $labelClass = 'text-xs font-semibold text-foreground';
        @endphp
        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div x-data x-show="true" x-transition class="bg-white rounded-2xl w-full max-w-xl overflow-hidden border border-border shadow-2xl flex flex-col max-h-[90vh]">
                <div class="flex items-center justify-between px-5 py-3.5 border-b border-border bg-surface">
                    <div>
                        <h3 class="text-base font-bold text-foreground">{{ $editingEventId ? 'Edit Event' : 'Create New Event' }}</h3>
                        <p class="text-[12px] text-muted-foreground mt-0.5">{{ $editingEventId ? 'Update the details of this event' : 'Fill in the details below to publish an event' }}</p>
                    </div>
                    <button type="button" wire:click="closeModal" class="p-1 rounded-lg text-muted-foreground hover:bg-slate-100 hover:text-foreground transition-colors cursor-pointer">
                        <x-icon name="x" class="h-4 w-4" />
                    </button>
                </div>

                <form wire:submit="save" class="flex flex-col overflow-hidden">
                    <div class="p-5 space-y-4 overflow-y-auto max-h-[60vh]">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-3">
                            <div class="space-y-1">
                                <label class="{{ $labelClass }}">Event title</label>
                                <input type="text" wire:model="title" placeholder="e.g. Sabha networking night" class="{{ $inputClass }}" />
                                @error('title') <p class="text-[12px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-1">
                                <label class="{{ $labelClass }}">Location</label>
                                <input type="text" wire:model="location" placeholder="e.g. Aloft Hotel, Ahmedabad" class="{{ $inputClass }}" />
                                @error('location') <p class="text-[12px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="{{ $labelClass }}">Date</label>
                                <input type="date" wire:model="date" class="{{ $inputClass }}" />
                                @error('date') <p class="text-[12px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-1">
                                <label class="{{ $labelClass }}">Category</label>
                                <select wire:model="type" class="{{ $inputClass }}">
                                    <option value="Mixer">Mixer</option>
                                    <option value="Workshop">Workshop</option>
                                    <option value="Summit">Summit</option>
                                </select>
                            </div>

                            <div class="space-y-1">
                                <label class="{{ $labelClass }}">Price for Normal Member</label>
                                <input type="text" wire:model="price_normal" placeholder="e.g. ₹2,499 or Free" class="{{ $inputClass }}" />
                                @error('price_normal') <p class="text-[12px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-1">
                                <label class="{{ $labelClass }}">Price for Verified Member</label>
                                <input type="text" wire:model="price_verified" placeholder="e.g. ₹1,499 or Free" class="{{ $inputClass }}" />
                                @error('price_verified') <p class="text-[12px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-1 sm:col-span-2">
                                <label class="{{ $labelClass }}">Description</label>
                                <textarea wire:model="description" rows="2" placeholder="What is this event about?" class="{{ $inputClass }}"></textarea>
                                @error('description') <p class="text-[12px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-1 sm:col-span-2">
                                <label class="{{ $labelClass }}">Event Cover Image</label>
                                <div class="mt-1 flex items-center gap-4">
                                    @if ($imageFile)
                                        <div class="relative h-20 w-32 rounded-xl overflow-hidden border border-border bg-slate-50 group">
                                            <img src="{{ $imageFile->temporaryUrl() }}" alt="Preview" class="h-full w-full object-cover" />
                                            <button type="button" wire:click="removeImage" class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-white text-xs font-semibold cursor-pointer">
                                                Remove
                                            </button>
                                        </div>
                                    @elseif ($imagePreviewUrl)
                                        <div class="relative h-20 w-32 rounded-xl overflow-hidden border border-border bg-slate-50 group">
                                            <img src="{{ $imagePreviewUrl }}" alt="Preview" class="h-full w-full object-cover" />
                                            <button type="button" wire:click="removeImage" class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-white text-xs font-semibold cursor-pointer">
                                                Remove
                                            </button>
                                        </div>
                                    @else
                                        <label class="flex h-20 w-32 cursor-pointer flex-col items-center justify-center rounded-xl border border-dashed border-border bg-slate-50/50 hover:bg-slate-50 transition-colors">
                                            <x-icon name="upload" class="h-5 w-5 text-muted-foreground" />
                                            <span class="text-[12px] text-muted-foreground mt-1 font-semibold">Upload Image</span>
                                            <input type="file" wire:model="imageFile" accept="image/*" class="hidden" />
                                        </label>
                                    @endif
                                    <div class="text-[12px] text-muted-foreground">
                                        <p class="font-semibold">Upload a high-quality cover photo.</p>
                                        <p>Supports PNG, JPG, GIF, WebP (max 10MB).</p>
                                    </div>
                                </div>
                                @error('imageFile') <p class="text-[12px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                            </div>

                            {{-- Agenda --}}
                            <div class="sm:col-span-2 border-t border-border pt-3">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="{{ $labelClass }}">Agenda Items</label>
                                    <button type="button" wire:click="addAgendaItem" class="text-[12px] font-bold text-primary hover:underline flex items-center gap-1 cursor-pointer">+ Add Item</button>
                                </div>
                                @if (empty($agenda))
                                    <p class="text-[12px] text-muted-foreground italic">No agenda items added yet.</p>
                                @else
                                    <div class="space-y-2 max-h-[150px] overflow-y-auto pr-1">
                                        @foreach ($agenda as $idx => $item)
                                            <div class="flex items-center gap-2">
                                                <span class="text-[12px] font-bold text-muted-foreground w-4">{{ $idx + 1 }}.</span>
                                                <input type="text" wire:model="agenda.{{ $idx }}" placeholder="e.g. Registration & Welcome" class="{{ $inputClass }}" />
                                                <button type="button" wire:click="removeAgendaItem({{ $idx }})" class="text-red-500 hover:text-red-700 p-1 cursor-pointer">
                                                    <x-icon name="x" class="h-3.5 w-3.5" />
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            {{-- Speakers --}}
                            <div class="sm:col-span-2 border-t border-border pt-3">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="{{ $labelClass }}">Speakers</label>
                                    <button type="button" wire:click="addSpeaker" class="text-[12px] font-bold text-primary hover:underline flex items-center gap-1 cursor-pointer">+ Add Speaker</button>
                                </div>
                                @if (empty($speakers))
                                    <p class="text-[12px] text-muted-foreground italic">No speakers added yet.</p>
                                @else
                                    <div class="space-y-3 max-h-[220px] overflow-y-auto pr-1">
                                        @foreach ($speakers as $idx => $speaker)
                                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 relative space-y-2">
                                                <button type="button" wire:click="removeSpeaker({{ $idx }})" class="absolute top-2 right-2 text-red-500 hover:text-red-700 cursor-pointer">
                                                    <x-icon name="x" class="h-3.5 w-3.5" />
                                                </button>
                                                <div class="grid grid-cols-2 gap-2">
                                                    <div class="space-y-0.5">
                                                        <label class="text-[12px] font-semibold text-muted-foreground">Speaker Name</label>
                                                        <input type="text" wire:model="speakers.{{ $idx }}.name" placeholder="Name" class="{{ $inputClass }}" />
                                                    </div>
                                                    <div class="space-y-0.5">
                                                        <label class="text-[12px] font-semibold text-muted-foreground">Role / Designation</label>
                                                        <input type="text" wire:model="speakers.{{ $idx }}.role" placeholder="Role (e.g. CEO)" class="{{ $inputClass }}" />
                                                    </div>
                                                </div>
                                                <div class="space-y-0.5">
                                                    <label class="text-[12px] font-semibold text-muted-foreground">Short Bio</label>
                                                    <textarea wire:model="speakers.{{ $idx }}.bio" rows="1" placeholder="Brief description..." class="{{ $inputClass }}"></textarea>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="px-5 py-3 border-t border-border bg-surface flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div class="flex items-center gap-1.5 text-muted-foreground text-[12px] self-start sm:self-auto">
                            <x-icon name="info" class="h-3 w-3 text-primary shrink-0" />
                            <span>Appears on public site immediately.</span>
                        </div>

                        <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
                            <button type="button" wire:click="closeModal" class="w-1/2 sm:w-auto rounded-xl border border-border bg-white px-3 py-1.5 text-[12px] font-semibold text-foreground hover:bg-slate-50 transition-all cursor-pointer shadow-sm">
                                Cancel
                            </button>
                            <button type="submit" wire:loading.attr="disabled" wire:target="save" class="w-1/2 sm:w-auto inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-3.5 py-1.5 text-[12px] font-semibold text-white transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60 cursor-pointer shadow-sm">
                                <span wire:loading.remove wire:target="save">
                                    {{ $editingEventId ? 'Update Event' : 'Create Event' }} <x-icon name="check-circle-2" class="h-3 w-3 inline" />
                                </span>
                                <span wire:loading wire:target="save">{{ $editingEventId ? 'Updating...' : 'Creating...' }}</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <x-admin.confirm-modal
        :show="$deletingId !== null"
        title="Delete Event Profile"
        :message="'Are you sure you want to delete event &quot;' . $deletingTitle . '&quot;? All associated data will be permanently removed.'"
        confirm-label="Delete Event"
        variant="danger"
        confirm-action="confirmDelete"
        cancel-action="cancelDelete"
    />
</div>
