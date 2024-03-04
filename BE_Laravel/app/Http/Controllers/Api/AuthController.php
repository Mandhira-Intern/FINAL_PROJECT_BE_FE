<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserRole;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(Request $request){
        try{
            if(!Auth::attempt($request->only('username', 'password'))){
                Log::error('Username atau password salah');
                return response()->json([
                    'data' => null,
                    'status' => 'failed',
                    'message' => 'Username atau password salah'
                ], 401);
            }
            
            $token = Auth::user()->createToken('AuthToken')->accessToken;

            $user = Auth::user();
            $getRole = UserRole::join('roles', 'user_roles.role_id', '=', 'roles.id')->where('user_id', $user->id)->
            select('roles.role_name')->get();

            Log::info('Login berhasil');
            return response()->json([
                'data' => ['user' => $user, 'role' => $getRole,'token' => $token],
                'status' => 'success',
                'message' => 'Login berhasil'
            ], 200);
        }catch(\Exception $e){
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);   
        }
    }

    public function logout(){
        try{
            Auth::user()->token()->revoke();
            
            Log::info('Logout berhasil');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Logout berhasil'
            ], 200);
        }catch(\Exception $e){
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);   
        }
    }
}
