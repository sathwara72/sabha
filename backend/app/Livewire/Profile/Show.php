<?php

namespace App\Livewire\Profile;

use App\Models\Business;
use App\Models\BusinessCategory;
use App\Models\EventRegistration;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class Show extends Component
{
    use WithFileUploads;

    private const FALLBACK_CATEGORIES = [
        'Software Development', 'Supply Chain', 'Digital Marketing', 'Construction',
        'Financial Services', 'Renewables', 'Creative Agency', 'Venture Capital',
    ];

    public string $activeTab = 'profile';

    public array $categories = [];

    // ---- Personal profile ----
    public string $profileName = '';
    public string $profileEmail = '';
    public string $profilePassword = '';
    public string $profilePhone = '';
    public string $profileCity = '';
    public string $profileNativeCity = '';
    public ?string $profileBirthDate = null;
    public ?string $profileAnniversaryDate = null;
    public string $profileResidenceAddress = '';
    public $avatarFile;
    public string $avatarPreview = '';
    public string $profileSuccess = '';
    public string $profileError = '';
    public bool $profileLoading = false;

    // ---- Business ----
    public ?Business $business = null;
    public bool $isEditingBusiness = false;
    public bool $bizSubmitting = false;
    public string $bizSuccess = '';
    public string $bizError = '';
    public $paymentFile;
    public $logoFile;
    public $coverFile;
    public string $logoPreview = '';
    public string $coverPreview = '';

    public string $bizName = '';
    public string $bizDesignation = '';
    public string $bizCategory = 'Software Development';
    public string $bizWebsite = '';
    public string $bizDescription = '';
    public string $bizTagline = '';
    public string $bizAddress = '';
    public string $bizArea = '';
    public string $bizState = '';
    public string $bizPincode = '';
    public string $bizMapIframe = '';
    public string $bizHours = '';
    public string $bizFounded = '';
    public string $bizTeamSize = '';
    public string $bizProjects = '';
    public string $bizPhone = '';
    public string $bizEmail = '';
    public string $bizLinkedin = '';
    public string $bizInstagram = '';
    public string $bizYoutube = '';
    public string $bizTwitter = '';
    public string $bizWhatsapp = '';
    public array $bizServices = [];

    public function mount(): void
    {
        $user = Auth::user();

        $this->activeTab = request('tab') === 'business' ? 'business' : 'profile';

        $this->profileName = $user->name;
        $this->profileEmail = $user->email ?? '';
        $this->profilePhone = $user->phone ?? '';
        $this->profileCity = $user->city ?? '';
        $this->profileNativeCity = $user->native_city ?? '';
        $this->profileBirthDate = optional($user->birth_date)->format('Y-m-d');
        $this->profileAnniversaryDate = optional($user->anniversary_date)->format('Y-m-d');
        $this->profileResidenceAddress = $user->residence_address ?? '';

        $this->categories = BusinessCategory::active()->pluck('name')->all() ?: self::FALLBACK_CATEGORIES;

        $this->loadBusiness();
    }

    private function loadBusiness(): void
    {
        $this->business = Business::where('user_id', Auth::id())->first();

        if (! $this->business) {
            return;
        }

        $b = $this->business;
        $user = Auth::user();

        $this->bizName = $b->name;
        $this->bizDesignation = $b->designation ?? '';
        $this->bizCategory = $b->category;
        $this->logoPreview = media_url($b->logo) ?? '';
        $this->coverPreview = media_url($b->cover_image) ?? '';
        $this->bizWebsite = $b->website ?? '';
        $this->bizDescription = $b->description ?? '';
        $this->bizTagline = $b->tagline ?? '';
        $this->bizAddress = $b->address ?? '';
        $this->bizArea = $b->area ?? '';
        $this->bizState = $b->state ?? '';
        $this->bizPincode = $b->pincode ?? '';
        $this->bizMapIframe = $b->map_iframe ?? '';
        $this->bizHours = $b->hours ?? '';
        $this->bizFounded = $b->founded ?? '';
        $this->bizTeamSize = $b->team_size ?? '';
        $this->bizProjects = $b->projects ?? '';
        $this->bizPhone = $b->business_phone ?? $user->phone ?? '';
        $this->bizEmail = $b->business_email ?? $user->email ?? '';
        $this->bizLinkedin = $b->linkedin ?? '';
        $this->bizInstagram = $b->instagram ?? '';
        $this->bizYoutube = $b->youtube ?? '';
        $this->bizTwitter = $b->twitter ?? '';
        $this->bizWhatsapp = $b->whatsapp ?? $b->business_phone ?? $user->phone ?? '';

        $services = $b->services ?? [];
        $this->bizServices = collect($services)->map(function ($s) {
            return is_array($s) ? ['title' => $s['title'] ?? '', 'desc' => $s['desc'] ?? ''] : ['title' => (string) $s, 'desc' => ''];
        })->values()->all();
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function updatedProfilePhone(): void
    {
        $this->profilePhone = substr(preg_replace('/\D/', '', $this->profilePhone), 0, 10);
    }

    public function updatedBizPhone(): void
    {
        $this->bizPhone = substr(preg_replace('/\D/', '', $this->bizPhone), 0, 10);
        if (! $this->bizWhatsapp) {
            $this->bizWhatsapp = $this->bizPhone;
        }
    }

    public function updatedBizWhatsapp(): void
    {
        $this->bizWhatsapp = substr(preg_replace('/\D/', '', $this->bizWhatsapp), 0, 10);
    }

    public function updatedBizPincode(): void
    {
        $this->bizPincode = substr(preg_replace('/\D/', '', $this->bizPincode), 0, 6);
    }

    public function addService(): void
    {
        if (count($this->bizServices) < 4) {
            $this->bizServices[] = ['title' => '', 'desc' => ''];
        }
    }

    public function removeService(int $index): void
    {
        unset($this->bizServices[$index]);
        $this->bizServices = array_values($this->bizServices);
    }

    public function updateProfile(): void
    {
        $this->profileSuccess = '';
        $this->profileError = '';
        $this->profileLoading = true;

        try {
            $validated = $this->validate([
                'profileName' => 'required|string',
                'profileEmail' => 'required|email|unique:users,email,' . Auth::id(),
                'profilePassword' => 'nullable|string|min:6',
                'profilePhone' => 'nullable|string|max:10',
                'profileCity' => 'nullable|string',
                'profileNativeCity' => 'nullable|string',
                'profileBirthDate' => 'nullable|date',
                'profileAnniversaryDate' => 'nullable|date',
                'profileResidenceAddress' => 'nullable|string',
                'avatarFile' => 'nullable|image|max:5120',
            ]);

            $user = Auth::user();
            $user->name = $validated['profileName'];
            $user->email = $validated['profileEmail'];
            if (! empty($validated['profilePassword'])) {
                $user->password = Hash::make($validated['profilePassword']);
            }
            $user->phone = $validated['profilePhone'] ?: null;
            $user->city = $validated['profileCity'] ?: null;
            $user->native_city = $validated['profileNativeCity'] ?: null;
            $user->birth_date = $validated['profileBirthDate'] ?: null;
            $user->anniversary_date = $validated['profileAnniversaryDate'] ?: null;
            $user->residence_address = $validated['profileResidenceAddress'] ?: null;

            if ($this->avatarFile) {
                $fileName = 'avatar_' . $user->id . '_' . time() . '.' . $this->avatarFile->getClientOriginalExtension();
                $this->avatarFile->storeAs('avatars', $fileName, 'public');
                $user->avatar = '/storage/avatars/' . $fileName;
            }

            $user->save();

            $this->avatarFile = null;
            $this->profilePassword = '';
            $this->profileSuccess = __('site.profile.profile_success');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->profileError = __('site.profile.profile_error');
        } finally {
            $this->profileLoading = false;
        }
    }

    public function submitBusiness(): void
    {
        $this->bizSuccess = '';
        $this->bizError = '';

        if (! $this->paymentFile && ! $this->business) {
            $this->bizError = __('site.profile.biz_payment_required');

            return;
        }

        $this->bizSubmitting = true;

        try {
            $this->validate([
                'bizName' => 'nullable|string',
                'bizDescription' => $this->business?->status === 'approved' ? 'required|string' : 'nullable|string',
                'paymentFile' => 'nullable|image|max:10240',
                'logoFile' => 'nullable|image|max:5120',
                'coverFile' => 'nullable|image|max:10240',
            ]);

            $user = Auth::user();

            $bizPhone = $this->bizPhone ?: $user->phone;
            $bizEmail = $this->bizEmail ?: $user->email;

            $data = [
                'name' => $this->bizName ?: ('Business of ' . $user->name),
                'designation' => $this->bizDesignation ?: null,
                'category' => $this->bizCategory ?: 'Software Development',
                'tagline' => $this->bizTagline ?: null,
                'address' => $this->bizAddress ?: null,
                'area' => $this->bizArea ?: null,
                'state' => $this->bizState ?: null,
                'pincode' => $this->bizPincode ?: null,
                'map_iframe' => $this->bizMapIframe ?: null,
                'description' => $this->bizDescription ?: null,
                'website' => $this->bizWebsite ?: null,
                'business_phone' => $bizPhone,
                'business_email' => $bizEmail,
                'linkedin' => $this->bizLinkedin ?: null,
                'instagram' => $this->bizInstagram ?: null,
                'youtube' => $this->bizYoutube ?: null,
                'twitter' => $this->bizTwitter ?: null,
                'whatsapp' => $this->bizWhatsapp ?: $bizPhone,
                'hours' => $this->bizHours ?: null,
                'founded' => $this->bizFounded ?: null,
                'team_size' => $this->bizTeamSize ?: null,
                'projects' => $this->bizProjects ?: null,
                'services' => $this->bizServices,
                'user_id' => $user->id,
            ];

            if ($this->paymentFile) {
                $fileName = time() . '_payment_' . uniqid() . '.' . $this->paymentFile->getClientOriginalExtension();
                $this->paymentFile->storeAs('payments', $fileName, 'public');
                $data['payment_screenshot'] = '/storage/payments/' . $fileName;
            }
            if ($this->logoFile) {
                $fileName = time() . '_logo_' . uniqid() . '.' . $this->logoFile->getClientOriginalExtension();
                $this->logoFile->storeAs('logos', $fileName, 'public');
                $data['logo'] = '/storage/logos/' . $fileName;
            }
            if ($this->coverFile) {
                $fileName = time() . '_cover_' . uniqid() . '.' . $this->coverFile->getClientOriginalExtension();
                $this->coverFile->storeAs('covers', $fileName, 'public');
                $data['cover_image'] = '/storage/covers/' . $fileName;
            }

            if ($this->business) {
                if ($this->business->status === 'pending') {
                    $this->bizError = 'Your business profile is already in progress. Please wait for approval.';
                    $this->bizSubmitting = false;

                    return;
                }
                if ($this->business->status === 'approved') {
                    $data['status'] = 'approved';
                    $data['is_verified'] = true;
                } else {
                    $data['status'] = 'pending';
                    $data['is_verified'] = false;
                    $data['rejection_reason'] = null;
                }
                $this->business->update($data);
            } else {
                $data['status'] = 'pending';
                $data['is_verified'] = false;
                $this->business = Business::create($data);
            }

            $this->bizSuccess = __('site.profile.biz_success');
            $this->isEditingBusiness = false;
            $this->paymentFile = null;
            $this->logoFile = null;
            $this->coverFile = null;
            $this->loadBusiness();
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } finally {
            $this->bizSubmitting = false;
        }
    }

    public function render()
    {
        $registeredEvents = EventRegistration::where('user_id', Auth::id())
            ->with('event')
            ->latest()
            ->get();

        return view('livewire.profile.show', [
            'registeredEvents' => $registeredEvents,
            'user' => Auth::user(),
        ]);
    }
}
