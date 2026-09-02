<?php

namespace App\Livewire\Admin\Analytics;

use App\Models\Business;
use App\Models\BusinessReferral;
use App\Models\OneToOneMeeting;
use App\Models\User;
use Livewire\Component;

class Index extends Component
{
    public string $dateFrom = '';

    public string $dateTo = '';

    public string $leaderboardTab = 'givers';

    public function setLeaderboardTab(string $tab): void
    {
        $this->leaderboardTab = $tab;
    }

    public function resetFilters(): void
    {
        $this->dateFrom = '';
        $this->dateTo = '';
    }

    private function applyDateRange($query, string $column)
    {
        if ($this->dateFrom !== '') {
            $query->whereDate($column, '>=', $this->dateFrom);
        }
        if ($this->dateTo !== '') {
            $query->whereDate($column, '<=', $this->dateTo);
        }

        return $query;
    }

    public function render()
    {
        $referralQuery = fn () => $this->applyDateRange(BusinessReferral::query(), 'created_at');
        $meetingQuery = fn () => $this->applyDateRange(OneToOneMeeting::query(), 'meeting_at');

        $totalMembers = User::whereNotIn('role', ['admin', 'sub_admin'])->count();
        $totalBusinesses = Business::where('status', 'approved')->count();
        $totalMeetings = $meetingQuery()->count();
        $totalReferrals = $referralQuery()->count();
        $closedReferrals = $referralQuery()->where('status', 'closed')->count();
        $businessGenerated = (float) $referralQuery()->where('status', 'closed')->sum('amount');

        $months = collect(range(5, 0))->map(fn ($i) => now()->subMonths($i));
        $monthLabels = $months->map(fn ($m) => $m->format('M'))->all();
        $referralsByMonth = $months->map(function ($m) {
            return BusinessReferral::whereYear('created_at', $m->year)->whereMonth('created_at', $m->month)->count();
        })->all();

        $statusSegments = collect(['pending' => 'amber', 'in_progress' => 'sky', 'closed' => 'emerald', 'rejected' => 'rose'])
            ->map(function ($color, $status) use ($referralQuery) {
                return ['label' => ucfirst(str_replace('_', ' ', $status)), 'value' => $referralQuery()->where('status', $status)->count(), 'color' => $color];
            })->values()->all();

        $topGivers = User::select('users.id', 'users.name', 'users.city', 'users.phone')
            ->selectSub(function ($q) {
                $this->applyDateRange($q->from('business_referrals')->selectRaw('count(*)')->whereColumn('giver_id', 'users.id'), 'created_at');
            }, 'referrals_given_count')
            ->orderByDesc('referrals_given_count')
            ->having('referrals_given_count', '>', 0)
            ->limit(10)
            ->get();

        $topReceivers = User::select('users.id', 'users.name', 'users.city', 'users.phone')
            ->selectSub(function ($q) {
                $this->applyDateRange($q->from('business_referrals')->selectRaw('count(*)')->whereColumn('receiver_id', 'users.id')->where('status', 'closed'), 'created_at');
            }, 'closed_referrals_count')
            ->selectSub(function ($q) {
                $this->applyDateRange($q->from('business_referrals')->selectRaw('coalesce(sum(amount), 0)')->whereColumn('receiver_id', 'users.id')->where('status', 'closed'), 'created_at');
            }, 'business_value')
            ->orderByDesc('business_value')
            ->having('closed_referrals_count', '>', 0)
            ->limit(10)
            ->get();

        $topNetworkers = User::select('users.id', 'users.name', 'users.city', 'users.phone')
            ->selectSub(function ($q) {
                $this->applyDateRange(
                    $q->from('one_to_one_meetings')->selectRaw('count(*)')
                        ->where(fn ($sub) => $sub->whereColumn('user_id', 'users.id')->orWhereColumn('with_member_id', 'users.id')),
                    'meeting_at',
                );
            }, 'meetings_count')
            ->orderByDesc('meetings_count')
            ->having('meetings_count', '>', 0)
            ->limit(10)
            ->get();

        return view('livewire.admin.analytics.index', [
            'totalMembers' => $totalMembers,
            'totalBusinesses' => $totalBusinesses,
            'totalMeetings' => $totalMeetings,
            'totalReferrals' => $totalReferrals,
            'closedReferrals' => $closedReferrals,
            'businessGenerated' => $businessGenerated,
            'monthLabels' => $monthLabels,
            'referralsByMonth' => $referralsByMonth,
            'statusSegments' => $statusSegments,
            'topGivers' => $topGivers,
            'topReceivers' => $topReceivers,
            'topNetworkers' => $topNetworkers,
        ]);
    }
}
