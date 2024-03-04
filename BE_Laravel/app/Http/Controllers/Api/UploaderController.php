<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Uploader;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UploaderController extends Controller
{
    public function index()
    {
        $uploader = Uploader::with('assignment')
        ->whereNull('deleted_at') 
        ->get();
        if(count($uploader) > 0)
        {
            return response()->json([
                'data' => $uploader,
                'status' => 'success',
                'message' => 'Uploader Data Displayed Successfully',
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Uploader Data is Empty',
            'data' => null
        ], 200);
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'uploader_name' => 'required|max:255',
            'uploader_file' => 'required',
            'assignment_id' => 'required',
            'student_id' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validate->errors(),
            ], 400);
        }

        if ($request->hasFile('uploader_file')) {
            $file = $request->file('uploader_file');
            $filePath = $file->store('UploaderFiles'); // Simpan file ke direktori 'UploaderFiles' di penyimpanan lokal

            $mediaData = [
                'uploader_name' => $request->input('uploader_name'),
                'uploader_file' => $filePath, // Simpan path file ke dalam basis data
                'uploader_time' => Carbon::now(),
                'assignment_id' => $request->input('assignment_id'),
                'student_id' => $request->input('student_id'),
            ];
            
            $uploader = Uploader::create($mediaData);

            return response()->json([
                'status' => 'success',
                'message' => 'Media Data Added Successfully',
                'data' => $uploader,
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'File Not Found',
        ], 400);


    }

    public function show($id)
    {
        $uploader = Uploader::whereNull('deleted_at')->find($id);

        if (!$uploader) {
            return response()->json([
                'status' => 'error',
                'message' => 'Uploader Not Found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $uploader,
        ], 200);
    }

    public function updatee(Request $request, string $id)
    {
        $uploader = Uploader::whereNull('deleted_at')->find($id);

        if (!$uploader) {
            return response()->json([
                'status' => 'error',
                'message' => 'Uploader Not Found',
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'uploader_name' => 'required|max:255',
            'uploader_file' => 'required',
            'uploader_time' => 'required|date',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => $validate->errors(),
            ], 400);
        }
        if ($request->hasFile('uploader_file')) {
            // Hapus file lama jika diinginkan
            Storage::delete($uploader->uploader_file);
    
            // Simpan file baru di folder UploaderFiles
            $file = $request->file('uploader_file');
            $filePath = $file->store('UploaderFiles');
    
            // Perbarui path file di basis data
            $uploader->file = $filePath;
        }
        $uploader->uploader_name = $request->input('uploader_name');
        $uploader->uploader_time = $request->input('uploader_time');

        $uploader->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Uploader Data Updated Successfully',
            'data' => $uploader,
        ], 200);
    }

    public function destroy($id)
    {
        $uploader = Uploader::whereNull('deleted_at')->find($id);

        if (!$uploader) {
            return response()->json([
                'status' => 'error',
                'message' => 'Uploader Not Found',
            ], 404);
        }

        $uploader->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Uploader Data Deleted Successfully',
        ], 200);
    }

    public function download($id)
    {
        $uploader = Uploader::whereNull('deleted_at')->find($id);

        if (!$uploader) {
            return response()->json([
                'status' => 'error',
                'message' => 'uploader Not Found',
            ], 404);
        }

        $filePath = storage_path('app/' . $uploader->uploader_file);

        if (file_exists($filePath)) {
            return response()->download($filePath, $uploader->uploader_name);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'File Not Found',
            ], 404);
        }
    }


}
