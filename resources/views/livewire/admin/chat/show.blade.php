<div class="space-y-3 w-full">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.chat.index') }}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-border bg-white text-muted-foreground hover:text-foreground hover:bg-slate-50 transition-all cursor-pointer shadow-xs shrink-0">
                <x-icon name="arrow-left" class="h-4 w-4" />
            </a>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-foreground">
                    {{ $conversation->type === 'group' ? $conversation->title : 'Direct Conversation' }}
                </h1>
                <p class="text-xs text-muted capitalize">{{ $conversation->type }} · {{ $messages->count() }} messages</p>
            </div>
        </div>
        <button
            wire:click="toggleArchive"
            class="inline-flex items-center gap-1.5 rounded-xl border border-amber-200/60 bg-amber-50 px-3.5 py-2 text-xs font-bold text-amber-700 hover:bg-amber-500 hover:text-white transition-all cursor-pointer shrink-0"
        >
            <x-icon name="{{ $conversation->is_archived ? 'refresh-cw' : 'archive' }}" class="h-3.5 w-3.5" />
            {{ $conversation->is_archived ? 'Unarchive' : 'Archive' }}
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
        {{-- Participants --}}
        <div class="glass-card p-4 space-y-2.5">
            <h2 class="text-xs font-bold text-muted uppercase tracking-wide">Participants ({{ $conversation->participants->where('status', 'active')->count() }})</h2>
            <div class="space-y-1.5">
                @foreach ($conversation->participants->where('status', 'active') as $p)
                    <div class="flex items-center gap-2 text-xs">
                        <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-primary-soft text-primary font-bold text-[10px]">{{ mb_substr($p->user?->name ?? '?', 0, 1) }}</div>
                        <span class="font-semibold text-foreground truncate">{{ $p->user?->name }}</span>
                        @if ($p->role !== 'member')
                            <span class="text-[10px] font-bold text-amber-600 uppercase shrink-0">{{ $p->role === 'main_admin' ? 'Main Admin' : 'Admin' }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Messages (read-only moderation view) --}}
        <div class="lg:col-span-2 glass-card p-4 space-y-2.5 max-h-[70vh] overflow-y-auto">
            <h2 class="text-xs font-bold text-muted uppercase tracking-wide">Messages</h2>
            @forelse ($messages as $m)
                @if ($m->message_type === 'system_event')
                    <p class="text-center text-[11px] text-muted-foreground italic py-1">{{ $m->body }}</p>
                @else
                    <div class="flex items-start gap-2.5 p-2 rounded-xl {{ $m->is_deleted ? 'opacity-50' : 'hover:bg-surface' }} transition-colors group">
                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-primary-soft text-primary font-bold text-[11px]">{{ mb_substr($m->sender?->name ?? '?', 0, 1) }}</div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1.5">
                                <span class="text-[11px] font-bold text-foreground">{{ $m->sender?->name }}</span>
                                <span class="text-[10px] text-muted-foreground">{{ $m->created_at->format('M j, g:i A') }}</span>
                                @if ($m->is_edited && ! $m->is_deleted) <span class="text-[10px] text-muted-foreground italic">(edited)</span> @endif
                            </div>
                            @if ($m->is_deleted)
                                <p class="text-xs italic text-muted-foreground">This message was deleted</p>
                            @else
                                <p class="text-xs text-foreground whitespace-pre-wrap break-words">{{ $m->body }}</p>
                            @endif
                        </div>
                        @if (! $m->is_deleted)
                            <button
                                type="button"
                                x-on:click="if (confirm('Delete this message for moderation?')) { $wire.deleteMessage({{ $m->id }}) }"
                                class="hidden group-hover:inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-lg text-rose-500 hover:bg-rose-50 transition-colors cursor-pointer"
                                title="Delete message"
                            >
                                <x-icon name="trash-2" class="h-3 w-3" />
                            </button>
                        @endif
                    </div>
                @endif
            @empty
                <p class="text-center text-xs text-muted-foreground italic py-8">No messages in this conversation.</p>
            @endforelse
        </div>
    </div>
</div>
