<?php

namespace Tests\Traits;

use Laravel\Passport\ClientRepository;
use Illuminate\Support\Facades\DB;
use DateTime;
use App\User;

trait InteractsWithPassport
{

    protected $headers = [];
    protected $scopes = [];
    protected $user;
    
    public function createUserWithToken()
    {
        config(['database.default' => 'tenant']);
        
        $clientRepository = new ClientRepository();
        $client = $clientRepository->createPersonalAccessClient(
            null, 'Test Personal Access Client', 'http://'.$this->tenantUrl
        );
    
        DB::table('oauth_personal_access_clients')->insert([
            'client_id' => $client->id,
            'created_at' => new DateTime,
            'updated_at' => new DateTime,
        ]);
        
        $this->user = factory(User::class)->create();
        $token = $this->user->createToken('TestToken', $this->scopes)->accessToken;
        $this->headers['Accept'] = 'application/json';
        $this->headers['Authorization'] = 'Bearer '.$token;
    }

    public function getJson($uri, array $headers = [])
    {
        return parent::getJson($this->addBaseUrl($uri), array_merge($this->headers, $headers));
    }
    
    public function postJson($uri, array $data = [], array $headers = [])
    {
        return parent::postJson($this->addBaseUrl($uri), $data, array_merge($this->headers, $headers));
    }
        
    public function putJson($uri, array $data = [], array $headers = [])
    {
        return parent::putJson($this->addBaseUrl($uri), $data, array_merge($this->headers, $headers));
    }
        
    public function deleteJson($uri, array $data = [], array $headers = [])
    {
        return parent::deleteJson($this->addBaseUrl($uri), $data, array_merge($this->headers, $headers));
    }
    
    protected function addBaseUrl($uri)
    {
        return 'http://'.$this->tenantUrl . $uri;
    }
}