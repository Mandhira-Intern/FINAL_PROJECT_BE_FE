<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Lecture;
use App\Models\Student;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MonitoringController extends Controller
{
    public function getLectureAttendance()
    {
        try{
            $keyCache = 'attendance_lecture_all';

            $attendances = Cache::remember($keyCache, config('app.cache_time'), function () {
                return Attendance::join('lectures', 'attendances.user_id', '=', 'lectures.user_id')
                ->whereNull('attendances.deleted_at')->whereNull('lectures.deleted_at')->get();
            });
            
            if(count($attendances) > 0)
            {
                Log::info('Data Attendance Lecture Berhasil Ditampilkan');
                return response()->json([
                    'data' => $attendances,
                    'status' => 'success',
                    'message' => 'Data Attendance Lecture Berhasil Ditampilkan',
                ], 200);
            }
    
            Log::info('Data Attendance Lecture Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Attendance Lecture Kosong',
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

    public function getStudentAttendance()
    {
        try{
            $keyCache = 'attendance_student_all';

            $attendances = Cache::remember($keyCache, config('app.cache_time'), function () {
                return Attendance::join('students', 'attendances.user_id', '=', 'students.user_id')
                ->whereNull('attendances.deleted_at')->whereNull('students.deleted_at')->get();
            });
            
            if(count($attendances) > 0)
            {
                Log::info('Data Attendance Student Berhasil Ditampilkan');
                return response()->json([
                    'data' => $attendances,
                    'status' => 'success',
                    'message' => 'Data Attendance Student Berhasil Ditampilkan',
                ], 200);
            }
    
            Log::info('Data Attendance Student Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Attendance Student Kosong',
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
