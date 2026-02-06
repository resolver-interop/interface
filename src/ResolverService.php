<?php
declare(strict_types=1);

namespace ResolverInterop\Interface;

use IocInterop\Interface\IocContainer;
use ReflectionParameter;
use ReflectionType;

/**
 * [_ResolverService_][] affords resolving classes, calls, parameter arrays, and
 * types.
 */
interface ResolverService extends ReflectionParameterResolver
{
    /**
     * Resolves the `$class` to return a new instance.
     *
     * - Directives:
     *
     *     - Implementations MUST support parameter injection on the `$class`
     *       constructor using logic equivalent to that specified by
     *       `resolveParameters()`.
     *
     *     - Implementations MAY support [_ReflectionPropertyResolver_][]
     *       attributes on the instantiated `$class` properties.
     *
     *     - Implementations MAY support [_ReflectionMethodResolver_][]
     *       attributes on the instantiated `$class` methods.
     *
     *     - Implementations MAY support other forms of injection not specified
     *       herein.
     *
     *     - Implementations MUST throw [_ResolverThrowable_][] if the `$class`
     *       cannot be resolved.
     *
     * @template T of object
     * @param class-string<T> $class
     * @param mixed[] $arguments
     * @return T
     */
    public function resolveClass(
        IocContainer $ioc,
        string $class,
        array $arguments = [],
    ) : object;

    /**
     * Does the `$class` exist, and is it instantiable?
     *
     * @param string $class
     */
    public function isResolvableClass(string $class) : bool;

    /**
     * Resolves the `$callable` to return its result.
     *
     * - Directives:
     *
     *     - Implementations MUST support parameter injection on the `$callable`
     *       using logic equivalent to that specified by `resolveParameters()`.
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
     *       implementations MUST do so using logic equivalent to that specified
     *       by [_ReflectionParameterResolver_][].
     *
     *     - Implementations MUST retain each resolved [_ReflectionParameter_][]
     *       by name in the `$arguments`.
     *
     *     - Implementations MUST resolve all [_Resolvable_][] objects in
     *       the `$arguments`.
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

    /**
     * Resolves a [_ReflectionType_][] to a string, or `null` if it cannot be
     * be resolved.
     *
     * - Notes:
     *
     *     - **TBD** Typically only for named types, but may help to convert
     *       union and intersection types to a single named type.
     *
     * @return ?string
     */
    public function resolveType(
        IocContainer $ioc,
        ReflectionType $type,
    ) : mixed;
}
