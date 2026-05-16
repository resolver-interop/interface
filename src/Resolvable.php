<?php
declare(strict_types=1);

namespace ResolverInterop\Interface;

use IocInterop\Interface\IocContainer;

/**
 * [_Resolvable_][] affords an implementing object the ability to resolve itself
 * to a value.
 *
 * - Notes:
 *
 *     - **`Resolvable` defers container calls until parameter
 *       resolution.** Wrapping a container lookup in a `Resolvable`
 *       lets callers pass it in `$arguments` without forcing the
 *       lookup at construction time; the resolver invokes `resolve()`
 *       only at the moment of parameter resolution.
 */
interface Resolvable
{
    /**
     * Resolves the implementing object to a value.
     *
     * - Directives:
     *
     *     - Implementations MUST return a value that is neither itself a
     *       [_Resolvable_][] nor contains any [_Resolvable_]s.
     *
     *     - Implementations MUST throw [_ResolverThrowable_][] if the object
     *       cannot be resolved.
     *
     * - Notes:
     *
     *     - **Resolve recursively as needed.** Some implementations may return
     *       arrays or objects; the implementation might need to check their
     *       contents for other [_Resolvable_]s so that the return value is
     *       fully and deeply resolved.
     *
     */
    public function resolve(IocContainer $ioc) : mixed;
}
