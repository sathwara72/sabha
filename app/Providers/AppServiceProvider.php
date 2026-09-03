<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $mailSettings = \App\Models\Setting::where('key', 'like', 'mail_%')->pluck('value', 'key');
                if ($mailSettings->isNotEmpty()) {
                    if (!empty($mailSettings['mail_mailer'])) {
                        config(['mail.default' => $mailSettings['mail_mailer']]);
                    }
                    if (!empty($mailSettings['mail_host'])) {
                        config(['mail.mailers.smtp.host' => $mailSettings['mail_host']]);
                    }
                    if (!empty($mailSettings['mail_port'])) {
                        config(['mail.mailers.smtp.port' => (int) $mailSettings['mail_port']]);
                    }
                    if (!empty($mailSettings['mail_username'])) {
                        config(['mail.mailers.smtp.username' => $mailSettings['mail_username']]);
                    }
                    if (!empty($mailSettings['mail_password'])) {
                        config(['mail.mailers.smtp.password' => $mailSettings['mail_password']]);
                    }
                    if (isset($mailSettings['mail_encryption'])) {
                        $enc = $mailSettings['mail_encryption'];
                        config(['mail.mailers.smtp.encryption' => ($enc === 'none' || $enc === '' ? null : $enc)]);
                    }
                    if (!empty($mailSettings['mail_from_address'])) {
                        config(['mail.from.address' => $mailSettings['mail_from_address']]);
                    }
                    if (!empty($mailSettings['mail_from_name'])) {
                        config(['mail.from.name' => $mailSettings['mail_from_name']]);
                    }
                }
            }
        } catch (\Throwable $e) {
            // ignore if database not accessible yet
        }
    }
}
