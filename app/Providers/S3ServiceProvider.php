<?php

namespace App\Providers;

use Aws\S3\S3Client;
use Illuminate\Support\ServiceProvider;

class S3ServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(S3Client::class, function ($app) {
            return new S3Client([
                'version' => 'latest',
                'region'  => env('AWS_DEFAULT_REGION'),
                'credentials' => [
                    'key'    => env('AWS_ACCESS_KEY_ID'),
                    'secret' => env('AWS_SECRET_ACCESS_KEY'),
                ],
                'http' => [
                    'verify' => false,  
                ],
            ]);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
