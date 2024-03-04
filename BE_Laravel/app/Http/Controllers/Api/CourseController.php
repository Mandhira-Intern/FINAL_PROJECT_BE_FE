<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Course;
use App\Models\User;
use App\Models\UserRole;
use App\Models\Lecture;
use App\Models\StudyProgram;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $keyCache = 'course_all';

            $courses = Cache::remember($keyCache, config('app.cache_time'), function () {
                return Course::join('lectures', 'courses.lecture_id', '=', 'lectures.id')
                ->join('study_programs', 'courses.studyProgram_id', '=', 'study_programs.id')
                ->join('users', 'lectures.user_id', '=', 'users.id')
                ->select('courses.*', 'users.name as lecture_name', 'study_programs.studyProgram_name')
                ->whereNull('courses.deleted_at')->get();
            });
            
            if(count($courses) > 0)
            {
                Log::info('Data Course Berhasil Ditampilkan');
                return response()->json([
                    'data' => $courses,
                    'status' => 'success',
                    'message' => 'Data Course Berhasil Ditampilkan',
                ], 200);
            }
    
            Log::info('Data Course Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Course Kosong',
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
                'course_name' => 'required|max:255',
                'capacity' => 'required|Integer',
                'semester' => 'required|Integer',
                'lecture_name' => 'required|max:255|exists:users,name',
                'study_program_name' => 'required|max:255|exists:study_programs,studyProgram_name',
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
    
            //dapat memiliki nama yang sama tapi berbeda role
            $user = User::whereNull('deleted_at')->where('name', $request['lecture_name'])->get();
    
            if(!$user){
                Log::error('The selected lecture name is invalid.');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'The selected lecture name is invalid.',
                ], 400);
            }
    
            //lakukan pengecekan hanya untuk role lecture
            foreach($user as $u){
                $temp = UserRole::whereNull('user_roles.deleted_at')->where('user_id', $u->id)
                ->join('roles', 'user_roles.role_id', '=', 'roles.id')->where('roles.role_name', 'Lecture')->get();
    
                if(count($temp) > 0){
                    $storeData['lecture_id'] = Lecture::whereNull('deleted_at')->where('user_id', $u->id)->first()->id;
                    break;  
                }
            }
    
            if(!$storeData['lecture_id']){
                Log::error('The selected lecture name is invalid.');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'The selected lecture name is invalid.',
                ], 400);
            }
    
            $studyProgram = StudyProgram::whereNull('deleted_at')
            ->where('studyProgram_name', $request['study_program_name'])->first();
    
            if(!$studyProgram){
                Log::error('The selected study program name is invalid.');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'The selected study program name is invalid.',
                ], 400);
            }
    
            $storeData['studyProgram_id'] = $studyProgram->id;
    
            $course = Course::create($storeData);
    
            Log::info('Data Course Berhasil Ditambahakan');
            return response()->json([
                'data' => $course,
                'status' => 'success',
                'message' => 'Data Course Berhasil Ditambahakan',
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
            $course = Course::whereNull('deleted_at')->find($id);

            if(!$course){
                Log::error('Data Course Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Course Tidak Ditemukan',
                ], 404);
            }
    
            $validate = Validator::make($request->all(), [
                'course_name' => 'required|max:255',
                'capacity' => 'required|Integer',
                'semester' => 'required|Integer',
                'lecture_name' => 'required|max:255|exists:users,name',
                'study_program_name' => 'required|max:255|exists:study_programs,studyProgram_name',
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
    
            //dapat memiliki nama yang sama tapi berbeda role
            $user = User::whereNull('deleted_at')->where('name', $request['lecture_name'])->get();
    
            if(!$user){
                Log::error('The selected lecture name is invalid.');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'The selected lecture name is invalid.',
                ], 400);
            }
    
            //lakukan pengecekan hanya untuk role lecture
            foreach($user as $u){
                $temp = UserRole::whereNull('user_roles.deleted_at')->where('user_id', $u->id)
                ->join('roles', 'user_roles.role_id', '=', 'roles.id')->where('roles.role_name', 'Lecture')->get();
    
                if(count($temp) > 0){
                    $request['lecture_id'] = Lecture::whereNull('deleted_at')->where('user_id', $u->id)->first()->id;
                    break;  
                }
            }
    
            if(!$request['lecture_id']){
                Log::error('The selected lecture name is invalid.');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'The selected lecture name is invalid.',
                ], 400);
            }
    
            $studyProgram = StudyProgram::whereNull('deleted_at')
            ->where('studyProgram_name', $request['study_program_name'])->first();
    
            if(!$studyProgram){
                Log::error('The selected study program name is invalid.');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'The selected study program name is invalid.',
                ], 400);
            }
    
            $request['studyProgram_id'] = $studyProgram->id;
    
            $course->course_name = $request->course_name;
            $course->capacity = $request->capacity;
            $course->semester = $request->semester;
            $course->studyProgram_id = $request->studyProgram_id;
            $course->lecture_id = $request->lecture_id;
            
            $course->save();
    
            Log::info('Data Course Berhasil Diupdate');
            return response()->json([
                'data' => $course,
                'status' => 'success',
                'message' => 'Data Course Berhasil Diupdate',
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
            $course = Course::whereNull('deleted_at')->find($id);

            if(!$course){
                Log::error('Data Course Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Course Tidak Ditemukan',
                ], 404);
            }
    
            if($course->delete()){
                Log::info('Data Course Berhasil Dihapus');
                return response()->json([
                    'data' => $course,
                    'status' => 'success',
                    'message' => 'Data Course Berhasil Dihapus',
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
