<div x-show="$store.auth.loginOpen" x-cloak x-data="{
        email: '',
        password: '',
        showPassword: false,
        error: '',
        loading: false,
        reset() {
            this.email = '';
            this.password = '';
            this.showPassword = false;
            this.error = '';
            this.loading = false;
        },
        close() {
            this.reset();
            $store.auth.closeLogin();
        },
        async submit() {
            this.error = '';
            this.loading = true;
            try {
                const res = await fetch('/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify({ email: this.email, password: this.password }),
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || '{{ __('site.auth.login_btn') }}');
                window.location.reload();
            } catch (e) {
                this.error = e.message;
            } finally {
                this.loading = false;
            }
        },
    }" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" x-on:click="close"></div>

    <div x-show="$store.auth.loginOpen" x-transition
        class="relative w-full max-w-sm rounded-2xl border border-border bg-white p-5 shadow-xl">
        <button x-on:click="close"
            class="absolute right-3 top-3 rounded-lg p-1 text-muted transition-colors hover:bg-surface hover:text-foreground"
            aria-label="Close">
            <x-icon name="x" class="h-4 w-4" />
        </button>

        <div class="mb-4 text-center">
            <img src="{{ asset('logo2.png') }}" alt="SABHA" class="mx-auto h-11 w-11 object-contain mb-2.5" />
            <h2 class="text-base font-bold text-foreground">{{ __('site.auth.login_title') }}</h2>
            <p class="mt-0.5 text-xs text-muted">{{ __('site.auth.login_subtitle') }}</p>
        </div>

        <div x-show="error" x-cloak
            class="mb-3 rounded-xl bg-red-50 border border-red-100 p-2.5 text-center text-xs font-semibold text-red-600"
            x-text="error"></div>

        <form x-on:submit.prevent="submit" class="space-y-3">
            <div class="space-y-1">
                <label class="text-xs font-semibold text-foreground">{{ __('site.auth.email') }}</label>
                <div class="group relative">
                    <x-icon name="mail"
                        class="absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-muted-foreground transition-colors group-focus-within:text-primary" />
                    <input type="email" required x-model="email" placeholder="{{ __('site.auth.email_placeholder') }}"
                        class="w-full rounded-xl border border-border bg-white py-2 pl-9 pr-3 text-xs text-foreground outline-none transition-colors placeholder:text-muted-foreground focus:border-primary" />
                </div>
            </div>

            <div class="space-y-1">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-semibold text-foreground">{{ __('site.auth.password') }}</label>
                    <a href="/forgot-password" x-on:click="$store.auth.closeLogin()"
                        class="text-[12px] font-semibold text-primary hover:opacity-80 transition-opacity">
                        {{ __('site.auth.forgot_password') }}
                    </a>
                </div>
                <div class="group relative">
                    <x-icon name="lock"
                        class="absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-muted-foreground transition-colors group-focus-within:text-primary" />
                    <input :type="showPassword ? 'text' : 'password'" required x-model="password"
                        placeholder="{{ __('site.auth.password_placeholder') }}"
                        class="w-full rounded-xl border border-border bg-white py-2 pl-9 pr-9 text-xs text-foreground outline-none transition-colors placeholder:text-muted-foreground focus:border-primary" />
                    <button type="button" x-on:click="showPassword = !showPassword"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors p-0.5"
                        tabindex="-1">
                        <x-icon name="eye" x-show="!showPassword" class="h-3.5 w-3.5" />
                        <x-icon name="eye-off" x-show="showPassword" class="h-3.5 w-3.5" />
                    </button>
                </div>
            </div>

            <button type="submit" :disabled="loading"
                class="group inline-flex w-full items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60">
                <span x-text="loading ? '{{ __('site.auth.logging_in') }}' : '{{ __('site.auth.login_btn') }}'"></span>
                <x-icon name="arrow-right" x-show="!loading"
                    class="h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5" />
            </button>
        </form>

        <p class="mt-4 text-center text-xs text-muted">
            {{ __('site.auth.no_account') }}
            <a href="/register" class="font-semibold text-primary hover:opacity-80 transition-opacity">
                {{ __('site.auth.create_one') }}
            </a>
        </p>
    </div>
</div>