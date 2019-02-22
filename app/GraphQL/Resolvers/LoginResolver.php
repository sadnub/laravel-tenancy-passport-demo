<?php

namespace App\GraphQL\Resolvers;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Nuwave\Lighthouse\Exceptions\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Laravel\Passport\Passport;

class LoginResolver
{

    /**
     * @param $rootValue
     * @param array $args
     * @param \Nuwave\Lighthouse\Support\Contracts\GraphQLContext|null $context
     * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo
     * @return array
     * @throws \Exception
     */
    public function resolve($rootValue, array $args, GraphQLContext $context = null, ResolveInfo $resolveInfo)
    {

        if (! Auth::once(['email' => $args['data']['username'], 'password' => $args['data']['password']])) {

            throw new AuthenticationException('Authentication Failed');
        }

        //Create Personal Access Tokena and return a JWT
        if (! $context->request->hasCookie('_token')) {
            
            $token = Auth::user()->createToken('Access Token')->accessToken;
            Cookie::queue('_token', $token, 1800, '/', $context->request->getHost(), false, true);

        }

        return true;
    }

}
