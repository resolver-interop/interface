<?php
declare(strict_types=1);

namespace ResolverInterop\Interface;

use IocInterop\Interface\IocContainer;
use ReflectionMethod;

/**
 * [_ReflectionMethodResolver_][] affords invoking a method on an object.
 */
interface ReflectionMethodResolver
{
    /**
     * Invokes the [_ReflectionMethod_][] on the `$object`.
     *
     * - Directives:
     *
     *     - Implementations MUST invoke `$method` on `$object` with the
     *       resolved arguments.
     *
     *     - Implementations MUST support parameter injection using logic
     *       identical to that specified by
     *       [_ReflectionParametersResolver_][]'s `resolveParameters()`.
     *
     *     - Implementations MUST throw [_ResolverThrowable_][] if resolution
     *       of `$method` is attempted and fails. Orchestrating
     *       implementations (those that iterate over the methods of a class
     *       looking for [_ReflectionMethodResolver_][] attributes) MAY skip
     *       methods with no applicable attribute; the MUST-throw rule
     *       applies when resolution is invoked, not when the orchestrator
     *       declines to invoke it.
     */
    public function resolveMethod(
        IocContainer $ioc,
        ReflectionMethod $method,
        object $object,
    ) : void;
}
