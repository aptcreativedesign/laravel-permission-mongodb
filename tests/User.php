<?php

namespace AptCD\Permission\Test;

use Illuminate\Auth\Authenticatable;
use Jenssegers\Mongodb\Eloquent\Model;
use AptCD\Permission\Traits\HasRoles;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

/**
 * Class User
 * @package AptCD\Permission\Test
 */
class User extends Model implements AuthorizableContract, AuthenticatableContract
{
    use HasRoles, Authorizable, Authenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['email'];

    public $timestamps = false;

    protected $collection = 'users';
    protected $connection = 'mongodb';
}
