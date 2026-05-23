<?php
declare(strict_types=1);

namespace ResolverInterop\Interface;

use IocInterop\Interface\IocContainer;
use ReflectionProperty;

/**
 * [_ReflectionPropertyResolver_][] affords property injection when implemented
 * on a property-targeted [_Attribute_][].
 *
 * - Directives:
 *
 *     - An [_Attribute_][] implementing this interface MUST NOT be declared
 *       with `Attribute::IS_REPEATABLE`.
 */
interface ReflectionPropertyResolver
{
    /**
     * Sets the [_ReflectionProperty_][] on an object.
     *
     * - Directives:
     *
     *     - Implementations MUST resolve the `$property` in this order:
     *
     *         - Implementations MAY attempt to resolve the `$property` using
     *           implementation-specific logic; such logic is not defined
     *           herein.
     *
     *         - Otherwise, implementations MUST resolve the `$property` to the
     *           [_IocContainer_][] service whose name matches the property
     *           type as produced by [_ReflectionTypeResolver_][], via
     *           `$ioc->getService()`.
     *
     *     - Implementations MUST throw [_ResolverThrowable_][] if resolution
     *       of `$property` is attempted and fails.
     */
    public function resolveProperty(
        IocContainer $ioc,
        ReflectionProperty $property,
        object $object,
    ) : void;
}
