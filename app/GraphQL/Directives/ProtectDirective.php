<?php

namespace App\GraphQL\Directives;

use Nuwave\Lighthouse\Exceptions\AuthenticationException;
use Illuminate\Contracts\Auth\Factory as Auth;
use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\NodeList;
use GraphQL\Language\AST\FieldDefinitionNode;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use Nuwave\Lighthouse\Schema\AST\PartialParser;
use Nuwave\Lighthouse\Schema\AST\ASTHelper;
use Nuwave\Lighthouse\Schema\AST\DocumentAST;
use Nuwave\Lighthouse\Support\Contracts\CreatesContext;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Support\Contracts\NodeManipulator;
use Nuwave\Lighthouse\Support\Contracts\FieldMiddleware;

class ProtectDirective extends BaseDirective implements NodeManipulator, FieldMiddleware
{

    /**
     * The authentication factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /** @var CreatesContext */
    protected $createsContext;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth, CreatesContext $createsContext)
    {
        $this->auth = $auth;
        $this->createsContext = $createsContext;
    }

    /**
     * Directive name.
     *
     * @return string
     */
    public function name(): string
    {
        return 'protect';
    }

    /**
     * Resolve the field directive.
     *
     * @param FieldValue $value
     * @param \Closure   $next
     *
     * @return FieldValue
     */
    public function handleField(FieldValue $value, \Closure $next)
    {

        $resolver = $value->getResolver();

        return $next($value->setResolver(function ($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo) use ($resolver) {
            
            $guards = $this->directiveArgValue('guards', []);

            $this->authenticate($context->request, $guards);

            return $resolver(
                    $root,
                    $args,
                    $this->createsContext->generate($context->request()),
                    $resolveInfo
                );

        }));
    }

    /**
     * @param Node $node
     * @param DocumentAST $documentAST
     *
     * @return DocumentAST
     */
    public function manipulateSchema(Node $node, DocumentAST $documentAST): DocumentAST
    {

        $args = $this->directiveArgValue('guards', []);
        $node = $this->setProtectDirectiveOnFields($node, $args);

        $documentAST->setDefinition($node);

        return $documentAST;
    }

    /**
     * @param ObjectTypeDefinitionNode|ObjectTypeExtensionNode $objectType
     *
     * @throws \Nuwave\Lighthouse\Exceptions\DirectiveException
     *
     * @return ObjectTypeDefinitionNode|ObjectTypeExtensionNode
     */
    protected function setProtectDirectiveOnFields($objectType, array $args)
    {
       
        $objectType->fields = new NodeList(
            collect($objectType->fields)
                ->map(function (FieldDefinitionNode $fieldDefinition) use ($args) {
                    $existingProtectDirective = ASTHelper::directiveDefinition(
                        $fieldDefinition,
                        $this->name()
                    );

                    if ($existingProtectDirective){
                        return $fieldDefinition;

                    } else {  

                        $protectArgValue = collect($args)->implode('", "');

                        $directive = !empty($args)
                            ? PartialParser::directive("@protect(guards: [\"$protectArgValue\"])")
                            : PartialParser::directive("@protect");

                        $fieldDefinition->directives = $fieldDefinition->directives->merge([$directive]);

                        return $fieldDefinition;

                    }
                })
                ->toArray()
        );

        return $objectType;
    }

    /**
     * Determine if the user is logged in to any of the given guards.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $guards
     * @return void
     *
     * @throws \App\Containers\Core\GraphQL\Exceptions\AuthException
     */
    protected function authenticate($request, array $guards)
    {
        if (empty($guards)) {
            $guards = [null];
        }

        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->check()) {
                return $this->auth->shouldUse($guard);
            }
        }

        throw new AuthenticationException("You are not authorized to view this resource");
    }

}
