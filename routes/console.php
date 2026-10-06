<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('zybra:check', function () {
    $checks = app(\App\Services\ZybraService::class)->diagnostics();

    $this->table(['Check', 'OK', 'Result'], array_map(
        fn ($c) => [$c[0], $c[1] ? 'yes' : 'NO', $c[2]],
        $checks
    ));

    return collect($checks)->every(fn ($c) => $c[1]) ? 0 : 1;
})->purpose('Verify the Zybra API key and the lookups the sync depends on (read-only)');
