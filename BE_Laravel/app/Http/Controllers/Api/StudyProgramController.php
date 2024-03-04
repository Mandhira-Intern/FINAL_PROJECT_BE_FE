<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\StudyProgram;
use App\Models\Faculty;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class StudyProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $keyCache = 'study_program_all';

            $studyPrograms = Cache::remember($keyCache, config('app.cache_time'), function () {
                return StudyProgram::join('faculties', 'study_programs.faculty_id', '=', 'faculties.id')
                ->join('programs', 'faculties.program_id', '=', 'programs.id')
                ->whereNull('study_programs.deleted_at')
                ->select('study_programs.*', 'faculties.faculty_name', 'programs.program_name')
                ->get();
            }); 
            
            if(count($studyPrograms) > 0)
            {
                Log::info('Data Study Program Berhasil Ditampilkan');
                return response()->json([
                    'data' => $studyPrograms,
                    'status' => 'success',
                    'message' => 'Data Study Program Berhasil Ditampilkan',
                ], 200);
            }
    
            Log::info('Data Study Program Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Study Program Kosong',
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
                'studyProgram_name' => 'required|max:255',
                'faculty_name' => 'required|max:255|exists:faculties,faculty_name',
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
    
            $faculty = Faculty::whereNull('deleted_at')->where('faculty_name', $request['faculty_name'])->first();
    
            if(!$faculty){
                Log::error('The selected faculty name is invalid.');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'The selected faculty name is invalid.',
                ], 400);
            }
    
            $storeData['faculty_id'] = $faculty->id;
    
            $studyProgram = StudyProgram::create($storeData);
    
            Log::info('Data Study Program Berhasil Ditambahakan');
            return response()->json([
                'data' => $studyProgram,
                'status' => 'success',
                'message' => 'Data Study Program Berhasil Ditambahakan',
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
            $studyProgram = StudyProgram::whereNull('deleted_at')->find($id);

            if(!$studyProgram){
                Log::error('Data Study Program Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Study Program Tidak Ditemukan',
                ], 404);
            }
    
            $validate = Validator::make($request->all(), [
                'studyProgram_name' => 'required|max:255',
                'faculty_name' => 'required|max:255|exists:faculties,faculty_name',
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
    
            $faculty = Faculty::whereNull('deleted_at')->where('faculty_name', $request['faculty_name'])->first();
    
            if(!$faculty){
                Log::error('The selected faculty name is invalid.');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'The selected faculty name is invalid.',
                ], 400);
            } 
    
            $request['faculty_id'] = $faculty->id;
            
            $studyProgram->studyProgram_name = $request->studyProgram_name;
            $studyProgram->faculty_id = $request->faculty_id;
            
            $studyProgram->save();
    
            Log::info('Data Study Program Berhasil Diupdate');
            return response()->json([
                'data' => $faculty,
                'status' => 'success',
                'message' => 'Data Study Program Berhasil Diupdate',
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
            $studyProgram = StudyProgram::whereNull('deleted_at')->find($id);

            if(!$studyProgram){
                Log::error('Data Study Program Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Study Program Tidak Ditemukan',
                ], 404);
            }
    
            if($studyProgram->delete()){
                Log::info('Data Study Program Berhasil Dihapus');
                return response()->json([
                    'data' => $studyProgram,
                    'status' => 'success',
                    'message' => 'Data Study Program Berhasil Dihapus',
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
