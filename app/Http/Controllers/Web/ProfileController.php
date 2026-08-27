<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\EventRegistration;
use App\Services\TicketCardGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect('/?login=1');
        }

        return view('pages.profile');
    }

    public function eventShow(int $id): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect('/?login=1');
        }

        $registration = EventRegistration::where('id', $id)
            ->where('user_id', Auth::id())
            ->with('event')
            ->first();

        return view('pages.profile-event-show', [
            'registration' => $registration && $registration->event ? $registration : null,
        ]);
    }

    public function downloadTicket(int $id, TicketCardGenerator $generator): Response|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect('/?login=1');
        }

        $registration = EventRegistration::where('id', $id)
            ->where('user_id', Auth::id())
            ->with('event')
            ->first();

        abort_if(! $registration || ! $registration->event, 404);
        abort_unless(
            in_array($registration->status, ['approved', 'confirmed'], true) && $registration->ticket_number,
            403,
            'This ticket is not approved yet.'
        );

        $png = $generator->render($registration);
        $filename = 'sabha-ticket-' . $registration->ticket_number . '.png';

        return response($png, 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
