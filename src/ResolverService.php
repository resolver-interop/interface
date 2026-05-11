<?php
declare(strict_types=1);

namespace ResolverInterop\Interface;

use IocInterop\Interface\IocContainer;
use ReflectionParameter;
use ReflectionType;

/**
 * [_ResolverService_][] affords resolving classes, calls, parameter arrays, and
 * types.
 *
 * - Notes:
 *
 *     - **The [_IocContainer_][] first-parameter pattern is intentional.**
 *       Unlike most of the surveyed PHP DI/IoC projects, which internalise
 *       the container as instance state, this interface passes the
 *       container explicitly on every call. Implementations are therefore
 *       stateless with respect to a specific container and may be reused
 *       across multiple containers (a single resolver shared by a test
 *       container and a production container, for example). Implementations
 *       that prefer the internalised pattern can wrap a `ResolverService`
 *       and inject their own container, but the interface itself does not
 *       require it.
 *
 *     - **Autowiring mode is implementation-defined.** The surveyed PHP
 *       DI/IoC projects vary across four modes (Always, Opt-Out, Opt-In,
 *       Never). The directives on `resolveClass()`, `resolveCall()`, and
 *       `resolveParameter()` describe what an implementation MUST do
 *       *when* it attempts autowiring; they do not require any particular
 *       mode. An Opt-In implementation may decline to invoke
 *       `resolveParameter()` for unannotated parameters; an Opt-Out
 *       implementation may do the opposite. The reference implementation
 *       uses Always mode.
 *
 *     - **Parameter resolution is structurally required; property and method
 *       resolution are not.** This interface extends
 *       [_ReflectionParameterResolver_][] because every `ResolverService`
 *       method that constructs or invokes something MUST perform parameter
 *       injection. Property and method resolution, by contrast, are MAY
 *       directives on `resolveClass()`: implementations advertise support
 *       by separately implementing [_ReflectionPropertyResolver_][] and
 *       [_ReflectionMethodResolver_][], which consumers detect with an
 *       `instanceof` check. The interface hierarchy mirrors that MUST/MAY
 *       split rather than forcing every implementation to ship no-op
 *       property and method resolvers.
 */
interface ResolverService extends ReflectionParameterResolver
{
    /**
     * Resolves the `$class` to return a new instance.
     *
     * - Directives:
     *
     *     - Implementations MUST resolve the `$class` constructor parameters
     *       using logic equivalent to that specified by `resolveParameters()`,
     *       including the `$arguments` pre-fill and [_Resolvable_][]-unwrap
     *       semantics specified there.
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
     * - Notes:
     *
     *     - **The logic for this method is expressly unspecified.**
     *       Implementations may check `class_exists` and
     *       `ReflectionClass::isInstantiable()`, or apply stricter rules —
     *       e.g., requiring a registered service-name alias, rejecting
     *       abstract bases that lack a concrete binding, or excluding
     *       PHP-internal classes. Consumers should treat a `true` result
     *       as "the implementation is willing to try `resolveClass()`",
     *       not as a guarantee that resolution will succeed.
     *
     * @phpstan-assert-if-true class-string $class
     */
    public function isResolvableClass(string $class) : bool;

    /**
     * Resolves the `$callable` to return its result.
     *
     * - Directives:
     *
     *     - Implementations MUST invoke the `$callable` with the resolved
     *       arguments and MUST return the result of that invocation.
     *
     *     - Implementations MUST resolve the `$callable`'s parameters using
     *       logic equivalent to that specified by `resolveParameters()`,
     *       including the `$arguments` pre-fill and [_Resolvable_][]-unwrap
     *       semantics specified there.
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
     *       by name in the `$arguments`. Pre-filled positional arguments
     *       retain their integer keys; only resolver-produced arguments are
     *       stored by name.
     *
     *     - After resolving all [_ReflectionParameter_][]s, implementations
     *       MUST unwrap any [_Resolvable_][] value in `$arguments` by
     *       calling its `resolve()` method, repeating until no
     *       [_Resolvable_][] remains.
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
     * Resolves a [_ReflectionType_][] to a string, or `null` if it cannot
     * be resolved.
     *
     * - Directives:
     *
     *     - For a [_ReflectionNamedType_][], implementations MUST return
     *       the type name as produced by `ReflectionNamedType::getName()`.
     *
     *     - For a [_ReflectionUnionType_][] or
     *       [_ReflectionIntersectionType_][], implementations MAY return
     *       the name of any branch type — typically one whose name
     *       corresponds to a container service. Implementations that
     *       decline to inspect compound types MUST return `null`.
     *
     *     - If `$type` is `null`, implementations MUST return `null`.
     */
    public function resolveType(
        IocContainer $ioc,
        ?ReflectionType $type,
    ) : ?string;
}
