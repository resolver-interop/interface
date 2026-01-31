<?php
declare(strict_types=1);

namespace ResolverInterop\Interface;

use IocInterop\Interface\IocContainer;
use ReflectionParameter;

/**
 * [_ReflectionParametersResolver_][] affords resolving an array of
 * [_ReflectionParameter_][]s to an array of named arguments.
 */
interface ReflectionParametersResolver
{
    /**
     * Resolves an array of [_ReflectionParameter_][]s to an array of named
     * arguments, allowing for an array of override arguments.
     *
     * - Directives:
     *
     *     - Implementations MUST NOT attempt to resolve a
     *       [_ReflectionParameter_][] that already exists by name as an
     *       argument.
     *
     *     - When resolving a [_ReflectionParameter_][] to an argument,
     *       implementations MUST do so using logic equivalent to that specified
     *       by [_ReflectionParameterResolver_][].
     *
     *     - Implementations MUST resolve all [_InvokableResolver_][] objects in
     *       the `$arguments`.
     *
     *     - Implementations MUST return an array of arguments keyed by the
     *       [_ReflectionParameter_][] names.
     *
     * @param ReflectionParameter[] $parameters
     * @param mixed[] $arguments
     * @return mixed[]
     */
    public function resolveParameters(
        IocContainer $ioc,
        array $parameters,
        array $arguments = []
    ) : array;
}
