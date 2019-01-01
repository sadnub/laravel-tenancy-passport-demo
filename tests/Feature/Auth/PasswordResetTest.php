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
        $this->postJson($this->url.'/email', [])
            ->assertStatus(422);
            
        $user = factory(User::class)->create();
        
        // Send valid response
        $this->postJson($this->url.'/email', [
            'email' => $user->email,
        ])
        ->assertOk()
        ->assertJsonFragment([
            'status' => 'success'
        ]);
        
        //Make sure email was sent
        Notification::assertSentTo($user, \App\Notifications\ResetPassword::class);
        
        // Send invalid email
        $this->postJson($this->url.'/email', [
            'email' => 'test@email.com',
        ])
        ->assertOk()
        ->assertJsonFragment([
            'status' => 'error'
        ]);
    }
    
    /** @test */
    public function resetPassword()
    {
        
        $user = factory(User::class)->create();
        
        $token = $this->getValidToken($user);

        $this->postJson($this->url.'/reset', [
            'token' => 'invalid',
            'email' => $user->email,
            'password' => 'new-awesome-password',
            'password_confirmation' => 'new-awesome-password',    
        ])
        ->assertOk()
        ->assertJsonFragment([
            'status' => 'error'
        ]);
            
        $this->postJson($this->url.'/reset', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-awesome-password',
            'password_confirmation' => 'new-awesome-password',    
        ])
        ->assertOk()
        ->assertJsonFragment([
            'status' => 'success'
        ]);
            
        $this->assertTrue(Hash::check('new-awesome-password', $user->fresh()->password));
    }
    
}