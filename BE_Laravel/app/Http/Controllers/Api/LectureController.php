<?php

namespace App\Http\Controllers\Api;

use App\Models\Role;
use App\Models\User;
use App\Models\Lecture;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class LectureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $keyCache = 'lecture_all';

            $lectures = Cache::remember($keyCache, config('app.cache_time'), function () {
                return Lecture::join('users', 'lectures.user_id', '=', 'users.id')
                ->whereNull('lectures.deleted_at')
                ->select('lectures.*', 'users.name', 'users.address', 'users.phone_number', 'users.date_of_birth', 'users.email', 'users.username')
                ->get();
            });
            
            if(count($lectures) > 0)
            {
                Log::info('Data Lecture Berhasil Ditampilkan');
                return response()->json([
                    'data' => $lectures,
                    'status' => 'success',
                    'message' => 'Data Lecture Berhasil Ditampilkan',
                ], 200);
            }
    
            Log::info('Data Lecture Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Lecture Kosong',
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
                'year_of_teach' => 'required|max:11',
                'field' => 'required|max:255',
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

            $storeData['user_id'] = $user->id;

            //lecture create
            $lecture = Lecture::create($storeData);

            //user role create
            $userRole = UserRole::create([
                'user_id' => $user->id,
                'role_id' => Role::where('role_name', 'Lecture')->first()->id,
            ]);

            DB::commit();
            Log::info('Data Lecture Berhasil Ditambahakan');
            return response()->json([
                'data' => $storeData,
                'status' => 'success',
                'message' => 'Data Lecture Berhasil Ditambahakan',
            ], 200);
        }catch(\Exception $e){
            DB::rollBack();
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'exception' => $e->getMessage(),
                'status' => 'error',
                'message' => "Doesn't have a lecture role in the database",
            ], 422);
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
            $lecture = Lecture::whereNull('deleted_at')->find($id);
        
            if(!$lecture){
                Log::error('Data Lecture Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Lecture Tidak Ditemukan',
                ], 404);
            }
            
            $user = User::whereNull('deleted_at')->find($lecture->user_id);
    
            $validate = Validator::make($request->all(), [
                'name' => 'required|max:255',
                'address' => 'required|max:255',
                'phone_number' => 'required|regex:/^08[0-9]+$/|max:13',
                'date_of_birth' => 'required|date',
                'email' => 'required|email|max:255', Rule::unique('users', 'email')->ignore($lecture->user_id), //membuat rule email unique dan ingore untuk user id yang lagi diedit
                'username' => 'required|max:255', Rule::unique('users', 'username')->ignore($lecture->user_id),
                'year_of_teach' => 'required|max:11',
                'field' => 'required|max:255',
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
    
            $lecture->update($request->all());
            $user->update($request->all());
    
            $lecture->name = $user->name;
            $lecture->address = $user->address;
            $lecture->phone_number = $user->phone_number;
            $lecture->date_of_birth = $user->date_of_birth;
            $lecture->email = $user->email;
            $lecture->username = $user->username;
    
            Log::info('Data Lecture Berhasil Diupdate');
            return response()->json([
                'data' => $lecture,
                'status' => 'success',
                'message' => 'Data Lecture Berhasil Diupdate',
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
     * ini akan menghapus data lecture saja jika role user tersebut lebih dari 1
     * tetapi jika hanya 1 maka akan menghapus kedua data di tabel lecture dan user
     */
    public function destroy(string $id)
    {
        try{
            $lecture = Lecture::whereNull('deleted_at')->find($id);

            if(!$lecture){
                Log::error('Data Lecture Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Lecture Tidak Ditemukan',
                ], 404);
            }
    
            $user = User::whereNull('deleted_at')->find($lecture->user_id);
            
            //digunakan untuk mengambil id role lecture
            $lectureRole = Role::whereNull('deleted_at')->where('role_name', 'Lecture')->first();
    
            //ambil semua role dari user terkait
            $userRole = UserRole::whereNull('deleted_at')->where('user_id', $user->id)->get(); 
            
            //cari userRole sebelumnya dengan role lecture
            $userLectureRole = $userRole->where('role_id', $lectureRole->id)->first();
            
            $lecture->name = $user->name;
            $lecture->address = $user->address;
            $lecture->phone_number = $user->phone_number;
            $lecture->date_of_birth = $user->date_of_birth;
            $lecture->email = $user->email;
            $lecture->username = $user->username;
    
            //hapus user dengan role lecture saja
            if(count($userRole) > 1){
                $lecture->delete();
                $userLectureRole->delete();
                
                Log::info('Data Lecture Berhasil Dihapus, ini lec lebih dari 1'); 
                return response()->json([
                    'data' => $lecture,
                    'status' => 'success',
                    'message' => 'Data Lecture Berhasil Dihapus, ini lec lebih dari 1',
                ], 200); 
            }
            
            $lecture->delete();
            $user->delete();
            $userLectureRole->delete();
            
            Log::info('Data Lecture Berhasil Dihapus, ini lec hanya 1');
            return response()->json([
                'data' => $lecture,
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


    public function showCourse()
    {
        try {
            // Retrieve the currently authenticated user's ID
            $userId = Auth::id();

            // Use the user's ID to retrieve lecture and courses data
            $lecture = Lecture::with('courses')->whereNull('lectures.deleted_at')
                ->where('lectures.user_id', $userId)
                ->first();

            if (!$lecture) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Lecture Tidak Ditemukan',
                ], 404);
            }

            return response()->json([
                'data' => $lecture->courses,
                'status' => 'success',
                'message' => 'Data Courses yang Diajar Berhasil Ditampilkan',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data courses yang diajar',
            ], 500);
        }
    }
    

}
