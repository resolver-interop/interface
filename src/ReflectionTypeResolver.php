<?php
declare(strict_types=1);

namespace ResolverInterop\Interface;

use IocInterop\Interface\IocContainer;
use ReflectionType;

/**
 * [_ReflectionTypeResolver_][] affords resolving a [_ReflectionType_][] to a
 * single class-string, or `null` if it cannot be resolved.
 */
interface ReflectionTypeResolver
{
    /**
     * Resolves a [_ReflectionType_][] to a single class-string, or `null` if it
     * cannot be resolved.
     *
     * - Directives:
     *
     *     - If `$type` is `null`, implementations MUST return `null`.
     *
     *     - For a [_ReflectionNamedType_][] ...
     *
     *         - Implementations MUST return the type name as produced by
     *           `ReflectionNamedType::getName()` if the name identifies a
     *           class, interface, trait, or enum.
     *
     *         - Implementations MAY return the name as-is, MAY return `null`, or MAY
     *           transform it (e.g., resolving `self` to the declaring
     *           class name) if the name is a PHP built-in scalar (`int`,
     *           `string`, `bool`, `float`, `array`, `object`, `iterable`,
     *           etc.) or pseudo-type (`mixed`, `void`, `never`, `null`,
     *           `self`, `static`, `parent`). The choice is implementation-
     *           defined and implementations SHOULD document their
     *           behaviour.
     *
     *     - For a [_ReflectionUnionType_][] or
     *       [_ReflectionIntersectionType_][], implementations MAY return
     *       the name of any branch type, typically one whose name
     *       corresponds to a service from `$ioc`. Implementations that
     *       decline to inspect compound types MUST return `null`.
     */
    public function resolveType(
        IocContainer $ioc,
        ?ReflectionType $type,
    ) : ?string;
}
