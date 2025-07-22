<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;

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
    public function boot()
    {
        // Force HTTPS in production
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
            
            // Additional HTTPS enforcement
            if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] !== 'https') {
                $redirectURL = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                header("Location: $redirectURL");
                exit();
            }
        }

        // Ensure storage directories exist (Digital Ocean fix)
        $this->ensureStorageDirectories();

        View::composer('homeadmin', function ($view) {
            $view->with('users', User::all()); // Or any specific query
        });
    }

    /**
     * Ensure storage directories exist for Digital Ocean compatibility
     */
    private function ensureStorageDirectories()
    {
        $directories = [
            storage_path('app'),
            storage_path('app/public'),
            storage_path('app/public/game_images'),
            storage_path('app/public/avatars'),
        ];

        foreach ($directories as $dir) {
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
        }

        // Create test files to verify storage works
        $testFiles = [
            storage_path('app/public/game_images/.gitkeep') => '',
            storage_path('app/public/avatars/.gitkeep') => '',
        ];

        foreach ($testFiles as $file => $content) {
            if (!file_exists($file)) {
                file_put_contents($file, $content);
            }
        }
    }
}
