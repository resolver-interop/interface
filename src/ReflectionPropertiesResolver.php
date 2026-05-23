<?php
declare(strict_types=1);

namespace ResolverInterop\Interface;

use IocInterop\Interface\IocContainer;
use ReflectionProperty;

/**
 * [_ReflectionPropertiesResolver_][] affords instantiating and
 * invoking [_ReflectionPropertyResolver_][] attributes on object properties.
 */
interface ReflectionPropertiesResolver
{
    /**
     * Resolves the attributed `$properties` on the `$object`.
     *
     * - Directives:
     *
     *     - For each [_ReflectionProperty_][] in `$properties` that carries
     *       one or more [_Attribute_][] implementing
     *       [_ReflectionPropertyResolver_][], implementations MUST resolve
     *       the property using only the first such [_Attribute_][].
     *
     *     - Implementations MUST leave un-attributed properties unchanged.
     *
     *     - Implementations MUST throw [_ResolverThrowable_][] if any
     *       attempted resolution fails.
     *
     * @param ReflectionProperty[] $properties
     */
    public function resolveProperties(
        IocContainer $ioc,
        array $properties,
        object $object,
    ) : void;
}
