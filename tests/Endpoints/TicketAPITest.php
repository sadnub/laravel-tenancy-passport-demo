<?php
namespace Tests\Endpoints;

use App\Ticket;
use Tests\Traits\InteractsWithPassport;
use Tests\TenantTestCase;

class TicketsTest extends TenantTestCase
{
    use InteractsWithPassport;
    
    protected function duringSetup()
    {
        $this->setUpHostnames(true);
        $this->setUpWebsites(true, true);
        $this->activateTenant();
    }
    
    /** @test */
    public function gettingAllTickets()
    {
        
        $ticket = factory(Ticket::class)->create();

        // Without Authentication
        $response = $this->getJson('/api/v1/tickets')
            ->assertStatus(401);
        
        // Create User and Auth Headers
        $this->createUserWithToken();
        
        // With Authencation
        $this->get('/api/v1/tickets')
            ->assertOk()
            ->assertJsonFragment([
                'id' => $ticket->id,
                'title' => $ticket->title,
                'contact' => $ticket->contact,
                'status' => $ticket->status,
                'issue' => $ticket->issue,
            ]);
        
    }
    
    /** @test */
    public function gettingSpecificTicket()
    {

        //Create Ticket
        $ticket = factory(Ticket::class)->create();

        // Without Authentication
        $this->getJson('/api/v1/tickets/12345')
            ->assertStatus(401);
        
        // Create User and Auth Headers
        $this->createUserWithToken();
        
        // With Authentication
        $response = $this->getJson('/api/v1/tickets/'.$ticket->id)
            ->assertOk()
            ->assertJsonFragment([
                'id' => $ticket->id,
                'title' => $ticket->title,
                'contact' => $ticket->contact,
                'status' => $ticket->status,
                'issue' => $ticket->issue,
            ]);
        
        // Get Undefined Ticket
        $this->getJson('/api/v1/tickets/13232323')
            ->assertStatus(404);
    }
    
    /** @test */
    public function creatingTicket()
    {

        // Without Authentication
        $this->postJson('/api/v1/tickets', [])
            ->assertStatus(401);
        
        // Create User and Auth Headers
        $this->createUserWithToken();
        
        // Empty Response
        $this->postJson('/api/v1/tickets', [])
            ->assertStatus(422);
        
        // Create Ticket
        $this->postJson('/api/v1/tickets', [
            'title' => 'Help!',
            'contact' => 'Josh',
            'status' => 'New',
            'issue' => 'I can\'t print!',
            ]
        )->assertStatus(201);
        
    }
    
    /** @test */
    public function updatingTicket()
    {
        //Create Ticket
        $ticket = factory(Ticket::class)->create();
        
        // Without Authentication
        $this->putJson('/api/v1/tickets/'.$ticket->id, [])
            ->assertStatus(401);
        
        // Create User and Auth Headers
        $this->createUserWithToken();
        
        // Update Ticket
        $this->putJson('/api/v1/tickets/'.$ticket->id, [
            'contact' => 'Jash'
            ]
        )->assertOk();
        
        // Update Ticket that doesn't exist
        $this->putJson('/api/v1/tickets/234324', [
            'name' => 'updated_first'
            ]
        )->assertStatus(404);
        
    }
    
    /** @test */
    public function deletingTicket()
    {

        //Create Ticket
        $ticket = factory(Ticket::class)->create();

        // Without Authentication
        $this->deleteJson('/api/v1/tickets/12345')
            ->assertStatus(401);
        
        // Create User and Auth Headers
        $this->createUserWithToken();
        
        // Delete Ticket
        $this->deleteJson('/api/v1/tickets/'.$ticket->id)
            ->assertStatus(202);
        
        // Deleting a ticket that doesn't exist
        $this->deleteJson('/api/v1/tickets/13232323')
            ->assertStatus(404);
    }
    
}