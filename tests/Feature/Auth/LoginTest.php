<?php

namespace Tests\Feature\Auth;

use App\User;
use Tests\TenantTestCase;
use Auth;

class LoginTest extends TenantTestCase
{
    protected $url;
    
    protected function duringSetup()
    {
        $this->setUpHostnames(true);
        $this->setUpWebsites(true, true);
        $this->activateTenant();
        
        $this->url = $this->tenantUrl . '/login';
    }
    
    /** @test */
    public function browsePage()
    {
        // Visit login page at tenant url
        $this->get($this->url)
            ->assertViewIs('auth.login')
            ->assertSee('Login - ' . env('APP_NAME'));
            
    }
    
    /** @test */
    public function sendLogin()
    {
        // Visit Page first for correct redirect
        $this->get($this->url);
        
        // Send invalid request
        $this->post($this->url, [])
            ->assertRedirect($this->url)
            ->assertSessionHasErrors();
        
        $user = factory(User::class)->create();
        
        // Test user login
        $response = $this->post($this->url, [
            'email' => $user->email,
            'password' => 'secret',
        ])
        ->assertRedirect($this->tenantUrl)
        ->assertSessionHasNoErrors();
        
        //Make sure application logs in use
        $this->assertAuthenticatedAs($user);
    }
    
    /** @test */
    public function loginWithRememberMeToken()
    {
        $user = factory(User::class)->create();
        
        $this->post($this->url, [
            'email' => $user->email,
            'password' => 'secret',
            'remember' => 'on',
        ])
        ->assertRedirect($this->tenantUrl)
        ->assertCookie(Auth::guard()->getRecallerName(), vsprintf('%s|%s|%s', [
            $user->id,
            $user->getRememberToken(),
            $user->password,
        ]));
        
        $this->assertAuthenticatedAs($user);
    }
}