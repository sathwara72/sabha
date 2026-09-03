<div class="space-y-4 font-outfit">
    {{-- Top Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 leading-tight">Site Settings</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Manage website contact details, social links, statistics, and coordinators</p>
        </div>
        <button
            wire:click="loadData"
            class="inline-flex h-8 w-8 items-center justify-center rounded-xl border border-border bg-white text-muted hover:bg-surface hover:text-foreground cursor-pointer transition-colors self-start sm:self-auto shadow-2xs"
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

    {{-- Tabs Navigation Bar --}}
    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 border-b border-border text-xs font-bold scrollbar-none">
        <button
            type="button"
            wire:click="setTab('general')"
            class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl transition-all cursor-pointer whitespace-nowrap {{ $activeTab === 'general' ? 'bg-primary text-white shadow-sm' : 'bg-white border border-border text-slate-600 hover:bg-slate-50' }}"
        >
            <x-icon name="settings" class="h-3.5 w-3.5" />
            <span>General Contact</span>
        </button>

        <button
            type="button"
            wire:click="setTab('social')"
            class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl transition-all cursor-pointer whitespace-nowrap {{ $activeTab === 'social' ? 'bg-primary text-white shadow-sm' : 'bg-white border border-border text-slate-600 hover:bg-slate-50' }}"
        >
            <x-icon name="share-2" class="h-3.5 w-3.5" />
            <span>Social Links</span>
        </button>

        <button
            type="button"
            wire:click="setTab('statistics')"
            class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl transition-all cursor-pointer whitespace-nowrap {{ $activeTab === 'statistics' ? 'bg-primary text-white shadow-sm' : 'bg-white border border-border text-slate-600 hover:bg-slate-50' }}"
        >
            <x-icon name="bar-chart-3" class="h-3.5 w-3.5" />
            <span>Website Statistics</span>
            <span class="inline-flex items-center justify-center px-1.5 py-0.2 rounded-full text-[10px] {{ $activeTab === 'statistics' ? 'bg-white/20 text-white' : 'bg-primary-soft text-primary' }}">{{ count($stats) }}</span>
        </button>

        <button
            type="button"
            wire:click="setTab('membership_qr')"
            class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl transition-all cursor-pointer whitespace-nowrap {{ $activeTab === 'membership_qr' ? 'bg-primary text-white shadow-sm' : 'bg-white border border-border text-slate-600 hover:bg-slate-50' }}"
        >
            <x-icon name="qr-code" class="h-3.5 w-3.5" />
            <span>Payment QR/UPI</span>
        </button>

        <button
            type="button"
            wire:click="setTab('mail')"
            class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl transition-all cursor-pointer whitespace-nowrap {{ $activeTab === 'mail' ? 'bg-primary text-white shadow-sm' : 'bg-white border border-border text-slate-600 hover:bg-slate-50' }}"
        >
            <x-icon name="mail" class="h-3.5 w-3.5" />
            <span>Mail & SMTP Settings</span>
        </button>
    </div>

    {{-- Tab 1: General Contact Info --}}
    @if ($activeTab === 'general')
        <div class="rounded-2xl border border-border/80 bg-white p-5 space-y-4 shadow-2xs">
            <h3 class="text-xs font-bold text-foreground border-b border-border pb-2 flex items-center gap-1.5">
                <x-icon name="settings" class="h-3.5 w-3.5 text-primary" /> General Contact Info
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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

            <div class="flex items-center justify-end pt-3 border-t border-slate-100">
                <button
                    wire:click="save"
                    wire:loading.attr="disabled"
                    wire:target="save"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60 cursor-pointer"
                >
                    <x-icon name="save" class="h-3 w-3" />
                    <span wire:loading.remove wire:target="save">Save Contact Info</span>
                    <span wire:loading wire:target="save">Saving...</span>
                </button>
            </div>
        </div>
    @endif

    {{-- Tab 2: Social Media Links --}}
    @if ($activeTab === 'social')
        <div class="rounded-2xl border border-border/80 bg-white p-5 space-y-4 shadow-2xs">
            <h3 class="text-xs font-bold text-foreground border-b border-border pb-2 flex items-center gap-1.5">
                <x-icon name="share-2" class="h-3.5 w-3.5 text-primary" /> Footer Social Media Links
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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

            <div class="flex items-center justify-end pt-3 border-t border-slate-100">
                <button
                    wire:click="save"
                    wire:loading.attr="disabled"
                    wire:target="save"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60 cursor-pointer"
                >
                    <x-icon name="save" class="h-3 w-3" />
                    <span wire:loading.remove wire:target="save">Save Social Links</span>
                    <span wire:loading wire:target="save">Saving...</span>
                </button>
            </div>
        </div>
    @endif

    {{-- Tab 3: Website Statistics --}}
    @if ($activeTab === 'statistics')
        <div class="space-y-3">
            <div class="rounded-2xl border border-border/80 bg-white p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2 shadow-2xs">
                <div>
                    <h3 class="text-xs font-bold text-foreground flex items-center gap-1.5">
                        <x-icon name="bar-chart-3" class="h-3.5 w-3.5 text-primary" /> Key Counters & Statistics
                    </h3>
                    <p class="text-[11px] text-muted">Manage the counters and labels displayed on the website homepage and banner sections</p>
                </div>
                <button
                    wire:click="loadStats"
                    class="inline-flex items-center gap-1 text-[11px] font-bold text-primary bg-primary-soft hover:opacity-90 px-2.5 py-1.5 rounded-lg transition-all cursor-pointer self-start sm:self-auto"
                >
                    <x-icon name="refresh-cw" class="h-3 w-3" /> Refresh Stats
                </button>
            </div>

            @if ($stats->isEmpty())
                <div class="glass-card py-16 text-center text-muted border border-dashed border-border rounded-2xl text-xs">
                    No statistics found. Run database seeders to populate defaults.
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3.5">
                    @foreach ($stats as $stat)
                        <div wire:key="stat-setting-card-{{ $stat->id }}" class="glass-card p-4 flex flex-col gap-3 rounded-2xl border border-border/80 bg-white shadow-2xs">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                <span class="flex items-center gap-1 text-[11px] font-bold text-primary bg-primary-soft px-2 py-0.5 rounded-md">
                                    <x-icon name="layers" class="h-2.5 w-2.5" /> Stat #{{ $stat->id }}
                                </span>
                                <span class="text-[11px] text-muted-foreground font-semibold truncate max-w-[150px]">
                                    Live: <span class="text-foreground font-bold">{{ $stat->value }}</span> — {{ $stat->label }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-2.5">
                                <div class="space-y-0.5">
                                    <label class="text-[11px] font-bold text-muted uppercase tracking-wider block">Label</label>
                                    <input
                                        type="text"
                                        wire:model="editStats.{{ $stat->id }}.label"
                                        class="w-full rounded-lg border border-border bg-white px-3 py-1.5 text-xs text-foreground outline-none transition-colors focus:border-primary font-semibold"
                                        placeholder="e.g. Active Members"
                                    />
                                </div>
                                <div class="space-y-0.5">
                                    <label class="text-[11px] font-bold text-muted uppercase tracking-wider block">Value</label>
                                    <input
                                        type="text"
                                        wire:model="editStats.{{ $stat->id }}.value"
                                        class="w-full rounded-lg border border-border bg-white px-3 py-1.5 text-xs text-foreground outline-none transition-colors focus:border-primary font-semibold"
                                        placeholder="e.g. 500+"
                                    />
                                </div>
                            </div>

                            <div class="flex justify-end pt-1">
                                <button
                                    wire:click="updateStat({{ $stat->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="updateStat({{ $stat->id }})"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-1.5 text-[11px] font-bold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60 cursor-pointer"
                                >
                                    <x-icon name="save" class="h-3 w-3" />
                                    <span wire:loading.remove wire:target="updateStat({{ $stat->id }})">Save Stat #{{ $stat->id }}</span>
                                    <span wire:loading wire:target="updateStat({{ $stat->id }})">Saving...</span>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    {{-- Tab 5: Membership Payment QR/UPI --}}
    @if ($activeTab === 'membership_qr')
        <div class="rounded-2xl border border-border/80 bg-white p-5 space-y-4 shadow-2xs">
            <h3 class="text-xs font-bold text-foreground border-b border-border pb-2 flex items-center gap-1.5">
                <x-icon name="qr-code" class="h-3.5 w-3.5 text-primary" /> Membership Payment QR / UPI Scanner
            </h3>
            <p class="text-[12px] text-muted">This QR image will be displayed to new members during Step 2 of registration so they can scan and pay membership fees.</p>

            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5 p-4 rounded-xl bg-slate-50/70 border border-slate-200/80">
                <label class="group flex flex-col items-center justify-center border-2 border-dashed border-slate-300 rounded-2xl p-3 bg-white hover:bg-slate-50 hover:border-primary transition-all cursor-pointer relative h-40 w-40 shrink-0 shadow-2xs overflow-hidden">
                    <input type="file" accept="image/*" wire:model="membershipQrUpiFile" class="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10" />
                    @if ($membershipQrUpiFile)
                        <img src="{{ $membershipQrUpiFile->temporaryUrl() }}" class="h-full w-full object-contain rounded-lg" />
                    @elseif ($membershipQrUpiImage)
                        <img src="{{ media_url($membershipQrUpiImage) }}" class="h-full w-full object-contain rounded-lg" />
                    @else
                        <div class="flex flex-col items-center text-center">
                            <div class="h-10 w-10 rounded-full bg-primary-soft text-primary flex items-center justify-center mb-1.5 group-hover:scale-110 transition-transform">
                                <x-icon name="upload-cloud" class="h-5 w-5" />
                            </div>
                            <span class="text-[11px] font-bold text-slate-700">Choose QR Image</span>
                            <span class="text-[10px] text-muted-foreground mt-0.5">PNG, JPG or WEBP</span>
                        </div>
                    @endif
                </label>

                <div class="space-y-2 flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        @if ($membershipQrUpiFile)
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-md">
                                <x-icon name="file-check" class="h-3 w-3" /> New Image Selected
                            </span>
                        @elseif ($membershipQrUpiImage)
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-700 bg-blue-50 border border-blue-200 px-2 py-0.5 rounded-md">
                                <x-icon name="check-circle" class="h-3 w-3" /> Current Active QR Code
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-md">
                                <x-icon name="alert-circle" class="h-3 w-3" /> No QR Code Uploaded
                            </span>
                        @endif
                    </div>

                    <p class="text-xs text-slate-600 font-medium">Click the box to browse and select a new UPI / QR Code image from your device.</p>

                    @if ($membershipQrUpiImage || $membershipQrUpiFile)
                        <div class="pt-1">
                            <button
                                type="button"
                                wire:click="removeMembershipQrUpi"
                                class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-600 hover:text-rose-700 bg-rose-50 border border-rose-200 px-2.5 py-1 rounded-lg transition-colors cursor-pointer"
                            >
                                <x-icon name="trash-2" class="h-3 w-3" /> Remove QR Image
                            </button>
                        </div>
                    @endif

                    @error('membershipQrUpiFile')
                        <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end pt-3 border-t border-slate-100">
                <button
                    wire:click="uploadMembershipQrUpi"
                    wire:loading.attr="disabled"
                    wire:target="uploadMembershipQrUpi"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60 cursor-pointer"
                >
                    <x-icon name="save" class="h-3 w-3" />
                    <span wire:loading.remove wire:target="uploadMembershipQrUpi">Save Payment QR</span>
                    <span wire:loading wire:target="uploadMembershipQrUpi">Saving...</span>
                </button>
            </div>
        </div>
    @endif

    {{-- Tab 5: Mail & SMTP Settings --}}
    @if ($activeTab === 'mail')
        <div class="space-y-4">
            <div class="rounded-2xl border border-border/80 bg-white p-5 space-y-4 shadow-2xs">
                <div class="flex items-center justify-between border-b border-border pb-2">
                    <h3 class="text-xs font-bold text-foreground flex items-center gap-1.5">
                        <x-icon name="mail" class="h-3.5 w-3.5 text-primary" /> Outgoing Mail (SMTP) Configuration
                    </h3>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-50 border border-emerald-200 text-[10px] font-bold text-emerald-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Active
                    </span>
                </div>

                <form wire:submit="saveMailSettings" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        <div>
                            <label class="{{ $labelClass }}">Mail Driver</label>
                            <select wire:model="mailMailer" class="{{ $inputClass }}">
                                <option value="smtp">SMTP (Recommended)</option>
                                <option value="sendmail">Sendmail</option>
                                <option value="log">Log (Testing)</option>
                            </select>
                            @error('mailMailer') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">SMTP Host</label>
                            <input type="text" wire:model="mailHost" class="{{ $inputClass }}" placeholder="e.g. smtp.hostinger.com" />
                            @error('mailHost') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">SMTP Port</label>
                            <input type="number" wire:model="mailPort" class="{{ $inputClass }}" placeholder="e.g. 465 or 587" />
                            @error('mailPort') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">SMTP Username / Email</label>
                            <input type="text" wire:model="mailUsername" class="{{ $inputClass }}" placeholder="e.g. info@sabhaglobal.org" />
                            @error('mailUsername') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">SMTP Password</label>
                            <input type="password" wire:model="mailPassword" class="{{ $inputClass }}" placeholder="SMTP Password" />
                            @error('mailPassword') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">Encryption</label>
                            <select wire:model="mailEncryption" class="{{ $inputClass }}">
                                <option value="ssl">SSL (Port 465)</option>
                                <option value="tls">TLS (Port 587)</option>
                                <option value="none">None</option>
                            </select>
                            @error('mailEncryption') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">From Email Address</label>
                            <input type="email" wire:model="mailFromAddress" class="{{ $inputClass }}" placeholder="e.g. info@sabhaglobal.org" />
                            @error('mailFromAddress') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">From Sender Name</label>
                            <input type="text" wire:model="mailFromName" class="{{ $inputClass }}" placeholder="e.g. SABHA" />
                            @error('mailFromName') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end pt-3 border-t border-slate-100">
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="saveMailSettings"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white hover:bg-primary-dark active:scale-[0.98] transition-all cursor-pointer shadow-sm"
                        >
                            <span wire:loading.remove wire:target="saveMailSettings">Save Mail Settings</span>
                            <span wire:loading wire:target="saveMailSettings" class="inline-flex items-center gap-1.5">
                                <x-icon name="loader-2" class="h-3.5 w-3.5 animate-spin" />
                                <span>Saving...</span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Test Email Card --}}
            <div class="rounded-2xl border border-border/80 bg-white p-5 space-y-3 shadow-2xs">
                <div class="flex items-center gap-1.5 border-b border-border pb-2">
                    <x-icon name="send" class="h-3.5 w-3.5 text-primary" />
                    <h3 class="text-xs font-bold text-foreground">Send Test Email</h3>
                </div>
                <p class="text-xs text-slate-500">Send a test email to verify that your SMTP server connection and credentials are working correctly.</p>

                @if ($testEmailStatus)
                    <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-xs font-bold text-emerald-800 flex items-center gap-2">
                        <x-icon name="check-circle-2" class="h-4 w-4 text-emerald-600 shrink-0" />
                        <span>{{ $testEmailStatus }}</span>
                    </div>
                @endif
                @if ($testEmailError)
                    <div class="rounded-xl bg-red-50 border border-red-200 p-3 text-xs font-semibold text-red-700 flex items-start gap-2">
                        <x-icon name="alert-triangle" class="h-4 w-4 text-red-600 shrink-0 mt-0.5" />
                        <span class="break-all">{{ $testEmailError }}</span>
                    </div>
                @endif

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 pt-1">
                    <div class="flex-1">
                        <input type="email" wire:model="testEmail" placeholder="Enter recipient email (e.g. info@sabhaglobal.org)" class="{{ $inputClass }}" />
                        @error('testEmail') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <button
                        type="button"
                        wire:click="sendTestEmail"
                        wire:loading.attr="disabled"
                        wire:target="sendTestEmail"
                        class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white hover:bg-slate-800 active:scale-[0.98] transition-all cursor-pointer shadow-sm shrink-0 disabled:opacity-60"
                    >
                        <x-icon name="send" class="h-3.5 w-3.5" wire:loading.remove wire:target="sendTestEmail" />
                        <x-icon name="loader-2" class="h-3.5 w-3.5 animate-spin" wire:loading wire:target="sendTestEmail" />
                        <span wire:loading.remove wire:target="sendTestEmail">Send Test Email</span>
                        <span wire:loading wire:target="sendTestEmail">Sending...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
