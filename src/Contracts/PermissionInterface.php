<?php

namespace AptCD\Permission\Contracts;

use MongoDB\Laravel\Relations\BelongsToMany;
use AptCD\Permission\Exceptions\PermissionDoesNotExist;

/**
 * Interface PermissionInterface
 * @package AptCD\Permission\Contracts
 */
interface PermissionInterface
{
    /**
     * A permission can be applied to roles.
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany;

    /**
     * Find a permission by its name.
     *
     * @param string $name
     * @param string $guardName
     *
     * @throws PermissionDoesNotExist
     *
     * @return PermissionInterface
     */
    public static function findByName(string $name, string $guardName): PermissionInterface;
}
