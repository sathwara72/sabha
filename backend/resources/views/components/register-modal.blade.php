<div
    x-show="$store.auth.registerOpen"
    x-cloak
    x-data="{
        step: 1,
        name: '', email: '', phone: '', password: '', confirmPassword: '',
        showPassword: false, showConfirmPassword: false, otp: '',
        error: '', loading: false, otpSentEmail: '',
        reset() {
            this.step = 1;
            this.name = ''; this.email = ''; this.phone = ''; this.password = ''; this.confirmPassword = '';
            this.showPassword = false; this.showConfirmPassword = false; this.otp = '';
            this.error = ''; this.loading = false; this.otpSentEmail = '';
        },
        close() {
            this.reset();
            $store.auth.closeRegister();
        },
        async sendOtp() {
            this.error = '';
            if (this.password !== this.confirmPassword) {
                this.error = '{{ __('site.auth.passwords_no_match') }}';
                return;
            }
            if (this.phone.length !== 10) {
                this.error = '{{ __('site.auth.invalid_phone') }}';
                return;
            }
            this.loading = true;
            try {
                const res = await fetch('/register/send-otp', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify({ name: this.name, email: this.email, phone: this.phone, password: this.password }),
                });
                const data = await res.json();
                if (!res.ok) {
                    const firstField = data.errors ? Object.keys(data.errors)[0] : null;
                    throw new Error((firstField && data.errors[firstField]?.[0]) || data.message || '{{ __('site.auth.reg_failed') }}');
                }
                this.otpSentEmail = data.email || this.email;
                this.step = 2;
            } catch (e) {
                this.error = e.message;
            } finally {
                this.loading = false;
            }
        },
        async verifyOtp() {
            this.error = '';
            this.loading = true;
            try {
                const res = await fetch('/register/confirm', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify({ email: this.email, otp: this.otp }),
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || '{{ __('site.auth.otp_failed') }}');
                window.location.reload();
            } catch (e) {
                this.error = e.message;
            } finally {
                this.loading = false;
            }
        },
    }"
    class="fixed inset-0 z-[100] flex items-center justify-center p-4 overflow-y-auto"
>
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" x-on:click="close"></div>

    <div x-show="$store.auth.registerOpen" x-transition class="relative w-full max-w-md rounded-2xl border border-border bg-white p-5 shadow-xl z-10 font-outfit">
        <button x-on:click="close" class="absolute right-3 top-3 rounded-lg p-1 text-muted transition-colors hover:bg-surface hover:text-foreground" aria-label="Close">
            <x-icon name="x" class="h-4 w-4" />
        </button>

        <div class="mb-4 text-center">
            <img src="{{ asset('logo.png') }}" alt="SABHA" class="mx-auto h-9 w-9 rounded-full object-contain mb-2" />
            <h2 class="text-base font-bold text-foreground" x-text="step === 1 ? '{{ __('site.auth.register_title') }}' : '{{ __('site.auth.verify_title') }}'"></h2>
            <p class="mt-0.5 text-xs text-muted">
                <span x-show="step === 1">{{ __('site.auth.register_subtitle') }}</span>
                <span x-show="step === 2">{{ __('site.auth.verify_subtitle') }} <span x-text="email"></span></span>
            </p>
        </div>

        <div x-show="error" x-cloak class="mb-3 rounded-xl bg-red-50 border border-red-100 p-2.5 text-center text-xs font-semibold text-red-600" x-text="error"></div>
        <div x-show="step === 2 && otpSentEmail" x-cloak class="mb-3 rounded-xl bg-green-50 border border-green-100 p-2.5 text-xs font-semibold text-green-700 flex items-center gap-2">
            <x-icon name="shield-check" class="h-3.5 w-3.5 text-green-600 shrink-0" />
            <span>{{ __('site.auth.otp_sent') }} <strong x-text="otpSentEmail"></strong>. {{ __('site.auth.otp_inbox') }}</span>
        </div>

        <form x-show="step === 1" x-on:submit.prevent="sendOtp" class="space-y-3">
            <div class="space-y-1">
                <label class="text-xs font-semibold text-foreground">{{ __('site.auth.full_name') }}</label>
                <div class="group relative">
                    <x-icon name="user" class="absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-muted-foreground transition-colors group-focus-within:text-primary" />
                    <input type="text" required x-model="name" placeholder="{{ __('site.auth.full_name_placeholder') }}" class="w-full rounded-xl border border-border bg-white py-2 pl-9 pr-3 text-xs text-foreground outline-none transition-colors placeholder:text-muted-foreground focus:border-primary" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-foreground">{{ __('site.auth.email') }}</label>
                    <div class="group relative">
                        <x-icon name="mail" class="absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-muted-foreground transition-colors group-focus-within:text-primary" />
                        <input type="email" required x-model="email" placeholder="name@company.com" class="w-full rounded-xl border border-border bg-white py-2 pl-9 pr-3 text-xs text-foreground outline-none transition-colors placeholder:text-muted-foreground focus:border-primary" />
                    </div>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-foreground">{{ __('site.auth.phone') }}</label>
                    <div class="group relative">
                        <x-icon name="phone" class="absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-muted-foreground transition-colors group-focus-within:text-primary" />
                        <input type="tel" required maxlength="10" x-model="phone" x-on:input="phone = phone.replace(/\D/g, '').slice(0, 10)" placeholder="{{ __('site.auth.phone_placeholder') }}" class="w-full rounded-xl border border-border bg-white py-2 pl-9 pr-3 text-xs text-foreground outline-none transition-colors placeholder:text-muted-foreground focus:border-primary" />
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-foreground">{{ __('site.auth.password') }}</label>
                    <div class="group relative">
                        <x-icon name="lock" class="absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-muted-foreground transition-colors group-focus-within:text-primary" />
                        <input :type="showPassword ? 'text' : 'password'" required x-model="password" placeholder="{{ __('site.auth.create_password_placeholder') }}" class="w-full rounded-xl border border-border bg-white py-2 pl-9 pr-8 text-xs text-foreground outline-none transition-colors placeholder:text-muted-foreground focus:border-primary" />
                        <button type="button" x-on:click="showPassword = !showPassword" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors p-0.5" tabindex="-1">
                            <x-icon name="eye" x-show="!showPassword" class="h-3.5 w-3.5" />
                            <x-icon name="eye-off" x-show="showPassword" class="h-3.5 w-3.5" />
                        </button>
                    </div>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-foreground">{{ __('site.auth.confirm_password') }}</label>
                    <div class="group relative">
                        <x-icon name="lock" class="absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-muted-foreground transition-colors group-focus-within:text-primary" />
                        <input :type="showConfirmPassword ? 'text' : 'password'" required x-model="confirmPassword" placeholder="{{ __('site.auth.confirm_password_placeholder') }}" class="w-full rounded-xl border border-border bg-white py-2 pl-9 pr-8 text-xs text-foreground outline-none transition-colors placeholder:text-muted-foreground focus:border-primary" />
                        <button type="button" x-on:click="showConfirmPassword = !showConfirmPassword" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors p-0.5" tabindex="-1">
                            <x-icon name="eye" x-show="!showConfirmPassword" class="h-3.5 w-3.5" />
                            <x-icon name="eye-off" x-show="showConfirmPassword" class="h-3.5 w-3.5" />
                        </button>
                    </div>
                </div>
            </div>

            <button type="submit" :disabled="loading" class="group inline-flex w-full items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60">
                <span x-text="loading ? '{{ __('site.auth.sending_otp') }}' : '{{ __('site.auth.send_otp_btn') }}'"></span>
                <x-icon name="arrow-right" x-show="!loading" class="h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5" />
            </button>
        </form>

        <form x-show="step === 2" x-cloak x-on:submit.prevent="verifyOtp" class="space-y-3">
            <div class="space-y-1">
                <label class="text-xs font-semibold text-foreground">{{ __('site.auth.otp_label') }}</label>
                <div class="group relative">
                    <x-icon name="key" class="absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-muted-foreground transition-colors group-focus-within:text-primary" />
                    <input type="text" required x-model="otp" maxlength="6" placeholder="{{ __('site.auth.otp_placeholder') }}" class="w-full rounded-xl border border-border bg-white py-2 pl-9 pr-3 text-xs text-foreground outline-none transition-colors placeholder:text-muted-foreground focus:border-primary tracking-[0.2em] font-mono text-center" />
                </div>
            </div>

            <div class="flex gap-2">
                <button type="button" x-on:click="step = 1" class="flex-1 rounded-xl border border-border bg-white px-3 py-2 text-xs font-semibold text-foreground transition-colors hover:bg-surface text-center">
                    {{ __('site.auth.back_btn') }}
                </button>
                <button type="submit" :disabled="loading" class="flex-[2] inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60">
                    <span x-text="loading ? '{{ __('site.auth.verifying') }}' : '{{ __('site.auth.verify_register') }}'"></span>
                </button>
            </div>
        </form>

        <p class="mt-4 text-center text-xs text-muted">
            {{ __('site.auth.have_account') }}
            <button x-on:click="$store.auth.closeRegister(); $store.auth.openLogin()" class="font-semibold text-primary hover:opacity-80 transition-opacity">
                {{ __('site.auth.log_in') }}
            </button>
        </p>
    </div>
</div>
