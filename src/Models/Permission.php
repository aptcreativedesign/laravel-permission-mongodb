<?php

namespace AptCD\Permission\Models;

use Illuminate\Support\Collection;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Relations\BelongsToMany;
use AptCD\Permission\Contracts\PermissionInterface;
use AptCD\Permission\Exceptions\PermissionAlreadyExists;
use AptCD\Permission\Exceptions\PermissionDoesNotExist;
use AptCD\Permission\Guard;
use AptCD\Permission\Helpers;
use AptCD\Permission\PermissionRegistrar;
use AptCD\Permission\Traits\HasRoles;
use AptCD\Permission\Traits\RefreshesPermissionCache;

/**
 * Class Permission
 *
 * @package AptCD\Permission\Models
 */
class Permission extends Model implements PermissionInterface
{
    use HasRoles;
    use RefreshesPermissionCache;

    public $guarded = ['id'];
    protected $helpers;

    /**
     * Permission constructor.
     *
     * @param array $attributes
     *
     * @throws \ReflectionException
     */
    public function __construct(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? (new Guard())->getDefaultName(static::class);

        $attributes['parent_permission_name'] = $attributes['parent_permission_name'] ?? null;

        parent::__construct($attributes);

        $this->helpers = new Helpers();

        $this->setTable(config('permission.collection_names.permissions'));
    }

    /**
     * Create new Permission
     *
     * @param array $attributes
     *
     * @return $this|\Illuminate\Database\Eloquent\Model
     * @throws \AptCD\Permission\Exceptions\PermissionAlreadyExists
     * @throws \ReflectionException
     */
    public static function create(array $attributes = [])
    {
        $helpers = new Helpers();
        $attributes['guard_name'] = $attributes['guard_name'] ?? (new Guard())->getDefaultName(static::class);
        $attributes['parent_permission_name'] = $attributes['parent_permission_name'] ?? null;

        if (static::getPermissions()->where('name', $attributes['name'])->where(
            'guard_name',
            $attributes['guard_name']
        )->first()) {
            $name = (string)$attributes['name'];
            $guardName = (string)$attributes['guard_name'];
            throw new PermissionAlreadyExists($helpers->getPermissionAlreadyExistsMessage($name, $guardName));
        }

        return static::query()->create($attributes);
//        return $helpers->checkVersion() ? parent::create($attributes) : static::query()->create($attributes);
    }

    /**
     * Find or create permission by its name (and optionally guardName).
     *
     * @param string $name
     * @param string $guardName
     *
     * @return PermissionInterface
     * @throws \AptCD\Permission\Exceptions\PermissionAlreadyExists
     * @throws \ReflectionException
     */
    public static function findOrCreate(string $name, string $guardName = null, string $paraenPermissionName = null): PermissionInterface
    {
        $guardName = $guardName ?? (new Guard())->getDefaultName(static::class);

        $permission = static::getPermissions()->filter(function ($permission) use ($name, $guardName) {
            return $permission->name === $name && $permission->guard_name === $guardName;
        })->first();

        if (!$permission) {
            $permission = static::create(['name' => $name, 'guard_name' => $guardName, 'parent_permission_name' => $paraenPermissionName]);
        }

        return $permission;
    }

    /**
     * A permission can be applied to roles.
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(config('permission.models.role'));
    }

    /**
     * A permission belongs to some users of the model associated with its guard.
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany($this->helpers->getModelForGuard($this->attributes['guard_name']));
    }

    /**
     * Find a permission by its name (and optionally guardName).
     *
     * @param string $name
     * @param string $guardName
     *
     * @return PermissionInterface
     * @throws PermissionDoesNotExist
     * @throws \ReflectionException
     */
    public static function findByName(string $name, string $guardName = null): PermissionInterface
    {
        $guardName = $guardName ?? (new Guard())->getDefaultName(static::class);

        $permission = static::getPermissions()->filter(function ($permission) use ($name, $guardName) {
            return $permission->name === $name && $permission->guard_name === $guardName;
        })->first();

        if (!$permission) {
            $helpers = new Helpers();
            throw new PermissionDoesNotExist($helpers->getPermissionDoesNotExistMessage($name, $guardName));
        }

        return $permission;
    }

    /**
     * Get the current cached permissions.
     *
     * @return Collection
     */
    protected static function getPermissions(): Collection
    {
        return \app(PermissionRegistrar::class)->getPermissions();
    }
}
