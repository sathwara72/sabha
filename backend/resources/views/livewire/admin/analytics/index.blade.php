<div class="space-y-5 font-outfit">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-foreground">Network Analytics</h1>
            <p class="text-xs text-muted">Platform-wide networking activity, referral performance, and leaderboards</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <input type="date" wire:model.live="dateFrom" class="rounded-xl border border-border bg-white px-3 py-2 text-xs text-foreground outline-none focus:border-primary" />
            <span class="text-xs text-muted-foreground">to</span>
            <input type="date" wire:model.live="dateTo" class="rounded-xl border border-border bg-white px-3 py-2 text-xs text-foreground outline-none focus:border-primary" />
            @if ($dateFrom || $dateTo)
                <button wire:click="resetFilters" class="rounded-xl border border-border bg-white px-3 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer">Clear</button>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="bg-white p-3.5 rounded-2xl border border-border shadow-xs text-center">
            <p class="text-xl font-extrabold text-primary">{{ $totalMembers }}</p>
            <p class="text-[12px] text-muted-foreground mt-0.5">Members</p>
        </div>
        <div class="bg-white p-3.5 rounded-2xl border border-border shadow-xs text-center">
            <p class="text-xl font-extrabold text-primary">{{ $totalBusinesses }}</p>
            <p class="text-[12px] text-muted-foreground mt-0.5">Businesses</p>
        </div>
        <div class="bg-white p-3.5 rounded-2xl border border-border shadow-xs text-center">
            <p class="text-xl font-extrabold text-primary">{{ $totalMeetings }}</p>
            <p class="text-[12px] text-muted-foreground mt-0.5">1-to-1 Meetings</p>
        </div>
        <div class="bg-white p-3.5 rounded-2xl border border-border shadow-xs text-center">
            <p class="text-xl font-extrabold text-primary">{{ $totalReferrals }}</p>
            <p class="text-[12px] text-muted-foreground mt-0.5">Referrals</p>
        </div>
        <div class="bg-white p-3.5 rounded-2xl border border-border shadow-xs text-center">
            <p class="text-xl font-extrabold text-emerald-600">{{ $closedReferrals }}</p>
            <p class="text-[12px] text-muted-foreground mt-0.5">Closed Referrals</p>
        </div>
        <div class="bg-white p-3.5 rounded-2xl border border-border shadow-xs text-center">
            <p class="text-xl font-extrabold text-emerald-600">₹{{ number_format($businessGenerated) }}</p>
            <p class="text-[12px] text-muted-foreground mt-0.5">Business Generated</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-border shadow-xs space-y-3">
            <h4 class="text-xs font-bold text-foreground">Referrals per Month</h4>
            <x-charts.bar-chart :labels="$monthLabels" :values="$referralsByMonth" color="primary" />
        </div>
        <div class="bg-white p-4 rounded-2xl border border-border shadow-xs space-y-3">
            <h4 class="text-xs font-bold text-foreground">Referral Status Breakdown</h4>
            @if (array_sum(array_column($statusSegments, 'value')) > 0)
                <x-charts.donut-chart :segments="$statusSegments" />
            @else
                <p class="text-[12px] text-muted-foreground italic">No referrals in this range.</p>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-border shadow-xs">
        <div class="flex items-center gap-1 p-3 border-b border-border">
            @foreach (['givers' => 'Top Referral Givers', 'receivers' => 'Top Closers (Business Value)', 'networkers' => 'Top Networkers'] as $tab => $label)
                <button
                    wire:click="setLeaderboardTab('{{ $tab }}')"
                    class="px-3 py-1.5 text-xs font-bold rounded-xl transition-all cursor-pointer {{ $leaderboardTab === $tab ? 'bg-primary-soft text-primary' : 'text-muted hover:text-foreground' }}"
                >{{ $label }}</button>
            @endforeach
        </div>

        <div class="p-4">
            @if ($leaderboardTab === 'givers')
                @if ($topGivers->isEmpty())
                    <p class="py-8 text-center text-xs text-muted-foreground italic">No referrals given in this range yet.</p>
                @else
                    <div class="space-y-2">
                        @foreach ($topGivers as $i => $u)
                            <div class="flex items-center justify-between px-3 py-2 rounded-xl {{ $i % 2 === 0 ? 'bg-slate-50/60' : '' }}">
                                <span class="text-xs font-bold text-foreground">#{{ $i + 1 }} {{ $u->name }}</span>
                                <span class="text-xs font-extrabold text-primary">{{ $u->referrals_given_count }} referrals</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            @elseif ($leaderboardTab === 'receivers')
                @if ($topReceivers->isEmpty())
                    <p class="py-8 text-center text-xs text-muted-foreground italic">No closed referrals in this range yet.</p>
                @else
                    <div class="space-y-2">
                        @foreach ($topReceivers as $i => $u)
                            <div class="flex items-center justify-between px-3 py-2 rounded-xl {{ $i % 2 === 0 ? 'bg-slate-50/60' : '' }}">
                                <span class="text-xs font-bold text-foreground">#{{ $i + 1 }} {{ $u->name }}</span>
                                <span class="text-xs font-extrabold text-emerald-700">₹{{ number_format((float) $u->business_value) }} &middot; {{ $u->closed_referrals_count }} closed</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            @else
                @if ($topNetworkers->isEmpty())
                    <p class="py-8 text-center text-xs text-muted-foreground italic">No meetings logged in this range yet.</p>
                @else
                    <div class="space-y-2">
                        @foreach ($topNetworkers as $i => $u)
                            <div class="flex items-center justify-between px-3 py-2 rounded-xl {{ $i % 2 === 0 ? 'bg-slate-50/60' : '' }}">
                                <span class="text-xs font-bold text-foreground">#{{ $i + 1 }} {{ $u->name }}</span>
                                <span class="text-xs font-extrabold text-primary">{{ $u->meetings_count }} meetings</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
