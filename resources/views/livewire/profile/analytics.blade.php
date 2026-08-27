<div class="space-y-4">
    <div>
        <h3 class="text-sm font-bold text-foreground">{{ __('site.profile.analytics.title') }}</h3>
        <p class="text-[12px] text-muted">{{ __('site.profile.analytics.subtitle') }}</p>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
        <div class="glass-card p-3.5 text-center">
            <p class="text-xl font-extrabold text-primary">{{ $meetingsCount }}</p>
            <p class="text-[12px] text-muted-foreground mt-0.5">{{ __('site.profile.analytics.kpi_meetings') }}</p>
        </div>
        <div class="glass-card p-3.5 text-center">
            <p class="text-xl font-extrabold text-primary">{{ $givenCount }}</p>
            <p class="text-[12px] text-muted-foreground mt-0.5">{{ __('site.profile.analytics.kpi_given') }}</p>
        </div>
        <div class="glass-card p-3.5 text-center">
            <p class="text-xl font-extrabold text-primary">{{ $receivedCount }}</p>
            <p class="text-[12px] text-muted-foreground mt-0.5">{{ __('site.profile.analytics.kpi_received') }}</p>
        </div>
        <div class="glass-card p-3.5 text-center">
            <p class="text-xl font-extrabold text-emerald-600">{{ $closedReceivedCount }}</p>
            <p class="text-[12px] text-muted-foreground mt-0.5">{{ __('site.profile.analytics.kpi_closed') }}</p>
        </div>
        <div class="glass-card p-3.5 text-center">
            <p class="text-xl font-extrabold text-emerald-600">₹{{ number_format($businessReceived) }}</p>
            <p class="text-[12px] text-muted-foreground mt-0.5">{{ __('site.profile.analytics.kpi_business_received') }}</p>
        </div>
        <div class="glass-card p-3.5 text-center">
            <p class="text-xl font-extrabold text-primary">{{ $testimonialsOnMyBusiness }}</p>
            <p class="text-[12px] text-muted-foreground mt-0.5">{{ __('site.profile.analytics.kpi_testimonials') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="glass-card p-4 space-y-3">
            <h4 class="text-xs font-bold text-foreground">{{ __('site.profile.analytics.chart_given_title') }}</h4>
            <x-charts.bar-chart :labels="$monthLabels" :values="$givenByMonth" color="primary" :empty-text="__('site.profile.analytics.no_data')" />
        </div>
        <div class="glass-card p-4 space-y-3">
            <h4 class="text-xs font-bold text-foreground">{{ __('site.profile.analytics.chart_received_title') }}</h4>
            <x-charts.bar-chart :labels="$monthLabels" :values="$receivedByMonth" color="emerald" :empty-text="__('site.profile.analytics.no_data')" />
        </div>
    </div>

    <div class="glass-card p-4 space-y-3">
        <h4 class="text-xs font-bold text-foreground">{{ __('site.profile.analytics.chart_status_title') }}</h4>
        @if (array_sum(array_column($statusSegments, 'value')) > 0)
            <x-charts.donut-chart :segments="$statusSegments" />
        @else
            <p class="text-[12px] text-muted-foreground italic">{{ __('site.profile.analytics.no_referrals') }}</p>
        @endif
    </div>
</div>
