<?php

namespace App\Http\Controllers\Api;

use App\Models\Role;
use App\Models\User;
use App\Models\Student;
use App\Models\UserRole;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\EnrollCourse;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $keyCache = 'student_all';

            $students = Cache::remember($keyCache, config('app.cache_time'), function () {
                return Student::join('users', 'students.user_id', '=', 'users.id')
                ->whereNull('students.deleted_at')
                ->select('students.*', 'users.name', 'users.address', 'users.phone_number', 'users.date_of_birth', 'users.email', 'users.username')
                ->get();
            }); 
            
            if(count($students) > 0)
            {
                Log::info('Data Student Berhasil Ditampilkan');
                return response()->json([
                    'data' => $students,
                    'status' => 'success',
                    'message' => 'Data Student Berhasil Ditampilkan',
                ], 200);
            }
    
            Log::info('Data Student Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Student Kosong',
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

            $validate = Validator::make($storeData, [
                'name' => 'required|max:255',
                'address' => 'required|max:255',
                'phone_number' => 'required|regex:/^08[0-9]+$/|max:13',
                'date_of_birth' => 'required|date',
                'email' => 'required|email|unique:users,email|max:255',
                'username' => 'required|unique:users,username|max:255',
                'city' => 'required|max:255',
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

            $storeData['password'] = $storeData['date_of_birth'];

        
            DB::beginTransaction();
            //user create
            $user = User::create($storeData);

            $storeData['grade'] = 0;
            $storeData['status'] = true;
            $storeData['user_id'] = $user->id;

            //student create
            $student = Student::create($storeData);

            //user role create
            $userRole = UserRole::create([
                'user_id' => $user->id,
                'role_id' => Role::where('role_name', 'Student')->first()->id,
            ]);

            DB::commit();
            Log::info('Data Student Berhasil Ditambahakan');
            return response()->json([
                'data' => $storeData,
                'status' => 'success',
                'message' => 'Data Student Berhasil Ditambahakan',
            ], 200);
        }catch(\Exception $e){
            DB::rollBack();
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'exception' => $e->getMessage(),
                'status' => 'error',
                'message' => "Doesn't have a student role in the database",
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $student = Student::whereNull('deleted_at')->find($id);

        if (!$student) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Data Student Tidak Ditemukan',
            ], 404);
        }

        // Query untuk mendapatkan kursus yang diambil oleh mahasiswa
        $courses = DB::table('enroll_courses')
            ->join('courses', 'enroll_courses.course_id', '=', 'courses.id')
            ->where('enroll_courses.student_id', $id)
            ->select('courses.course_name')
            ->get();


        if (count($courses) > 0) {
            return response()->json([
                'student_data' => $student,
                'courses' => $courses,
                'status' => 'success',
                'message' => 'Data Student dan Kursus Berhasil Ditampilkan',
            ], 200);
        }

        return response()->json([
            'student_data' => $student,
            'courses' => null,
            'status' => 'success',
            'message' => 'Data Student dan Kursus Kosong',
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try{
            $student = Student::whereNull('deleted_at')->find($id);
        
            if(!$student){
                Log::error('Data Student Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Student Tidak Ditemukan',
                ], 404);
            }
            
            $user = User::whereNull('deleted_at')->find($student->user_id);
    
            $validate = Validator::make($request->all(), [
                'name' => 'required|max:255',
                'address' => 'required|max:255',
                'phone_number' => 'required|regex:/^08[0-9]+$/|max:13',
                'date_of_birth' => 'required|date',
                'email' => 'required|email|max:255', Rule::unique('users', 'email')->ignore($student->user_id), //membuat rule email unique dan ingore untuk user id yang lagi diedit
                'username' => 'required|max:255', Rule::unique('users', 'username')->ignore($student->user_id),
                'city' => 'required|max:255',
                'grade' => 'required|max:4',
                'status' => 'required|boolean',
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
    
            $student->update($request->all());
            $user->update($request->all());
    
            $student->name = $user->name;
            $student->address = $user->address;
            $student->phone_number = $user->phone_number;
            $student->date_of_birth = $user->date_of_birth;
            $student->email = $user->email;
            $student->username = $user->username;
    
            Log::info('Data Student Berhasil Diupdate');
            return response()->json([
                'data' => $student,
                'status' => 'success',
                'message' => 'Data Student Berhasil Diupdate',
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
     * ini akan menghapus data student saja jika role user tersebut lebih dari 1
     * tetapi jika hanya 1 maka akan menghapus kedua data di tabel student dan user
     */
    public function destroy(string $id)
    {
        try{
            $student = Student::whereNull('deleted_at')->find($id);

            if(!$student){
                Log::error('Data Student Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Student Tidak Ditemukan',
                ], 404);
            }
    
            $user = User::whereNull('deleted_at')->find($student->user_id);
            
            //digunakan untuk mengambil id role student
            $studentRole = Role::whereNull('deleted_at')->where('role_name', 'Student')->first();
    
            //ambil semua role dari user terkait
            $userRole = UserRole::whereNull('deleted_at')->where('user_id', $user->id)->get();
    
            //cari userRole sebelumnya dengan role student
            $userStudentRole = $userRole->where('role_id', $studentRole->id)->first();
    
            $student->name = $user->name;
            $student->address = $user->address;
            $student->phone_number = $user->phone_number;
            $student->date_of_birth = $user->date_of_birth;
            $student->email = $user->email;
            $student->username = $user->username;
    
            //hapus user dengan role student saja di tabel userRole
            if(count($userRole) > 1){
                $student->delete();
                $userStudentRole->delete();
                
                Log::info('Data Student Berhasil Dihapus, user memiliki lebih dari 1 role'); 
                return response()->json([
                    'data' => $student,
                    'status' => 'success',
                    'message' => 'Data Student Berhasil Dihapus, user memiliki lebih dari 1 role',
                ], 200); 
            }
            
            $student->delete();
            $user->delete();
            $userStudentRole->delete();

            Log::info('Data Student Berhasil Dihapus, user hanya memiliki 1 role');
            return response()->json([
                'data' => $student,
                'status' => 'success',
                'message' => 'Data Lecture Berhasil Dihapus',
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


    public function showSchedules (string $id)
    {
        $student = Student::whereNull('deleted_at')->find($id);

        if (!$student) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Data Student Tidak Ditemukan',
            ], 404);
        }

        // Query untuk mendapatkan jadwal yang diambil oleh mahasiswa
        $schedulesCourse = DB::table('enroll_courses')
            ->join('courses', 'enroll_courses.course_id', '=', 'courses.id')
            ->join('schedules', 'courses.id', '=', 'schedules.course_id')
            ->where('enroll_courses.student_id', $id)
            ->select('schedules.day', 'schedules.start_time', 'schedules.end_time', 'courses.course_name')
            ->get();
        

        // Get schedules for assignments
        $schedulesAssignment = DB::table('assignments')
            ->join('activities', 'assignments.activity_id', '=', 'activities.id')
            ->join('courses', 'activities.course_id', '=', 'courses.id')
            ->join('enroll_courses', 'courses.id', '=', 'enroll_courses.course_id')
            ->where('enroll_courses.student_id', $id)
            ->where('assignments.cut_off', '>', Carbon::now()) // Hanya assignment dengan cut-off date belum lewat
            ->whereIn('activities.learning_media_type', ['assignment'])
            ->select(
                'courses.id as course_id',
                'activities.activity_name as activity_name',
                DB::raw("'assignment' as activity_type"), // Set activity_type langsung menjadi 'assignment'
                'assignments.name_assignment as name_assignment',
                'assignments.due_date as due_date',
                'assignments.cut_off as cut_off'
            )
            ->get();


        // Get schedules for quizzes
        $schedulesQuiz = DB::table('quizzes')
            ->join('activities', 'quizzes.activity_id', '=', 'activities.id')
            ->join('courses', 'activities.course_id', '=', 'courses.id')
            ->join('enroll_courses', 'courses.id', '=', 'enroll_courses.course_id')
            ->where('enroll_courses.student_id', $id)
            ->where('quizzes.close_quiz', '>', Carbon::now())
            ->select(
                'courses.id as course_id',
                'activities.activity_name as activity_name',
                DB::raw("'quiz' as activity_type"), // Set activity_type langsung menjadi 'quiz'
                'quizzes.title_quiz as title_quiz',
                'quizzes.open_quiz as open_quiz',
                'quizzes.close_quiz as close_quiz'
            )
            ->get();


        if (count($schedulesCourse) > 0) {
            return response()->json([
                'student_data' => $student,
                'schedules_course' => $schedulesCourse,
                'assignment' => $schedulesAssignment,
                'quiz' => $schedulesQuiz,
                'status' => 'success',
                'message' => 'Student Data and Schedule Displayed Successfully',
            ], 200);
        }

        return response()->json([
            'student_data' => $student,
            'schedules' => null,
            'status' => 'success',
            'message' => 'Student Data and Schedule are Empty',
        ], 200);
    }


}
