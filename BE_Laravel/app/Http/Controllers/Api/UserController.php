<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $keyCache = 'user_all';

            $users = Cache::remember($keyCache, config('app.cache_time'), function () {
                return User::whereNull('deleted_at')->get();
            });
        
            if(count($users) > 0)
            {
                Log::info('Data User Berhasil Ditampilkan');
                return response()->json([
                    'data' => $users,
                    'status' => 'success',
                    'message' => 'Data User Berhasil Ditampilkan',
                ], 200);
            }

            Log::info('Data User Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data User Kosong',
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
