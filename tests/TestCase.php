<?php

namespace Tests;

use Tests\Traits\CreatesApplication;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected function setUp()
    {
        parent::setUp();
        $this->duringSetUp();

    }

    protected function duringSetUp()
    {
       //

    }

    protected function assertTenantExists($fqdn)
    {

        $this->assertDatabaseHas('hostnames', ['fqdn' => $fqdn], env('DB_CONNECTION'));
        
    }
    
}
