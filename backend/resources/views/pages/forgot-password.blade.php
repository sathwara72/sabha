<x-layouts.app :title="__('site.auth.forgot_title') . ' | Sabha'" :description="__('site.auth.forgot_subtitle')" :noindex="true">
    <div
        class="flex flex-col items-center justify-start bg-background font-outfit px-4 sm:px-6 pt-6 pb-12"
        x-data="{
            step: 'request',
            email: '', otp: '', password: '', confirmPassword: '',
            showPassword: false, showConfirmPassword: false,
            loading: false, resending: false, error: '', success: '',
            async sendOtp() {
                this.error = ''; this.success = ''; this.loading = true;
                try {
                    const res = await fetch('/forgot-password/send-otp', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        },
                        body: JSON.stringify({ email: this.email }),
                    });
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message || '{{ __('site.auth.send_otp') }}');
                    this.success = data.message || '{{ __('site.auth.forgot_subtitle') }}';
                    this.step = 'verify';
                } catch (e) {
                    this.error = e.message;
                } finally {
                    this.loading = false;
                }
            },
            async resendOtp() {
                this.error = ''; this.success = ''; this.resending = true;
                try {
                    const res = await fetch('/forgot-password/send-otp', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        },
                        body: JSON.stringify({ email: this.email }),
                    });
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message || '{{ __('site.auth.resend_otp') }}');
                    this.success = data.message || '{{ __('site.auth.resend_otp') }}';
                } catch (e) {
                    this.error = e.message;
                } finally {
                    this.resending = false;
                }
            },
            async resetPassword() {
                this.error = ''; this.success = '';
                if (!this.otp || this.otp.trim().length !== 6) {
                    this.error = '{{ __('site.auth.invalid_otp') }}';
                    return;
                }
                if (this.password.length < 6) {
                    this.error = '{{ __('site.auth.short_password') }}';
                    return;
                }
                if (this.password !== this.confirmPassword) {
                    this.error = '{{ __('site.auth.passwords_not_match') }}';
                    return;
                }
                this.loading = true;
                try {
                    const res = await fetch('/forgot-password/reset', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        },
                        body: JSON.stringify({ email: this.email, otp: this.otp.trim(), password: this.password }),
                    });
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message || '{{ __('site.auth.reset_password_btn') }}');
                    this.success = data.message || '{{ __('site.auth.reset_success_desc') }}';
                    this.step = 'success';
                } catch (e) {
                    this.error = e.message;
                } finally {
                    this.loading = false;
                }
            },
        }"
    >
        <div class="w-full max-w-md">
            <div class="text-center mb-4">
                <a href="/" class="inline-flex items-center gap-2 mb-2 group">
                    <img src="{{ asset('logo.png') }}" alt="SABHA" class="h-8 w-8 rounded-full object-contain" />
                    <span class="text-xl font-bold tracking-tight text-primary-dark">SABHA</span>
                </a>
                <h1 class="text-xl sm:text-2xl font-extrabold text-foreground mb-1">
                    <span x-show="step === 'request'">{{ __('site.auth.forgot_title') }}</span>
                    <span x-show="step === 'verify'">{{ __('site.auth.forgot_verify_title') }}</span>
                    <span x-show="step === 'success'">{{ __('site.auth.forgot_success_title') }}</span>
                </h1>
                <p class="text-xs text-muted max-w-xs mx-auto leading-relaxed">
                    <span x-show="step === 'request'">{{ __('site.auth.forgot_subtitle') }}</span>
                    <span x-show="step === 'verify'">{{ __('site.auth.forgot_verify_subtitle') }} <span x-text="email"></span></span>
                    <span x-show="step === 'success'">{{ __('site.auth.forgot_success_subtitle') }}</span>
                </p>
            </div>

            <div class="bg-white border border-border/80 rounded-2xl shadow-md p-5 sm:p-7">
                <div x-show="error" x-cloak class="mb-4 rounded-xl bg-red-50 border border-red-100 p-3 text-center text-xs font-semibold text-red-600" x-text="error"></div>
                <div x-show="success && step === 'verify'" x-cloak class="mb-4 rounded-xl bg-emerald-50 border border-emerald-100 p-3 text-center text-xs font-semibold text-emerald-800" x-text="success"></div>

                <form x-show="step === 'request'" x-on:submit.prevent="sendOtp" class="space-y-4">
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-foreground">{{ __('site.auth.email_address') }}</label>
                        <div class="relative group">
                            <x-icon name="mail" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground group-focus-within:text-primary transition-colors" />
                            <input type="email" required placeholder="{{ __('site.auth.email_placeholder') }}" x-model="email" class="w-full rounded-xl border border-border bg-white pl-10 pr-4 py-2.5 text-xs text-foreground outline-none placeholder:text-muted-foreground focus:border-primary transition-colors" />
                        </div>
                    </div>

                    <button type="submit" :disabled="loading" class="group w-full inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-xs font-bold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60">
                        <span x-text="loading ? '{{ __('site.auth.sending') }}' : '{{ __('site.auth.send_otp') }}'"></span>
                        <x-icon name="arrow-right" x-show="!loading" class="w-4 h-4 transition-transform group-hover:translate-x-0.5" />
                    </button>

                    <div class="pt-1 text-center">
                        <a href="/?login=1" class="inline-flex items-center gap-1.5 text-xs font-semibold text-muted hover:text-primary transition-colors">
                            <x-icon name="arrow-left" class="w-3.5 h-3.5" /> {{ __('site.auth.back_to_login') }}
                        </a>
                    </div>
                </form>

                <form x-show="step === 'verify'" x-cloak x-on:submit.prevent="resetPassword" class="space-y-3.5">
                    <div class="space-y-1">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-semibold text-foreground">{{ __('site.auth.verify_code') }}</label>
                            <button type="button" x-on:click="step = 'request'; error = ''; success = ''" class="text-[11px] font-medium text-primary hover:underline">
                                {{ __('site.auth.change_email') }}
                            </button>
                        </div>
                        <div class="relative group">
                            <x-icon name="key-round" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground group-focus-within:text-primary transition-colors" />
                            <input type="text" required maxlength="6" placeholder="{{ __('site.auth.enter_otp_placeholder') }}" x-model="otp" x-on:input="otp = otp.replace(/\D/g, '')" class="w-full rounded-xl border border-border bg-white pl-10 pr-4 py-2.5 text-xs font-mono tracking-widest text-foreground outline-none placeholder:text-muted-foreground placeholder:font-sans placeholder:tracking-normal focus:border-primary transition-colors" />
                        </div>
                        <div class="flex items-center justify-between pt-1">
                            <span class="text-muted text-[11px]">{{ __('site.auth.didnt_receive') }}</span>
                            <button type="button" :disabled="resending" x-on:click="resendOtp" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg border border-primary/20 bg-primary/5 text-[11px] font-bold text-primary hover:bg-primary/10 transition-colors disabled:opacity-50">
                                <x-icon name="refresh-cw" class="w-3 h-3" x-bind:class="resending ? 'animate-spin' : ''" />
                                <span x-text="resending ? '{{ __('site.auth.resending') }}' : '{{ __('site.auth.resend_otp') }}'"></span>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-foreground">{{ __('site.auth.new_password') }}</label>
                        <div class="relative group">
                            <x-icon name="lock" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground group-focus-within:text-primary transition-colors" />
                            <input :type="showPassword ? 'text' : 'password'" required minlength="6" placeholder="{{ __('site.auth.min_chars') }}" x-model="password" class="w-full rounded-xl border border-border bg-white pl-10 pr-9 py-2.5 text-xs text-foreground outline-none placeholder:text-muted-foreground focus:border-primary transition-colors" />
                            <button type="button" x-on:click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors p-1">
                                <x-icon name="eye" x-show="!showPassword" class="w-3.5 h-3.5" />
                                <x-icon name="eye-off" x-show="showPassword" class="w-3.5 h-3.5" />
                            </button>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-foreground">{{ __('site.auth.confirm_new_password') }}</label>
                        <div class="relative group">
                            <x-icon name="lock" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground group-focus-within:text-primary transition-colors" />
                            <input :type="showConfirmPassword ? 'text' : 'password'" required minlength="6" placeholder="{{ __('site.auth.reenter_new_password') }}" x-model="confirmPassword" class="w-full rounded-xl border border-border bg-white pl-10 pr-9 py-2.5 text-xs text-foreground outline-none placeholder:text-muted-foreground focus:border-primary transition-colors" />
                            <button type="button" x-on:click="showConfirmPassword = !showConfirmPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors p-1">
                                <x-icon name="eye" x-show="!showConfirmPassword" class="w-3.5 h-3.5" />
                                <x-icon name="eye-off" x-show="showConfirmPassword" class="w-3.5 h-3.5" />
                            </button>
                        </div>
                    </div>

                    <button type="submit" :disabled="loading" class="group w-full inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-5 py-2.5 text-xs font-bold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60 mt-1">
                        <span x-text="loading ? '{{ __('site.auth.resetting') }}' : '{{ __('site.auth.reset_password_btn') }}'"></span>
                        <x-icon name="arrow-right" x-show="!loading" class="w-3.5 h-3.5 transition-transform group-hover:translate-x-0.5" />
                    </button>

                    <div class="pt-2 text-center">
                        <a href="/?login=1" class="inline-flex items-center gap-1 text-xs font-semibold text-muted hover:text-primary transition-colors">
                            <x-icon name="arrow-left" class="w-3.5 h-3.5" /> {{ __('site.auth.back_to_login') }}
                        </a>
                    </div>
                </form>

                <div x-show="step === 'success'" x-cloak class="space-y-5">
                    <div class="rounded-xl bg-emerald-50 border border-emerald-100 p-5 text-sm font-semibold text-emerald-800 flex flex-col items-center gap-2.5">
                        <x-icon name="shield-check" class="h-9 w-9 text-emerald-600 animate-pulse" />
                        <p class="text-center text-sm font-bold text-emerald-900">{{ __('site.auth.reset_complete') }}</p>
                        <p class="text-center text-xs text-emerald-700 leading-relaxed font-normal" x-text="success || '{{ __('site.auth.reset_success_desc') }}'"></p>
                    </div>
                    <a href="/?login=1" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-xs font-bold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98]">
                        {{ __('site.auth.login_to_continue') }} <x-icon name="arrow-right" class="w-4 h-4" />
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
