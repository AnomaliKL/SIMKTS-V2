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

        if (app()->runningInConsole()) {
            return;
        }

        // Kode di bawah ini hanya berjalan jika diakses via web browser normal
        if (Schema::hasTable('banks')) {
            $setup = Bank::first();

            if ($setup && $setup->smtp_email && $setup->smtp_app_password) {
                Config::set('mail.mailers.smtp.username', $setup->smtp_email);
                Config::set('mail.mailers.smtp.password', $setup->smtp_app_password);
                Config::set('mail.from.address', $setup->smtp_email);
            }
        }
    }
}
