<?php
declare(strict_types=1);

namespace ResolverInterop\Interface;

use IocInterop\Interface\IocContainer;
use ReflectionType;

/**
 * [_ReflectionTypeResolver_][] affords resolving a [_ReflectionType_][] to a
 * `string` name, or `null` if it cannot be resolved.
 */
interface ReflectionTypeResolver
{
    /**
     * Resolves a [_ReflectionType_][] to a `string` name, or `null` if it
     * cannot be resolved.
     *
     * - Directives:
     *
     *     - If `$type` is `null`, implementations MUST return `null`.
     *
     *     - Otherwise, if `$type` is a [_ReflectionNamedType_][],
     *       implementations MUST return the type name as produced by
     *       its `getName()` method.
     *
     *     - Otherwise, the logic for determining the return type name is
     *       implementation-defined.
     *
     * - Notes:
     *
     *     - **Compound and other type handling is left to the implementation.**
     *       For example, [_ReflectionUnionType_][] and
     *       [_ReflectionIntersectionType_][] handling may vary between
     *       implementations: one might iterate the branches and return the
     *       first whose name corresponds to an `$ioc` service name; another
     *       might return the compound stringification (e.g. `"Foo|Bar"`) and
     *       let the container lookup handle it; yet another might return `null`
     *       out of hand.
     */
    public function resolveType(
        IocContainer $ioc,
        ?ReflectionType $type,
    ) : ?string;
}
