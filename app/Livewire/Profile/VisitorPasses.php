<?php

namespace App\Livewire\Profile;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class VisitorPasses extends Component
{
    use WithFileUploads;

    public ?int $event_id = null;

    public string $guest_name = '';

    public string $guest_mobile = '';

    public string $guest_email = '';

    public $paymentFile;

    public string $successMsg = '';

    public function updatedGuestMobile(): void
    {
        $this->guest_mobile = substr(preg_replace('/\D/', '', $this->guest_mobile), 0, 10);
    }

    public function purchase(): void
    {
        $this->successMsg = '';

        $validated = $this->validate([
            'event_id' => 'required|integer|exists:events,id',
            'guest_name' => 'required|string|max:255',
            'guest_mobile' => 'required|digits:10',
            'guest_email' => 'required|email',
            'paymentFile' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ], [], [
            'event_id' => 'event',
        ]);

        $fileName = time() . '_visitor_' . uniqid() . '.' . $this->paymentFile->getClientOriginalExtension();
        $this->paymentFile->storeAs('event_payments', $fileName, 'public');

        EventRegistration::create([
            'event_id' => $validated['event_id'],
            'user_id' => null,
            'purchased_by_user_id' => Auth::id(),
            'guest_name' => $validated['guest_name'],
            'guest_mobile' => $validated['guest_mobile'],
            'guest_email' => $validated['guest_email'],
            'ticket_number' => null,
            'status' => 'pending',
            'payment_screenshot' => '/storage/event_payments/' . $fileName,
            'ticket_type' => 'standard',
            'amount_paid' => '0.00',
        ]);

        $this->reset(['event_id', 'guest_name', 'guest_mobile', 'guest_email', 'paymentFile']);
        $this->successMsg = 'Visitor pass request submitted. It will appear here once approved, and the ticket will be emailed to the visitor.';
    }

    public function render()
    {
        $upcomingEvents = Event::where('date', '>=', now()->startOfDay())->orderBy('date')->get();

        $passes = EventRegistration::where('purchased_by_user_id', Auth::id())
            ->with('event')
            ->latest()
            ->get();

        return view('livewire.profile.visitor-passes', [
            'upcomingEvents' => $upcomingEvents,
            'passes' => $passes,
        ]);
    }
}
