<?php
namespace Tests;

use Tests\TestCase;
use Tests\Traits\InteractsWithTenancy;
use Tests\Traits\CreatesApplication;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TenantTestCase extends BaseTestCase
{
    use CreatesApplication, InteractsWithTenancy;
    
    protected $tenantUrl;
    
    protected function setUp()
    {
        parent::setUp();

        $this->tenantUrl = 'testing.' . env('TENANT_URL_BASE');
        $this->setUpTenancy();
        $this->activateTenant();
        
        $this->duringSetup();
        
    }
    
    protected function duringSetup()
    {
        //
    }
    
    protected function tearDown()
    {
        $this->cleanupTenancy();
        parent::tearDown();
    }
    
    protected function assertTenantExists($fqdn)
    {

        $this->assertDatabaseHas('hostnames', ['fqdn' => $fqdn], env('DB_CONNECTION'));
        
    }

}