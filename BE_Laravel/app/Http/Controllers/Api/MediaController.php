<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MediaController extends Controller
{
    public function index()
    {
        $media = Media::whereNull('deleted_at')->get();

        if(count($media) > 0)
        {
            return response()->json([
                'data' => $media,
                'status' => 'success',
                'message' => 'Media Data Displayed Successfully',
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Media Data is Empty',
            'data' => null
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_media' => 'required|max:255',
            'type_media' => 'required|max:255',
            'size_media' => 'required|integer',
            'file' => 'required|mimes:jpg,jpeg,png,pdf,doc,docx,ppt,pptx,mp4|max:2048', 
            'activity_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], 400);
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('MediaFiles'); // Simpan file ke direktori 'MediaFiles' di penyimpanan lokal

            $mediaData = [
                'name_media' => $request->input('name_media'),
                'type_media' => $request->input('type_media'),
                'size_media' => $request->input('size_media'),
                'file' => $filePath, // Simpan path file ke dalam basis data
                'activity_id' => $request->input('activity_id'),
            ];
            
            $media = Media::create($mediaData);

            return response()->json([
                'status' => 'success',
                'message' => 'Media Data Added Successfully',
                'data' => $media,
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'File Not Found',
        ], 400);
    }


    public function update(Request $request, $id)
    {
        $media = Media::whereNull('deleted_at')->find($id);

        if (!$media) {
            return response()->json([
                'status' => 'error',
                'message' => 'Media Not Found',
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'name_media' => 'required|max:255',
            'type_media' => 'required|max:255',
            'size_media' => 'required|integer',
            'file' => 'required|max:255',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => $validate->errors(),
            ], 400);
        }

        if ($request->hasFile('file')) {
            // Hapus file lama jika diinginkan
            Storage::delete($media->file);
    
            // Simpan file baru di folder UploaderFiles
            $file = $request->file('file');
            $filePath = $file->store('MediaFiles');
    
            // Perbarui path file di basis data
            $media->file = $filePath;
        }

        $media->name_media = $request->input('name_media');
        $media->type_media = $request->input('type_media');
        $media->size_media = $request->input('size_media');
        

        $media->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Media Data Updated Successfully',
            'data' => $media,
        ], 200);
    }

    public function show($id)
    {
        $media = Media::whereNull('deleted_at')->find($id);

        if (!$media) {
            return response()->json([
                'status' => 'error',
                'message' => 'Media Not Found',
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'data' => $media,
        ], 200);
    }

    public function destroy($id)
    {
        $media = Media::whereNull('deleted_at')->find($id);

        if (!$media) {
            return response()->json([
                'status' => 'error',
                'message' => 'Media Not Found',
            ], 400);
        }

        $media->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Media Data Deleted Successfully',
        ], 200);
    }

    public function download($id)
    {
        $media = Media::whereNull('deleted_at')->find($id);

        if (!$media) {
            return response()->json([
                'status' => 'error',
                'message' => 'Media Not Found',
            ], 404);
        }

        
        $userRole = auth()->user()->role; 

        if ($userRole === 'student') {
            $enrolled = DB::table('enroll_courses')
                ->join('courses', 'enroll_courses.course_id', '=', 'courses.id')
                ->join('activities', 'courses.id', '=', 'activities.course_id')
                ->where('enroll_courses.student_id', auth()->id()) 
                ->where('activities.id', $media->activity_id)
                ->exists();

            if (!$enrolled) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not enrolled in the course related to this media',
                ], 403);
            }
        } elseif ($userRole === 'lecturer') {
            $teaching = DB::table('enroll_courses')
                ->join('courses', 'enroll_courses.course_id', '=', 'courses.id')
                ->join('activities', 'courses.id', '=', 'activities.course_id')
                ->where('enroll_courses.lecturer_id', auth()->id()) 
                ->where('activities.id', $media->activity_id)
                ->exists();

            if (!$teaching) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not teaching the course related to this media',
                ], 403);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized role',
            ], 403);
        }

        $filePath = storage_path('app/' . $media->file);

        if (file_exists($filePath)) {
            return response()->download($filePath, $media->name_media);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'File Not Found',
            ], 404);
        }
    }

}
