<?php

namespace AptCD\Permission\Contracts;

use MongoDB\Laravel\Relations\BelongsToMany;
use AptCD\Permission\Exceptions\RoleDoesNotExist;

/**
 * Interface RoleInterface
 * @package AptCD\Permission\Contracts
 */
interface RoleInterface
{
    /**
     * A role may be given various permissions.
     * @return BelongsToMany
     */
    public function permissions(): BelongsToMany;

    /**
     * Find a role by its name and guard name.
     *
     * @param string $name
     * @param string|null $guardName
     *
     * @return RoleInterface
     *
     * @throws RoleDoesNotExist
     */
    public static function findByName(string $name, $guardName): RoleInterface;

    /**
     * Determine if the user may perform the given permission.
     *
     * @param string|PermissionInterface $permission
     *
     * @return bool
     */
    public function hasPermissionTo($permission): bool;
}
