<?php

namespace App\Providers;

use App\Services\Hikvision\HikvisionInterface;
use App\Services\Hikvision\HikvisionService;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use GuzzleRetry\GuzzleRetryMiddleware;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class HikvisionServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ClientInterface::class, function () {
            $stack = HandlerStack::create();
            $stack->push(GuzzleRetryMiddleware::factory());

            return new Client(['handler' => $stack]);
        });

        $this->app->bind(HikvisionInterface::class, HikvisionService::class);

        $this->app->when(HikvisionService::class)
            ->needs('$url')
            ->give(function () {
                return env('HIKVISION_URL');
        });

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
