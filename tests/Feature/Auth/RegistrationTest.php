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
    
    /** @test 
        @group failed */
    public function sendRegistration()
    {
        
        // Send invalid request
        $this->postJson($this->url, [])
            ->assertStatus(422);
        
        // Send valid response
        $this->postJson($this->url, [
            'name' => 'Josh',
            'email' => 'test123@test.com',
            'password' => 'secret',
            'password_confirmation' => 'secret',
            'fqdn' => 'testing'
        ])
        ->assertStatus(201)
        ->assertJsonFragment([
            'redirect' => 'http://testing.'. env('TENANT_URL_BASE'). '/login'
        ]);

        // Make sure tenant is created
        $this->assertTenantExists('testing.' . env('TENANT_URL_BASE'));
        
        // Make sure same fqdn can't be registered
        $this->postJson($this->url, [
            'name' => 'Josh K',
            'email' => 'test@test.com',
            'password' => 'Test Pass',
            'fqdn' => 'testing.' . env('TENANT_URL_BASE')
        ])
        ->assertStatus(422);
        
    }
    
}