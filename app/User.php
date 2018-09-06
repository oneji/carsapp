<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Laratrust\Traits\LaratrustUserTrait;

class User extends Authenticatable implements JWTSubject
{
    use LaratrustUserTrait;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fullname', 'email', 'password', 'type'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier() {
        return $this->getKey(); // Eloquent Model method
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims() {
        return [];
    }

    /**
     * Get user's companies.
     * 
     */
    public function companies()
    {
        return $this->belongsToMany('App\Company');
    }

    /**
     * Get user's stos.
     * 
     */
    public function stos()
    {
        return $this->belongsToMany('App\Sto');
    }

    /**
     * Get car card comments.
     */
    public function comments() 
    {
        return $this->hasMany('App\CarCardComment');
    }

    /**
     * Get user role names
     * 
     * @return  array $roles
     */
    public function getRoleNames()
    {
        $roles = [];
        foreach($this->roles as $role)
            array_push($roles, $role->name);

        return $roles;
    }

    /**
     * Get user permission names
     * 
     * @return  array $permissions
     */
    public function getPermissionNames()
    {
        $permissions = [];
        foreach($this->permissions as $permission) {
            array_push($permissions, $permission->name);
        }
        
        return $permissions;
    }

    /**
     * Get user role permission names
     */
    public function getFilteredPermissionNames()
    {        
        $rolePermissions = $this->getPermissionNames();
        foreach($this->roles as $role) {
            foreach($role->permissions as $permission) {
                array_push($rolePermissions, $permission->name);
            }
        }

        return array_unique($rolePermissions);
    }
}
