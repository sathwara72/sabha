@php
    $inputClass = 'w-full rounded-xl border border-border bg-white px-3 py-2 text-xs text-foreground outline-none transition-colors placeholder:text-muted-foreground focus:border-primary';
    $labelClass = 'text-xs font-semibold text-foreground';
@endphp

<div class="space-y-3 w-full max-w-2xl">
    <div class="flex items-center gap-3">
        <a href="{{ route('chat.index') }}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-border bg-white text-muted-foreground hover:text-foreground hover:bg-slate-50 transition-all cursor-pointer shadow-xs">
            <x-icon name="arrow-left" class="h-4 w-4" />
        </a>
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-foreground">New Group</h1>
            <p class="text-xs text-muted">Create a community group chat</p>
        </div>
    </div>

    <form wire:submit="save" class="glass-card p-5 space-y-4">
        <div class="space-y-1">
            <label class="{{ $labelClass }}">Group Photo</label>
            <div class="flex items-center gap-4">
                @if ($avatarFile)
                    <div class="relative h-16 w-16 rounded-full overflow-hidden border border-border bg-slate-50 group shrink-0">
                        <img src="{{ $avatarFile->temporaryUrl() }}" alt="Preview" class="h-full w-full object-cover" />
                        <button type="button" wire:click="removeAvatar" class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-white text-[10px] font-semibold cursor-pointer">Remove</button>
                    </div>
                @else
                    <label class="flex h-16 w-16 shrink-0 cursor-pointer flex-col items-center justify-center rounded-full border border-dashed border-border bg-slate-50/50 hover:bg-slate-50 transition-colors">
                        <x-icon name="upload" class="h-4 w-4 text-muted-foreground" />
                        <input type="file" wire:model="avatarFile" accept="image/*" class="hidden" />
                    </label>
                @endif
                <p class="text-[12px] text-muted-foreground">Optional. PNG, JPG, WebP (max 5MB).</p>
            </div>
            @error('avatarFile') <p class="text-[12px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-1">
            <label class="{{ $labelClass }}">Group Name</label>
            <input type="text" wire:model="title" placeholder="e.g. Ahmedabad Founders Circle" class="{{ $inputClass }}" />
            @error('title') <p class="text-[12px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-1">
            <label class="{{ $labelClass }}">Description</label>
            <textarea wire:model="description" rows="2" placeholder="What is this group about?" class="{{ $inputClass }}"></textarea>
            @error('description') <p class="text-[12px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-1">
            <label class="{{ $labelClass }}">Who can join?</label>
            <select wire:model="joinSetting" class="{{ $inputClass }}">
                <option value="direct_join">Anyone can join directly</option>
                <option value="approval_required">Requires admin approval</option>
            </select>
        </div>

        <div class="space-y-1.5 border-t border-border pt-3">
            <label class="{{ $labelClass }}">Add Members (optional)</label>
            <div class="relative">
                <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-muted-foreground" />
                <input type="text" wire:model.live.debounce.300ms="memberSearch" placeholder="Search by name or phone..." class="{{ $inputClass }} pl-9" />
            </div>

            @if (trim($memberSearch) !== '')
                <div class="rounded-xl border border-border divide-y divide-border/60 max-h-[160px] overflow-y-auto">
                    @forelse ($memberResults as $result)
                        <button type="button" wire:click="addMember({{ $result->id }})" class="w-full flex items-center gap-2.5 p-2 hover:bg-surface transition-colors cursor-pointer text-left">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-primary-soft text-primary font-bold text-[11px]">{{ mb_substr($result->name, 0, 1) }}</div>
                            <span class="text-xs font-semibold text-foreground truncate">{{ $result->name }}</span>
                            <x-icon name="plus" class="h-3 w-3 text-primary ml-auto shrink-0" />
                        </button>
                    @empty
                        <p class="p-3 text-xs text-muted-foreground italic text-center">No members found.</p>
                    @endforelse
                </div>
            @endif

            @if ($selectedMembers->isNotEmpty())
                <div class="flex flex-wrap gap-1.5 pt-1">
                    @foreach ($selectedMembers as $member)
                        <span class="inline-flex items-center gap-1 rounded-full bg-primary-soft px-2.5 py-1 text-[11px] font-bold text-primary">
                            {{ $member->name }}
                            <button type="button" wire:click="removeMember({{ $member->id }})" class="hover:opacity-70 cursor-pointer">
                                <x-icon name="x" class="h-2.5 w-2.5" />
                            </button>
                        </span>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-border">
            <a href="{{ route('chat.index') }}" class="rounded-xl border border-border bg-white px-3.5 py-1.5 text-[12px] font-semibold text-foreground hover:bg-slate-50 transition-all cursor-pointer">Cancel</a>
            <button type="submit" wire:loading.attr="disabled" wire:target="save" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-1.5 text-[12px] font-semibold text-white transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60 cursor-pointer shadow-sm">
                <span wire:loading.remove wire:target="save">Create Group</span>
                <span wire:loading wire:target="save">Creating...</span>
            </button>
        </div>
    </form>
</div>
