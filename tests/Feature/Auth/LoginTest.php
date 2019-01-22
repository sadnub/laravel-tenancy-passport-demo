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
        
        $this->url = 'http://'.$this->tenantUrl . '/login';
        
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
        ->assertRedirect('http://'.$this->tenantUrl)
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
        ->assertRedirect('http://'.$this->tenantUrl)
        ->assertCookie(Auth::guard()->getRecallerName(), vsprintf('%s|%s|%s', [
            $user->id,
            $user->getRememberToken(),
            $user->password,
        ]));
        
        $this->assertAuthenticatedAs($user);
    }
}