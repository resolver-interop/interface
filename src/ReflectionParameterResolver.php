<?php
declare(strict_types=1);

namespace ResolverInterop\Interface;

use IocInterop\Interface\IocContainer;
use ReflectionParameter;

/**
 * [_ReflectionParameterResolver_][] affords resolving a
 * [_ReflectionParameter_][] to an argument value.
 *
 * - Notes:
 *
 *     - **This interface can be implemented as an attribute.** Doing so allows
 *       implementors to define custom resolution approaches for consumers to
 *       apply to specific [_ReflectionParameter_][]s. For example, implementors
 *       may declare a `#[GetEnv($name)]` attribute to resolve the
 *       [_ReflectionParameter_][] to an environment value.
 */
interface ReflectionParameterResolver
{
    /**
     * Resolves the [_ReflectionParameter_][] to an argument value.
     *
     * - Directives:
     *
     *     - Implementations MUST resolve the `$parameter` in this order:
     *
     *         - If the `$parameter` has an [_Attribute_][] that implements
     *           [_ReflectionParameterResolver_][], implementations MUST resolve
     *           the `$parameter` using that attribute.
     *
     *         - Otherwise, if the `$parameter` type is a
     *           [_ReflectionNamedType_][], and the container has a service for
     *           that type, implementations MUST resolve the `$parameter` to
     *           that service.
     *
     *         - Otherwise, implementations MAY attempt to resolve the
     *           `$parameter` using implementation-specific logic; such logic is
     *           not defined herein.
     *
     *         - Otherwise, if the `$parameter` has a default value,
     *           implementations MUST resolve the `$parameter` to that value.
     *
     *     - Implementations MUST throw [_ResolverThrowable_][] if the
     *       `$parameter` cannot be resolved.
     */
    public function resolveParameter(
        IocContainer $ioc,
        ReflectionParameter $parameter,
    ) : mixed;
}
