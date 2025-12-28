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
        if (config('app.env') === 'local' || config('app.debug')) {
            \Illuminate\Support\Facades\DB::listen(function ($query) {
                $sql = $query->sql;
                $bindings = $query->bindings;
                $time = $query->time;

                try {
                    foreach ($bindings as $binding) {
                        $value = is_numeric($binding) ? $binding : "'".$binding."'";
                        $sql = preg_replace('/\?/', $value, $sql, 1);
                    }
                } catch (\Exception $e) {
                    // Ignore binding errors
                }

                \Illuminate\Support\Facades\Log::channel('database')->info('SQL Query', [
                    'sql' => $sql,
                    'time' => $time . 'ms',
                ]);

                if ($time > 1000) {
                    \Illuminate\Support\Facades\Log::channel('database')->warning('Slow Query Detected', [
                        'sql' => $sql,
                        'time' => $time . 'ms',
                    ]);
                }
            });
        }
    }
}
