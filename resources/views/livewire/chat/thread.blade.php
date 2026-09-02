<div
    class="glass-card flex-1 flex flex-col h-full min-h-0 overflow-hidden"
    x-data="chatThread({
        conversationId: {{ $conversationId }},
        currentUserId: {{ auth()->id() }},
        initialMessages: @js($initialMessages),
    })"
    wire:poll.5s="pollSync"
>
    {{-- Header --}}
    <div class="flex items-center gap-2.5 p-3 border-b border-border shrink-0">
        <a href="{{ route('chat.index') }}" class="lg:hidden inline-flex h-8 w-8 items-center justify-center rounded-xl text-muted-foreground hover:bg-surface transition-colors">
            <x-icon name="arrow-left" class="h-4 w-4" />
        </a>
        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-soft text-primary font-bold text-xs overflow-hidden">
            @if ($otherUser && media_url($otherUser->avatar))
                <img src="{{ media_url($otherUser->avatar) }}" alt="" class="h-full w-full object-cover" />
            @else
                {{ mb_substr($otherUser?->name ?? '?', 0, 1) }}
            @endif
        </div>
        <p class="text-sm font-bold text-foreground">{{ $otherUser?->name ?? __('site.chat.unknown_member') }}</p>
    </div>

    @if ($errorMessage)
        <div class="mx-3 mt-2 rounded-xl bg-red-50 border border-red-100 p-2 text-[11px] font-semibold text-red-600 shrink-0">{{ $errorMessage }}</div>
    @endif

    {{-- Messages --}}
    <div x-ref="messageList" class="flex-1 min-h-0 overflow-y-auto p-3 space-y-2.5">
        <template x-if="messages.length === 0">
            <p class="text-center text-xs text-muted-foreground italic py-8">{{ __('site.chat.no_messages_yet') }}</p>
        </template>

        <template x-for="msg in messages" :key="msg.id">
            <div class="flex" :class="msg.is_mine ? 'justify-end' : 'justify-start'">
                <div class="max-w-[75%] group">
                    <div
                        class="rounded-2xl px-3.5 py-2 text-xs transition-all"
                        :class="msg.is_mine ? 'bg-primary text-white rounded-br-sm' : 'bg-surface text-foreground rounded-bl-sm'"
                        :style="msg.is_pending ? 'opacity: 0.9;' : ''"
                    >
                        <div>
                            <template x-if="editingId === msg.id">
                                <div class="space-y-1.5 min-w-[180px]">
                                    <textarea x-model="editText" rows="2" class="w-full rounded-lg border border-white/30 bg-white/10 px-2 py-1 text-xs outline-none" :class="msg.is_mine ? 'text-white placeholder-white/60' : 'text-foreground'"></textarea>
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button" x-on:click="cancelEdit()" class="text-[11px] font-semibold opacity-80 hover:opacity-100 cursor-pointer">{{ __('site.chat.cancel') }}</button>
                                        <button type="button" x-on:click="saveEdit()" class="text-[11px] font-bold underline cursor-pointer">{{ __('site.chat.save') }}</button>
                                    </div>
                                </div>
                            </template>
                            <template x-if="editingId !== msg.id">
                                <div>
                                    <template x-if="msg.is_deleted">
                                        <p class="italic opacity-70">{{ __('site.chat.message_deleted') }}</p>
                                    </template>
                                    <template x-if="!msg.is_deleted">
                                        <p class="whitespace-pre-wrap break-words" x-html="msg.body_html || msg.body"></p>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 mt-0.5 px-1" :class="msg.is_mine ? 'justify-end' : 'justify-start'">
                        <template x-if="msg.is_pending">
                            <span class="text-[10px] text-slate-500 font-semibold flex items-center gap-1">
                                <x-icon name="clock" class="h-2.5 w-2.5 animate-pulse text-amber-500" /> {{ __('site.chat.sending') }}
                            </span>
                        </template>
                        <template x-if="!msg.is_pending">
                            <div class="flex items-center gap-1.5">
                                <span class="text-[10px] text-muted-foreground" x-text="msg.created_at_human"></span>
                                <span class="text-[10px] text-muted-foreground italic" x-show="msg.is_edited && !msg.is_deleted">{{ __('site.chat.edited') }}</span>
                                <template x-if="!msg.is_deleted && editingId !== msg.id">
                                    <span class="hidden group-hover:inline-flex items-center gap-1">
                                        <button type="button" x-show="msg.editable" x-on:click="startEdit(msg)" class="text-[10px] font-semibold text-muted-foreground hover:text-foreground cursor-pointer">{{ __('site.chat.edit') }}</button>
                                        <button type="button" x-show="msg.deletable" x-on:click="deleteMsg(msg.id)" class="text-[10px] font-semibold text-rose-500 hover:text-rose-700 cursor-pointer">{{ __('site.chat.delete') }}</button>
                                    </span>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- Composer --}}
    <form x-on:submit.prevent="sendMessage()" class="flex items-end gap-2 p-3 border-t border-border shrink-0">
        <textarea
            x-model="messageInput"
            rows="1"
            placeholder="{{ __('site.chat.type_message') }}"
            x-on:keydown.enter.prevent="if (!$event.shiftKey) sendMessage()"
            class="flex-1 rounded-xl border border-border bg-slate-50/50 px-3.5 py-2.5 text-xs font-medium text-slate-900 outline-none transition-all focus:bg-white focus:border-primary resize-none"
        ></textarea>
        <button
            type="submit"
            :disabled="!messageInput.trim() || isSending"
            class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-primary text-white shadow-sm hover:opacity-90 active:scale-95 transition-all cursor-pointer shrink-0 disabled:opacity-50 disabled:cursor-not-allowed"
            title="{{ __('site.chat.send') }}"
        >
            <template x-if="!isSending">
                <x-icon name="send" class="h-4 w-4" />
            </template>
            <template x-if="isSending">
                <x-icon name="loader-2" class="h-4 w-4 animate-spin" />
            </template>
        </button>
    </form>

    {{-- Custom Delete Confirmation Pop-up Modal --}}
    <template x-teleport="body">
        <div
            x-show="showDeleteModal"
            x-cloak
            x-on:keydown.escape.window="cancelDelete()"
            class="fixed inset-0 z-[99999] overflow-y-auto p-4 flex min-h-full items-center justify-center font-outfit"
            role="dialog"
            aria-modal="true"
        >
            {{-- Backdrop --}}
            <div
                x-show="showDeleteModal"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm"
                x-on:click="cancelDelete()"
            ></div>

            {{-- Modal Content Box --}}
            <div
                x-show="showDeleteModal"
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
                        <h2 class="text-base font-bold text-slate-900 leading-tight">{{ __('site.chat.delete_title') }}</h2>
                        <p class="mt-1 text-xs text-slate-500 font-medium leading-relaxed">
                            {{ __('site.chat.delete_desc') }}
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
                        {{ __('site.chat.delete_confirm') }}
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
