<?php
declare(strict_types=1);

namespace ResolverInterop\Interface;

use IocInterop\Interface\IocContainer;

/**
 * [_ResolverService_][] affords resolving a class name to a new instance of
 * that class.
 */
interface ResolverService
{
    /**
     * Returns a new instance of the `$class` with constructor `$arguments`.
     *
     * - Directives:
     *
     *     - Implementations MUST support constructor injection using logic
     *       equivalent to that specified by [_ReflectionParametersResolver_][].
     *
     *     - Implementations MAY support [_ReflectionPropertyResolver_][]
     *       attributes on the instantiated class properties.
     *
     *     - Implementations MAY support [_ReflectionMethodResolver_][]
     *       attributes on the instantiated class methods.
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
    public function resolve(
        IocContainer $ioc,
        string $class,
        array $arguments = [],
    ) : object;

    /**
     * Does the `$class` exist, and is it instantiable?
     *
     * @param string $class
     */
    public function isResolvable(string $class) : bool;
}
