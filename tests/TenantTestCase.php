<?php
namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Tests\Traits\InteractsWithTenancy;
use Tests\TestCase;

abstract class TenantTestCase extends TestCase
{
    use InteractsWithTenancy;
    
    protected $tenantUrl;
    
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();
        
        $this->setUpTenancy();
        
        return $app;
    }
    
    protected function refreshApplication()
    {
        parent::refreshApplication();
        $this->artisan('migrate:fresh');
    }
    
    protected function setUp()
    {
        parent::setUp();

        $this->tenantUrl = 'http://testing.' . env('TENANT_URL_BASE');
        
        $this->artisan('migrate:fresh', [
            '--no-interaction' => 1,
            '--force' => 1
        ]);
        
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