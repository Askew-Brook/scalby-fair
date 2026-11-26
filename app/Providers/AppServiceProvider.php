<?php

namespace App\Providers;

use App\Fieldtypes\ArticleBody;
use App\Fieldtypes\Bard\EditorialTextColour;
use App\Listeners\SendContactEnquiry;
use App\Support\ArticleLivePreview;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Statamic\CP\LivePreview;
use Statamic\Events\FormSubmitted;
use Statamic\Fieldtypes\Bard\Augmentor;
use Statamic\Statamic;

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
        Augmentor::addExtension('editorialTextColour', new EditorialTextColour);
        Statamic::vite('scalby-fair', ['resources/js/cp.js']);

        Event::listen(FormSubmitted::class, SendContactEnquiry::class);
    }
}
