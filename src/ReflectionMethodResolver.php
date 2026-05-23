<?php
declare(strict_types=1);

namespace ResolverInterop\Interface;

use IocInterop\Interface\IocContainer;
use ReflectionMethod;

/**
 * [_ReflectionMethodResolver_][] affords method injection when implemented
 * on a method-targeted [_Attribute_][].
 *
 * - Directives:
 *
 *     - An [_Attribute_][] implementing this interface MUST NOT be declared
 *       with `Attribute::IS_REPEATABLE`.
 */
interface ReflectionMethodResolver
{
    /**
     * Invokes the [_ReflectionMethod_][] on the `$object`.
     *
     * - Directives:
     *
     *     - Implementations MUST support parameter injection using logic
     *       identical to that specified by [_ReflectionParametersResolver_][].
     *
     *     - Implementations MUST invoke `$method` on `$object` with the
     *       resolved arguments.
     *
     *     - Implementations MUST throw [_ResolverThrowable_][] if resolution
     *       fails.
     */
    public function resolveMethod(
        IocContainer $ioc,
        ReflectionMethod $method,
        object $object,
    ) : void;
}
