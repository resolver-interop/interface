<?php
declare(strict_types=1);

namespace ResolverInterop\Interface;

use IocInterop\Interface\IocContainer;
use ReflectionProperty;

/**
 * [_ReflectionPropertyResolver_][] affords setting a property on an object.
 */
interface ReflectionPropertyResolver
{
    /**
     * Sets the [_ReflectionProperty_][] on an object.
     *
     * - Directives:
     *
     *     - Implementations MUST set the value of `$property` on `$object`.
     *
     *     - If `$property` has an [_Attribute_][] that implements
     *       [_ReflectionPropertyResolver_][], implementations MUST resolve
     *       the `$property` using that attribute. If more than one such
     *       attribute is present, implementations MUST use the first one
     *       returned by `ReflectionProperty::getAttributes()` and MUST
     *       ignore the rest.
     *
     *     - Implementations MAY support other forms of property resolution
     *       not specified herein.
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
