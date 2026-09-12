<div class="space-y-4 font-outfit w-full">
    {{-- Top Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 leading-tight">Chat Moderation</h1>
        </div>
        <div class="text-xs font-bold text-slate-700 bg-white rounded-xl px-3 py-1.5 border border-slate-200 shadow-2xs shrink-0 self-start sm:self-auto">
            Total: <span class="text-primary font-black">{{ $totalCount }}</span>
            · Direct: <span class="font-black">{{ $directCount }}</span>
            · Groups: <span class="font-black">{{ $groupCount }}</span>
        </div>
    </div>

    {{-- Search & Type Filter Toolbar --}}
    <div class="flex flex-col sm:flex-row items-center gap-3 bg-white p-2.5 sm:p-3 rounded-xl border border-slate-200/90 shadow-2xs">
        <div class="relative flex-1 w-full">
            <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400" />
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search by group name or participant..."
                class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-1.5 pl-9 pr-4 text-xs font-semibold text-slate-900 outline-none transition-all placeholder:text-slate-400 focus:bg-white focus:border-primary shadow-2xs"
            />
        </div>
        <div class="flex items-center gap-1.5 shrink-0">
            @foreach (['all' => 'All', 'direct' => 'Direct', 'group' => 'Groups'] as $key => $label)
                <button
                    type="button"
                    wire:click="setTypeFilter('{{ $key }}')"
                    class="rounded-xl px-3 py-1.5 text-[12px] font-bold transition-all cursor-pointer {{ $typeFilter === $key ? 'bg-primary text-white' : 'bg-slate-100 text-muted-foreground hover:bg-slate-200' }}"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    <div class="glass-card overflow-hidden p-0">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface border-b border-border">
                    <th class="px-4 py-2.5 text-xs font-bold text-muted">Conversation</th>
                    <th class="px-4 py-2.5 text-xs font-bold text-muted">Type</th>
                    <th class="px-4 py-2.5 text-xs font-bold text-muted">Participants</th>
                    <th class="px-4 py-2.5 text-xs font-bold text-muted">Messages</th>
                    <th class="px-4 py-2.5 text-xs font-bold text-muted">Status</th>
                    <th class="px-4 py-2.5 text-xs font-bold text-muted text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($conversations as $conv)
                    <tr class="transition-colors hover:bg-surface/50">
                        <td class="px-4 py-2.5">
                            <p class="text-xs font-bold text-foreground">{{ $conv->display_title ?: 'Untitled' }}</p>
                        </td>
                        <td class="px-4 py-2.5">
                            <span class="inline-block rounded-full bg-primary-soft px-2 py-0.5 text-[12px] font-bold text-primary capitalize">{{ $conv->type }}</span>
                        </td>
                        <td class="px-4 py-2.5 text-xs font-semibold text-foreground">{{ $conv->active_participant_count }}</td>
                        <td class="px-4 py-2.5 text-xs font-semibold text-foreground">{{ $conv->messages_count }}</td>
                        <td class="px-4 py-2.5">
                            @if ($conv->is_archived)
                                <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-[12px] font-bold text-slate-500">Archived</span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[12px] font-bold text-emerald-600 border border-emerald-100">Active</span>
                            @endif
                        </td>
                        <td class="px-4 py-2.5 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a
                                    href="{{ route('admin.chat.show', $conv->id) }}"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-primary-soft text-primary hover:bg-primary hover:text-white transition-all cursor-pointer shadow-xs active:scale-95"
                                    title="View Conversation"
                                >
                                    <x-icon name="eye" class="h-3.5 w-3.5" />
                                </a>
                                <button
                                    wire:click="toggleArchive({{ $conv->id }})"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-amber-50 border border-amber-200/60 text-amber-700 hover:bg-amber-500 hover:text-white transition-all cursor-pointer shadow-xs active:scale-95"
                                    title="{{ $conv->is_archived ? 'Unarchive' : 'Archive' }}"
                                >
                                    <x-icon name="{{ $conv->is_archived ? 'refresh-cw' : 'archive' }}" class="h-3.5 w-3.5" />
                                </button>
                                <button
                                    wire:click="openDelete({{ $conv->id }}, {{ Illuminate\Support\Js::from($conv->display_title ?: 'this conversation') }})"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-rose-50 border border-rose-200/60 text-rose-600 hover:bg-rose-600 hover:text-white transition-all cursor-pointer shadow-xs active:scale-95"
                                    title="Delete"
                                >
                                    <x-icon name="trash-2" class="h-3.5 w-3.5" />
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-10 text-center text-xs text-muted font-medium">
                            No conversations found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <x-pagination :paginator="$conversations" item-label="conversations" />

    <x-admin.confirm-modal
        :show="$deletingId !== null"
        title="Delete Conversation"
        :message="'Are you sure you want to permanently delete &quot;' . $deletingTitle . '&quot;? All messages will be removed for everyone.'"
        confirm-label="Delete Conversation"
        variant="danger"
        confirm-action="confirmDelete"
        cancel-action="cancelDelete"
    />
</div>
