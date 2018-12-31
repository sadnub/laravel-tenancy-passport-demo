<?php

namespace Tests\Feature\Auth;

use App\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Tests\TenantTestCase;

class ResetPasswordTest extends TenantTestCase
{
    
    protected $url;
    
    protected function duringSetup()
    {
        $this->setUpHostnames(true);
        $this->setUpWebsites(true, true);
        $this->activateTenant();
        

        $this->url = $this->tenantUrl . '/api/v1/password';
    }
    
    protected function getValidToken($user)
    {
        return Password::broker()->createToken($user);
    }
    
    
    /** @test */
    public function sendResetEmail()
    {
        Notification::fake();
        
        // Send invalid request
        $this->post($this->url.'/email', [])
            ->assertStatus(422);
            
        $user = factory(User::class)->create();
        
        // Send valid response
        $this->post($this->url.'/email', [
            'email' => $user->email,
        ])
        ->assertOk()
        ->JsonFragment([
            'status' => 'success'
        ]);
        
        //Make sure email was sent
        Notification::assertSentTo($user, \App\Notifications\ResetPassword::class);
        
        // Send invalid email
        $this->post($this->url.'/email', [
            'email' => 'test@email.com',
        ])
        ->assertOk()
        ->JsonFragment([
            'status' => 'error'
        ]);
    }
    
    /** @test */
    public function resetPassword()
    {
        
        $user = factory(User::class)->create();
        
        $token = $this->getValidToken($user);

        $this->post($this->url.'/reset', [
            'token' => 'invalid',
            'email' => $user->email,
            'password' => 'new-awesome-password',
            'password_confirmation' => 'new-awesome-password',    
        ])
        ->assertOk()
        ->JsonFragment([
            'status' => 'error'
        ]);
            
        $this->post($this->url.'/reset', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-awesome-password',
            'password_confirmation' => 'new-awesome-password',    
        ])
        ->assertOk()
        ->JsonFragment([
            'status' => 'success'
        ]);
            
        $this->assertTrue(Hash::check('new-awesome-password', $user->fresh()->password));
    }
    
}