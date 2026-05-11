<?php
declare(strict_types=1);

namespace ResolverInterop\Interface;

use IocInterop\Interface\IocContainer;

/**
 * [_Resolvable_][] affords an implementing object resolving itself to a
 * value.
 *
 * - Notes:
 *
 *     - **Use `Resolvable` to defer container calls.** Wrapping a
 *       container lookup in a `Resolvable` lets callers pass it in
 *       `$arguments` without forcing the lookup at construction time;
 *       the resolver invokes `resolve()` only at the moment of
 *       parameter resolution.
 */
interface Resolvable
{
    /**
     * Resolves the implementing object to a value.
     *
     * - Directives:
     *
     *     - Implementations MUST throw [_ResolverThrowable_][] if the object
     *       cannot be resolved.
     */
    public function resolve(IocContainer $ioc) : mixed;
}
