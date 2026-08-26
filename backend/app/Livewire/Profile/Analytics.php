<?php

namespace App\Livewire\Profile;

use App\Models\BusinessReferral;
use App\Models\OneToOneMeeting;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Analytics extends Component
{
    public function render()
    {
        $userId = Auth::id();

        $meetingsCount = OneToOneMeeting::where('user_id', $userId)->orWhere('with_member_id', $userId)->count();

        $givenCount = BusinessReferral::where('giver_id', $userId)->count();
        $receivedCount = BusinessReferral::where('receiver_id', $userId)->count();
        $closedReceivedCount = BusinessReferral::where('receiver_id', $userId)->where('status', 'closed')->count();
        $businessReceived = (float) BusinessReferral::where('receiver_id', $userId)->where('status', 'closed')->sum('amount');
        $testimonialsOnMyBusiness = BusinessReferral::where('giver_id', $userId)
            ->whereNotNull('testimonial')
            ->where('display_testimonial', true)
            ->count();

        // Last 6 months: referrals given vs. received, per month.
        $months = collect(range(5, 0))->map(fn ($i) => now()->subMonths($i));
        $monthLabels = $months->map(fn ($m) => $m->format('M'))->all();
        $givenByMonth = $months->map(function ($m) use ($userId) {
            return BusinessReferral::where('giver_id', $userId)
                ->whereYear('created_at', $m->year)->whereMonth('created_at', $m->month)
                ->count();
        })->all();
        $receivedByMonth = $months->map(function ($m) use ($userId) {
            return BusinessReferral::where('receiver_id', $userId)
                ->whereYear('created_at', $m->year)->whereMonth('created_at', $m->month)
                ->count();
        })->all();

        $statusSegments = collect(['pending' => 'amber', 'in_progress' => 'sky', 'closed' => 'emerald', 'rejected' => 'rose'])
            ->map(function ($color, $status) use ($userId) {
                $count = BusinessReferral::where(function ($q) use ($userId) {
                    $q->where('giver_id', $userId)->orWhere('receiver_id', $userId);
                })->where('status', $status)->count();

                return ['label' => __('site.profile.referrals.status_' . $status), 'value' => $count, 'color' => $color];
            })->values()->all();

        return view('livewire.profile.analytics', [
            'meetingsCount' => $meetingsCount,
            'givenCount' => $givenCount,
            'receivedCount' => $receivedCount,
            'closedReceivedCount' => $closedReceivedCount,
            'businessReceived' => $businessReceived,
            'testimonialsOnMyBusiness' => $testimonialsOnMyBusiness,
            'monthLabels' => $monthLabels,
            'givenByMonth' => $givenByMonth,
            'receivedByMonth' => $receivedByMonth,
            'statusSegments' => $statusSegments,
        ]);
    }
}
