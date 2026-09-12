@php
    $inputClass = 'w-full rounded-xl border border-slate-200 bg-slate-50/50 py-2.5 pl-10 pr-4 text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 outline-none transition-all focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10';
    $locale = app()->getLocale();
@endphp

<div class="w-full">
    {{-- ===================== Just submitted step 1 (guest) ===================== --}}
    @if ($justSubmitted)
        <div class="rounded-2xl border border-slate-200/90 bg-white p-6 sm:p-8 shadow-xl shadow-slate-200/40 text-center space-y-4">
            <div class="mx-auto h-16 w-16 rounded-2xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 shadow-sm">
                <x-icon name="clock" class="h-8 w-8" />
            </div>
            <div>
                <h1 class="text-2xl font-black text-slate-900">{{ __('site.auth.app_submitted_title') }}</h1>
                <p class="mt-2 text-xs sm:text-sm text-slate-500 max-w-md mx-auto leading-relaxed font-medium">
                    {{ __('site.auth.app_submitted_desc') }}
                </p>
            </div>
            <div class="pt-2">
                <a href="/" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#00379D] to-[#082e6e] px-6 py-3 text-xs sm:text-sm font-bold text-white shadow-md hover:opacity-95 transition-all">
                    <span>{{ __('site.auth.back_to_home') }}</span>
                    <x-icon name="arrow-right" class="h-4 w-4" />
                </a>
            </div>
        </div>

    {{-- ===================== Guest: step 1 form / OTP ===================== --}}
    @elseif (! auth()->check())
        {{-- Card Header --}}
        <div class="mb-4">
            <div class="inline-flex items-center gap-1.5 rounded-full bg-primary/10 px-2.5 py-0.5 text-[11px] font-bold text-primary mb-1.5">
                <span class="h-1.5 w-1.5 rounded-full bg-primary animate-pulse"></span>
                <span>{{ __('site.login_view.portal_badge') }}</span>
            </div>
            <h2 class="text-2xl xl:text-[26px] font-black tracking-tight text-slate-900">
                {{ $step1Stage === 'form' ? __('site.auth.register_title') : __('site.auth.otp_title') }}
            </h2>
            <p class="mt-0.5 text-xs text-slate-500 leading-relaxed font-medium">
                @if ($step1Stage === 'form')
                    {{ __('site.auth.join_community_subtitle') }}
                @else
                    {{ __('site.auth.otp_instruction') }} <strong class="text-slate-800">{{ $email }}</strong>
                @endif
            </p>
        </div>

        {{-- Error Banner --}}
        @if ($step1Error)
            <div class="mb-4 flex items-start gap-2.5 rounded-xl bg-red-50 border border-red-200/80 p-3 text-xs font-semibold text-red-700 shadow-sm">
                <x-icon name="alert-circle" class="h-4 w-4 text-red-600 shrink-0 mt-0.5" />
                <span class="leading-relaxed">{{ $step1Error }}</span>
            </div>
        @endif

        @if ($step1Stage === 'otp' && $otpSentEmail)
            <div class="mb-4 flex items-start gap-2.5 rounded-xl bg-emerald-50 border border-emerald-200/80 p-3 text-xs font-semibold text-emerald-800 shadow-sm">
                <x-icon name="shield-check" class="h-4 w-4 text-emerald-600 shrink-0 mt-0.5" />
                <span>{{ __('site.auth.otp_sent') }} <strong>{{ $otpSentEmail }}</strong>. {{ __('site.auth.otp_inbox') }}</span>
            </div>
        @endif

        {{-- Floating Form Card --}}
        <div class="rounded-2xl border border-slate-200/90 bg-white p-5 sm:p-6 shadow-xl shadow-slate-200/40">
            @if ($step1Stage === 'form')
                <form wire:submit="sendOtp" class="space-y-3.5">
                    {{-- Full Name --}}
                    <div class="space-y-1">
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                            {{ __('site.auth.full_name') }}
                        </label>
                        <div class="relative group">
                            <x-icon name="user" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400 transition-colors group-focus-within:text-primary" />
                            <input type="text" wire:model="name" placeholder="{{ __('site.auth.full_name_placeholder') }}" class="{{ $inputClass }}" />
                        </div>
                        @error('name') <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Email and Phone --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                                {{ __('site.auth.email') }}
                            </label>
                            <div class="relative group">
                                <x-icon name="mail" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400 transition-colors group-focus-within:text-primary" />
                                <input type="email" wire:model="email" placeholder="{{ __('site.auth.email_placeholder') }}" class="{{ $inputClass }}" />
                            </div>
                            @error('email') <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                                {{ __('site.auth.phone') }}
                            </label>
                            <div class="relative group">
                                <x-icon name="phone" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400 transition-colors group-focus-within:text-primary" />
                                <input type="tel" maxlength="10" wire:model.live="phone" placeholder="{{ __('site.auth.phone_placeholder') }}" class="{{ $inputClass }}" />
                            </div>
                            @error('phone') <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Password and Confirm Password --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                                {{ __('site.auth.password') }}
                            </label>
                            <div class="relative group">
                                <x-icon name="lock" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400 transition-colors group-focus-within:text-primary" />
                                <input type="password" wire:model="password" placeholder="{{ __('site.auth.create_password_placeholder') }}" class="{{ $inputClass }}" />
                            </div>
                            @error('password') <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                                {{ __('site.auth.confirm_password') }}
                            </label>
                            <div class="relative group">
                                <x-icon name="lock" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400 transition-colors group-focus-within:text-primary" />
                                <input type="password" wire:model="confirmPassword" placeholder="{{ __('site.auth.confirm_password_placeholder') }}" class="{{ $inputClass }}" />
                            </div>
                            @error('confirmPassword') <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- References Section --}}
                    <div class="border-t border-slate-100 pt-3 mt-2">
                        <div class="mb-2">
                            <h4 class="text-xs font-bold text-slate-900">{{ __('site.auth.references') }}</h4>
                            <p class="text-[11px] text-slate-500 font-medium">{{ __('site.auth.references_desc') }}</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <div class="space-y-1">
                                <label class="block text-[10px] font-bold text-slate-600 uppercase">{{ __('site.auth.ref_1_name') }}</label>
                                <div class="relative group">
                                    <x-icon name="user" class="absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400" />
                                    <input type="text" wire:model="ref1Name" placeholder="{{ __('site.auth.ref_name_placeholder') }}" class="w-full rounded-lg border border-slate-200 bg-slate-50/50 py-2 pl-9 pr-3 text-xs text-slate-900 outline-none focus:border-primary focus:bg-white" />
                                </div>
                                @error('ref1Name') <p class="text-[11px] font-semibold text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="block text-[10px] font-bold text-slate-600 uppercase">{{ __('site.auth.ref_1_phone') }}</label>
                                <div class="relative group">
                                    <x-icon name="phone" class="absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400" />
                                    <input type="tel" maxlength="10" wire:model.live="ref1Phone" placeholder="10-digit mobile" class="w-full rounded-lg border border-slate-200 bg-slate-50/50 py-2 pl-9 pr-3 text-xs text-slate-900 outline-none focus:border-primary focus:bg-white" />
                                </div>
                                @error('ref1Phone') <p class="text-[11px] font-semibold text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="block text-[10px] font-bold text-slate-600 uppercase">{{ __('site.auth.ref_2_name') }}</label>
                                <div class="relative group">
                                    <x-icon name="user" class="absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400" />
                                    <input type="text" wire:model="ref2Name" placeholder="{{ __('site.auth.ref_name_placeholder') }}" class="w-full rounded-lg border border-slate-200 bg-slate-50/50 py-2 pl-9 pr-3 text-xs text-slate-900 outline-none focus:border-primary focus:bg-white" />
                                </div>
                                @error('ref2Name') <p class="text-[11px] font-semibold text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="block text-[10px] font-bold text-slate-600 uppercase">{{ __('site.auth.ref_2_phone') }}</label>
                                <div class="relative group">
                                    <x-icon name="phone" class="absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400" />
                                    <input type="tel" maxlength="10" wire:model.live="ref2Phone" placeholder="10-digit mobile" class="w-full rounded-lg border border-slate-200 bg-slate-50/50 py-2 pl-9 pr-3 text-xs text-slate-900 outline-none focus:border-primary focus:bg-white" />
                                </div>
                                @error('ref2Phone') <p class="text-[11px] font-semibold text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Submit CTA --}}
                    <div class="pt-2">
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="sendOtp"
                            class="group relative inline-flex w-full items-center justify-center gap-2 overflow-hidden rounded-xl bg-gradient-to-r from-[#00379D] via-[#082e6e] to-[#0F3459] px-5 py-3 text-xs sm:text-sm font-extrabold text-white shadow-md shadow-primary/25 transition-all duration-200 hover:shadow-lg hover:shadow-primary/30 hover:opacity-95 active:scale-[0.99] disabled:opacity-60 cursor-pointer"
                        >
                            <span wire:loading.remove wire:target="sendOtp">{{ __('site.auth.send_otp_btn') }}</span>
                            <span wire:loading wire:target="sendOtp">{{ __('site.auth.sending_otp') }}</span>
                            <x-icon name="arrow-right" class="h-4 w-4 transition-transform group-hover:translate-x-1" wire:loading.remove wire:target="sendOtp" />
                        </button>
                    </div>
                </form>
            @else
                {{-- OTP Verification Screen --}}
                <form wire:submit="verifyOtp" class="space-y-4 max-w-sm mx-auto py-2">
                    <div class="space-y-1.5 text-center">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            {{ __('site.auth.otp_label') }}
                        </label>
                        <div class="relative group">
                            <x-icon name="key" class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
                            <input
                                type="text"
                                wire:model="otp"
                                maxlength="6"
                                placeholder="{{ __('site.auth.otp_placeholder') }}"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-3 pl-10 pr-4 text-center font-mono text-lg font-bold tracking-[0.25em] text-slate-900 outline-none focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10"
                            />
                        </div>
                        @error('otp') <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-2.5 pt-2">
                        <button
                            type="button"
                            wire:click="backToForm"
                            class="flex-1 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 transition-colors hover:bg-slate-50 text-center"
                        >
                            {{ __('site.auth.go_back_btn') }}
                        </button>
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="verifyOtp"
                            class="flex-[2] inline-flex items-center justify-center gap-1.5 rounded-xl bg-gradient-to-r from-[#00379D] to-[#082e6e] px-4 py-2.5 text-xs font-bold text-white shadow-md hover:opacity-95 active:scale-[0.99] disabled:opacity-60"
                        >
                            <span wire:loading.remove wire:target="verifyOtp">{{ __('site.auth.verify_submit_btn') }}</span>
                            <span wire:loading wire:target="verifyOtp">{{ __('site.auth.verifying') }}</span>
                        </button>
                    </div>
                </form>
            @endif

            {{-- Already Have Account --}}
            <div class="mt-4 pt-3.5 border-t border-slate-100 text-center">
                <p class="text-xs font-medium text-slate-500">
                    {{ __('site.auth.have_account') }}
                    <a href="/login" class="font-bold text-primary hover:underline ml-1">
                        {{ __('site.auth.log_in') }} →
                    </a>
                </p>
            </div>
        </div>

    {{-- ===================== Logged in: pending payment review ===================== --}}
    @elseif (auth()->user()->registration_status === 'pending_payment_review')
        <div class="rounded-2xl border border-slate-200/90 bg-white p-6 sm:p-8 shadow-xl shadow-slate-200/40 text-center space-y-4">
            <div class="mx-auto h-16 w-16 rounded-2xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 shadow-sm">
                <x-icon name="clock" class="h-8 w-8" />
            </div>
            <h1 class="text-2xl font-black text-slate-900">Your Payment is Under Review</h1>
            <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto leading-relaxed font-medium">
                We've received your documents and payment screenshot. Our team is verifying them now — you'll receive an email as soon as your membership is confirmed.
            </p>
        </div>

    {{-- ===================== Logged in: step 2 (documents + payment) ===================== --}}
    @else
        @php $user = auth()->user(); @endphp
        <div class="mb-4">
            <h2 class="text-2xl font-black tracking-tight text-slate-900">Complete Your Membership</h2>
            <p class="mt-0.5 text-xs text-slate-500 font-medium">Upload your documents and payment screenshot to activate your account</p>
        </div>

        @if ($user->registration_status === 'payment_rejected' && $user->registration_rejection_reason)
            <div class="mb-4 rounded-xl bg-red-50 border border-red-200 p-3.5 flex items-start gap-3">
                <x-icon name="alert-circle" class="h-5 w-5 text-red-600 shrink-0 mt-0.5" />
                <div>
                    <p class="font-bold text-red-800 text-xs sm:text-sm">Your previous submission was not approved</p>
                    <p class="text-xs mt-0.5 text-red-700 font-medium">Reason: {{ $user->registration_rejection_reason }}</p>
                </div>
            </div>
        @endif

        @if ($step2Error)
            <div class="mb-4 rounded-xl bg-red-50 border border-red-200 p-3 text-center text-xs font-semibold text-red-600">{{ $step2Error }}</div>
        @endif
        @if ($step2Success)
            <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-center text-xs font-semibold text-emerald-700">{{ $step2Success }}</div>
        @endif

        @if ($qrUpiImage)
            <div class="mb-4 rounded-2xl border border-primary/20 bg-blue-50/50 p-4 text-center">
                <p class="text-xs font-bold text-slate-900 mb-2">Scan to Pay Membership Fee</p>
                <img src="{{ $qrUpiImage }}" alt="Payment QR / UPI" class="mx-auto h-40 w-40 object-contain rounded-xl border border-slate-200 bg-white p-2 shadow-sm" />
                <p class="text-[11px] text-slate-500 mt-2">Take a screenshot of your payment confirmation and upload it below</p>
            </div>
        @endif

        <div class="rounded-2xl border border-slate-200/90 bg-white p-5 sm:p-6 shadow-xl shadow-slate-200/40">
            <form wire:submit="submitStep2" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-700">Aadhar Card</label>
                        <label class="flex flex-col items-center justify-center border-2 border-dashed border-slate-200 rounded-xl p-3 bg-slate-50 hover:bg-slate-100/70 transition-colors cursor-pointer relative min-h-[90px]">
                            <input type="file" accept="image/*,.pdf" wire:model="aadharFile" class="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10" />
                            @if ($aadharFile)
                                <img src="{{ $aadharFile->temporaryUrl() }}" class="h-12 w-12 rounded-lg object-cover" />
                                <span class="text-[11px] font-bold text-emerald-600 mt-1">Selected ✓</span>
                            @elseif ($aadharPreview)
                                <img src="{{ $aadharPreview }}" class="h-12 w-12 rounded-lg object-cover" />
                                <span class="text-[10px] text-slate-500 mt-1">Change file</span>
                            @else
                                <x-icon name="upload" class="h-5 w-5 text-primary mb-0.5" />
                                <span class="text-[11px] font-semibold text-slate-600">Click to upload</span>
                            @endif
                        </label>
                        @error('aadharFile') <p class="text-[11px] font-semibold text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-700">PAN Card</label>
                        <label class="flex flex-col items-center justify-center border-2 border-dashed border-slate-200 rounded-xl p-3 bg-slate-50 hover:bg-slate-100/70 transition-colors cursor-pointer relative min-h-[90px]">
                            <input type="file" accept="image/*,.pdf" wire:model="panFile" class="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10" />
                            @if ($panFile)
                                <img src="{{ $panFile->temporaryUrl() }}" class="h-12 w-12 rounded-lg object-cover" />
                                <span class="text-[11px] font-bold text-emerald-600 mt-1">Selected ✓</span>
                            @elseif ($panPreview)
                                <img src="{{ $panPreview }}" class="h-12 w-12 rounded-lg object-cover" />
                                <span class="text-[10px] text-slate-500 mt-1">Change file</span>
                            @else
                                <x-icon name="upload" class="h-5 w-5 text-primary mb-0.5" />
                                <span class="text-[11px] font-semibold text-slate-600">Click to upload</span>
                            @endif
                        </label>
                        @error('panFile') <p class="text-[11px] font-semibold text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-700">Business Proof Document</label>
                    <div class="flex gap-1.5 mb-1.5">
                        @foreach (\App\Livewire\Auth\Register::BUSINESS_DOCUMENT_TYPES as $type)
                            <button
                                type="button"
                                wire:click="$set('businessDocumentType', '{{ $type }}')"
                                class="flex-1 rounded-lg border px-2 py-1.5 text-[10px] font-bold uppercase tracking-wide transition-all cursor-pointer {{ $businessDocumentType === $type ? 'bg-primary text-white border-primary shadow-sm' : 'border-slate-200 text-slate-600 hover:bg-slate-50' }}"
                            >{{ str_replace('_', ' ', $type) }}</button>
                        @endforeach
                    </div>
                    <label class="flex flex-col items-center justify-center border-2 border-dashed border-slate-200 rounded-xl p-3 bg-slate-50 hover:bg-slate-100/70 transition-colors cursor-pointer relative min-h-[90px]">
                        <input type="file" accept="image/*,.pdf" wire:model="businessDocumentFile" class="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10" />
                        @if ($businessDocumentFile)
                            <img src="{{ $businessDocumentFile->temporaryUrl() }}" class="h-12 w-12 rounded-lg object-cover" />
                            <span class="text-[11px] font-bold text-emerald-600 mt-1">Selected ✓</span>
                        @elseif ($businessDocumentPreview)
                            <img src="{{ $businessDocumentPreview }}" class="h-12 w-12 rounded-lg object-cover" />
                            <span class="text-[10px] text-slate-500 mt-1">Change file</span>
                        @else
                            <x-icon name="upload" class="h-5 w-5 text-primary mb-0.5" />
                            <span class="text-[11px] font-semibold text-slate-600">Click to upload</span>
                        @endif
                    </label>
                    @error('businessDocumentFile') <p class="text-[11px] font-semibold text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1 border-t border-slate-100 pt-3">
                    <label class="text-xs font-bold text-slate-700">Payment Screenshot</label>
                    <label class="flex flex-col items-center justify-center border-2 border-dashed border-primary/30 rounded-xl p-3 bg-blue-50/40 hover:bg-blue-50/70 transition-colors cursor-pointer relative min-h-[90px]">
                        <input type="file" accept="image/*,.pdf" wire:model="paymentScreenshotFile" class="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10" />
                        @if ($paymentScreenshotFile)
                            <img src="{{ $paymentScreenshotFile->temporaryUrl() }}" class="h-12 w-12 rounded-lg object-cover" />
                            <span class="text-[11px] font-bold text-emerald-600 mt-1">Selected ✓</span>
                        @elseif ($paymentScreenshotPreview)
                            <img src="{{ $paymentScreenshotPreview }}" class="h-12 w-12 rounded-lg object-cover" />
                            <span class="text-[10px] text-slate-500 mt-1">Change file</span>
                        @else
                            <x-icon name="upload" class="h-5 w-5 text-primary mb-0.5" />
                            <span class="text-[11px] font-semibold text-slate-600">Click to upload</span>
                        @endif
                    </label>
                    @error('paymentScreenshotFile') <p class="text-[11px] font-semibold text-red-600">{{ $message }}</p> @enderror
                </div>

                <button type="submit" wire:loading.attr="disabled" wire:target="submitStep2" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#00379D] to-[#082e6e] px-5 py-3 text-xs sm:text-sm font-bold text-white shadow-md hover:opacity-95 active:scale-[0.99] disabled:opacity-50 mt-2">
                    <span wire:loading.remove wire:target="submitStep2">Submit for Review</span>
                    <span wire:loading wire:target="submitStep2">Submitting...</span>
                </button>
            </form>
        </div>
    @endif
</div>
