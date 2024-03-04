<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Schedule;
use App\Models\Course;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\EnrollCourse;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $keyCache = 'schedule_all';

            $schedules = Cache::remember($keyCache, config('app.cache_time'), function () {
                return Schedule::join('courses', 'schedules.course_id', '=', 'courses.id')
                ->whereNull('schedules.deleted_at')
                ->select('schedules.*', 'courses.course_name')
                ->get();
            });
            
            if(count($schedules) > 0)
            {
                Log::info('Data Schedule Berhasil Ditampilkan');
                return response()->json([
                    'data' => $schedules,
                    'status' => 'success',
                    'message' => 'Data Schedule Berhasil Ditampilkan',
                ], 200);
            }
    
            Log::info('Data Schedule Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Schedule Kosong',
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
                'day' => 'required|max:255',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i',
                'course_name' => 'required|max:255|exists:courses,course_name',
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
    
            //pengecekan lanjut apakah data course_name ada di table courses dan tidak terhapus secara soft delete
            $course = Course::whereNull('deleted_at')->where('course_name', $storeData['course_name'])->first();
    
            if(!$course){
                Log::error('The selected course name is invalid.');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'The selected course name is invalid.',
                ], 400);
            }
    
            $storeData['course_id'] = $course->id;
            //
    
            //dapat kan semua course yang memiliki lecture_id yang sama dengan course yang dipilih
            $courses = Course::whereNull('deleted_at')->whereNot('id', $storeData['course_id'])
            ->where('lecture_id', $course->lecture_id)->get();
    
            //lakukan pengecekan tabrakan jadwal
            foreach($courses as $c){
                $collision = Schedule::whereNull('deleted_at')->where('course_id', $c->id)
                ->where('day', $storeData['day'])->where('start_time', $storeData['start_time'])
                ->where('end_time', $storeData['end_time'])->first();
                
                if($collision){
                    Log::error('The selected schedule conflicts.');
                    return response()->json([
                        'data' => null,
                        'status' => 'error',
                        'message' => 'The selected schedule conflicts.',
                    ], 400);
                }
            }
    
            $schedule = Schedule::create($storeData);
    
            Log::info('Data Schedule Berhasil Ditambahakan');
            return response()->json([
                'data' => $schedule,
                'status' => 'success',
                'message' => 'Data Schedule Berhasil Ditambahakan',
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

    public function showScheduleByStudent(string $studentId)
    {
        $enrollments = EnrollCourse::where('student_id', $studentId)->get();
     
        if (count($enrollments) === 0) {
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'The student has not enrolled in any courses yet',
            ], 200);
        }
     
        $scheduleData = [];
     
        foreach ($enrollments as $enrollment) {
            $schedule = Schedule::join('courses', 'schedules.course_id', '=', 'courses.id')
                ->where('schedules.course_id', $enrollment->course_id)
                ->whereNull('schedules.deleted_at')
                ->select('schedules.*', 'courses.course_name')
                ->get();
     
            if (count($schedule) > 0) {
                $scheduleData[] = [
                    'course_name' => $enrollment->course->course_name,
                    'schedule' => $schedule,
                ];
            }
        }
     
        if (count($scheduleData) > 0) {
            return response()->json([
                'data' => $scheduleData,
                'status' => 'success',
                'message' => 'The students schedule has been successfully displayed',
            ], 200);
        }
     
        return response()->json([
            'data' => null,
            'status' => 'success',
            'message' => 'The students schedule is empty',
        ], 200);
    }
     


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try{
            $schedule = Schedule::whereNull('deleted_at')->find($id);

            if(!$schedule){
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Schedule data not found',
                ], 404);
            }

            $validate = Validator::make($request->all(), [
                'day' => 'required|max:255',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i',
                'course_name' => 'required|max:255|exists:courses,course_name',
            ]);

            if(!$schedule){
                Log::error('Data Schedule Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Schedule Tidak Ditemukan',
                ], 404);
            }
    
            $validate = Validator::make($request->all(), [
                'day' => 'required|max:255',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i',
                'course_name' => 'required|max:255|exists:courses,course_name',
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
    
            $course = Course::whereNull('deleted_at')->where('course_name', $request['course_name'])->first();
    
            if(!$course){
                Log::error('The selected course name is invalid.');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'The selected course name is invalid.',
                ], 400);
            }
    
            $request['course_id'] = $course->id;
    
            $courses = Course::whereNull('deleted_at')->whereNot('id', $request['course_id'])
            ->where('lecture_id', $course->lecture_id)->get();
    
            //lakukan pengecekan tabrakan jadwal
            foreach($courses as $c){
                $collision = Schedule::whereNull('deleted_at')->where('course_id', $c->id)
                ->where('day', $request['day'])->where('start_time', $request['start_time'])
                ->where('end_time', $request['end_time'])->first();
                
                if($collision){
                    Log::error('The selected schedule conflicts.');
                    return response()->json([
                        'data' => null,
                        'status' => 'error',
                        'message' => 'The selected schedule conflicts.',
                    ], 400);
                }
            }
    
            $schedule->day = $request->day;
            $schedule->start_time = $request->start_time;
            $schedule->end_time = $request->end_time;
            $schedule->course_id = $request->course_id;
            
            $schedule->save();
    
            Log::info('Data Schedule Berhasil Diupdate');
            return response()->json([
                'data' => $schedule,
                'status' => 'success',
                'message' => 'Data Schedule Berhasil Diupdate',
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
            $schedule = Schedule::whereNull('deleted_at')->find($id);

            if(!$schedule){
                Log::error('Data Schedule Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Schedule Tidak Ditemukan',
                ], 404);
            }
    
            if($schedule->delete()){
                Log::info('Data Schedule Berhasil Dihapus');
                return response()->json([
                    'data' => $schedule,
                    'status' => 'success',
                    'message' => 'Data Schedule Berhasil Dihapus',
                ], 200);
            }
        }catch(\Exception $e){
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Schedule data not found',
            ], 404);
        }

        if($schedule->delete()){
            return response()->json([
                'data' => $schedule,
                'status' => 'success',
                'message' => 'The schedule data has been successfully deleted',
            ], 200);

        }
    }
}
