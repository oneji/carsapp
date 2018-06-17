<?php

namespace App\Http\Controllers;

use JWTAuth;
use Illuminate\Http\Request;

class ClientHomeController extends Controller
{
    public function fetchUserProjects()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $user->load(['roles', 'permissions', 'companies', 'stos']);

        return response()->json($user);
    }
}
