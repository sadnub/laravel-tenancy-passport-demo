<?php

namespace Tests\Feature\Auth;

use App\User;
use Tests\TenantTestCase;

class RegistrationTest extends TenantTestCase
{
    
    protected $url;

    protected function duringSetup()
    {

        $this->url = env('APP_URL') . '/api/v1/register';
    }
    
    /** @test */
    public function sendRegistration()
    {
        
        // Send invalid request
        $this->post($this->url, [])
            ->assertStatus(422);
        
        // Send valid response
        $this->post($this->url, [
            'name' => 'Josh',
            'email' => 'test123@test.com',
            'password' => 'secret',
            'password_confirmation' => 'secret',
            'fqdn' => 'testing.' . env('TENANT_URL_BASE')
        ])
        ->assertOk()
        ->JsonFragment([
            'redirect' => 'http://testing'. env('TENANT_URL_BASE'). '/login'
        ]);

        // Make sure tenant is created
        $this->assertTenantExists('testing.' . env('TENANT_URL_BASE'));
        
        // Make sure same fqdn can't be registered
        $this->post($this->url, [
            'name' => 'Josh K',
            'email' => 'test@test.com',
            'password' => 'Test Pass',
            'fqdn' => 'testing.' . env('TENANT_URL_BASE')
        ])
        ->assertStatus(422);
        
    }
    
}