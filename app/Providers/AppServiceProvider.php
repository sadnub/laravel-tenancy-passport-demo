<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\Resource;
use Laravel\Passport\Passport;
use Laravel\Passport\Client;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Passport::withoutCookieSerialization();
        
        $env = $this->app->make(\Hyn\Tenancy\Environment::class);

        //Force tenant connection for hostname if identified
        if ($fqdn = optional($env->hostname())->fqdn) {
            config(['database.default' => 'tenant']);

            /*
            //Gets the passport client that is configured during registration
            $client = Client::find(2);

            config([
                'lighthouse-graphql-passport.client_id' => $client->id,
                'lighthouse-graphql-passport.client_secret' => $client->secret
              ]);*/
        }

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        Passport::ignoreMigrations();
    }
}
