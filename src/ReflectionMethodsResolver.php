<?php
declare(strict_types=1);

namespace ResolverInterop\Interface;

use IocInterop\Interface\IocContainer;
use ReflectionMethod;

/**
 * [_ReflectionMethodsResolver_][] affords instantiating and invoking
 * [_ReflectionMethodResolver_][] attributes on object methods.
 */
interface ReflectionMethodsResolver
{
    /**
     * Invokes the attributed `$methods` on the `$object`.
     *
     * - Directives:
     *
     *     - For each [_ReflectionMethod_][] in `$methods` that carries one
     *       or more [_Attribute_][] implementing [_ReflectionMethodResolver_][],
     *       implementations MUST invoke only the first such [_Attribute_][].
     *
     *     - Implementations MUST NOT invoke un-attributed methods.
     *
     *     - Implementations MUST throw [_ResolverThrowable_][] if resolution
     *       fails.
     *
     * @param ReflectionMethod[] $methods
     */
    public function resolveMethods(
        IocContainer $ioc,
        array $methods,
        object $object,
    ) : void;
}
