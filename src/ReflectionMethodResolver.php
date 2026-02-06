<?php
declare(strict_types=1);

namespace ResolverInterop\Interface;

use IocInterop\Interface\IocContainer;
use ReflectionMethod;

/**
 * [_ReflectionMethodResolver_][] affords invoking a method on an object.
 *
 * - Notes:
 *
 *     - **This interface can be implemented as an attribute.** Doing so allows
 *       implementors to define custom resolution approaches for consumers to
 *       apply to specific [_ReflectionMethod_][]s.
 */
interface ReflectionMethodResolver
{
    /**
     * Invokes the [_ReflectionMethod_][] on the `$object`.
     *
     * - Directives:
     *
     *     - Implementations MUST support parameter injection using logic
     *       equivalent to that specified by [_ReflectionParametersResolver_][].
     *
     *     - Implementations MUST throw [_ResolverThrowable_][] if the
     *       `$method` cannot be resolved.
     *
     * - Notes:
     *
     *     - **TBD** $object is by reference so you can set to a replacement
     *       object a la immutability.
     */
    public function resolveMethod(
        IocContainer $ioc,
        ReflectionMethod $method,
        object &$object
    ) : void;
}
