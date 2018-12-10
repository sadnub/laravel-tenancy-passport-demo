<?php

namespace Tests\Traits;

use Hyn\Tenancy\Contracts\Repositories\HostnameRepository;
use Hyn\Tenancy\Contracts\Repositories\WebsiteRepository;
use Hyn\Tenancy\Database\Connection;
use Hyn\Tenancy\Environment;
use Hyn\Tenancy\Events\Websites\Identified;
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
        if ($this->connection->system()->getConfig('driver') !== 'pgsql') {
            //$this->connection->system()->beginTransaction();
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
    protected function loadHostnames()
    {
        $this->hostname = Hostname::where('fqdn', $this->tenantUrl)->firstOrFail();
    }
    protected function getReplicatedWebsite(): Website
    {
        $this->tenant = Website::unguarded(function () {
            return Website::firstOrNew([
                'uuid' => $this->tenantUrl
            ]);
        });
        $this->websites->create($this->tenant);
        return $this->tenant;
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
        // Start global tenant transaction.
        //$this->connection->get()->beginTransaction();
    }
    /**
     * @param bool $save
     * @param bool $connect
     */
    protected function setUpWebsites(bool $save = false, bool $connect = false)
    {
        if (!$this->website) {
            $this->website = new Website;
        }
        if ($save && !$this->website->exists) {
            $this->websites->create($this->website);
        }
        if ($connect && $this->hostname->website_id !== $this->website->id) {
            $this->hostnames->attach($this->hostname, $this->website);
        }
    }
    protected function rollbackTenant()
    {
        if ($this->connection->exists() && $this->connection->get()->transactionLevel() > 0) {
            $this->connection->get()->rollBack();
        }
    }
    protected function cleanupTenancy()
    {
        $this->connection->purge();
        collect($this->tenants)
            ->merge(compact('website', 'tenant'))
            ->filter()
            ->each(function ($website) {
                $this->connection->set($website);
                $this->connection->purge();
                $this->websites->delete($website, true);
            });
        if ($this->connection->system()->getConfig('driver') !== 'pgsql') {
            $this->connection->system()->rollback();
        }
        $this->connection->system()->disconnect();
    }
}