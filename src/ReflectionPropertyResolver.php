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
     *     - Implementations MUST throw [_ResolverThrowable_][] if the
     *       `$property` cannot be resolved.
     */
    public function resolveProperty(
        IocContainer $ioc,
        ReflectionProperty $property,
        object $object
    ) : void;
}
