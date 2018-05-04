<?php

namespace App\Http\Controllers\Api;

use App\User;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    /**
     * API Register
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullname' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed'
        ]);

        if($validator->fails()) {
            $error = $validator->messages()->toJson();
            return response()->json(['success'=> false, 'message'=> $error]);
        }

        $user = new User;
        $user->fullname = $request->fullname;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->type = $request->type;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Пользователь успешно зарегистрирован.'
        ]);
    }

    /**
     * API Login
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        if($validator->fails()) {
            $error = $validator->messages()->toJson();
            return response()->json([ 'false' => true, 'message' => $error ]);
        }

        $user = User::where('email', $request->email)->first();
        if(!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователя с таким email не существует.'
            ], 401);
        }
        
        try {
            if(!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Неверные пароль.'
                ], 401);
            }
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Не удалось создать токен.'
            ], 500);
        }       
 
        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $token
        ]);
    }

    /**
     * API Log out
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request) {
        $header = $request->header('Authorization');
        $token = explode(' ', $header);

        try {
            JWTAuth::invalidate($token[1]);
            return response()->json(['success' => true]);
        } catch (JWTException $e) {
            return response()->json(['success' => false, 'error' => 'Что то пошло не так. Пожалуйста повторите попытку.'], 500);
        }
    }

    /**
     * Get currently authenticated user via token.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function user()
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }

        // the token is valid and we have found the user via the sub claim
        return response()->json(compact('user'));
    }
}
