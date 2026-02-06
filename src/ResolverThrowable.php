<?php
declare(strict_types=1);

namespace ResolverInterop\Interface;

use Throwable;

/**
 * [_ResolverThrowable_][] extends [_Throwable_][] to mark an [_Exception_][] as
 * resolver-related.
 *
 * It adds no class members.
 */
interface ResolverThrowable extends Throwable
{
}
