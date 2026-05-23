<?php
declare(strict_types=1);

namespace ResolverInterop\Interface;

use IocInterop\Interface\IocContainer;
use ReflectionParameter;

/**
 * [_ReflectionParameterResolver_][] affords resolving a
 * [_ReflectionParameter_][] to an argument value.
 *
 * This interface is also suitable for implementation on an [_Attribute_][] to
 * resolve a custom argument on a parameter.
 *
 * - Directives:
 *
 *     - An [_Attribute_][] implementing this interface MUST NOT be declared
 *       with `Attribute::IS_REPEATABLE`.
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
     *         - If the `$parameter` has one or more [_Attribute_][] that implements
     *           [_ReflectionParameterResolver_][], implementations MUST resolve
     *           the `$parameter` using only the first such [_Attribute_][].
     *
     *         - Otherwise, if the `$parameter` type is resolvable using logic
     *           identical to [_ReflectionTypeResolver_][] **and**
     *           `$ioc->hasService()` returns `true` for that resolved type,
     *           implementations MUST resolve the `$parameter` to that service
     *           via `$ioc->getService()`.
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
     *
     * - Notes:
     *
     *     - **Variadic parameters resolve to a single value.** Implementations
     *       resolve a variadic `$parameter` once (via attribute, type, or
     *       default) and return that value as the variadic argument.
     *       Spreading across multiple variadic slots is not specified by
     *       this interface.
     */
    public function resolveParameter(
        IocContainer $ioc,
        ReflectionParameter $parameter,
    ) : mixed;
}
