<?php

namespace App\Http\Controllers\Admin;

use App\Role;
use App\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AclController extends Controller
{
    /**
     * Get all roles and permissions.
     * 
     * @return \Illuminate\Http\Response
     */
    public function get()
    {
        $roles = Role::all();
        $permissions = Permission::all();

        return response()->json([
            'acl' => [
                'roles' => $roles,
                'permissions' => $permissions
            ]
        ]);
    }
}
