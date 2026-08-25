<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\EventRegistration;
use Illuminate\Http\RedirectResponse;
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
}
