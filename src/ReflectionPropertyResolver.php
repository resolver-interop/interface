<?php
declare(strict_types=1);

namespace ResolverInterop\Interface;

use IocInterop\Interface\IocContainer;
use ReflectionProperty;

/**
 * [_ReflectionPropertyResolver_][] affords setting a property on an object.
 *
 * - Notes:
 *
 *     - **This interface can be implemented as an attribute.** Doing so allows
 *       implementors to define custom resolution approaches for consumers to
 *       apply to specific [_ReflectionProperty_][]s.
 */
interface ReflectionPropertyResolver
{
    /**
     * Sets the [_ReflectionProperty_][] on an object.
     *
     * - Directives:
     *
     *     - Implementations MUST throw [_ResolverThrowable_][] if resolution
     *       of `$property` is attempted and fails. Orchestrating
     *       implementations (those that iterate over the properties of a
     *       class looking for [_ReflectionPropertyResolver_][] attributes)
     *       MAY skip properties with no applicable attribute; the MUST-throw
     *       rule applies when resolution is invoked, not when the
     *       orchestrator declines to invoke it.
     */
    public function resolveProperty(
        IocContainer $ioc,
        ReflectionProperty $property,
        object $object,
    ) : void;
}
