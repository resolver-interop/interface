<?php
declare(strict_types=1);

namespace ResolverInterop\Interface;

use IocInterop\Interface\IocContainer;
use ReflectionParameter;

/**
 * [_ReflectionParametersResolver_][] affords resolving an array of
 * [_ReflectionParameter_][] instances into an array of arguments.
 */
interface ReflectionParametersResolver
{
    /**
     * Resolves the `$parameters` into the `$arguments`.
     *
     * - Directives:
     *
     *     - Implementations MUST NOT attempt to resolve a
     *       [_ReflectionParameter_][] whose name or position already exists
     *       in the `$arguments` keys.
     *
     *     - When resolving a [_ReflectionParameter_][] to an argument,
     *       implementations MUST do so using logic identical to that specified
     *       by [_ReflectionParameterResolver_][].
     *
     *     - Implementations MUST retain each resolved [_ReflectionParameter_][]
     *       by name in the `$arguments`.
     *
     *     - Implementations MUST `resolve()` every [_Resolvable_][] at the top
     *       level of the `$arguments` array.
     *
     * - Notes:
     *
     *     - **Mixed name and position keys for the same parameter in the
     *       `$arguments` pass through unchanged.** Named and positional
     *       keys referring to the same parameters are preseved in the
     *       returned array as-is.
     *
     *     - **Extra and out-of-range keys in `$arguments` pass through
     *       unchanged.** Keys that do not correspond to any parameter name
     *       or position, and positional keys whose integer index exceeds the
     *       parameter count, are preserved in the returned array as-is.
     *
     *     - **Resolved parameters are retained by name.** Pre-filled
     *       positional arguments retain their integer keys; newly-resolved
     *       parameters are keyed on their parameter name, not their position.
     *
     *     - **Resolve all `$arguments`.** Callers might pass one or more
     *       [_Resolvable_][] as an argument; the implementation has to resolve
     *       them before returning.
     *
     * @param ReflectionParameter[] $parameters
     * @param mixed[] $arguments
     * @return mixed[]
     */
    public function resolveParameters(
        IocContainer $ioc,
        array $parameters,
        array $arguments = [],
    ) : array;
}
