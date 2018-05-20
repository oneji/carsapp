<?php

namespace App\Http\Controllers\Admin;

use App\Role;
use App\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class AclController extends Controller
{
    /**
     * Get all roles and permissions.
     * 
     * @return \Illuminate\Http\Response
     */
    public function get()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();

        return response()->json([
            'acl' => [
                'roles' => $roles,
                'permissions' => $permissions
            ]
        ]);
    }

    public function update(Request $request)
    {
        $roles = $request->all();

        foreach($roles as $r) {
            $role = Role::where('id', $r['id'])->first();

            $role->syncPermissions($r['permissions']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Роли успешно обновлены!'
        ]);
    } 

    public function createRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'display_name' => 'required'
        ]);

        if($validator->fails()) {
            $errors = $validator->messages()->toJson();
            return response()->json([
                'success' => false,
                'message' => $errors
            ]);
        }

        $role = new Role;
        $role->name = $request->name;
        $role->display_name = $request->display_name;
        $role->description = $request->description;
        $role->save();

        return response()->json([
            'success' => true,
            'message' => 'Роль успешно создана!'
        ]);
    }

    public function createPermission(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'display_name' => 'required'
        ]);

        if($validator->fails()) {
            $errors = $validator->messages()->toJson();
            return response()->json([
                'success' => false,
                'message' => $errors
            ]);
        }

        $permission = new Permission;
        $permission->name = $request->name;
        $permission->display_name = $request->display_name;
        $permission->description = $request->description;
        $permission->save();

        return response()->json([
            'success' => true,
            'message' => 'Право доступа успешно создано!'
        ]);
    }
}
