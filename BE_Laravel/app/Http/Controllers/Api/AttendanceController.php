<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\EnrollCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    // Mendapatkan semua data attendance beserta informasi course dan student
    public function index()
    {
        $attendance = Attendance::whereNull('deleted_at')->get();
        if(count($attendance) > 0)
        {
            return response()->json([
                'data' => $attendance,
                'status' => 'success',
                'message' => 'Attendance Data Displayed Successfully',
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Attendance Data is Empty',
            'data' => null
        ], 200);
    }

    // Menyimpan data attendance baru
    public function store(Request $request)
    {
        try {
            // Validasi request
            $request->validate([
                'kategory' => 'required',
                'info' => 'required',
                'time_arrive' => 'required:date',
                'time_leave' => 'required:date',
                'student_id' => 'required|exists:students,id',
                'course_id' => 'required|exists:courses,id',
            ]);

            // Membuat objek Attendance
            $attendance = new Attendance([
                'kategory' => $request->kategory,
                'info' => $request->info,
                'time_arrive' => $request->time_arrive,
                'time_leave' => $request->time_leave,
                'student_id' => $request->student_id,
                'course_id' => $request->course_id,
            ]);

            // Menyimpan Attendance ke database
            $attendance->save();

            // Mengembalikan respons sukses
            return response()->json(['message' => 'Attendance created successfully', 'data' => $attendance]);
        } catch (\Exception $e) {
            // Mengembalikan respons kesalahan
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            
            $request->validate([
                'kategory' => 'required',
                'info' => 'required',
                'time_arrive' => 'required:date',
                'time_leave' => 'required:date',
            ]);

            
            $attendance = Attendance::find($id);

            
            if (!$attendance) {
                return response()->json(['message' => 'Attendance not found'], 404);
            }

            
            $attendance->update([
                'kategory' => $request->kategory,
                'info' => $request->info,
                'time_arrive' => $request->time_arrive,
                'time_leave' => $request->time_leave,
            ]);

            // Mengembalikan respons sukses
            return response()->json(['message' => 'Attendance updated successfully', 'data' => $attendance]);
        } catch (\Exception $e) {
            // Mengembalikan respons kesalahan
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    
    public function show($id)
    {
        try {
            
            $attendance = Attendance::find($id);

            if (!$attendance) {
                return response()->json(['message' => 'Attendance not found'], 404);
            }

            return response()->json(['data' => $attendance, 'message' => 'Attendance data displayed successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    
    public function destroy($id)
    {
        try {
            
            $attendance = Attendance::find($id);

            
            if (!$attendance) {
                return response()->json(['message' => 'Attendance not found'], 404);
            }

            
            $attendance->delete();

            
            return response()->json(['message' => 'Attendance deleted successfully']);
        } catch (\Exception $e) {
            
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function showStudentsInCourse(string $courseCode)
    {
        try {
            // Cari course berdasarkan kode
            $course = Course::where('course_name', $courseCode)->first();


            if (!$course) {
                return response()->json(['message' => 'Course not found'], 404);
            }

            // Variabel penampung nama course
            $courseName = $course->name;

            // Dapatkan daftar siswa yang mengambil course tersebut
            $students = $course->enrollments()->with('student')->get();

            // Bandingkan nama course dengan variabel penampung
            foreach ($students as $enrollment) {
                $enrolledCourseName = $enrollment->course->name;

                if ($enrolledCourseName !== $courseName) {
                    return response()->json(['message' => 'Mismatched course name'], 400);
                }
            }

            return response()->json(['student_data' => $students,'message' => 'List of students in the course'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


}
