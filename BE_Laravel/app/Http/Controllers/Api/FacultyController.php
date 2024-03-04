<?php

namespace App\Http\Controllers\Api;

use App\Models\Faculty;
use App\Models\Program;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FacultyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $keyCache = 'faculty_all';

            $faculties = Cache::remember($keyCache, config('app.cache_time'), function () {
                return Faculty::join('programs', 'faculties.program_id', '=', 'programs.id')
                ->whereNull('faculties.deleted_at')
                ->select('faculties.*', 'programs.program_name')
                ->get();
            });
            
            if(count($faculties) > 0)
            {
                Log::info('Data Faculty Berhasil Ditampilkan');
                return response()->json([
                    'data' => $faculties,
                    'status' => 'success',
                    'message' => 'Data Faculty Berhasil Ditampilkan',
                ], 200);
            }
    
            Log::info('Data Faculty Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Faculty Kosong',
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
                'faculty_name' => 'required|max:255',
                'program_name' => 'required|max:255|exists:programs,program_name',
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
    
            $program = Program::whereNull('deleted_at')->where('program_name', $request['program_name'])->first();
    
            if(!$program){
                Log::error('The selected program name is invalid.');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'The selected program name is invalid.',
                ], 400);
            }
    
            $storeData['program_id'] = $program->id;
    
            $faculty = Faculty::create($storeData);
    
            Log::info('Data Faculty Berhasil Ditambahakan');
            return response()->json([
                'data' => $faculty,
                'status' => 'success',
                'message' => 'Data Faculty Berhasil Ditambahakan',
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
            $faculty = Faculty::whereNull('deleted_at')->find($id);

            if(!$faculty){
                Log::error('Data Faculty Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Faculty Tidak Ditemukan',
                ], 404);
            }
    
            $validate = Validator::make($request->all(), [
                'faculty_name' => 'required|max:255',
                'program_name' => 'required|max:255|exists:programs,program_name',
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
    
            $program = Program::whereNull('deleted_at')->where('program_name', $request['program_name'])->first();
    
            if(!$program){
                Log::error('The selected program name is invalid.');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'The selected program name is invalid.',
                ], 400);
            } 
    
            $request['program_id'] = $program->id;
            
            $faculty->faculty_name = $request->faculty_name;
            $faculty->program_id = $request->program_id;
            
            $faculty->save();
    
            Log::info('Data Faculty Berhasil Diupdate');
            return response()->json([
                'data' => $faculty,
                'status' => 'success',
                'message' => 'Data Faculty Berhasil Diupdate',
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
            $faculty = Faculty::whereNull('deleted_at')->find($id);

            if(!$faculty){
                Log::error('Data Faculty Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Faculty Tidak Ditemukan',
                ], 404);
            }
    
            if($faculty->delete()){
                Log::info('Data Faculty Berhasil Dihapus');
                return response()->json([
                    'data' => $faculty,
                    'status' => 'success',
                    'message' => 'Data Faculty Berhasil Dihapus',
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
