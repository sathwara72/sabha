<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        return view('pages.events');
    }

    public function show(int $id): View
    {
        $event = Event::find($id);

        if (! $event) {
            return view('pages.event-show', ['event' => null]);
        }

        $event->load(['galleryImages', 'approvedRegistrations.user']);

        $bookingStatus = $event->bookingStatus();
        $isBookingOpen = $event->isBookingOpen();

        $members = $event->approvedRegistrations
            ->filter(fn ($reg) => $reg->user)
            ->map(fn ($reg) => ['name' => $reg->user->name, 'role' => 'SABHA Member'])
            ->values();

        $isVerifiedMember = auth()->check() && optional(auth()->user()->business)->status === 'approved';

        $userRegistration = auth()->check()
            ? EventRegistration::where('event_id', $event->id)->where('user_id', auth()->id())->first()
            : null;

        return view('pages.event-show', [
            'event' => $event,
            'status' => $bookingStatus,
            'isBookingOpen' => $isBookingOpen,
            'userRegistration' => $userRegistration,
            'members' => $members,
            'isVerifiedMember' => $isVerifiedMember,
            'priceNormal' => $event->price_normal ?: ($event->type === 'Workshop' ? 'Free' : '₹1,499'),
            'priceVerified' => $event->price_verified ?: ($event->type === 'Workshop' ? 'Free' : '₹1,499'),
        ]);
    }

    public function reserve(Request $request, Event $event): JsonResponse
    {
        if (! $event->isBookingOpen()) {
            $status = $event->bookingStatus();
            $msg = match ($status) {
                'upcoming' => 'Ticket booking has not opened yet for this event (' . ($event->booking_start_date ? 'Opens on ' . $event->booking_start_date->format('M j, Y') : 'Starts soon') . ').',
                'closed' => 'Ticket booking is now closed for this event (' . ($event->booking_end_date ? 'Closed on ' . $event->booking_end_date->format('M j, Y') : 'Ended') . ').',
                'past' => 'This event has already taken place.',
                default => 'Booking is currently unavailable for this event.',
            };

            return response()->json(['message' => $msg], 400);
        }

        $user = $request->user();

        $existing = EventRegistration::where('event_id', $event->id)->where('user_id', $user->id)->first();
        if ($existing) {
            return response()->json(['message' => 'You have already requested a reservation for this event.'], 400);
        }

        $validated = $request->validate([
            'ticket_type' => 'required|string|in:standard,verified',
            'amount_paid' => 'required|numeric',
            'payment_screenshot' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        $screenshotPath = null;
        if ($request->hasFile('payment_screenshot')) {
            $file = $request->file('payment_screenshot');
            $fileName = time() . '_ticket_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('event_payments', $fileName, 'public');
            $screenshotPath = '/storage/event_payments/' . $fileName;
        }

        EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'ticket_number' => null,
            'status' => 'pending',
            'payment_screenshot' => $screenshotPath,
            'ticket_type' => $validated['ticket_type'],
            'amount_paid' => $validated['amount_paid'],
        ]);

        return response()->json(['message' => 'Reservation request submitted successfully and is pending verification']);
    }
}
