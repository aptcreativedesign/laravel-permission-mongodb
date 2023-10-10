<?php

namespace AptCD\Permission\Traits;

use AptCD\Permission\PermissionRegistrar;

/**
 * Trait RefreshesPermissionCache
 * @package AptCD\Permission\Traits
 */
trait RefreshesPermissionCache
{
    public static function bootRefreshesPermissionCache()
    {
        static::saved(function () {
            \app(\config('permission.models.permission'))->forgetCachedPermissions();
        });

        static::deleted(function () {
            \app(\config('permission.models.permission'))->forgetCachedPermissions();
        });
    }
}
