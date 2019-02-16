<?php

namespace App\GraphQL\Resolvers

use App\User;
use App\Tenant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Connection;
use Illuminate\Auth\Events\Registered;

class RegisterResolver {
    
    public function resolve($root, array $args) {

        event(new Registered($this->createTenant($args['input'])));

        return [ 'status' => 'success',
           'message' => 'Registration was successful! Please use the below link to go to the login page',
           'redirect' => 'http://' . $args['input']['fqdn'] . '/login'
          ];
    }

    /**
     * Create a new Tenant instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Containers\Core\Models\User
     */
    protected function createTenant(array $data)
    {
        
        //Set Database security to LOW
        app(Connection::class)->statement("SET GLOBAL validate_password_policy=LOW");

        //Create Tenant
        $tenant = Tenant::createFrom($data['fqdn']);

        //Create a new user object
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'active' => true
        ]);

        return $user;
    }
}