<?php

namespace App\Providers;

use App\Http\Clients\DropifyClient;
use App\Http\Clients\FreebayClient;
use App\Http\Clients\WamazonClient;
use Illuminate\Support\ServiceProvider;
use App\Http\Clients\CustomDropifyClient;
use App\Http\Clients\OrderRetrievableContract;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // $this->app->bind(OrderRetrievableContract::class, function ($app) {
        //     if (request()->customer == 'W') {
        //         return app()->make(DropifyClient::class);
        //     }

        //     if (request()->customer == 'X') {
        //         return app()->make(WamazonClient::class);
        //     }

        //     if (request()->customer == 'Y') {
        //         return app()->make(FreebayClient::class);
        //     }

        //     if (request()->customer == 'Z') {
        //         return app()->make(CustomDropifyClient::class);
        //     }
        // });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
