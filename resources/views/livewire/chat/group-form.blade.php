<div class="space-y-4 w-full max-w-7xl">
    {{-- Top Header --}}
    <div class="flex items-center gap-3 border-b border-slate-200/80 pb-3">
        <a
            href="{{ route('chat.index') }}"
            class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 hover:text-primary hover:border-primary/40 hover:bg-slate-50 transition-all cursor-pointer shadow-2xs shrink-0"
        >
            <x-icon name="arrow-left" class="h-4 w-4" />
        </a>
        <div class="min-w-0">
            <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 leading-tight">
                {{ __('site.chat.new_group') }}
            </h1>
            <p class="text-xs text-slate-500 font-medium truncate">
                {{ __('site.chat.create_group_desc') }}
            </p>
        </div>
    </div>

    {{-- Single Unified Card --}}
    <form wire:submit="save" class="bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-6 shadow-xs space-y-5">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 lg:gap-8 items-start">
            {{-- Left Side: Group Details & Settings (7 cols) --}}
            <div class="lg:col-span-7 space-y-4 lg:border-r lg:border-slate-100 lg:pr-8">
                <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                    <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-50 text-primary">
                        <x-icon name="info" class="h-3.5 w-3.5" />
                    </div>
                    <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                        {{ __('site.chat.group_details') }}
                    </h2>
                </div>

                {{-- Group Photo Uploader --}}
                <div class="space-y-1">
                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wide">
                        {{ __('site.chat.group_photo') }}
                    </label>
                    <div class="flex items-center gap-3.5 p-2.5 rounded-xl border border-slate-100 bg-slate-50/60">
                        @if ($avatarFile)
                            <div class="relative h-14 w-14 rounded-xl overflow-hidden border border-primary/40 bg-slate-900 group shrink-0 shadow-2xs">
                                <img src="{{ $avatarFile->temporaryUrl() }}" alt="Preview" class="h-full w-full object-cover" />
                                <button
                                    type="button"
                                    wire:click="removeAvatar"
                                    class="absolute inset-0 bg-slate-950/65 backdrop-blur-xs flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-white text-[10px] font-bold cursor-pointer gap-0.5"
                                >
                                    <x-icon name="trash-2" class="h-3 w-3 text-rose-400" />
                                    <span>{{ __('site.chat.remove') }}</span>
                                </button>
                            </div>
                        @else
                            <label class="flex h-14 w-14 shrink-0 cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-300 bg-white hover:bg-blue-50/50 hover:border-primary/50 transition-all group shadow-2xs">
                                <x-icon name="camera" class="h-4 w-4 text-slate-400 group-hover:text-primary transition-colors" />
                                <span class="text-[9px] font-bold text-slate-400 group-hover:text-primary mt-0.5">Photo</span>
                                <input type="file" wire:model="avatarFile" accept="image/*" class="hidden" />
                            </label>
                        @endif
                        <div class="text-xs text-slate-500 min-w-0">
                            <p class="font-bold text-slate-800 text-xs">{{ __('site.chat.group_photo') }}</p>
                            <p class="text-[10px] text-slate-400 leading-tight mt-0.5">{{ __('site.chat.photo_hint') }}</p>
                        </div>
                    </div>
                    @error('avatarFile') <p class="text-[10px] text-rose-600 font-bold mt-0.5">{{ $message }}</p> @enderror
                </div>

                {{-- Group Name Input --}}
                <div class="space-y-1">
                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wide">
                        {{ __('site.chat.group_name') }} <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <x-icon name="users" class="absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400" />
                        <input
                            type="text"
                            wire:model="title"
                            placeholder="{{ __('site.chat.group_name_placeholder') }}"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-2 pl-9 pr-3 text-xs font-semibold text-slate-900 outline-none transition-all placeholder:text-slate-400 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 shadow-2xs"
                        />
                    </div>
                    @error('title') <p class="text-[10px] text-rose-600 font-bold mt-0.5">{{ $message }}</p> @enderror
                </div>

                {{-- Description Textarea --}}
                <div class="space-y-1">
                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wide">
                        {{ __('site.chat.description') }}
                    </label>
                    <textarea
                        wire:model="description"
                        rows="2"
                        placeholder="{{ __('site.chat.description_placeholder') }}"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50/50 p-2.5 text-xs font-semibold text-slate-900 outline-none transition-all placeholder:text-slate-400 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 shadow-2xs"
                    ></textarea>
                    @error('description') <p class="text-[10px] text-rose-600 font-bold mt-0.5">{{ $message }}</p> @enderror
                </div>

                {{-- Who Can Join Selector --}}
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wide">
                        {{ __('site.chat.who_can_join') }}
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        {{-- Direct Join Option --}}
                        <label
                            wire:click="$set('joinSetting', 'direct_join')"
                            class="flex items-start gap-2.5 rounded-xl border p-2.5 cursor-pointer transition-all {{ $joinSetting === 'direct_join' ? 'border-primary bg-blue-50/60 ring-2 ring-primary/20 shadow-2xs' : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/50' }}"
                        >
                            <input type="radio" wire:model.live="joinSetting" value="direct_join" class="sr-only" />
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg {{ $joinSetting === 'direct_join' ? 'bg-primary text-white shadow-xs' : 'bg-slate-100 text-slate-600' }}">
                                <x-icon name="globe" class="h-3.5 w-3.5" />
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-900 leading-tight">{{ __('site.chat.direct_join') }}</p>
                                <p class="text-[10px] text-slate-500 font-medium mt-0.5 leading-tight">{{ __('site.chat.direct_join_desc') }}</p>
                            </div>
                        </label>

                        {{-- Approval Required Option --}}
                        <label
                            wire:click="$set('joinSetting', 'approval_required')"
                            class="flex items-start gap-2.5 rounded-xl border p-2.5 cursor-pointer transition-all {{ $joinSetting === 'approval_required' ? 'border-primary bg-blue-50/60 ring-2 ring-primary/20 shadow-2xs' : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/50' }}"
                        >
                            <input type="radio" wire:model.live="joinSetting" value="approval_required" class="sr-only" />
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg {{ $joinSetting === 'approval_required' ? 'bg-primary text-white shadow-xs' : 'bg-slate-100 text-slate-600' }}">
                                <x-icon name="shield-check" class="h-3.5 w-3.5" />
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-900 leading-tight">{{ __('site.chat.approval_required') }}</p>
                                <p class="text-[10px] text-slate-500 font-medium mt-0.5 leading-tight">{{ __('site.chat.approval_required_desc') }}</p>
                            </div>
                        </label>
                    </div>
                    @error('joinSetting') <p class="text-[10px] text-rose-600 font-bold mt-0.5">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Right Side: Members Selection (5 cols) --}}
            <div class="lg:col-span-5 space-y-3">
                <div class="flex items-center justify-between gap-2 pb-2 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-50 text-primary">
                            <x-icon name="user-plus" class="h-3.5 w-3.5" />
                        </div>
                        <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                            {{ __('site.chat.add_members') }}
                        </h2>
                    </div>
                    @if ($selectedMembers->isNotEmpty())
                        <span class="rounded-full bg-blue-100 px-2 py-0.5 text-[11px] font-bold text-primary">
                            {{ $selectedMembers->count() }}
                        </span>
                    @endif
                </div>

                <div class="relative">
                    <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400" />
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="memberSearch"
                        placeholder="{{ __('site.chat.search_members') }}"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-2 pl-8 pr-3 text-xs font-semibold text-slate-900 outline-none transition-all placeholder:text-slate-400 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 shadow-2xs"
                    />
                </div>

                {{-- Search Results Dropdown List --}}
                @if (trim($memberSearch) !== '')
                    <div class="rounded-xl border border-slate-200 divide-y divide-slate-100 max-h-[160px] overflow-y-auto bg-white shadow-md">
                        @forelse ($memberResults as $result)
                            <button
                                type="button"
                                wire:click="addMember({{ $result->id }})"
                                class="w-full flex items-center justify-between gap-2.5 p-2 hover:bg-slate-50 transition-colors cursor-pointer text-left"
                            >
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-primary font-black text-[11px] border border-blue-100">
                                        {{ mb_substr($result->name, 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-slate-900 truncate">{{ $result->name }}</p>
                                        @if ($result->phone)
                                            <p class="text-[10px] text-slate-400 font-medium truncate">{{ $result->phone }}</p>
                                        @endif
                                    </div>
                                </div>
                                <span class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-md bg-blue-50 text-primary hover:bg-primary hover:text-white transition-colors">
                                    <x-icon name="plus" class="h-3 w-3" />
                                </span>
                            </button>
                        @empty
                            <p class="p-3 text-xs text-slate-400 italic text-center">{{ __('site.chat.no_members_found') }}</p>
                        @endforelse
                    </div>
                @endif

                {{-- Selected Members Container --}}
                <div class="space-y-1.5 pt-0.5">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        {{ $selectedMembers->isNotEmpty() ? 'Selected (' . $selectedMembers->count() . ')' : 'No members added yet' }}
                    </p>
                    @if ($selectedMembers->isNotEmpty())
                        <div class="flex flex-wrap gap-1.5 max-h-[120px] overflow-y-auto">
                            @foreach ($selectedMembers as $member)
                                <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 border border-blue-200/80 px-2.5 py-0.5 text-[11px] font-bold text-primary shadow-2xs">
                                    <span>{{ $member->name }}</span>
                                    <button
                                        type="button"
                                        wire:click="removeMember({{ $member->id }})"
                                        class="rounded-full p-0.5 hover:bg-blue-200/70 transition-colors cursor-pointer"
                                        title="{{ __('site.chat.remove') }}"
                                    >
                                        <x-icon name="x" class="h-2.5 w-2.5" />
                                    </button>
                                </span>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-xl border border-dashed border-slate-200 p-2.5 text-center text-[11px] text-slate-400 font-medium bg-slate-50/40">
                            {{ __('site.chat.search_members') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Single Bottom Action Bar inside Card --}}
        <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
            <a
                href="{{ route('chat.index') }}"
                class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white py-2 px-4 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-all cursor-pointer shadow-2xs"
            >
                {{ __('site.chat.cancel') }}
            </a>
            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="save"
                class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary py-2 px-5 text-xs font-bold text-white transition-all hover:bg-primary-dark active:scale-[0.98] disabled:opacity-60 cursor-pointer shadow-sm shadow-primary/20"
            >
                <span wire:loading.remove wire:target="save">{{ __('site.chat.create_group_btn') }}</span>
                <span wire:loading wire:target="save" class="inline-flex items-center gap-1.5">
                    <x-icon name="loader-2" class="h-3.5 w-3.5 animate-spin" />
                    <span>{{ __('site.chat.creating_group') }}</span>
                </span>
            </button>
        </div>
    </form>
</div>
