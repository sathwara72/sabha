<div
    class="glass-card flex-1 flex flex-col h-full overflow-hidden"
    x-data="chatThread({
        conversationId: {{ $conversationId }},
        currentUserId: {{ auth()->id() }},
        isGroup: {{ $isGroup ? 'true' : 'false' }},
        isGroupAdmin: {{ $isGroupAdmin ? 'true' : 'false' }},
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
            @if ($isGroup)
                @if (media_url($conversation->avatar))
                    <img src="{{ media_url($conversation->avatar) }}" alt="" class="h-full w-full object-cover" />
                @else
                    <x-icon name="users" class="h-4 w-4" />
                @endif
            @elseif ($otherUser && media_url($otherUser->avatar))
                <img src="{{ media_url($otherUser->avatar) }}" alt="" class="h-full w-full object-cover" />
            @else
                {{ mb_substr($otherUser?->name ?? '?', 0, 1) }}
            @endif
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-bold text-foreground truncate">{{ $isGroup ? $conversation->title : ($otherUser?->name ?? 'Unknown member') }}</p>
            @if ($isGroup)
                <p class="text-[11px] text-muted-foreground">{{ $participantCount }} {{ $participantCount === 1 ? 'member' : 'members' }}</p>
            @endif
        </div>
        @if ($isGroup)
            <button
                type="button"
                x-on:click="if (confirm('Leave this group?')) { $wire.leaveGroup() }"
                class="inline-flex h-8 w-8 items-center justify-center rounded-xl text-muted-foreground hover:bg-red-50 hover:text-rose-600 transition-colors shrink-0 cursor-pointer"
                title="Leave group"
            >
                <x-icon name="log-out" class="h-4 w-4" />
            </button>
            <a href="{{ route('chat.groups.settings', $conversationId) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-xl text-muted-foreground hover:bg-surface transition-colors shrink-0" title="Group settings">
                <x-icon name="settings" class="h-4 w-4" />
            </a>
        @endif
    </div>

    @if ($errorMessage)
        <div class="mx-3 mt-2 rounded-xl bg-red-50 border border-red-100 p-2 text-[11px] font-semibold text-red-600">{{ $errorMessage }}</div>
    @endif

    {{-- Messages --}}
    <div x-ref="messageList" class="flex-1 overflow-y-auto p-3 space-y-2.5">
        <template x-if="messages.length === 0">
            <p class="text-center text-xs text-muted-foreground italic py-8">No messages yet. Say hello!</p>
        </template>

        <template x-for="msg in messages" :key="msg.id">
            <div>
                <template x-if="msg.message_type === 'system_event'">
                    <p class="text-center text-[11px] text-muted-foreground italic py-1" x-text="msg.body"></p>
                </template>

            <div x-show="msg.message_type !== 'system_event'" class="flex" :class="msg.is_mine ? 'justify-end' : 'justify-start'">
                <div class="max-w-[75%] group">
                    <p x-show="isGroup && !msg.is_mine" class="text-[10px] font-bold text-muted-foreground px-1 mb-0.5" x-text="msg.sender_name"></p>
                    <div
                        class="rounded-2xl px-3.5 py-2 text-xs"
                        :class="msg.is_mine ? 'bg-primary text-white rounded-br-sm' : 'bg-surface text-foreground rounded-bl-sm'"
                    >
                        <template x-if="editingId === msg.id">
                            <div class="space-y-1.5 min-w-[180px]">
                                <textarea x-model="editText" rows="2" class="w-full rounded-lg border border-white/30 bg-white/10 px-2 py-1 text-xs outline-none" :class="msg.is_mine ? 'text-white placeholder-white/60' : 'text-foreground'"></textarea>
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" x-on:click="cancelEdit()" class="text-[11px] font-semibold opacity-80 hover:opacity-100 cursor-pointer">Cancel</button>
                                    <button type="button" x-on:click="saveEdit()" class="text-[11px] font-bold underline cursor-pointer">Save</button>
                                </div>
                            </div>
                        </template>
                        <template x-if="editingId !== msg.id">
                            <div>
                                <template x-if="msg.is_deleted">
                                    <p class="italic opacity-70">This message was deleted</p>
                                </template>
                                <template x-if="!msg.is_deleted">
                                    <p class="whitespace-pre-wrap break-words" x-html="msg.body_html"></p>
                                </template>
                            </div>
                        </template>
                    </div>
                    <div class="flex items-center gap-1.5 mt-0.5 px-1" :class="msg.is_mine ? 'justify-end' : 'justify-start'">
                        <span class="text-[10px] text-muted-foreground" x-text="msg.created_at_human"></span>
                        <span class="text-[10px] text-muted-foreground italic" x-show="msg.is_edited && !msg.is_deleted">(edited)</span>
                        <template x-if="!msg.is_deleted && editingId !== msg.id">
                            <span class="hidden group-hover:inline-flex items-center gap-1">
                                <button type="button" x-show="msg.editable" x-on:click="startEdit(msg)" class="text-[10px] font-semibold text-muted-foreground hover:text-foreground cursor-pointer">Edit</button>
                                <button type="button" x-show="msg.deletable" x-on:click="deleteMsg(msg.id)" class="text-[10px] font-semibold text-rose-500 hover:text-rose-700 cursor-pointer">Delete</button>
                            </span>
                        </template>
                    </div>
                </div>
            </div>
            </div>
        </template>
    </div>

    {{-- Composer --}}
    <form wire:submit="send" class="flex items-end gap-2 p-3 border-t border-border shrink-0">
        <textarea
            wire:model="body"
            rows="1"
            placeholder="Type a message..."
            x-on:keydown.enter.prevent="$wire.send()"
            class="flex-1 rounded-xl border border-border bg-slate-50/50 px-3.5 py-2.5 text-xs font-medium text-slate-900 outline-none transition-all focus:bg-white focus:border-primary resize-none"
        ></textarea>
        <button type="submit" class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-primary text-white shadow-sm hover:opacity-90 active:scale-95 transition-all cursor-pointer shrink-0">
            <x-icon name="send" class="h-4 w-4" />
        </button>
    </form>
</div>
