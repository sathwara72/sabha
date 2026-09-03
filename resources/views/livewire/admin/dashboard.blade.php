<div class="space-y-4 font-outfit">
    {{-- Top Header & Date Range Filters --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight leading-tight">Dashboard Overview</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Platform-wide networking activity, referral performance, and leaderboards</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap self-start sm:self-auto">
            <div class="flex items-center gap-1.5 bg-white p-1 rounded-xl border border-slate-200/90 shadow-2xs">
                <input
                    type="date"
                    wire:model.live="dateFrom"
                    class="rounded-lg bg-slate-50/50 px-2.5 py-1.5 text-xs font-semibold text-slate-800 outline-none border border-slate-200 focus:bg-white focus:border-primary transition-colors"
                />
                <span class="text-[11px] font-bold text-slate-400">to</span>
                <input
                    type="date"
                    wire:model.live="dateTo"
                    class="rounded-lg bg-slate-50/50 px-2.5 py-1.5 text-xs font-semibold text-slate-800 outline-none border border-slate-200 focus:bg-white focus:border-primary transition-colors"
                />
            </div>

            @if ($dateFrom || $dateTo)
                <button
                    wire:click="resetFilters"
                    class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-slate-900 active:scale-95 transition-all cursor-pointer shadow-2xs"
                >
                    Clear
                </button>
            @endif
        </div>
    </div>

    {{-- 6 Metric Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="bg-white p-3 rounded-2xl border border-slate-200/80 shadow-xs text-center flex flex-col justify-center">
            <p class="text-xl font-black text-slate-900">{{ number_format($totalMembers) }}</p>
            <p class="text-[11px] font-bold text-slate-500 mt-0.5">Active Members</p>
        </div>
        <div class="bg-white p-3 rounded-2xl border border-slate-200/80 shadow-xs text-center flex flex-col justify-center">
            <p class="text-xl font-black text-slate-900">{{ number_format($totalBusinesses) }}</p>
            <p class="text-[11px] font-bold text-slate-500 mt-0.5">Businesses</p>
        </div>
        <div class="bg-white p-3 rounded-2xl border border-slate-200/80 shadow-xs text-center flex flex-col justify-center">
            <p class="text-xl font-black text-primary">{{ number_format($totalMeetings) }}</p>
            <p class="text-[11px] font-bold text-slate-500 mt-0.5">1-to-1 Meetings</p>
        </div>
        <div class="bg-white p-3 rounded-2xl border border-slate-200/80 shadow-xs text-center flex flex-col justify-center">
            <p class="text-xl font-black text-primary">{{ number_format($totalReferrals) }}</p>
            <p class="text-[11px] font-bold text-slate-500 mt-0.5">Total Referrals</p>
        </div>
        <div class="bg-white p-3 rounded-2xl border border-slate-200/80 shadow-xs text-center flex flex-col justify-center">
            <p class="text-xl font-black text-emerald-600">{{ number_format($closedReferrals) }}</p>
            <p class="text-[11px] font-bold text-slate-500 mt-0.5">Closed Deals</p>
        </div>
        <div class="bg-white p-3 rounded-2xl border border-slate-200/80 shadow-xs text-center flex flex-col justify-center">
            <p class="text-xl font-black text-emerald-600 truncate" title="₹{{ number_format($businessGenerated) }}">₹{{ number_format($businessGenerated) }}</p>
            <p class="text-[11px] font-bold text-slate-500 mt-0.5">Business Volume</p>
        </div>
    </div>

    {{-- Charts Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs space-y-3">
            <div class="flex items-center justify-between">
                <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Referrals per Month</h4>
                <span class="text-[11px] font-semibold text-slate-400">Last 6 months</span>
            </div>
            <x-charts.bar-chart :labels="$monthLabels" :values="$referralsByMonth" color="primary" />
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs space-y-3">
            <div class="flex items-center justify-between">
                <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Referral Status Breakdown</h4>
                <span class="text-[11px] font-semibold text-slate-400">Current selection</span>
            </div>
            @if (array_sum(array_column($statusSegments, 'value')) > 0)
                <x-charts.donut-chart :segments="$statusSegments" />
            @else
                <p class="text-xs text-slate-400 italic py-12 text-center">No referrals in this range.</p>
            @endif
        </div>
    </div>

    {{-- All 3 Leaderboards Side-by-Side in 3 Columns --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Card 1: Top Referral Givers --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 flex flex-col justify-between space-y-3">
            <div class="space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                    <div class="flex items-center gap-2">
                        <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-50 text-primary border border-blue-100">
                            <x-icon name="send" class="h-3.5 w-3.5" />
                        </div>
                        <div>
                            <h3 class="text-xs font-bold text-slate-900">Top Referral Givers</h3>
                            <p class="text-[10px] text-slate-500 font-medium">Members passing most opportunities</p>
                        </div>
                    </div>
                </div>

                @if ($topGivers->isEmpty())
                    <p class="py-10 text-center text-xs text-slate-400 italic">No referrals given yet.</p>
                @else
                    <div class="space-y-1.5">
                        @foreach ($topGivers as $i => $u)
                            <div class="flex items-center justify-between p-2 rounded-xl {{ $i % 2 === 0 ? 'bg-slate-50/80' : 'bg-white' }} hover:bg-blue-50/40 transition-colors">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-[10px] font-black {{ $i === 0 ? 'bg-amber-100 text-amber-800' : ($i === 1 ? 'bg-slate-200 text-slate-700' : ($i === 2 ? 'bg-amber-700/15 text-amber-900' : 'text-slate-400')) }}">
                                        {{ $i + 1 }}
                                    </span>
                                    <span class="text-xs font-bold text-slate-800 truncate">{{ $u->name }}</span>
                                </div>
                                <span class="text-[11px] font-black text-primary shrink-0 bg-blue-50 border border-blue-100 px-2 py-0.5 rounded-lg">
                                    {{ $u->referrals_given_count }} referrals
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Card 2: Top Closers (Business Value) --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 flex flex-col justify-between space-y-3">
            <div class="space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                    <div class="flex items-center gap-2">
                        <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100">
                            <x-icon name="trending-up" class="h-3.5 w-3.5" />
                        </div>
                        <div>
                            <h3 class="text-xs font-bold text-slate-900">Top Closers</h3>
                            <p class="text-[10px] text-slate-500 font-medium">Most business volume closed</p>
                        </div>
                    </div>
                </div>

                @if ($topReceivers->isEmpty())
                    <p class="py-10 text-center text-xs text-slate-400 italic">No closed deals yet.</p>
                @else
                    <div class="space-y-1.5">
                        @foreach ($topReceivers as $i => $u)
                            <div class="flex items-center justify-between p-2 rounded-xl {{ $i % 2 === 0 ? 'bg-slate-50/80' : 'bg-white' }} hover:bg-emerald-50/40 transition-colors">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-[10px] font-black {{ $i === 0 ? 'bg-amber-100 text-amber-800' : ($i === 1 ? 'bg-slate-200 text-slate-700' : ($i === 2 ? 'bg-amber-700/15 text-amber-900' : 'text-slate-400')) }}">
                                        {{ $i + 1 }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-slate-800 truncate">{{ $u->name }}</p>
                                        <p class="text-[10px] text-slate-400">{{ $u->closed_referrals_count }} closed</p>
                                    </div>
                                </div>
                                <span class="text-[11px] font-black text-emerald-700 shrink-0 bg-emerald-50 border border-emerald-100 px-2 py-0.5 rounded-lg">
                                    ₹{{ number_format((float) $u->business_value) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Card 3: Top Networkers (1-to-1 Meetings) --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 flex flex-col justify-between space-y-3">
            <div class="space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                    <div class="flex items-center gap-2">
                        <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-100">
                            <x-icon name="users" class="h-3.5 w-3.5" />
                        </div>
                        <div>
                            <h3 class="text-xs font-bold text-slate-900">Top Networkers</h3>
                            <p class="text-[10px] text-slate-500 font-medium">Most 1-to-1 meetings completed</p>
                        </div>
                    </div>
                </div>

                @if ($topNetworkers->isEmpty())
                    <p class="py-10 text-center text-xs text-slate-400 italic">No meetings logged yet.</p>
                @else
                    <div class="space-y-1.5">
                        @foreach ($topNetworkers as $i => $u)
                            <div class="flex items-center justify-between p-2 rounded-xl {{ $i % 2 === 0 ? 'bg-slate-50/80' : 'bg-white' }} hover:bg-indigo-50/40 transition-colors">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-[10px] font-black {{ $i === 0 ? 'bg-amber-100 text-amber-800' : ($i === 1 ? 'bg-slate-200 text-slate-700' : ($i === 2 ? 'bg-amber-700/15 text-amber-900' : 'text-slate-400')) }}">
                                        {{ $i + 1 }}
                                    </span>
                                    <span class="text-xs font-bold text-slate-800 truncate">{{ $u->name }}</span>
                                </div>
                                <span class="text-[11px] font-black text-indigo-700 shrink-0 bg-indigo-50 border border-indigo-100 px-2 py-0.5 rounded-lg">
                                    {{ $u->meetings_count }} meetings
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
