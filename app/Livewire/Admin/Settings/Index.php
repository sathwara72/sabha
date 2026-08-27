<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Setting;
use Livewire\Component;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithFileUploads;

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

    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        $settings = Setting::all()->pluck('value', 'key');

        $this->contactEmail = $settings['contact_email'] ?? 'hello@sabha.global';
        $this->contactPhone = $settings['contact_phone'] ?? '+91 95377 33567';
        $this->contactAddress = $settings['contact_address'] ?? 'Ahmedabad, Gujarat, India';
        $this->responseTime = $settings['response_time'] ?? 'Within 1 Business Day';
        $this->instagramUrl = $settings['instagram_url'] ?? '';
        $this->whatsappUrl = $settings['whatsapp_url'] ?? '';
        $this->facebookUrl = $settings['facebook_url'] ?? '';

        $this->coordinators = $this->decodeJson($settings['coordinators'] ?? null);
        $this->trustees = $this->decodeJson($settings['trustees'] ?? null);
        $this->membershipQrUpiImage = $settings['membership_qr_upi_image'] ?? '';
    }

    public function uploadMembershipQrUpi(): void
    {
        $this->validate(['membershipQrUpiFile' => 'image|max:5120']);

        if (! $this->membershipQrUpiFile) {
            return;
        }

        $fileName = 'membership_qr_upi_' . time() . '.' . $this->membershipQrUpiFile->getClientOriginalExtension();
        $this->membershipQrUpiFile->storeAs('settings', $fileName, 'public');

        $this->membershipQrUpiImage = '/storage/settings/' . $fileName;
        Setting::updateOrCreate(['key' => 'membership_qr_upi_image'], ['value' => $this->membershipQrUpiImage]);
        $this->membershipQrUpiFile = null;
        $this->successMsg = 'Membership QR/UPI image updated successfully!';
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
            'coordinators' => json_encode(array_values($this->coordinators)),
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
