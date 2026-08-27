<div class="space-y-3">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex flex-col">
            <h1 class="text-xl sm:text-2xl font-semibold tracking-tight text-foreground">Site Settings</h1>
            <p class="text-xs text-muted">Manage website contact details, social media links, and coordinator roster</p>
        </div>
        <button
            wire:click="loadData"
            class="inline-flex h-8 w-8 items-center justify-center rounded-xl border border-border bg-white text-muted hover:bg-surface hover:text-foreground cursor-pointer transition-colors self-start sm:self-auto"
            title="Refresh Data"
        >
            <x-icon name="refresh-cw" class="h-3.5 w-3.5" />
        </button>
    </div>

    @if ($successMsg)
        <div wire:key="settings-success-{{ md5($successMsg) }}" x-data="{ show: true }" x-init="setTimeout(() => { show = false; $wire.successMsg = '' }, 4000)" x-show="show" x-transition class="rounded-xl bg-emerald-50 border border-emerald-100 px-3 py-2 text-xs font-semibold text-emerald-800 flex items-center gap-2">
            <x-icon name="check-circle-2" class="h-4 w-4 text-emerald-600 shrink-0" />
            <span>{{ $successMsg }}</span>
        </div>
    @endif
    @if ($errorMsg)
        <div class="rounded-xl bg-red-50 border border-red-100 px-3 py-2 text-xs font-semibold text-red-800 flex items-center gap-2">
            <x-icon name="alert-circle" class="h-4 w-4 text-red-600 shrink-0" />
            <span>{{ $errorMsg }}</span>
        </div>
    @endif

    @php
        $inputClass = 'w-full rounded-lg border border-border bg-white px-3 py-1.5 text-xs text-foreground outline-none transition-colors focus:border-primary font-semibold';
        $labelClass = 'text-[12px] font-bold text-muted uppercase tracking-wider mb-0.5 block';
    @endphp

    <div class="space-y-3">
        {{-- General Contact --}}
        <div class="glass-card p-4 space-y-3">
            <h3 class="text-xs font-bold text-foreground border-b border-border pb-2 flex items-center gap-1.5">
                <x-icon name="settings" class="h-3.5 w-3.5 text-primary" /> General Contact Info
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="{{ $labelClass }}">Footer Contact Email</label>
                    <input type="email" wire:model="contactEmail" class="{{ $inputClass }}" placeholder="e.g. hello@sabha.global" />
                </div>
                <div>
                    <label class="{{ $labelClass }}">Footer Mobile / Phone Number</label>
                    <input type="text" maxlength="10" wire:model="contactPhone" class="{{ $inputClass }}" placeholder="10-digit mobile number" />
                </div>
                <div>
                    <label class="{{ $labelClass }}">Footer Contact Address</label>
                    <input type="text" wire:model="contactAddress" class="{{ $inputClass }}" placeholder="e.g. Ahmedabad, Gujarat, India" />
                </div>
                <div>
                    <label class="{{ $labelClass }}">Expected Response Time</label>
                    <input type="text" wire:model="responseTime" class="{{ $inputClass }}" placeholder="e.g. Within 1 Business Day" />
                </div>
            </div>
        </div>

        {{-- Social Links --}}
        <div class="glass-card p-4 space-y-3">
            <h3 class="text-xs font-bold text-foreground border-b border-border pb-2 flex items-center gap-1.5">
                <x-icon name="share-2" class="h-3.5 w-3.5 text-primary" /> Footer Social Media Links
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="{{ $labelClass }}">Instagram URL</label>
                    <input type="text" wire:model="instagramUrl" class="{{ $inputClass }}" placeholder="https://instagram.com/yourpage" />
                </div>
                <div>
                    <label class="{{ $labelClass }}">WhatsApp Number / Link</label>
                    <input type="text" wire:model="whatsappUrl" class="{{ $inputClass }}" placeholder="https://wa.me/919876543210" />
                </div>
                <div>
                    <label class="{{ $labelClass }}">Facebook URL</label>
                    <input type="text" wire:model="facebookUrl" class="{{ $inputClass }}" placeholder="https://facebook.com/yourpage" />
                </div>
            </div>
        </div>

        {{-- Coordinators --}}
        <div class="glass-card p-4 space-y-3">
            <div class="flex items-center justify-between border-b border-border pb-2">
                <h3 class="text-xs font-bold text-foreground flex items-center gap-1.5">
                    <x-icon name="mail" class="h-3.5 w-3.5 text-primary" /> Regional Coordinators
                </h3>
                <button type="button" wire:click="addCoordinator" class="inline-flex items-center gap-1 text-[12px] font-bold text-primary bg-primary-soft hover:opacity-90 px-2.5 py-1.5 rounded-lg transition-all cursor-pointer">
                    <x-icon name="plus" class="h-3 w-3" /> Add Coordinator
                </button>
            </div>

            @if (empty($coordinators))
                <p class="text-xs text-muted italic text-center py-4">No coordinators added. Click "Add Coordinator" to define one.</p>
            @else
                <div class="space-y-2">
                    @foreach ($coordinators as $idx => $coordinator)
                        <div class="p-3 rounded-xl border border-border bg-surface/30 grid grid-cols-2 md:grid-cols-4 gap-2 items-end">
                            <div>
                                <label class="{{ $labelClass }}">Region / Title</label>
                                <input type="text" wire:model="coordinators.{{ $idx }}.city" class="{{ $inputClass }}" placeholder="e.g. Mumbai" />
                            </div>
                            <div>
                                <label class="{{ $labelClass }}">Contact Person</label>
                                <input type="text" wire:model="coordinators.{{ $idx }}.contact" class="{{ $inputClass }}" placeholder="e.g. Ravi Sharma" />
                            </div>
                            <div>
                                <label class="{{ $labelClass }}">Phone Number</label>
                                <input type="text" maxlength="10" wire:model="coordinators.{{ $idx }}.phone" class="{{ $inputClass }}" placeholder="10-digit mobile number" />
                            </div>
                            <div class="flex gap-1.5 items-end">
                                <div class="flex-1">
                                    <label class="{{ $labelClass }}">Email Address</label>
                                    <input type="email" wire:model="coordinators.{{ $idx }}.email" class="{{ $inputClass }}" placeholder="e.g. mumbai@sabha.global" />
                                </div>
                                <button
                                    type="button"
                                    wire:click="removeCoordinator({{ $idx }})"
                                    class="h-[30px] w-[30px] shrink-0 rounded-lg bg-red-50 text-red-600 border border-red-100 flex items-center justify-center hover:bg-red-100 transition-colors cursor-pointer mb-px"
                                    title="Delete Coordinator"
                                >
                                    <x-icon name="trash-2" class="h-3 w-3" />
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Trustees --}}
        <div class="glass-card p-4 space-y-3">
            <div class="flex items-center justify-between border-b border-border pb-2">
                <h3 class="text-xs font-bold text-foreground flex items-center gap-1.5">
                    <x-icon name="users" class="h-3.5 w-3.5 text-primary" /> Trustees & Committee Members
                </h3>
                <button type="button" wire:click="addTrustee" class="inline-flex items-center gap-1 text-[12px] font-bold text-primary bg-primary-soft hover:opacity-90 px-2.5 py-1.5 rounded-lg transition-all cursor-pointer">
                    <x-icon name="plus" class="h-3 w-3" /> Add Trustee
                </button>
            </div>

            @if (empty($trustees))
                <p class="text-xs text-muted italic text-center py-4">No trustees added. Click "Add Trustee" to define one.</p>
            @else
                <div class="space-y-2">
                    @foreach ($trustees as $idx => $trustee)
                        <div class="p-3 rounded-xl border border-border bg-surface/30 grid grid-cols-2 md:grid-cols-4 gap-2 items-end">
                            <div>
                                <div class="flex items-center gap-1 mb-0.5">
                                    @if (! empty($trustee['avatar']))
                                        <img src="{{ media_url($trustee['avatar']) ?? $trustee['avatar'] }}" alt="" class="h-3.5 w-3.5 rounded-full object-cover border border-border shrink-0" />
                                    @endif
                                    <label class="text-[12px] font-bold text-muted uppercase tracking-wider">Name</label>
                                </div>
                                <input type="text" wire:model="trustees.{{ $idx }}.name" class="{{ $inputClass }}" placeholder="e.g. Ravi Sharma" />
                            </div>
                            <div>
                                <label class="{{ $labelClass }}">Role / Title</label>
                                <input type="text" wire:model="trustees.{{ $idx }}.role" class="{{ $inputClass }}" placeholder="e.g. President & Trustee" />
                            </div>
                            <div>
                                <label class="{{ $labelClass }}">Company / Organization</label>
                                <input type="text" wire:model="trustees.{{ $idx }}.company" class="{{ $inputClass }}" placeholder="e.g. Founder, Vertex Solutions" />
                            </div>
                            <div class="flex gap-1.5 items-end">
                                <div class="flex-1">
                                    <label class="{{ $labelClass }}">Avatar URL</label>
                                    <div class="flex gap-1">
                                        <input type="text" wire:model="trustees.{{ $idx }}.avatar" class="{{ $inputClass }}" placeholder="e.g. https://images.unsplash.com/..." />
                                        <label class="h-[30px] px-2 rounded-lg border border-border bg-surface hover:bg-surface/70 text-[12px] font-bold text-muted flex items-center justify-center cursor-pointer transition-colors shrink-0">
                                            Upload
                                            <input
                                                type="file"
                                                accept="image/*"
                                                class="hidden"
                                                x-on:change="
                                                    const file = $event.target.files[0];
                                                    if (file) {
                                                        $wire.upload('trusteeAvatarUpload', file, () => $wire.uploadTrusteeAvatar({{ $idx }}), () => {});
                                                    }
                                                "
                                            />
                                        </label>
                                    </div>
                                </div>
                                <button
                                    type="button"
                                    wire:click="removeTrustee({{ $idx }})"
                                    class="h-[30px] w-[30px] shrink-0 rounded-lg bg-red-50 text-red-600 border border-red-100 flex items-center justify-center hover:bg-red-100 transition-colors cursor-pointer mb-px"
                                    title="Delete Trustee"
                                >
                                    <x-icon name="trash-2" class="h-3 w-3" />
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="flex items-center justify-end">
            <button
                wire:click="save"
                wire:loading.attr="disabled"
                wire:target="save"
                class="inline-flex items-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60 cursor-pointer"
            >
                <x-icon name="save" class="h-3 w-3" />
                <span wire:loading.remove wire:target="save">Save Settings</span>
                <span wire:loading wire:target="save">Saving...</span>
            </button>
        </div>
    </div>
</div>
