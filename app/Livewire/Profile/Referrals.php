<?php

namespace App\Livewire\Profile;

use App\Models\BusinessReferral;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

class Referrals extends Component
{
    use WithPagination;

    /** 'given' or 'received' — which side of the referral this instance shows. */
    #[Locked]
    public string $direction = 'given';

    public const LEAD_RATINGS = ['hot', 'warm', 'cold'];

    public const RECEIVER_STATUSES = ['in_progress', 'closed', 'rejected'];

    // ---- Give Referral (direction: given) ----
    public bool $isGiveModalOpen = false;

    public ?int $receiverMemberId = null;

    public string $contactName = '';

    public string $contactNumber = '';

    public string $companyDetails = '';

    public string $businessRequirement = '';

    public string $leadRating = 'warm';

    public string $giverComments = '';

    // ---- Update Referral (direction: received) ----
    public ?int $updatingId = null;

    public string $updateContactStatus = 'not_connected';

    public string $updateStatus = 'in_progress';

    public string $updateAmount = '';

    public string $updateReceiverComments = '';

    public string $updateTestimonial = '';

    public bool $updateDisplayTestimonial = false;

    // ---- Giver Testimonial (direction: given on closed referrals) ----
    public ?int $giverTestimonialId = null;

    public string $giverTestimonialText = '';

    public bool $giverDisplayTestimonial = true;

    public ?int $deletingId = null;

    public ?int $outcomeModalId = null;

    public string $successMsg = '';

    public function mount(string $direction = 'given'): void
    {
        $this->direction = in_array($direction, ['given', 'received'], true) ? $direction : 'given';
    }

    public function openOutcomeModal(int $id): void
    {
        $this->outcomeModalId = $id;
    }

    public function closeOutcomeModal(): void
    {
        $this->outcomeModalId = null;
    }

    // ───────────────────────── Give ─────────────────────────

    public function openGive(): void
    {
        $this->reset(['receiverMemberId', 'contactName', 'contactNumber', 'companyDetails', 'businessRequirement', 'giverComments']);
        $this->leadRating = 'warm';
        $this->resetErrorBag();
        $this->isGiveModalOpen = true;
    }

    public function cancelGive(): void
    {
        $this->isGiveModalOpen = false;
    }

    public function giveReferral(): void
    {
        $this->validate([
            'receiverMemberId' => 'required|integer|exists:users,id',
            'contactName' => 'required|string|max:255',
            'contactNumber' => ['required', 'string', 'regex:/^[0-9]{10}$/'],
            'companyDetails' => 'nullable|string|max:255',
            'businessRequirement' => 'required|string',
            'leadRating' => 'required|in:' . implode(',', self::LEAD_RATINGS),
            'giverComments' => 'nullable|string',
        ], [
            'contactNumber.regex' => 'The contact number must be exactly 10 digits.',
        ]);

        if ((int) $this->receiverMemberId === Auth::id()) {
            $this->addError('receiverMemberId', __('site.profile.referrals.error_self_referral'));

            return;
        }

        BusinessReferral::create([
            'giver_id' => Auth::id(),
            'receiver_id' => $this->receiverMemberId,
            'contact_name' => $this->contactName,
            'contact_number' => $this->contactNumber,
            'company_details' => $this->companyDetails ?: null,
            'business_requirement' => $this->businessRequirement,
            'lead_rating' => $this->leadRating,
            'giver_comments' => $this->giverComments ?: null,
        ]);

        $this->successMsg = __('site.profile.referrals.success_given');
        $this->isGiveModalOpen = false;
    }

    public function openDelete(int $id): void
    {
        $exists = BusinessReferral::where('giver_id', Auth::id())
            ->where('id', $id)
            ->where('status', 'pending')
            ->exists();

        if ($exists) {
            $this->deletingId = $id;
        }
    }

    public function cancelDelete(): void
    {
        $this->deletingId = null;
    }

    public function confirmDelete(): void
    {
        BusinessReferral::where('giver_id', Auth::id())
            ->where('id', $this->deletingId)
            ->where('status', 'pending')
            ->delete();

        $this->deletingId = null;
        $this->successMsg = __('site.profile.referrals.success_withdrawn');
    }

    // ───────────────────────── Update (receiver) ─────────────────────────

    public function openUpdate(int $id): void
    {
        $referral = BusinessReferral::where('receiver_id', Auth::id())->findOrFail($id);

        $this->updatingId = $referral->id;
        $this->updateContactStatus = $referral->contact_status;
        $this->updateStatus = in_array($referral->status, self::RECEIVER_STATUSES, true) ? $referral->status : 'in_progress';
        $this->updateAmount = $referral->amount !== null ? (string) $referral->amount : '';
        $this->updateReceiverComments = $referral->receiver_comments ?? '';
        $this->updateTestimonial = $referral->testimonial ?? '';
        $this->updateDisplayTestimonial = (bool) $referral->display_testimonial;
        $this->resetErrorBag();
    }

    public function cancelUpdate(): void
    {
        $this->updatingId = null;
    }

    public function updateReferral(): void
    {
        $this->validate([
            'updateContactStatus' => 'required|in:not_connected,connected',
            'updateStatus' => 'required|in:' . implode(',', self::RECEIVER_STATUSES),
            'updateAmount' => 'nullable|numeric|min:0|required_if:updateStatus,closed',
            'updateReceiverComments' => 'nullable|string',
            'updateTestimonial' => 'nullable|string',
        ]);

        $referral = BusinessReferral::where('receiver_id', Auth::id())->findOrFail($this->updatingId);

        $referral->update([
            'contact_status' => $this->updateContactStatus,
            'status' => $this->updateStatus,
            'amount' => $this->updateStatus === 'closed' ? $this->updateAmount : null,
            'receiver_comments' => $this->updateReceiverComments ?: null,
            'testimonial' => $this->updateStatus === 'closed' ? ($this->updateTestimonial ?: null) : null,
            'display_testimonial' => $this->updateStatus === 'closed' && $this->updateTestimonial ? ($referral->display_testimonial ?? true) : false,
        ]);

        $this->successMsg = __('site.profile.referrals.success_updated');
        $this->updatingId = null;
    }

    public function toggleWebsiteDisplay(int $id): void
    {
        $referral = BusinessReferral::where(function ($q) {
            $q->where('giver_id', Auth::id())
              ->orWhere('receiver_id', Auth::id());
        })->findOrFail($id);

        $referral->update([
            'display_testimonial' => ! $referral->display_testimonial,
        ]);

        $this->successMsg = $referral->display_testimonial
            ? 'Testimonial is now set to display on website.'
            : 'Testimonial is now hidden from website.';
    }

    // ───────────────────────── Giver Testimonial ─────────────────────────

    public function openGiverTestimonial(int $id): void
    {
        $referral = BusinessReferral::where('giver_id', Auth::id())->where('status', 'closed')->findOrFail($id);

        $this->giverTestimonialId = $referral->id;
        $this->giverTestimonialText = $referral->testimonial ?? '';
        $this->giverDisplayTestimonial = (bool) ($referral->display_testimonial ?? true);
        $this->resetErrorBag();
    }

    public function cancelGiverTestimonial(): void
    {
        $this->giverTestimonialId = null;
    }

    public function saveGiverTestimonial(): void
    {
        $this->validate([
            'giverTestimonialText' => 'required|string|min:3|max:1000',
        ]);

        $referral = BusinessReferral::where('giver_id', Auth::id())->where('status', 'closed')->findOrFail($this->giverTestimonialId);

        $referral->update([
            'testimonial' => $this->giverTestimonialText,
            'display_testimonial' => $this->giverDisplayTestimonial,
        ]);

        $this->successMsg = 'Testimonial saved successfully.';
        $this->giverTestimonialId = null;
    }

    public function render()
    {
        $query = BusinessReferral::with(['giver.business', 'receiver.business']);

        $query = $this->direction === 'given'
            ? $query->where('giver_id', Auth::id())
            : $query->where('receiver_id', Auth::id());

        $referrals = $query->latest()->paginate(10, ['*'], 'page-' . $this->direction);

        $memberLabels = [];
        $memberValueMap = [];
        if ($this->direction === 'given') {
            foreach (User::where('id', '!=', Auth::id())->whereNotIn('role', ['admin', 'sub_admin'])->where('is_blocked', false)->orderBy('name')->get(['id', 'name', 'phone']) as $member) {
                $label = $member->phone ? "{$member->name} ({$member->phone})" : $member->name;
                $memberLabels[] = $label;
                $memberValueMap[$label] = $member->id;
            }
        }

        return view('livewire.profile.referrals', [
            'referrals' => $referrals,
            'memberLabels' => $memberLabels,
            'memberValueMap' => $memberValueMap,
            'selectedOutcomeRef' => $this->outcomeModalId ? BusinessReferral::find($this->outcomeModalId) : null,
        ]);
    }
}
