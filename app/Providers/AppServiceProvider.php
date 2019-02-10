<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\Resource;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Resource::withoutWrapping();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        $env = app(\Hyn\Tenancy\Environment::class);

        //Force tenant connection for hostname is identified
        if ($fqdn = optional($env->hostname())->fqdn) {
            config(['database.default' => 'tenant']);

            //Gets the passport client that is configured during registration
            $client = Client::first();

            config('client-id', $client->id);
            config('client-secret', $client->secret);
        }

        Passport::ignoreMigrations();
    }
}
