<?php

namespace Tests\Feature\Auth;

use App\User;
use App\Notifications\TenantCreated;
use Tests\TenantTestCase;

class RegistrationTest extends TenantTestCase
{
    
    protected $url;

    protected function duringSetup()
    {

        $this->url = env('APP_URL') . '/register';
    }

    /** @test */
    public function browseFromLandingPageOnly()
    {

        //Setup Tenant
        $this->setUpHostnames(true);
        $this->setUpWebsites(true, true);
        $this->activateTenant();

        // Visit registration page from www subdomain
        $this->get($this->url)
            ->assertViewIs('auth.register')
            ->assertSee('Register - ' . env('APP_NAME'));
            
        // Visit registration page from tenant URL
        $this->get($this->tenantUrl . '/register')
            ->assertStatus(404);
            
    }
    
    /** @test */
    public function sendRegistration()
    {
        
        // Visit Page first for correct redirect
        $this->get($this->url);
        
        // Send invalid request
        $this->post($this->url, [])
            ->assertRedirect($this->url)
            ->assertSessionHasErrors();
        
        // Send valid response
        $this->post($this->url, [
            'name' => 'Josh',
            'email' => 'test123@test.com',
            'password' => 'secret',
            'password_confirmation' => 'secret',
            'fqdn' => 'testing.' . env('TENANT_URL_BASE')
            ])
            ->assertRedirect('http://testing.' . env('TENANT_URL_BASE') . '/login');

        // Make sure tenant is created
        $this->assertTenantExists('testing.' . env('TENANT_URL_BASE'));
        
        // Make sure same fqdn can't be registered
        $this->post($this->url, [
            'name' => 'Josh K',
            'email' => 'test@test.com',
            'password' => 'Test Pass',
            'fqdn' => 'testing.' . env('TENANT_URL_BASE')
        ])
            ->assertRedirect($this->url)
            ->assertSessionHasErrors();
        
    }
    
}