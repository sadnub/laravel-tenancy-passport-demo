<?php

namespace App\GraphQL\Resolvers;

use Hyn\Tenancy\Repositories\HostnameRepository;
use Log;

class CheckDomainResolver {
    
    public function resolve($root, array $args) {

        $hostname = app(HostnameRepository::class)->findByHostname($args['fqdn']);

        Log::debug($hostname);

        $valid = $hostname
            ? 0 
            : 1;

        Log::debug($valid);

        $message = $valid 
            ? "Domain is available!" 
            : "The Domain is taken";

        return [
            'valid' => $valid,
            'message' => $message
            ];
    }

}