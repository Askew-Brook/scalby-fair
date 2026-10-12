<?php

namespace App\Providers;

use App\Listeners\SendWalkContactEnquiry;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Statamic\Events\FormSubmitted;

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
        Event::listen(FormSubmitted::class, SendWalkContactEnquiry::class);

        // Statamic::vite('app', [
        //     'resources/js/cp.js',
        //     'resources/css/cp.css',
        // ]);
    }
}
