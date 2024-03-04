<?php

namespace App\Http\Controllers\Api;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $keyCache = 'role_all';

            $roles = Cache::remember($keyCache, config('app.cache_time'), function () {
                return Role::whereNull('deleted_at')->get();
            });
        
            if(count($roles) > 0)
            {
                Log::info('Data Role Berhasil Ditampilkan');
                return response()->json([
                    'data' => $roles,
                    'status' => 'success',
                    'message' => 'Data Role Berhasil Ditampilkan',
                ], 200);
            }
    
            Log::info('Data Role Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Role Kosong',
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
            $storeData = $request->all();

            $validate = Validator::make($request->all(), [
                'role_name' => 'required|max:255',
            ]);
    
            if($validate->fails())
            {
                Log::error('validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }
    
            $role = Role::create($storeData);
    
            Log::info('Data Role Berhasil Ditambahakan');
            return response()->json([
                'data' => $role,
                'status' => 'success',
                'message' => 'Data Role Berhasil Ditambahakan',
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

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try{
            $role = Role::whereNull('deleted_at')->find($id);

            if(!$role){
                Log::error('Data Role Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Role Tidak Ditemukan',
                ], 404);
            }
    
            $validate = Validator::make($request->all(), [
                'role_name' => 'required|max:255',
            ]);
    
            if($validate->fails())
            {
                Log::error('validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }
    
            $role->role_name = $request->role_name;
            
            $role->save();
    
            Log::info('Data Role Berhasil Diupdate');
            return response()->json([
                'data' => $role,
                'status' => 'success',
                'message' => 'Data Role Berhasil Diupdate',
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            $role = Role::whereNull('deleted_at')->find($id);

            if(!$role){
                Log::error('Data Role Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Role Tidak Ditemukan',
                ], 404);
            }
    
            if($role->delete()){
                Log::info('Data Role Berhasil Dihapus');
                return response()->json([
                    'data' => $role,
                    'status' => 'success',
                    'message' => 'Data Role Berhasil Dihapus',
                ], 200);
            }
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
