<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class FeedbackServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Merge configuration
        $this->mergeConfigFrom(
            config_path('feedback.php'), 'feedback'
        );
    }

    public function boot(): void
    {
        // Publish configuration
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/feedback.php' => config_path('feedback.php'),
            ], 'feedback-config');
        }
    }
}