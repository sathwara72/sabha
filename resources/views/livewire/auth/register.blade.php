@php
    $inputClass = 'w-full rounded-xl border border-border bg-white pl-11 pr-4 py-3 text-sm text-foreground outline-none placeholder:text-muted-foreground focus:border-primary transition-colors';
@endphp

<div class="min-h-screen flex items-center justify-center bg-background font-outfit px-6 pt-28 pb-16">
    <div class="w-full max-w-2xl">
        <div class="text-center mb-8">
            <a href="/" class="inline-flex items-center gap-2.5 mb-8 group">
                <img src="{{ asset('logo.png') }}" alt="SABHA" class="h-12 w-12 rounded-full object-contain" />
                <span class="text-2xl font-bold tracking-tight text-primary-dark">SABHA</span>
            </a>
        </div>

        <div class="bg-white border border-border rounded-2xl shadow-sm p-8 md:p-10">

            {{-- ===================== Just submitted step 1 (guest) ===================== --}}
            @if ($justSubmitted)
                <div class="text-center py-6 space-y-4">
                    <div class="mx-auto h-16 w-16 rounded-full bg-amber-50 flex items-center justify-center">
                        <x-icon name="clock" class="h-8 w-8 text-amber-600" />
                    </div>
                    <h1 class="text-2xl font-bold text-foreground">Application Submitted!</h1>
                    <p class="text-sm text-muted max-w-md mx-auto">
                        Your registration is now pending admin review. We'll send you an email as soon as it's approved, along with instructions for the next step.
                    </p>
                    <a href="/" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:opacity-90">
                        Back to Home
                    </a>
                </div>

            {{-- ===================== Guest: step 1 form / OTP ===================== --}}
            @elseif (! auth()->check())
                <div class="text-center mb-6">
                    <h1 class="text-3xl sm:text-4xl font-bold text-foreground mb-2">{{ $step1Stage === 'form' ? 'Create your account' : 'Verify your email' }}</h1>
                    <p class="text-sm text-muted">
                        @if ($step1Stage === 'form')
                            Join the community — applications are reviewed by our admin team
                        @else
                            Enter the 6-digit verification code sent to {{ $email }}
                        @endif
                    </p>
                </div>

                @if ($step1Error)
                    <div class="mb-6 rounded-xl bg-red-50 border border-red-100 p-4 text-center text-sm font-semibold text-red-600">{{ $step1Error }}</div>
                @endif
                @if ($step1Stage === 'otp' && $otpSentEmail)
                    <div class="mb-6 rounded-xl bg-green-50 border border-green-100 p-4 text-sm font-semibold text-green-700 flex items-center gap-2.5">
                        <x-icon name="shield-check" class="h-5 w-5 text-green-600 shrink-0" />
                        <span>A 6-digit OTP has been sent to <strong>{{ $otpSentEmail }}</strong>. Please check your inbox.</span>
                    </div>
                @endif

                @if ($step1Stage === 'form')
                    <form wire:submit="sendOtp" class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-2 md:col-span-2">
                            <label class="text-sm font-medium text-foreground">Full name</label>
                            <div class="relative group">
                                <x-icon name="user" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground group-focus-within:text-primary transition-colors" />
                                <input type="text" wire:model="name" placeholder="E.g. John Doe" class="{{ $inputClass }}" />
                            </div>
                            @error('name') <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-foreground">Email</label>
                            <div class="relative group">
                                <x-icon name="mail" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground group-focus-within:text-primary transition-colors" />
                                <input type="email" wire:model="email" placeholder="name@company.com" class="{{ $inputClass }}" />
                            </div>
                            @error('email') <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-foreground">Phone</label>
                            <div class="relative group">
                                <x-icon name="phone" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground group-focus-within:text-primary transition-colors" />
                                <input type="tel" maxlength="10" wire:model.live="phone" placeholder="10-digit mobile number" class="{{ $inputClass }}" />
                            </div>
                            @error('phone') <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-foreground">Password</label>
                            <div class="relative group">
                                <x-icon name="lock" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground group-focus-within:text-primary transition-colors" />
                                <input type="password" wire:model="password" placeholder="Create a password" class="{{ $inputClass }}" />
                            </div>
                            @error('password') <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-foreground">Confirm password</label>
                            <div class="relative group">
                                <x-icon name="lock" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground group-focus-within:text-primary transition-colors" />
                                <input type="password" wire:model="confirmPassword" placeholder="Re-enter your password" class="{{ $inputClass }}" />
                            </div>
                            @error('confirmPassword') <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2 border-t border-border pt-5 mt-1">
                            <p class="text-sm font-bold text-foreground mb-1">References</p>
                            <p class="text-xs text-muted mb-4">Two people in the community who can vouch for you</p>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-foreground">Reference 1 — Name</label>
                            <div class="relative group">
                                <x-icon name="user" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground group-focus-within:text-primary transition-colors" />
                                <input type="text" wire:model="ref1Name" placeholder="Reference name" class="{{ $inputClass }}" />
                            </div>
                            @error('ref1Name') <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-foreground">Reference 1 — Phone</label>
                            <div class="relative group">
                                <x-icon name="phone" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground group-focus-within:text-primary transition-colors" />
                                <input type="tel" maxlength="10" wire:model.live="ref1Phone" placeholder="10-digit mobile number" class="{{ $inputClass }}" />
                            </div>
                            @error('ref1Phone') <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-foreground">Reference 2 — Name</label>
                            <div class="relative group">
                                <x-icon name="user" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground group-focus-within:text-primary transition-colors" />
                                <input type="text" wire:model="ref2Name" placeholder="Reference name" class="{{ $inputClass }}" />
                            </div>
                            @error('ref2Name') <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-foreground">Reference 2 — Phone</label>
                            <div class="relative group">
                                <x-icon name="phone" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground group-focus-within:text-primary transition-colors" />
                                <input type="tel" maxlength="10" wire:model.live="ref2Phone" placeholder="10-digit mobile number" class="{{ $inputClass }}" />
                            </div>
                            @error('ref2Phone') <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2 pt-2">
                            <button type="submit" wire:loading.attr="disabled" wire:target="sendOtp" class="group w-full inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60">
                                <span wire:loading.remove wire:target="sendOtp">Create account</span>
                                <span wire:loading wire:target="sendOtp">Sending verification code...</span>
                                <x-icon name="arrow-right" class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" wire:loading.remove wire:target="sendOtp" />
                            </button>
                        </div>
                    </form>
                @else
                    <form wire:submit="verifyOtp" class="space-y-6 max-w-md mx-auto">
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-foreground">Enter the 6-Digit Code (OTP)</label>
                            <div class="relative group">
                                <x-icon name="key" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground group-focus-within:text-primary transition-colors" />
                                <input type="text" wire:model="otp" maxlength="6" placeholder="E.g. 123456" class="{{ $inputClass }} tracking-[0.2em] font-mono text-center text-lg" />
                            </div>
                            @error('otp') <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex gap-4 pt-2">
                            <button type="button" wire:click="backToForm" class="flex-1 rounded-xl border border-border bg-white px-5 py-3.5 text-sm font-semibold text-foreground transition-colors hover:bg-surface text-center">
                                Go Back
                            </button>
                            <button type="submit" wire:loading.attr="disabled" wire:target="verifyOtp" class="flex-[2] inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-6 py-3.5 text-sm font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-60">
                                <span wire:loading.remove wire:target="verifyOtp">Verify & Submit Application</span>
                                <span wire:loading wire:target="verifyOtp">Verifying...</span>
                            </button>
                        </div>
                    </form>
                @endif

                <p class="mt-8 text-center text-sm text-muted">
                    Already have an account?
                    <a href="/?login=1" class="font-semibold text-primary hover:opacity-80 transition-opacity">Log in</a>
                </p>

            {{-- ===================== Logged in: pending payment review ===================== --}}
            @elseif (auth()->user()->registration_status === 'pending_payment_review')
                <div class="text-center py-6 space-y-4">
                    <div class="mx-auto h-16 w-16 rounded-full bg-amber-50 flex items-center justify-center">
                        <x-icon name="clock" class="h-8 w-8 text-amber-600" />
                    </div>
                    <h1 class="text-2xl font-bold text-foreground">Your Payment is Under Review</h1>
                    <p class="text-sm text-muted max-w-md mx-auto">
                        We've received your documents and payment screenshot. Our team is verifying them now — you'll receive an email as soon as your membership is confirmed.
                    </p>
                </div>

            {{-- ===================== Logged in: step 2 (documents + payment) ===================== --}}
            @else
                @php $user = auth()->user(); @endphp
                <div class="text-center mb-6">
                    <h1 class="text-2xl sm:text-3xl font-bold text-foreground mb-2">Complete Your Membership</h1>
                    <p class="text-sm text-muted">Upload your documents and payment screenshot to activate your account</p>
                </div>

                @if ($user->registration_status === 'payment_rejected' && $user->registration_rejection_reason)
                    <div class="mb-6 rounded-xl bg-red-50 border border-red-100 p-4 flex items-start gap-3">
                        <x-icon name="x-circle" class="h-5 w-5 text-red-600 shrink-0 mt-0.5" />
                        <div>
                            <p class="font-bold text-red-800 text-sm">Your previous submission was not approved</p>
                            <p class="text-xs mt-1 text-red-700 font-medium">Reason: {{ $user->registration_rejection_reason }}</p>
                            <p class="text-xs mt-1 text-red-700">Please review and resubmit below.</p>
                        </div>
                    </div>
                @endif

                @if ($step2Error)
                    <div class="mb-6 rounded-xl bg-red-50 border border-red-100 p-4 text-center text-sm font-semibold text-red-600">{{ $step2Error }}</div>
                @endif
                @if ($step2Success)
                    <div class="mb-6 rounded-xl bg-green-50 border border-green-100 p-4 text-center text-sm font-semibold text-green-700">{{ $step2Success }}</div>
                @endif

                @if ($qrUpiImage)
                    <div class="mb-6 rounded-xl border border-primary/20 bg-primary-soft/30 p-5 text-center">
                        <p class="text-sm font-bold text-foreground mb-3">Scan to Pay Membership Fee</p>
                        <img src="{{ $qrUpiImage }}" alt="Payment QR / UPI" class="mx-auto h-48 w-48 object-contain rounded-xl border border-border bg-white p-2" />
                        <p class="text-xs text-muted mt-3">Take a screenshot of your payment confirmation and upload it below</p>
                    </div>
                @endif

                <form wire:submit="submitStep2" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-foreground">Aadhar Card</label>
                            <label class="flex flex-col items-center justify-center border-2 border-dashed border-border rounded-xl p-4 bg-surface/35 hover:bg-surface/65 transition-colors cursor-pointer relative min-h-[120px]">
                                <input type="file" accept="image/*,.pdf" wire:model="aadharFile" class="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10" />
                                @if ($aadharFile)
                                    <img src="{{ $aadharFile->temporaryUrl() }}" class="h-16 w-16 rounded-lg object-cover" />
                                    <span class="text-xs font-bold text-emerald-600 mt-1">Selected ✓</span>
                                @elseif ($aadharPreview)
                                    <img src="{{ $aadharPreview }}" class="h-16 w-16 rounded-lg object-cover" />
                                    <span class="text-[12px] text-muted mt-1">Previously submitted — click to change</span>
                                @else
                                    <x-icon name="upload" class="h-6 w-6 text-primary mb-1" />
                                    <span class="text-xs font-semibold text-foreground">Click to upload</span>
                                @endif
                            </label>
                            @error('aadharFile') <p class="text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-foreground">PAN Card</label>
                            <label class="flex flex-col items-center justify-center border-2 border-dashed border-border rounded-xl p-4 bg-surface/35 hover:bg-surface/65 transition-colors cursor-pointer relative min-h-[120px]">
                                <input type="file" accept="image/*,.pdf" wire:model="panFile" class="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10" />
                                @if ($panFile)
                                    <img src="{{ $panFile->temporaryUrl() }}" class="h-16 w-16 rounded-lg object-cover" />
                                    <span class="text-xs font-bold text-emerald-600 mt-1">Selected ✓</span>
                                @elseif ($panPreview)
                                    <img src="{{ $panPreview }}" class="h-16 w-16 rounded-lg object-cover" />
                                    <span class="text-[12px] text-muted mt-1">Previously submitted — click to change</span>
                                @else
                                    <x-icon name="upload" class="h-6 w-6 text-primary mb-1" />
                                    <span class="text-xs font-semibold text-foreground">Click to upload</span>
                                @endif
                            </label>
                            @error('panFile') <p class="text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-foreground">Business Proof Document</label>
                        <div class="flex gap-2 mb-2">
                            @foreach (\App\Livewire\Auth\Register::BUSINESS_DOCUMENT_TYPES as $type)
                                <button
                                    type="button"
                                    wire:click="$set('businessDocumentType', '{{ $type }}')"
                                    class="flex-1 rounded-xl border px-3 py-2 text-xs font-bold uppercase tracking-wide transition-all cursor-pointer {{ $businessDocumentType === $type ? 'bg-primary text-white border-primary' : 'border-border text-slate-500 hover:bg-slate-50' }}"
                                >{{ str_replace('_', ' ', $type) }}</button>
                            @endforeach
                        </div>
                        <label class="flex flex-col items-center justify-center border-2 border-dashed border-border rounded-xl p-4 bg-surface/35 hover:bg-surface/65 transition-colors cursor-pointer relative min-h-[120px]">
                            <input type="file" accept="image/*,.pdf" wire:model="businessDocumentFile" class="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10" />
                            @if ($businessDocumentFile)
                                <img src="{{ $businessDocumentFile->temporaryUrl() }}" class="h-16 w-16 rounded-lg object-cover" />
                                <span class="text-xs font-bold text-emerald-600 mt-1">Selected ✓</span>
                            @elseif ($businessDocumentPreview)
                                <img src="{{ $businessDocumentPreview }}" class="h-16 w-16 rounded-lg object-cover" />
                                <span class="text-[12px] text-muted mt-1">Previously submitted — click to change</span>
                            @else
                                <x-icon name="upload" class="h-6 w-6 text-primary mb-1" />
                                <span class="text-xs font-semibold text-foreground">Click to upload</span>
                            @endif
                        </label>
                        @error('businessDocumentFile') <p class="text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2 border-t border-border pt-5">
                        <label class="text-sm font-medium text-foreground">Payment Screenshot</label>
                        <label class="flex flex-col items-center justify-center border-2 border-dashed border-primary/30 rounded-xl p-4 bg-primary-soft/20 hover:bg-primary-soft/30 transition-colors cursor-pointer relative min-h-[120px]">
                            <input type="file" accept="image/*,.pdf" wire:model="paymentScreenshotFile" class="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10" />
                            @if ($paymentScreenshotFile)
                                <img src="{{ $paymentScreenshotFile->temporaryUrl() }}" class="h-16 w-16 rounded-lg object-cover" />
                                <span class="text-xs font-bold text-emerald-600 mt-1">Selected ✓</span>
                            @elseif ($paymentScreenshotPreview)
                                <img src="{{ $paymentScreenshotPreview }}" class="h-16 w-16 rounded-lg object-cover" />
                                <span class="text-[12px] text-muted mt-1">Previously submitted — click to change</span>
                            @else
                                <x-icon name="upload" class="h-6 w-6 text-primary mb-1" />
                                <span class="text-xs font-semibold text-foreground">Click to upload</span>
                            @endif
                        </label>
                        @error('paymentScreenshotFile') <p class="text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" wire:loading.attr="disabled" wire:target="submitStep2" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-white shadow-sm transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-50">
                        <span wire:loading.remove wire:target="submitStep2">Submit for Review</span>
                        <span wire:loading wire:target="submitStep2">Submitting...</span>
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
