<?php

namespace App\Providers;

use App\Models\Language;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class LanguageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Filter languages for admin views
        View::composer('admin.*', function ($view) {
            // Get only English and Persian languages
            $languages = Language::where('code', 'en')
                ->orWhere('code', 'fa')
                ->get();
          
            // Replace the 'langs' and 'languages' variables commonly used in admin views
            $view->with('langs', $languages);
            $view->with('languages', $languages);
        });
    }
} 