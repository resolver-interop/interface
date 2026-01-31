<?php
declare(strict_types=1);

namespace ResolverInterop\Interface;

use IocInterop\Interface\IocContainer;

/**
 * [_CallResolverService_][] affords invoking a callable with resolved
 * [_ReflectionParameter_][]s and argument overrides.
 *
 */
interface CallResolverService
{
    /**
     * Invokes the `$callable` with `$arguments` overrides, and returns the
     * result.
     *
     * - Directives:
     *
     *     - Implementations MUST resolve the `$callable`
     *       [_ReflectionParameter_][]s and `$arguments` using logic equivalent
     *       to that specified by [_ReflectionParametersResolver_][].
     *
     *     - Implementations MUST throw [_ResolverThrowable_][] if the callable
     *       [_ReflectionParameter_][]s or arguments cannot be resolved.
     *
     * @param mixed[] $arguments
     */
    public function resolveCall(
        IocContainer $ioc,
        callable $callable,
        array $arguments = [],
    ) : mixed;
}
