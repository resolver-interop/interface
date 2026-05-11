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
     *       equivalent to that specified by the [_ResolverService_][] method
     *       `resolveParameters()`.
     *
     *     - Implementations MUST throw [_ResolverThrowable_][] if resolution
     *       of `$method` is attempted and fails. Orchestrating
     *       implementations (those that iterate over the methods of a class
     *       looking for [_ReflectionMethodResolver_][] attributes) MAY skip
     *       methods with no applicable attribute; the MUST-throw rule
     *       applies when resolution is invoked, not when the orchestrator
     *       declines to invoke it.
     *
     *     - Implementations MAY assign a replacement object to `$object` to
     *       support immutable update patterns (e.g., invoking a `with*()`
     *       method that returns a modified clone).
     */
    public function resolveMethod(
        IocContainer $ioc,
        ReflectionMethod $method,
        object &$object,
    ) : void;
}
