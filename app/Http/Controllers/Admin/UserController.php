<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Get all users from database.
     * @param   int $currentUserID
     * 
     * @return  \Illuminate\Http\Response
     */
    public function getAll() 
    {
        $users = User::where('deleted', 0)->with(['companies', 'roles'])->get();
        return response()->json($users);
    }

    /**
     * Delete a user from database.
     * 
     * @param int $id
     */
    public function destroy($id)
    {
        $user = User::where('id', $id)->first();
        $user->deleted = 1;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Пользователь успешно удален!'
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullname' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        if($validator->fails()) {
            $errors = $validator->messages()->toJson();
            return response()->json([
                'success' => false,
                'message' => $errors
            ]);
        }

        if($request->hasFile('avatar')) {
            $fileFullName = $request->file('avatar')->getClientOriginalName(); 
            $filename = pathinfo($fileFullName, PATHINFO_FILENAME);
            $filePath = $request->file('avatar')->path();
            $fileExtension = $request->file('avatar')->getClientOriginalExtension();
            $fileNameToStore = time().'.'.$fileExtension;
            $path = $request->file('avatar')->move(public_path('/uploads/avatars'), $fileNameToStore);  
            $fileNameToStore = 'uploads/avatars/'.$fileNameToStore;
        } else {
            $fileNameToStore = null;
        }

        $user = new User($request->all());
        $user->avatar = $fileNameToStore;
        $user->password = bcrypt($request->password);
        $user->save();
        
        if($request->roles !== null)
            $user->attachRoles(explode(',', $request->roles));
        if($request->permissions !== null)
            $user->attachPermissions(explode(',', $request->permissions));

        return response()->json([
            'success' => true,
            'message' => 'Пользователь успешно создан!',
            'company' => $user
        ]);
    }
}
