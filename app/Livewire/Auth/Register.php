<?php

namespace App\Livewire\Auth;

use App\Mail\OtpMail;
use App\Mail\RegistrationStatusMail;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;
use Livewire\WithFileUploads;

class Register extends Component
{
    use WithFileUploads;

    public const BUSINESS_DOCUMENT_TYPES = ['msme', 'gst', 'shop_photo', 'other'];

    // ---- Step 1: basic details + references ----
    public string $step1Stage = 'form'; // 'form' | 'otp'

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $password = '';

    public string $confirmPassword = '';

    public string $ref1Name = '';

    public string $ref1Phone = '';

    public string $ref2Name = '';

    public string $ref2Phone = '';

    public string $otp = '';

    public string $otpSentEmail = '';

    public string $step1Error = '';

    public bool $justSubmitted = false;

    // ---- Step 2: KYC + payment ----
    public $aadharFile;

    public string $aadharPreview = '';

    public $panFile;

    public string $panPreview = '';

    public string $businessDocumentType = 'msme';

    public $businessDocumentFile;

    public string $businessDocumentPreview = '';

    public $paymentScreenshotFile;

    public string $paymentScreenshotPreview = '';

    public string $step2Error = '';

    public string $step2Success = '';

    public function mount(): void
    {
        if (Auth::check() && Auth::user()->isRegistrationComplete()) {
            $this->redirect('/profile');

            return;
        }

        if (Auth::check()) {
            $user = Auth::user();
            $this->aadharPreview = media_url($user->aadhar_document) ?? '';
            $this->panPreview = media_url($user->pan_document) ?? '';
            $this->businessDocumentPreview = media_url($user->business_document) ?? '';
            $this->paymentScreenshotPreview = media_url($user->membership_payment_screenshot) ?? '';
            $this->businessDocumentType = $user->business_document_type ?: 'msme';
        }
    }

    public function updatedPhone(): void
    {
        $this->phone = substr(preg_replace('/\D/', '', $this->phone), 0, 10);
    }

    public function updatedRef1Phone(): void
    {
        $this->ref1Phone = substr(preg_replace('/\D/', '', $this->ref1Phone), 0, 10);
    }

    public function updatedRef2Phone(): void
    {
        $this->ref2Phone = substr(preg_replace('/\D/', '', $this->ref2Phone), 0, 10);
    }

    public function sendOtp(): void
    {
        $this->step1Error = '';

        $rateLimitKey = 'register-otp:' . request()->ip();
        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            $this->step1Error = 'Too many attempts. Please wait a minute and try again.';

            return;
        }
        RateLimiter::hit($rateLimitKey, 60);

        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|digits:10',
            'password' => 'required|string|min:6',
            'confirmPassword' => 'required|same:password',
            'ref1Name' => 'required|string|max:255',
            'ref1Phone' => 'required|digits:10',
            'ref2Name' => 'required|string|max:255',
            'ref2Phone' => 'required|digits:10',
        ]);

        $otp = (string) mt_rand(100000, 999999);

        Cache::put('reg_otp_' . $this->email, [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'password' => Hash::make($this->password),
            'ref1_name' => $this->ref1Name,
            'ref1_phone' => $this->ref1Phone,
            'ref2_name' => $this->ref2Name,
            'ref2_phone' => $this->ref2Phone,
            'otp' => $otp,
        ], 900);

        try {
            Mail::to($this->email)->send(new OtpMail($otp, $this->name));
        } catch (\Exception $e) {
            Log::error("SABHA registration OTP email failed for {$this->email}: " . $e->getMessage());
            $this->step1Error = 'Could not send verification email. Please check your email address and try again.';

            return;
        }

        $this->otpSentEmail = $this->email;
        $this->step1Stage = 'otp';
    }

    public function backToForm(): void
    {
        $this->step1Stage = 'form';
        $this->otp = '';
        $this->step1Error = '';
    }

    public function verifyOtp(): void
    {
        $this->step1Error = '';

        $rateLimitKey = 'register-verify-otp:' . request()->ip();
        if (RateLimiter::tooManyAttempts($rateLimitKey, 6)) {
            $this->step1Error = 'Too many attempts. Please wait a minute and try again.';

            return;
        }
        RateLimiter::hit($rateLimitKey, 60);

        $this->validate(['otp' => 'required|string']);

        $cached = Cache::get('reg_otp_' . $this->email);

        if (! $cached || $cached['otp'] !== $this->otp) {
            $this->step1Error = 'Invalid or expired OTP verification code.';

            return;
        }

        User::create([
            'name' => $cached['name'],
            'email' => $cached['email'],
            'phone' => $cached['phone'],
            'password' => $cached['password'],
            'role' => 'user',
            'registration_status' => 'pending_review',
            'ref1_name' => $cached['ref1_name'],
            'ref1_phone' => $cached['ref1_phone'],
            'ref2_name' => $cached['ref2_name'],
            'ref2_phone' => $cached['ref2_phone'],
        ]);

        Cache::forget('reg_otp_' . $this->email);

        $this->justSubmitted = true;
    }

    public function submitStep2(): void
    {
        $this->step2Error = '';
        $this->step2Success = '';

        $this->validate([
            'aadharFile' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'panFile' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'businessDocumentType' => 'required|in:' . implode(',', self::BUSINESS_DOCUMENT_TYPES),
            'businessDocumentFile' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'paymentScreenshotFile' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        $user = Auth::user();

        $fileName = fn ($prefix, $file) => $prefix . '_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();

        $aadharName = $fileName('aadhar', $this->aadharFile);
        $this->aadharFile->storeAs('registration-documents', $aadharName, 'public');

        $panName = $fileName('pan', $this->panFile);
        $this->panFile->storeAs('registration-documents', $panName, 'public');

        $bizDocName = $fileName('bizdoc', $this->businessDocumentFile);
        $this->businessDocumentFile->storeAs('registration-documents', $bizDocName, 'public');

        $paymentName = $fileName('payment', $this->paymentScreenshotFile);
        $this->paymentScreenshotFile->storeAs('registration-documents', $paymentName, 'public');

        $user->update([
            'aadhar_document' => '/storage/registration-documents/' . $aadharName,
            'pan_document' => '/storage/registration-documents/' . $panName,
            'business_document' => '/storage/registration-documents/' . $bizDocName,
            'business_document_type' => $this->businessDocumentType,
            'membership_payment_screenshot' => '/storage/registration-documents/' . $paymentName,
            'registration_status' => 'pending_payment_review',
            'registration_rejection_reason' => null,
        ]);

        try {
            Mail::to($user->email)->send(new RegistrationStatusMail($user, 'step2_submitted'));
        } catch (\Exception $e) {
            Log::error("SABHA step2_submitted email failed for {$user->email}: " . $e->getMessage());
        }

        $this->step2Success = 'Your documents have been submitted for review!';
        $this->aadharFile = null;
        $this->panFile = null;
        $this->businessDocumentFile = null;
        $this->paymentScreenshotFile = null;
    }

    public function render()
    {
        $qrUpiImage = media_url(Setting::where('key', 'membership_qr_upi_image')->value('value'));

        return view('livewire.auth.register', [
            'qrUpiImage' => $qrUpiImage,
        ]);
    }
}
