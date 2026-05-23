<?php
declare(strict_types=1);

namespace ResolverInterop\Interface;

use IocInterop\Interface\IocContainer;

/**
 * [_CallResolver_][] affords resolving a callable's parameters and invoking
 * it, returning the call's result.
 */
interface CallResolver
{
    /**
     * Resolves the `$callable` to return its result.
     *
     * - Directives:
     *
     *     - Implementations MUST resolve the `$callable`'s parameters using
     *       logic identical to that specified by
     *       [_ReflectionParametersResolver_][]'s `resolveParameters()`,
     *       including the `$arguments` pre-fill and [_Resolvable_][]-unwrap
     *       semantics specified there.
     *
     *     - Implementations MUST invoke the `$callable` with the resolved
     *       `$arguments` and MUST return the result of that invocation.
     *
     *     - Implementations MUST throw [_ResolverThrowable_][] if the
     *       `$callable` cannot be resolved.
     *
     * @param mixed[] $arguments
     */
    public function resolveCall(
        IocContainer $ioc,
        callable $callable,
        array $arguments = [],
    ) : mixed;
}
