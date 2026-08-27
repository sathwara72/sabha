<?php

namespace App\Livewire\Admin\Registrations;

use App\Mail\RegistrationStatusMail;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $tab = 'pending_review';

    public string $search = '';

    public ?int $viewingId = null;

    public ?int $rejectingId = null;

    public string $rejectReason = '';

    public string $successMsg = '';

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function view(int $id): void
    {
        admin_authorize('registrations', 'can_view');

        $this->viewingId = $id;
    }

    public function closeView(): void
    {
        $this->viewingId = null;
    }

    public function approveStep1(int $id): void
    {
        admin_authorize('registrations', 'can_approve');

        $user = User::where('registration_status', 'pending_review')->findOrFail($id);
        $user->update(['registration_status' => 'step1_approved']);

        try {
            Mail::to($user->email)->send(new RegistrationStatusMail($user, 'step1_approved'));
        } catch (\Exception $e) {
            Log::error("SABHA step1_approved email failed for {$user->email}: " . $e->getMessage());
        }

        $this->successMsg = "{$user->name}'s application was approved — they can now log in to complete step 2.";
        $this->viewingId = null;
    }

    public function openRejectStep1(int $id): void
    {
        admin_authorize('registrations', 'can_approve');

        $this->rejectingId = $id;
        $this->rejectReason = '';
    }

    public function cancelRejectStep1(): void
    {
        $this->rejectingId = null;
        $this->rejectReason = '';
    }

    public function confirmRejectStep1(): void
    {
        admin_authorize('registrations', 'can_approve');

        $user = User::where('registration_status', 'pending_review')->findOrFail($this->rejectingId);
        $email = $user->email;
        $name = $user->name;
        $reason = $this->rejectReason ?: null;

        try {
            Mail::to($email)->send(new RegistrationStatusMail($user, 'step1_rejected', $reason));
        } catch (\Exception $e) {
            Log::error("SABHA step1_rejected email failed for {$email}: " . $e->getMessage());
        }

        $user->delete();

        $this->successMsg = "{$name}'s application was rejected and removed.";
        $this->rejectingId = null;
        $this->viewingId = null;
    }

    public function approvePayment(int $id): void
    {
        admin_authorize('registrations', 'can_approve');

        $user = User::where('registration_status', 'pending_payment_review')->findOrFail($id);
        $user->update(['registration_status' => 'active', 'registration_rejection_reason' => null]);

        try {
            Mail::to($user->email)->send(new RegistrationStatusMail($user, 'payment_approved'));
        } catch (\Exception $e) {
            Log::error("SABHA payment_approved email failed for {$user->email}: " . $e->getMessage());
        }

        $this->successMsg = "{$user->name}'s membership is now active.";
        $this->viewingId = null;
    }

    public function openRejectPayment(int $id): void
    {
        admin_authorize('registrations', 'can_approve');

        $this->rejectingId = $id;
        $this->rejectReason = '';
    }

    public function confirmRejectPayment(): void
    {
        admin_authorize('registrations', 'can_approve');

        $this->validate(['rejectReason' => 'required|string']);

        $user = User::where('registration_status', 'pending_payment_review')->findOrFail($this->rejectingId);
        $user->update([
            'registration_status' => 'payment_rejected',
            'registration_rejection_reason' => $this->rejectReason,
        ]);

        try {
            Mail::to($user->email)->send(new RegistrationStatusMail($user, 'payment_rejected', $this->rejectReason));
        } catch (\Exception $e) {
            Log::error("SABHA payment_rejected email failed for {$user->email}: " . $e->getMessage());
        }

        $this->successMsg = "{$user->name} was asked to resubmit their documents.";
        $this->rejectingId = null;
        $this->viewingId = null;
    }

    public function render()
    {
        $status = $this->tab === 'pending_review' ? 'pending_review' : 'pending_payment_review';

        $query = User::where('registration_status', $status);

        if ($this->search !== '') {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $applicants = $query->latest()->paginate(10);

        return view('livewire.admin.registrations.index', [
            'applicants' => $applicants,
            'pendingReviewCount' => User::where('registration_status', 'pending_review')->count(),
            'pendingPaymentCount' => User::where('registration_status', 'pending_payment_review')->count(),
            'viewingUser' => $this->viewingId ? User::find($this->viewingId) : null,
        ]);
    }
}
