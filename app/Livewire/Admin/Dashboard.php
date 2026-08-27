<?php

namespace App\Livewire\Admin;

use App\Models\Business;
use App\Models\EventRegistration;
use App\Models\User;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $userCount = User::count();
        $businessCount = Business::count();

        $pendingBiz = Business::where('status', 'pending')->get(['id', 'name']);
        $pendingReg = EventRegistration::with(['user:id,name', 'event:id,title'])
            ->where('status', 'pending')
            ->latest()
            ->take(3)
            ->get();
        $pendingApprovals = $pendingBiz->count() + EventRegistration::where('status', 'pending')->count();

        $alerts = [];
        foreach ($pendingBiz as $b) {
            $alerts[] = "New business '{$b->name}' is awaiting verification.";
        }
        foreach ($pendingReg as $r) {
            $uName = $r->user->name ?? 'A member';
            $eTitle = $r->event->title ?? 'an event';
            $alerts[] = "{$uName} requested a seat for '{$eTitle}'.";
        }
        if (empty($alerts)) {
            $alerts = [
                'All business listings are verified.',
                'All event seat reservations are approved.',
                'No outstanding admin actions pending.',
            ];
        }

        // Only the 3 most-recently-updated rows from each table are needed: the
        // combined top-3 by timestamp can never draw more than 3 from either side.
        $recentBiz = Business::with('user:id,name')
            ->latest('updated_at')
            ->take(3)
            ->get(['id', 'name', 'user_id', 'status', 'updated_at', 'created_at']);
        $recentReg = EventRegistration::with(['user:id,name', 'event:id,title'])
            ->latest('updated_at')
            ->take(3)
            ->get(['id', 'user_id', 'event_id', 'status', 'updated_at', 'created_at']);

        $activities = collect();
        foreach ($recentBiz as $b) {
            $activities->push([
                'user' => $b->user->name ?? $b->name,
                'action' => $b->status === 'approved' ? "Approved business '{$b->name}'" : "Submitted business '{$b->name}'",
                'time' => $b->updated_at ?? $b->created_at,
            ]);
        }
        foreach ($recentReg as $r) {
            $activities->push([
                'user' => $r->user->name ?? 'Member',
                'action' => $r->status === 'approved' ? "Approved booking for '" . ($r->event->title ?? '') . "'" : "Requested booking for '" . ($r->event->title ?? '') . "'",
                'time' => $r->updated_at ?? $r->created_at,
            ]);
        }

        $recentActivities = $activities
            ->sortByDesc(fn ($a) => $a['time'])
            ->take(3)
            ->map(function ($act) {
                $diffMins = $act['time'] ? now()->diffInMinutes($act['time']) : 0;
                if ($diffMins < 1) {
                    $timeStr = 'Just now';
                } elseif ($diffMins < 60) {
                    $timeStr = "{$diffMins} min ago";
                } else {
                    $diffHours = intdiv($diffMins, 60);
                    $timeStr = $diffHours === 1 ? '1 hour ago' : "{$diffHours} hours ago";
                }

                return ['user' => $act['user'], 'action' => $act['action'], 'time' => $timeStr];
            })
            ->values()
            ->all();

        if (empty($recentActivities)) {
            $recentActivities = [['user' => 'System', 'action' => 'Dashboard initialized', 'time' => 'Just now']];
        }

        $tiles = [
            ['icon' => 'briefcase', 'value' => $businessCount, 'label' => 'Businesses', 'soft' => 'bg-primary-soft text-primary'],
            ['icon' => 'users', 'value' => $userCount, 'label' => 'Members', 'soft' => 'bg-sky-50 text-accent'],
            ['icon' => 'calendar', 'value' => $pendingApprovals, 'label' => 'Pending approvals', 'soft' => 'bg-primary-soft text-primary'],
            ['icon' => 'zap', 'value' => '98.2%', 'label' => 'Uptime', 'soft' => 'bg-sky-50 text-accent'],
        ];

        return view('livewire.admin.dashboard', [
            'tiles' => $tiles,
            'alerts' => $alerts,
            'recentActivities' => $recentActivities,
        ]);
    }
}
