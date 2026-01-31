<?php
declare(strict_types=1);

namespace ResolverInterop\Interface;

use IocInterop\Interface\IocContainer;

/**
 * [_InvokableResolver_][] affords resolving the implementing object to a value.
 *
 * - Notes:
 *
 *     - **TBD** Use e.g. to defer container calls (i.e. Lazy), then
 *       can use in $arguments without actually creating anything until the
 *       moment of resolution.
 */
interface InvokableResolver
{
    /**
     * Resolves the implementing object to a value.
     *
     * - Directives:
     *
     *     - Implementations MUST throw [_ResolverThrowable_][] if the object
     *       cannot be resolved.
     */
    public function __invoke(IocContainer $ioc) : mixed;
}
