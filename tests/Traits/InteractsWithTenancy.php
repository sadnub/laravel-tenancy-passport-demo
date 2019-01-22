<?php

namespace Tests\Traits;

use Hyn\Tenancy\Contracts\Repositories\HostnameRepository;
use Hyn\Tenancy\Contracts\Repositories\WebsiteRepository;
use Hyn\Tenancy\Database\Connection;
use Hyn\Tenancy\Environment;
use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Models\Website;
use Hyn\Tenancy\Traits\DispatchesEvents;

trait InteractsWithTenancy
{
    use DispatchesEvents;

    /**
     * @var Hostname
     */
    protected $hostname;

    /**
     * @var Website
     */
    protected $website;

    /**
     * Replicated tenant Website.
     *
     * @var Website
     */
    protected $tenant;

    /**
     * @var HostnameRepository
     */
    protected $hostnames;

    /**
     * @var WebsiteRepository
     */
    protected $websites;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * Created websites, so we can destroy databases after a test.
     *
     * @var array|Website[]
     */
    protected $tenants = [];
    
    protected function setUpTenancy()
    {

        $this->websites  = app(WebsiteRepository::class);
        $this->hostnames = app(HostnameRepository::class);
        $this->connection = app(Connection::class);

        //Check if system database has correct tables and hostname and correct
        $this->checkTenancy();

        if ($this->connection->system()->getConfig('driver') !== 'pgsql') {
            $this->connection->system()->beginTransaction();
        }

        $this->handleTenantDestruction();

    }

    protected function handleTenantDestruction()
    {
        Website::created(function (Website $website) {
            $this->tenants[$website->uuid] = $website;
        });
        Website::updating(function (Website $website) {
            if ($website->isDirty('uuid')) {
                $this->tenants[$website->getOriginal('uuid')] = Website::unguarded(function () use ($website) {
                    return new Website($website->getOriginal());
                });
            }
        });
        Website::deleted(function (Website $website) {
            array_forget($this->tenants, $website->uuid);
        });
    }

    /**
     * @param bool $save
     */
    protected function setUpHostnames(bool $save = false)
    {
        Hostname::unguard();
        if (!$this->hostname) {
            $hostname = Hostname::firstOrNew([
                'fqdn' => $this->tenantUrl,
            ]);
            $this->hostname = $hostname;
        }
        Hostname::reguard();
        if ($save && !$this->hostname->exists) {
            $this->hostnames->create($this->hostname);
        }
    }

    protected function activateTenant()
    {
        app(Environment::class)->tenant($this->website);
    }

    /**
     * @param bool $save
     * @param bool $connect
     */
    protected function setUpWebsites(bool $save = false, bool $connect = false)
    {

        if (!$this->website) {
          if ($website = $this->websites->findById($this->hostname->website_id)) {

              $this->website = $website;
          } else {

            $this->website = new Website();
          }
        }

        if ($save && !$this->website->exists) {
            $this->websites->create($this->website);
        }

        if ($connect && $this->hostname->website_id !== $this->website->id) {
            $this->hostnames->attach($this->hostname, $this->website);
        }
    }

    protected function cleanUpTenancy()
    {
        $this->connection->purge();

        if ($this->connection->system()->getConfig('driver') !== 'pgsql') {
            $this->connection->system()->rollback();
        }

        $this->connection->system()->disconnect();
    }

    protected function checkTenancy()
    {
        if ($this->hostnames->findByhostname($this->tenantUrl)) {

            $this->setUpHostnames();
            $this->setUpWebsites();

        } else {

            $this->artisan('migrate:fresh', [
                '--no-interaction' => 1,
                '--force' => 1
            ]);

            $this->setUpHostnames(true);
            $this->setUpWebsites(true, true);
        }
    }
}