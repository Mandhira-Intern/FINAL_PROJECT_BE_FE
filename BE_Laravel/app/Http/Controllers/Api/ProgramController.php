<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Program;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $keyCache = 'program_all';

            $programs = Cache::remember($keyCache, config('app.cache_time'), function () {
                return Program::whereNull('deleted_at')->get();
            });
        
            if(count($programs) > 0)
            {
                Log::info('Data Program Berhasil Ditampilkan');
                return response()->json([
                    'data' => $programs,
                    'status' => 'success',
                    'message' => 'Data Program Berhasil Ditampilkan',
                ], 200);
            }
    
            Log::info('Data Program Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Program Kosong',
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
                'program_name' => 'required|max:255',
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
    
            $program = Program::create($storeData);
    
            Log::info('Data Program Berhasil Ditambahakan');
            return response()->json([
                'data' => $program,
                'status' => 'success',
                'message' => 'Data Program Berhasil Ditambahakan',
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
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try{
            $program = Program::whereNull('deleted_at')->find($id);

            if(!$program){
                Log::error('Data Program Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Program Tidak Ditemukan',
                ], 404);
            }
    
            $validate = Validator::make($request->all(), [
                'program_name' => 'required|max:255',
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
    
            $program->program_name = $request->program_name;
            
            $program->save();
    
            Log::info('Data Program Berhasil Diupdate');
            return response()->json([
                'data' => $program,
                'status' => 'success',
                'message' => 'Data Program Berhasil Diupdate',
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
            $program = Program::whereNull('deleted_at')->find($id);

            if(!$program){
                Log::error('Data Program Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Program Tidak Ditemukan',
                ], 404);
            }
    
            if($program->delete()){
                Log::info('Data Program Berhasil Dihapus');
                return response()->json([
                    'data' => $program,
                    'status' => 'success',
                    'message' => 'Data Program Berhasil Dihapus',
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
