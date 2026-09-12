<div
    x-data="{
        showDeleteConvModal: false,
        convToDelete: null,
        confirmDelete(id) {
            this.convToDelete = id;
            this.showDeleteConvModal = true;
        },
        cancelDelete() {
            this.showDeleteConvModal = false;
            this.convToDelete = null;
        },
        proceedDelete() {
            if (this.convToDelete) {
                $wire.deleteConversation(this.convToDelete);
            }
            this.cancelDelete();
        }
    }"
    class="bg-background font-outfit h-[calc(100dvh-4rem)] overflow-hidden"
>
    <div class="mx-auto max-w-6xl h-full py-2.5 sm:py-3 px-2 min-h-0">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 h-full min-h-0">
            {{-- ===== Conversation List ===== --}}
            <aside class="lg:col-span-4 glass-card p-0 overflow-hidden flex flex-col h-full min-h-0 {{ $activeId ? 'hidden lg:flex' : 'flex' }}">
                <div class="p-3 border-b border-border space-y-2 shrink-0">
                    <div class="flex items-center justify-between">
                        <h1 class="text-sm font-bold text-foreground">{{ __('site.chat.title') }}</h1>
                        <a
                            href="{{ route('chat.groups.create') }}"
                            class="inline-flex items-center gap-1 rounded-lg bg-primary-soft px-2.5 py-1 text-[11px] font-bold text-primary hover:opacity-90 transition-all cursor-pointer"
                        >
                            <x-icon name="plus" class="h-3 w-3" />
                            <span>{{ __('site.chat.new_group') }}</span>
                        </a>
                    </div>
                    <div class="relative">
                        <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-muted-foreground" />
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="{{ __('site.chat.search_placeholder') }}"
                            class="w-full rounded-xl border border-border bg-slate-50/50 py-2 pl-9 pr-3 text-xs font-medium text-slate-900 outline-none transition-all focus:bg-white focus:border-primary"
                        />
                    </div>
                </div>

                <div class="flex-1 min-h-0 overflow-y-auto">
                    @if (trim($search) !== '')
                        <div class="p-2 space-y-3">
                            {{-- Groups Search Results --}}
                            @if ($groupResults->isNotEmpty())
                                <div class="space-y-1">
                                    <p class="px-2 py-1 text-[11px] font-bold text-muted-foreground uppercase tracking-wide">{{ __('site.chat.groups') }}</p>
                                    @foreach ($groupResults as $group)
                                        <div class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-surface transition-colors">
                                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-soft text-primary font-bold text-xs overflow-hidden">
                                                @if (media_url($group->avatar))
                                                    <img src="{{ media_url($group->avatar) }}" alt="{{ $group->title }}" class="h-full w-full object-cover" />
                                                @else
                                                    <x-icon name="users" class="h-4 w-4" />
                                                @endif
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-xs font-bold text-foreground truncate">{{ $group->title }}</p>
                                            </div>
                                            <button
                                                type="button"
                                                wire:click="joinGroup({{ $group->id }})"
                                                class="shrink-0 rounded-lg bg-primary px-2.5 py-1 text-[11px] font-bold text-white hover:opacity-90 transition-all cursor-pointer"
                                            >
                                                {{ $group->join_setting === 'approval_required' ? __('site.chat.request_join') : __('site.chat.join') }}
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Members Search Results --}}
                            <div class="space-y-1">
                                <p class="px-2 py-1 text-[11px] font-bold text-muted-foreground uppercase tracking-wide">{{ __('site.chat.members') }}</p>
                                @forelse ($searchResults as $result)
                                    <button
                                        type="button"
                                        wire:click="startChat({{ $result->id }})"
                                        class="w-full flex items-center gap-2.5 p-2 rounded-xl hover:bg-surface transition-colors cursor-pointer text-left"
                                    >
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-soft text-primary font-bold text-xs overflow-hidden">
                                            @if (media_url($result->avatar))
                                                <img src="{{ media_url($result->avatar) }}" alt="{{ $result->name }}" class="h-full w-full object-cover" />
                                            @else
                                                {{ mb_substr($result->name, 0, 1) }}
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-foreground truncate">{{ $result->name }}</p>
                                            <p class="text-[11px] text-muted-foreground truncate">{{ $result->phone }}</p>
                                        </div>
                                    </button>
                                @empty
                                    @if ($groupResults->isEmpty())
                                        <p class="px-2 py-4 text-xs text-muted-foreground italic text-center">{{ __('site.chat.no_members_found') }}</p>
                                    @endif
                                @endforelse
                            </div>
                        </div>
                    @else
                        @if ($conversations->isEmpty())
                            <div class="p-6 text-center text-xs text-muted-foreground italic">
                                {{ __('site.chat.no_conversations') }}
                            </div>
                        @else
                            <div class="divide-y divide-border/60">
                                @foreach ($conversations as $conv)
                                    <div class="relative group/item">
                                        <a
                                            href="{{ route('chat.show', $conv['id']) }}"
                                            class="flex items-center gap-2.5 p-3 pr-8 hover:bg-surface transition-colors {{ $activeId === $conv['id'] ? 'bg-primary-soft' : '' }}"
                                        >
                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-soft text-primary font-bold text-sm overflow-hidden border border-slate-200/60 shadow-2xs">
                                                @if (media_url($conv['avatar']))
                                                    <img src="{{ media_url($conv['avatar']) }}" alt="" class="h-full w-full object-cover" />
                                                @elseif ($conv['type'] === 'group')
                                                    <x-icon name="users" class="h-4 w-4" />
                                                @else
                                                    {{ mb_substr($conv['title'] ?? '?', 0, 1) }}
                                                @endif
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center justify-between gap-2">
                                                    <p class="text-xs font-bold text-foreground truncate">{{ $conv['title'] ?? __('site.chat.unknown_member') }}</p>
                                                    @if ($conv['last_message'])
                                                        <span class="text-[10px] text-muted-foreground shrink-0">{{ $conv['last_message']->created_at->format('M j') }}</span>
                                                    @endif
                                                </div>
                                                <div class="flex items-center justify-between gap-2 mt-0.5">
                                                    <p class="text-[11px] text-muted-foreground truncate">
                                                        @if ($conv['last_message'])
                                                            @if ($conv['last_message']->is_deleted)
                                                                {{ __('site.chat.message_deleted') }}
                                                            @elseif ($conv['last_message']->message_type === 'system_event')
                                                                <span class="italic">{{ Str::limit($conv['last_message']->body, 40) }}</span>
                                                            @else
                                                                {{ $conv['type'] === 'group' && $conv['last_message']->sender ? $conv['last_message']->sender->name . ': ' : '' }}{{ Str::limit(strip_tags($conv['last_message']->body), 40) }}
                                                            @endif
                                                        @else
                                                            {{ __('site.chat.say_hello') }}
                                                        @endif
                                                    </p>
                                                    @if ($conv['unread'] > 0)
                                                        <span class="inline-flex h-4 min-w-4 items-center justify-center rounded-full bg-primary px-1 text-[10px] font-bold text-white shrink-0">{{ $conv['unread'] }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </a>
                                        <button
                                            type="button"
                                            x-on:click.stop="confirmDelete({{ $conv['id'] }})"
                                            class="absolute right-2 top-3 hidden group-hover/item:flex h-6 w-6 items-center justify-center rounded-lg bg-white/95 border border-slate-200 text-slate-400 hover:text-rose-600 hover:border-rose-200 hover:bg-rose-50 shadow-2xs transition-all cursor-pointer"
                                            title="{{ __('site.chat.delete_chat') }}"
                                        >
                                            <x-icon name="trash-2" class="h-3 w-3" />
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endif
                </div>
            </aside>

            {{-- ===== Active Thread ===== --}}
            <div class="lg:col-span-8 h-full min-h-0 {{ $activeId ? 'flex' : 'hidden lg:flex' }} flex-col overflow-hidden">
                @if ($activeId && $conversations->contains('id', (int) $activeId))
                    @livewire('chat.thread', ['id' => (int) $activeId], 'thread-' . $activeId)
                @else
                    <div class="glass-card flex-1 flex flex-col items-center justify-center text-center p-8">
                        <x-icon name="message-square" class="h-10 w-10 text-muted-foreground mb-3" />
                        <p class="text-sm font-semibold text-foreground">{{ __('site.chat.select_conversation') }}</p>
                        <p class="text-xs text-muted-foreground mt-1">{{ __('site.chat.select_conversation_desc') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Custom Delete Conversation Confirmation Modal --}}
    <template x-teleport="body">
        <div
            x-show="showDeleteConvModal"
            x-cloak
            x-on:keydown.escape.window="cancelDelete()"
            class="fixed inset-0 z-[99999] overflow-y-auto p-4 flex min-h-full items-center justify-center font-outfit"
            role="dialog"
            aria-modal="true"
        >
            <div
                x-show="showDeleteConvModal"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm"
                x-on:click="cancelDelete()"
            ></div>

            <div
                x-show="showDeleteConvModal"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                class="relative z-10 w-full max-w-sm rounded-3xl bg-white p-5 sm:p-6 shadow-2xl border border-slate-200 my-auto flex flex-col space-y-4"
            >
                <button
                    type="button"
                    x-on:click="cancelDelete()"
                    class="absolute top-4 right-4 flex h-7 w-7 items-center justify-center rounded-lg bg-slate-50 border border-slate-200 text-slate-400 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition-colors cursor-pointer shadow-2xs"
                    aria-label="{{ __('site.chat.cancel') }}"
                >
                    <x-icon name="x" class="h-3.5 w-3.5" />
                </button>

                <div class="flex flex-col items-center text-center gap-2.5 pt-1">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-50 text-rose-600 border border-rose-100 shadow-2xs">
                        <x-icon name="trash-2" class="h-5 w-5" />
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900 leading-tight">{{ __('site.chat.delete_chat') }}</h2>
                        <p class="mt-1 text-xs text-slate-500 font-medium leading-relaxed">
                            {{ __('site.chat.delete_chat_desc') }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2.5 pt-2 border-t border-slate-100">
                    <button
                        type="button"
                        x-on:click="cancelDelete()"
                        class="flex-1 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-100 active:scale-[0.98] transition-all cursor-pointer shadow-xs"
                    >
                        {{ __('site.chat.cancel') }}
                    </button>
                    <button
                        type="button"
                        x-on:click="proceedDelete()"
                        class="flex-1 rounded-xl px-4 py-2.5 text-xs font-bold active:scale-[0.98] transition-all cursor-pointer bg-gradient-to-r from-rose-600 to-red-700 hover:opacity-95 text-white shadow-md shadow-rose-600/20"
                    >
                        {{ __('site.chat.delete_chat_confirm') }}
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
