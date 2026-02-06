<?php
declare(strict_types=1);

namespace ResolverInterop\Interface;

use IocInterop\Interface\IocContainer;

/**
 * [_Resolvable_][] affords allowing the implementing object to resolve
 * itself to a value.
 *
 * - Notes:
 *
 *     - **TBD** Use to defer container calls (i.e. lazy salls), then
 *       can use in $arguments without actually creating anything until the
 *       moment of resolution.
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
     *
     *     - **TBD** Must recursively resolve all Resolvable in the resolved
     *       value.
     */
    public function resolve(IocContainer $ioc) : mixed;
}
