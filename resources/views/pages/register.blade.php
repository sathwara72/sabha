<x-layouts.app :title="'Create account | Sabha'" :description="__('site.auth.register_subtitle')">
    <div
        class="min-h-screen flex items-center justify-center bg-background font-outfit px-6 pt-28 pb-16"
        x-data="{
            step: 1,
            name: '', email: '', phone: '', password: '', confirmPassword: '',
            showPassword: false, showConfirmPassword: false, otp: '',
            error: '', loading: false, otpSentEmail: '',
            async sendOtp() {
                this.error = '';
                if (this.password !== this.confirmPassword) {
                    this.error = 'Passwords do not match!';
                    return;
                }
                if (this.phone.length !== 10) {
                    this.error = 'Please enter a valid 10-digit mobile number.';
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
                        throw new Error((firstField && data.errors[firstField]?.[0]) || data.message || 'Registration validation failed. Try again.');
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
                    if (!res.ok) throw new Error(data.message || 'Invalid or expired OTP. Please try again.');
                    window.location.href = '/profile';
                } catch (e) {
                    this.error = e.message;
                } finally {
                    this.loading = false;
                }
            },
        }"
    >
        <div class="w-full max-w-2xl">
            <div class="text-center mb-8">
                <a href="/" class="inline-flex items-center gap-2.5 mb-8 group">
                    <img src="{{ asset('logo.png') }}" alt="SABHA" class="h-12 w-12 rounded-full object-contain" />
                    <span class="text-2xl font-bold tracking-tight text-primary-dark">SABHA</span>
                </a>
                <h1 class="text-3xl sm:text-4xl font-bold text-foreground mb-2" x-text="step === 1 ? 'Create your account' : 'Verify your email'"></h1>
                <p class="text-sm text-muted">
                    <span x-show="step === 1">Join the community and list your business</span>
                    <span x-show="step === 2">Enter the 6-digit verification code sent to <span x-text="email"></span></span>
                </p>
            </div>

            <div class="bg-white border border-border rounded-2xl shadow-sm p-8 md:p-10">
                <div x-show="error" x-cloak class="mb-6 rounded-xl bg-red-50 border border-red-100 p-4 text-center text-sm font-semibold text-red-600" x-text="error"></div>
                <div x-show="step === 2 && otpSentEmail" x-cloak class="mb-6 rounded-xl bg-green-50 border border-green-100 p-4 text-sm font-semibold text-green-700 flex items-center gap-2.5">
                    <x-icon name="shield-check" class="h-5 w-5 text-green-600 shrink-0" />
                    <span>A 6-digit OTP has been sent to <strong x-text="otpSentEmail"></strong>. Please check your inbox.</span>
                </div>

                <form x-show="step === 1" x-on:submit.prevent="sendOtp" class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-2 md:col-span-2">
                        <label class="text-sm font-medium text-foreground">Full name or business name</label>
                        <div class="relative group">
                            <x-icon name="user" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground group-focus-within:text-primary transition-colors" />
                            <input type="text" required x-model="name" placeholder="E.g. John Doe / Acme Corp" class="w-full rounded-xl border border-border bg-white pl-11 pr-4 py-3 text-sm text-foreground outline-none placeholder:text-muted-foreground focus:border-primary transition-colors" />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-foreground">Email</label>
                        <div class="relative group">
                            <x-icon name="mail" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground group-focus-within:text-primary transition-colors" />
                            <input type="email" required x-model="email" placeholder="name@company.com" class="w-full rounded-xl border border-border bg-white pl-11 pr-4 py-3 text-sm text-foreground outline-none placeholder:text-muted-foreground focus:border-primary transition-colors" />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-foreground">Phone</label>
                        <div class="relative group">
                            <x-icon name="phone" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground group-focus-within:text-primary transition-colors" />
                            <input type="tel" required maxlength="10" x-model="phone" x-on:input="phone = phone.replace(/\D/g, '').slice(0, 10)" placeholder="10-digit mobile number" class="w-full rounded-xl border border-border bg-white pl-11 pr-4 py-3 text-sm text-foreground outline-none placeholder:text-muted-foreground focus:border-primary transition-colors" />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-foreground">Password</label>
                        <div class="relative group">
                            <x-icon name="lock" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground group-focus-within:text-primary transition-colors" />
                            <input :type="showPassword ? 'text' : 'password'" required x-model="password" placeholder="Create a password" class="w-full rounded-xl border border-border bg-white pl-11 pr-11 py-3 text-sm text-foreground outline-none placeholder:text-muted-foreground focus:border-primary transition-colors" />
                            <button type="button" x-on:click="showPassword = !showPassword" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors p-1" tabindex="-1">
                                <x-icon name="eye" x-show="!showPassword" class="w-5 h-5" />
                                <x-icon name="eye-off" x-show="showPassword" class="w-5 h-5" />
                            </button>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-foreground">Confirm password</label>
                        <div class="relative group">
                            <x-icon name="lock" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground group-focus-within:text-primary transition-colors" />
                            <input :type="showConfirmPassword ? 'text' : 'password'" required x-model="confirmPassword" placeholder="Re-enter your password" class="w-full rounded-xl border border-border bg-white pl-11 pr-11 py-3 text-sm text-foreground outline-none placeholder:text-muted-foreground focus:border-primary transition-colors" />
                            <button type="button" x-on:click="showConfirmPassword = !showConfirmPassword" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors p-1" tabindex="-1">
                                <x-icon name="eye" x-show="!showConfirmPassword" class="w-5 h-5" />
                                <x-icon name="eye-off" x-show="showConfirmPassword" class="w-5 h-5" />
                            </button>
                        </div>
                    </div>

                    <div class="md:col-span-2 pt-2">
                        <button type="submit" :disabled="loading" class="group w-full inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60">
                            <span x-text="loading ? 'Sending Verification code...' : 'Create account'"></span>
                            <x-icon name="arrow-right" x-show="!loading" class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" />
                        </button>
                    </div>
                </form>

                <form x-show="step === 2" x-cloak x-on:submit.prevent="verifyOtp" class="space-y-6 max-w-md mx-auto">
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-foreground">Enter the 6-Digit Code (OTP)</label>
                        <div class="relative group">
                            <x-icon name="key" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground group-focus-within:text-primary transition-colors" />
                            <input type="text" required x-model="otp" maxlength="6" placeholder="E.g. 123456" class="w-full rounded-xl border border-border bg-white pl-11 pr-4 py-3 text-sm text-foreground outline-none placeholder:text-muted-foreground focus:border-primary transition-colors tracking-[0.2em] font-mono text-center text-lg" />
                        </div>
                    </div>

                    <div class="flex gap-4 pt-2">
                        <button type="button" x-on:click="step = 1" class="flex-1 rounded-xl border border-border bg-white px-5 py-3.5 text-sm font-semibold text-foreground transition-colors hover:bg-surface text-center">
                            Go Back
                        </button>
                        <button type="submit" :disabled="loading" class="flex-[2] inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-6 py-3.5 text-sm font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60">
                            <span x-text="loading ? 'Verifying...' : 'Verify & Complete'"></span>
                        </button>
                    </div>
                </form>

                <p class="mt-8 text-center text-sm text-muted">
                    Already have an account?
                    <a href="/?login=1" class="font-semibold text-primary hover:opacity-80 transition-opacity">Log in</a>
                </p>
            </div>

            <div class="mt-8 flex justify-center gap-8 text-sm text-muted">
                <span>Business focused</span>
                <span>Community driven</span>
            </div>
        </div>
    </div>
</x-layouts.app>
