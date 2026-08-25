<x-layouts.app :title="__('site.contact.title') . ' | Sabha'" :description="__('site.contact.subtitle')">
    <div
        class="bg-background"
        x-data="{
            inquiryType: 'Membership',
            formSubmitted: false,
            isSubmitting: false,
            submitError: '',
            form: { name: '', email: '', subject: '', message: '' },
            async submit() {
                if (!this.form.name || !this.form.email || !this.form.message) return;
                this.isSubmitting = true;
                this.submitError = '';
                this.formSubmitted = false;
                try {
                    const res = await fetch('{{ route('contact.submit') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        },
                        body: JSON.stringify({
                            name: this.form.name,
                            email: this.form.email,
                            subject: this.form.subject,
                            message: this.form.message,
                            category: this.inquiryType,
                        }),
                    });
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message || 'Failed to send message. Please try again.');
                    this.formSubmitted = true;
                    this.form = { name: '', email: '', subject: '', message: '' };
                    setTimeout(() => { this.formSubmitted = false }, 7000);
                } catch (e) {
                    this.submitError = e.message;
                } finally {
                    this.isSubmitting = false;
                }
            },
        }"
    >
        <x-page-header :kicker="__('site.contact.kicker')" :title="__('site.contact.title')" :subtitle="__('site.contact.subtitle')" />

        <div class="mx-auto max-w-7xl px-6 py-5 lg:py-4">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-12 lg:gap-5">

                {{-- Contact details & Chapters info --}}
                <div class="lg:col-span-5 space-y-6">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-primary">{{ __('site.contact.get_in_touch') }}</span>
                        <h2 class="mt-1.5 text-2xl font-extrabold tracking-tight text-foreground sm:text-3xl">{{ __('site.contact.reach_title') }}</h2>
                        <p class="mt-2 text-xs leading-relaxed text-muted font-medium">{{ __('site.contact.reach_subtitle') }}</p>
                    </div>

                    <div class="space-y-3">
                        <div class="glass-card flex items-center gap-3 p-3.5">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary">
                                <x-icon name="mail" class="h-4 w-4" />
                            </div>
                            <div>
                                <h3 class="text-[10px] font-bold text-muted uppercase">{{ __('site.contact.general_email') }}</h3>
                                <p class="text-xs font-extrabold text-slate-900">{{ $contactEmail }}</p>
                            </div>
                        </div>
                        <div class="glass-card flex items-center gap-3 p-3.5">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-soft text-primary">
                                <x-icon name="clock" class="h-4 w-4" />
                            </div>
                            <div>
                                <h3 class="text-[10px] font-bold text-muted uppercase">{{ __('site.contact.response_time') }}</h3>
                                <p class="text-xs font-extrabold text-slate-900">{{ $responseTime }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3 pt-4 border-t border-border/80">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-foreground">{{ __('site.contact.regional_contacts') }}</h3>
                        <div class="grid grid-cols-1 gap-2.5">
                            @foreach ($coordinators as $ch)
                                @php
                                    $translationKey = 'site.contact.' . str_replace(' ', '_', strtolower($ch['city']));
                                    $translated = __($translationKey);
                                    $cityLabel = $translated === $translationKey ? $ch['city'] : $translated;
                                @endphp
                                <div class="rounded-xl border {{ $ch['border'] ?? 'border-border' }} {{ $ch['bg'] ?? 'bg-white' }} p-3 space-y-1.5">
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-extrabold text-foreground">{{ $cityLabel }}</span>
                                        <span class="inline-flex items-center gap-1 text-[8px] font-bold bg-white text-primary border border-border px-2 py-0.5 rounded-full">
                                            {{ __('site.contact.coordinator') }}
                                        </span>
                                    </div>
                                    <div class="space-y-0.5">
                                        <p class="text-xs font-extrabold text-slate-900">{{ $ch['contact'] }}</p>
                                        <div class="flex flex-wrap gap-x-3 text-[11px] text-muted font-medium">
                                            <span class="flex items-center gap-1"><x-icon name="phone" class="h-[11px] w-[11px] text-primary" /> {{ $ch['phone'] }}</span>
                                            <span class="flex items-center gap-1"><x-icon name="mail" class="h-[11px] w-[11px] text-primary" /> {{ $ch['email'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Contact form --}}
                <div class="lg:col-span-7">
                    <div class="glass-card p-5 border border-border/85">
                        <h3 class="text-sm font-bold text-foreground">{{ __('site.contact.send_message') }}</h3>
                        <p class="text-xs text-muted font-medium mt-0.5">{{ __('site.contact.send_subtitle') }}</p>

                        <form x-on:submit.prevent="submit" class="mt-4 space-y-3">

                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold uppercase tracking-wider text-muted">{{ __('site.contact.inquiry_category') }}</label>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ([
                                        ['value' => 'Membership', 'label' => __('site.contact.inquiry_membership')],
                                        ['value' => 'Sponsorship', 'label' => __('site.contact.inquiry_sponsorship')],
                                        ['value' => 'Event hosting', 'label' => __('site.contact.inquiry_event')],
                                        ['value' => 'Technical Support', 'label' => __('site.contact.inquiry_support')],
                                    ] as $opt)
                                        <button
                                            type="button"
                                            x-on:click="inquiryType = '{{ $opt['value'] }}'"
                                            :class="inquiryType === '{{ $opt['value'] }}' ? 'border-primary bg-primary text-white shadow-sm' : 'border-border bg-white text-muted hover:bg-surface hover:text-foreground'"
                                            class="rounded-lg border px-3 py-1.5 text-xs font-bold transition-all"
                                        >{{ $opt['label'] }}</button>
                                    @endforeach
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-foreground uppercase tracking-wide">{{ __('site.contact.full_name') }}</label>
                                    <input type="text" required placeholder="{{ __('site.contact.ph_name') }}" x-model="form.name" class="w-full rounded-xl border border-border bg-white px-3 py-2 text-xs text-foreground outline-none transition-colors focus:border-primary placeholder:text-muted-foreground" />
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-foreground uppercase tracking-wide">{{ __('site.contact.email_address') }}</label>
                                    <input type="email" required placeholder="{{ __('site.contact.ph_email') }}" x-model="form.email" class="w-full rounded-xl border border-border bg-white px-3 py-2 text-xs text-foreground outline-none transition-colors focus:border-primary placeholder:text-muted-foreground" />
                                </div>
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-foreground uppercase tracking-wide">{{ __('site.contact.subject') }}</label>
                                <input type="text" required placeholder="{{ __('site.contact.ph_subject') }}" x-model="form.subject" class="w-full rounded-xl border border-border bg-white px-3 py-2 text-xs text-foreground outline-none transition-colors focus:border-primary placeholder:text-muted-foreground" />
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-foreground uppercase tracking-wide">{{ __('site.contact.your_message') }}</label>
                                <textarea required rows="4" placeholder="{{ __('site.contact.ph_message') }}" x-model="form.message" class="w-full resize-none rounded-xl border border-border bg-white px-3 py-2 text-xs text-foreground outline-none transition-colors focus:border-primary placeholder:text-muted-foreground"></textarea>
                            </div>

                            <button
                                type="submit"
                                :disabled="isSubmitting"
                                class="group inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-xs font-bold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span x-text="isSubmitting ? 'Sending...' : '{{ __('site.contact.send_inquiry') }}'"></span>
                                <x-icon name="send" x-show="!isSubmitting" class="h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                            </button>

                            <div x-show="submitError" x-cloak x-transition class="flex items-center gap-2.5 rounded-xl border border-red-200 bg-red-50 p-4 text-xs font-bold text-red-700">
                                <div class="h-5 w-5 rounded-full border-2 border-red-600 flex items-center justify-center text-[10px] font-extrabold shrink-0">✕</div>
                                <p x-text="submitError"></p>
                            </div>

                            <div x-show="formSubmitted" x-cloak x-transition class="flex items-center gap-2.5 rounded-xl border border-green-200 bg-green-50 p-4 text-xs font-bold text-green-700">
                                <x-icon name="check-circle-2" class="h-5 w-5 text-green-600 shrink-0" />
                                <div>
                                    <p>{{ __('site.contact.success_msg') }}</p>
                                    <p class="text-[10px] text-green-600 font-semibold mt-0.5">{{ __('site.contact.success_note') }}</p>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>

        {{-- Trust verification banner --}}
        <section class="border-t border-border bg-surface">
            <div class="mx-auto max-w-4xl px-4 py-5 text-center space-y-3">
                <div class="mx-auto flex h-9 w-9 items-center justify-center rounded-xl bg-primary-soft text-primary">
                    <x-icon name="shield-check" class="h-4 w-4" />
                </div>
                <h2 class="text-xl font-extrabold tracking-tight text-foreground sm:text-2xl">{{ __('site.contact.integrity_title') }}</h2>
                <p class="mx-auto max-w-xl text-xs leading-relaxed text-muted font-semibold">{{ __('site.contact.integrity_desc') }}</p>
            </div>
        </section>
    </div>
</x-layouts.app>
