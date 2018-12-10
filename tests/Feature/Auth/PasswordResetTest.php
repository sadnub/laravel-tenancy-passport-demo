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
        

        $this->url = $this->tenantUrl . '/password';
    }
    
    protected function getValidToken($user)
    {
        return Password::broker()->createToken($user);
    }
    
    /** @test */
    public function browseEmailPage()
    {
        // Visit password reset email page
        $this->get($this->url.'/reset')
            ->assertViewIs('auth.passwords.email')
            ->assertSee('Reset Password - ' . env('APP_NAME'));
            
    }
    
    /** @test */
    public function browseResetPageWithInvalidToken()
    {
        
        // Visit reset page without valid token
        $this->get($this->url.'reset/invalidtoken')
            ->assertStatus(404);
            
    }
    
    /** @test */
    public function sendResetEmail()
    {
        Notification::fake();
        
        // Visit Page first for correct redirect
        $this->get($this->url.'/reset');
        
        // Send invalid request
        $this->post($this->url.'/email', [])
            ->assertRedirect($this->url.'/reset')
            ->assertSessionHasErrors();
            
        $user = factory(User::class)->create();
        
        // Send valid response
        $this->post($this->url.'/email', [
            'email' => $user->email,
            ])
            ->assertRedirect($this->url.'/reset')
            ->assertSessionHasNoErrors();
        
        //Make sure email was sent
        Notification::assertSentTo($user, \Illuminate\Auth\Notifications\ResetPassword::class);
        
        // Send invalid email
        $this->post($this->url.'/email', [
            'email' => 'test@email.com',
        ])
            ->assertRedirect($this->url.'/reset')
            ->assertSessionHasErrors();
    }
    
    /** @test */
    public function resetPassword()
    {
        
        $user = factory(User::class)->create();
        
        $token = $this->getValidToken($user);
        
        $this->get($this->url.'/reset/'.$token)
            ->assertOk()
            ->assertViewIs('auth.passwords.reset')
            ->assertSee('Reset Password - ' . env('APP_NAME'));
            
        $this->post($this->url.'/reset', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-awesome-password',
            'password_confirmation' => 'new-awesome-password',    
        ])
            ->assertRedirect($this->tenantUrl . '/home');
            
        $this->assertTrue(Hash::check('new-awesome-password', $user->fresh()->password));
        $this->assertAuthenticatedAs($user);
    }
    
}