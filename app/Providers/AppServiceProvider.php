<?php

namespace App\Providers;

use App\Fieldtypes\ArticleBody;
use App\Listeners\SendContactEnquiry;
use App\Support\ArticleLivePreview;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Statamic\CP\LivePreview;
use Statamic\Events\FormSubmitted;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LivePreview::class, ArticleLivePreview::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ArticleBody::register();

        Event::listen(FormSubmitted::class, SendContactEnquiry::class);

        // Statamic::vite('app', [
        //     'resources/js/cp.js',
        //     'resources/css/cp.css',
        // ]);
    }
}
