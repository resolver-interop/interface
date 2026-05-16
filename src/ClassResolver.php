<?php
declare(strict_types=1);

namespace ResolverInterop\Interface;

use IocInterop\Interface\IocContainer;

/**
 * [_ClassResolver_][] affords resolving a class to a new instance, and
 * indicating whether a class may be resolved at all.
 */
interface ClassResolver
{
    /**
     * Resolves the `$class` to return a new instance.
     *
     * - Directives:
     *
     *     - Implementations MUST resolve the `$class` constructor parameters
     *       using logic identical to that specified by
     *       [_ReflectionParametersResolver_].
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
     * @param class-string<T> $class The class to instantiate.
     * @param mixed[] $arguments Use these arguments for the class constructor.
     * @return T
     */
    public function resolveClass(
        IocContainer $ioc,
        string $class,
        array $arguments = [],
    ) : object;

    /**
     * May the resolver attempt to resolve `$class`?
     *
     * - Notes:
     *
     *     - **The logic for this method is expressly unspecified.**
     *       Implementations may check `class_exists` and
     *       `ReflectionClass::isInstantiable()`, or apply stricter rules;
     *       e.g., requiring a registered service-name alias, rejecting
     *       abstract bases that lack a concrete binding, or excluding
     *       PHP-internal classes. Consumers should treat a `true` result
     *       as "the implementation is willing to try `resolveClass()`",
     *       not as a guarantee that resolution will succeed.
     */
    public function mayResolveClass(string $class) : bool;
}
