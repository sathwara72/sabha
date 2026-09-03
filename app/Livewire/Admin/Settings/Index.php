<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Setting;
use App\Models\Statistic;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithFileUploads;

    #[Url(as: 'tab')]
    public string $activeTab = 'general';

    public string $contactEmail = '';

    public string $contactPhone = '';

    public string $contactAddress = '';

    public string $responseTime = '';

    public string $instagramUrl = '';

    public string $whatsappUrl = '';

    public string $facebookUrl = '';

    public array $coordinators = [];

    public array $trustees = [];

    public string $successMsg = '';

    public string $errorMsg = '';

    public $trusteeAvatarUpload = null;

    public $membershipQrUpiFile = null;

    public string $membershipQrUpiImage = '';
    public string $mailMailer = 'smtp';
    public string $mailHost = 'smtp.hostinger.com';
    public string $mailPort = '465';
    public string $mailUsername = 'info@sabhaglobal.org';
    public string $mailPassword = 'SabhaGlobal@26';
    public string $mailEncryption = 'ssl';
    public string $mailFromAddress = 'info@sabhaglobal.org';
    public string $mailFromName = 'SABHA';
    public string $testEmail = '';
    public string $testEmailStatus = '';
    public string $testEmailError = '';

    public function mount(): void
    {
        if ($this->activeTab === 'statistics') {
            $this->activeTab = 'general';
        }
        $this->loadData();
        $this->testEmail = auth()->user()?->email ?? 'info@sabhaglobal.org';
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab === 'statistics' ? 'general' : $tab;
    }

    public function loadData(): void
    {
        $settings = Setting::all()->pluck('value', 'key');

        $this->contactEmail = $settings['contact_email'] ?? 'info@sabhaglobal.org';
        $this->contactPhone = $settings['contact_phone'] ?? '+91 95377 33567';
        $this->contactAddress = $settings['contact_address'] ?? 'Ahmedabad, Gujarat, India';
        $this->responseTime = $settings['response_time'] ?? 'Within 1 Business Day';
        $this->instagramUrl = $settings['instagram_url'] ?? '';
        $this->whatsappUrl = $settings['whatsapp_url'] ?? '';
        $this->facebookUrl = $settings['facebook_url'] ?? '';

        $this->mailMailer = $settings['mail_mailer'] ?? config('mail.default', 'smtp');
        $this->mailHost = $settings['mail_host'] ?? config('mail.mailers.smtp.host', 'smtp.hostinger.com');
        $this->mailPort = (string) ($settings['mail_port'] ?? config('mail.mailers.smtp.port', '465'));
        $this->mailUsername = $settings['mail_username'] ?? config('mail.mailers.smtp.username', 'info@sabhaglobal.org');
        $this->mailPassword = $settings['mail_password'] ?? config('mail.mailers.smtp.password', 'SabhaGlobal@26');
        $this->mailEncryption = $settings['mail_encryption'] ?? (config('mail.mailers.smtp.encryption') ?: 'ssl');
        $this->mailFromAddress = $settings['mail_from_address'] ?? config('mail.from.address', 'info@sabhaglobal.org');
        $this->mailFromName = $settings['mail_from_name'] ?? config('mail.from.name', 'SABHA');

        $this->coordinators = $this->decodeJson($settings['coordinators'] ?? null);
        $this->trustees = $this->decodeJson($settings['trustees'] ?? null);
        $this->membershipQrUpiImage = $settings['membership_qr_upi_image'] ?? '';

    }

    public function uploadMembershipQrUpi(): void
    {
        if (! $this->membershipQrUpiFile) {
            $this->errorMsg = 'Please choose a QR code image to upload.';
            return;
        }

        $this->validate(['membershipQrUpiFile' => 'image|max:5120']);

        $fileName = 'membership_qr_upi_' . time() . '.' . $this->membershipQrUpiFile->getClientOriginalExtension();
        $this->membershipQrUpiFile->storeAs('settings', $fileName, 'public');

        $this->membershipQrUpiImage = '/storage/settings/' . $fileName;
        Setting::updateOrCreate(['key' => 'membership_qr_upi_image'], ['value' => $this->membershipQrUpiImage]);
        $this->membershipQrUpiFile = null;
        $this->successMsg = 'Membership QR/UPI image updated successfully!';
    }

    public function removeMembershipQrUpi(): void
    {
        $this->membershipQrUpiImage = '';
        $this->membershipQrUpiFile = null;
        Setting::updateOrCreate(['key' => 'membership_qr_upi_image'], ['value' => '']);
        $this->successMsg = 'Membership QR image removed successfully!';
    }

    private function decodeJson($raw): array
    {
        if (blank($raw)) {
            return [];
        }

        if (is_array($raw)) {
            return $raw;
        }

        $decoded = json_decode($raw, true);

        return json_last_error() === JSON_ERROR_NONE && is_array($decoded) ? $decoded : [];
    }

    public function addCoordinator(): void
    {
        $this->coordinators[] = ['city' => 'New Coordinator', 'contact' => '', 'phone' => '', 'email' => ''];
    }

    public function removeCoordinator(int $index): void
    {
        unset($this->coordinators[$index]);
        $this->coordinators = array_values($this->coordinators);
    }

    public function addTrustee(): void
    {
        $this->trustees[] = [
            'name' => 'New Trustee',
            'role' => '',
            'company' => '',
            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=150&auto=format&fit=crop',
        ];
    }

    public function removeTrustee(int $index): void
    {
        unset($this->trustees[$index]);
        $this->trustees = array_values($this->trustees);
    }

    public function uploadTrusteeAvatar(int $index): void
    {
        $this->validate(['trusteeAvatarUpload' => 'image|max:5120']);

        if (! $this->trusteeAvatarUpload) {
            return;
        }

        $fileName = time() . '_' . uniqid() . '.' . $this->trusteeAvatarUpload->getClientOriginalExtension();
        $this->trusteeAvatarUpload->storeAs('gallery', $fileName, 'public');

        $this->trustees[$index]['avatar'] = '/storage/gallery/' . $fileName;
        $this->trusteeAvatarUpload = null;
    }

    public function saveMailSettings(): void
    {
        $this->validate([
            'mailMailer' => 'required|string',
            'mailHost' => 'required|string',
            'mailPort' => 'required|numeric',
            'mailUsername' => 'required|string',
            'mailPassword' => 'required|string',
            'mailEncryption' => 'nullable|string',
            'mailFromAddress' => 'required|email',
            'mailFromName' => 'required|string',
        ]);

        $this->successMsg = '';
        $this->errorMsg = '';
        $this->testEmailStatus = '';
        $this->testEmailError = '';

        $values = [
            'mail_mailer' => trim($this->mailMailer),
            'mail_host' => trim($this->mailHost),
            'mail_port' => trim($this->mailPort),
            'mail_username' => trim($this->mailUsername),
            'mail_password' => $this->mailPassword,
            'mail_encryption' => trim($this->mailEncryption),
            'mail_from_address' => trim($this->mailFromAddress),
            'mail_from_name' => trim($this->mailFromName),
        ];

        foreach ($values as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // Apply immediately to runtime config
        config([
            'mail.default' => $values['mail_mailer'],
            'mail.mailers.smtp.host' => $values['mail_host'],
            'mail.mailers.smtp.port' => (int) $values['mail_port'],
            'mail.mailers.smtp.username' => $values['mail_username'],
            'mail.mailers.smtp.password' => $values['mail_password'],
            'mail.mailers.smtp.encryption' => ($values['mail_encryption'] === 'none' || $values['mail_encryption'] === '' ? null : $values['mail_encryption']),
            'mail.from.address' => $values['mail_from_address'],
            'mail.from.name' => $values['mail_from_name'],
        ]);

        $this->successMsg = 'Mail & SMTP settings updated successfully!';
    }

    public function sendTestEmail(): void
    {
        $this->validate([
            'testEmail' => 'required|email',
        ]);

        $this->testEmailStatus = '';
        $this->testEmailError = '';

        try {
            // Apply current form values dynamically for testing
            config([
                'mail.default' => trim($this->mailMailer),
                'mail.mailers.smtp.host' => trim($this->mailHost),
                'mail.mailers.smtp.port' => (int) trim($this->mailPort),
                'mail.mailers.smtp.username' => trim($this->mailUsername),
                'mail.mailers.smtp.password' => $this->mailPassword,
                'mail.mailers.smtp.encryption' => ($this->mailEncryption === 'none' || $this->mailEncryption === '' ? null : trim($this->mailEncryption)),
                'mail.from.address' => trim($this->mailFromAddress),
                'mail.from.name' => trim($this->mailFromName),
            ]);

            \Illuminate\Support\Facades\Mail::raw("Hello,\n\nThis is a test email sent from SABHA Admin Panel Mail Settings.\n\nSMTP Host: {$this->mailHost}\nSMTP Port: {$this->mailPort}\nFrom: {$this->mailFromAddress} ({$this->mailFromName})\nSent Time: " . now()->toDateTimeString() . "\n\nIf you received this email, your SMTP mail delivery is working perfectly!", function ($message) {
                $message->to(trim($this->testEmail))
                    ->subject('✅ SABHA Test Email - SMTP Working Perfectly');
            });

            $this->testEmailStatus = "Test email sent successfully to {$this->testEmail}!";
        } catch (\Throwable $e) {
            $this->testEmailError = 'Failed to send test email: ' . $e->getMessage();
        }
    }

    public function save(): void
    {
        $this->successMsg = '';
        $this->errorMsg = '';

        $values = [
            'contact_email' => $this->contactEmail,
            'contact_phone' => $this->contactPhone,
            'contact_address' => $this->contactAddress,
            'response_time' => $this->responseTime,
            'instagram_url' => $this->instagramUrl,
            'whatsapp_url' => $this->whatsappUrl,
            'facebook_url' => $this->facebookUrl,
            'trustees' => json_encode(array_values($this->trustees)),
        ];

        foreach ($values as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        $this->successMsg = 'Site settings updated successfully!';
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.admin.settings.index');
    }
}
