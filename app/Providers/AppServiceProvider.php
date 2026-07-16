<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use App\Models\Bank;
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
        URL::forceScheme('https');

        if (Schema::hasTable('pengaturans')) {
            $setup = Pengaturan::first();

            // Jika admin sudah mengisi data Gmail di pengaturan, timpa setelan .env
            if ($setup && $setup->smtp_email && $setup->smtp_app_password) {
                Config::set('mail.mailers.smtp.username', $setup->smtp_email);
                Config::set('mail.mailers.smtp.password', $setup->smtp_app_password);
                Config::set('mail.from.address', $setup->smtp_email);
            }
        }
    }
}
