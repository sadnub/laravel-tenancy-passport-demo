<?php

namespace App\GraphQL\Resolvers;

use App\Tenant;
use Log;

class CheckDomainResolver {
    
    public function resolve($root, array $args) {

        $valid = !Tenant::tenantExists($args['fqdn']);

        $message = $valid 
            ? "Domain is available!" 
            : "The Domain is taken";

        return [
            'valid' => $valid,
            'message' => $message
            ];
    }

}