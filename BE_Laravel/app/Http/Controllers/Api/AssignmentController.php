<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AssignmentController extends Controller
{
    public function index()
    {
        $assignment = Assignment::whereNull('deleted_at')->get();
        if(count($assignment) > 0)
        {
            return response()->json([
                'data' => $assignment,
                'status' => 'success',
                'message' => 'Assignment Data Displayed Successfully',
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Assignment Data is Empty',
            'data' => null
        ], 200);
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name_assignment' => 'required|max:255',
            'description' => 'required',
            'file_assignment' => 'max:2555',
            'type_assignment' => 'required',
            'allow_submission' => 'required|date',
            'due_date' => 'required|date',
            'cut_off' => 'required|date',
            'remind_grade' => 'required|date',
            'max_file' => 'required|numeric',
            'max_size' => 'required|numeric',
            'activity_id' => 'required',

        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validate->errors(),
            ], 400);
        }

        if ($request->hasFile('file_assignment')) {
            $file = $request->file('file_assignment');
            $filePath = $file->store('AssignmentFiles'); 

            $mediaData = [
                'name_assignment' => $request->input('name_assignment'),
                'description' => $request->input('description'),
                'file_assignment' => $filePath,
                'type_assignment' => $request->input('type_assignment'),
                'allow_submission' => $request->input('allow_submission'),
                'due_date' => $request->input('due_date'),
                'cut_off' => $request->input('cut_off'),
                'remind_grade' => $request->input('remind_grade'),
                'max_file' => $request->input('max_file'),
                'max_size' => $request->input('max_size'),
                'activity_id' => $request->input('activity_id'),
            ];
            
            $assignment = Assignment::create($mediaData);

            return response()->json([
                'status' => 'success',
                'message' => 'Assignment Data Added Successfully',
                'data' => $assignment,
            ], 200);
        }

        $assignment = Assignment::create($request->all());;

            return response()->json([
                'status' => 'success',
                'message' => 'Assignment Data Added Successfully',
                'data' => $assignment,
            ], 200);
    }

    public function show($id)
    {
        $assignment = Assignment::whereNull('deleted_at')->find($id);

        if (!$assignment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Assignment Not Found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $assignment,
        ], 200);
    }

    public function update(Request $request, string $id)
    {
        try {
            $assignment = Assignment::whereNull('deleted_at')->find($id);

            if (!$assignment) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Assignment Not Found',
                ], 404);
            }

            $validate = Validator::make($request->all(), [
                'name_assignment' => 'required|max:255',
                'description' => 'required',
                'type_assignment' => 'required',
                'allow_submission' => 'required|date',
                'due_date' => 'required|date',
                'cut_off' => 'required|date',
                'remind_grade' => 'required|date',
                'max_file' => 'required|numeric',
                'max_size' => 'required|numeric',
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            if ($request->hasFile('file_assignment')) {
                // Hapus file lama jika diinginkan
                Storage::delete($assignment->file_assignment);
        
                // Simpan file baru di folder UploaderFiles
                $file = $request->file('file_assignment');
                $filePath = $file->store('AssignmentFiles');
        
                // Perbarui path file di basis data
                $assignment->file_assignment = $filePath;
            }

            $assignment->name_assignment = $request->input('name_assignment');
            $assignment->description = $request->input('description');
            $assignment->type_assignment = $request->input('type_assignment');
            $assignment->allow_submission = $request->input('allow_submission');
            $assignment->due_date = $request->input('due_date');
            $assignment->cut_off = $request->input('cut_off');
            $assignment->remind_grade = $request->input('remind_grade');
            $assignment->max_file = $request->input('max_file');
            $assignment->max_size = $request->input('max_size');

            $assignment->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Assignment Updated Successfully',
                'data' => $assignment,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating the Assignment.',
                'error_details' => $e->getTraceAsString(),
            ], 500);
        }
    }


    public function destroy($id)
    {
        $assignment = Assignment::whereNull('deleted_at')->find($id);

        if (!$assignment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Assignment Not Found',
            ], 404);
        }

        $assignment->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Assignment Data Deleted Successfully',
        ], 200);
    }

    public function download($id)
    {
        $assignment = Assignment::whereNull('deleted_at')->find($id);

        if (!$assignment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Assignment Not Found',
            ], 404);
        }

        if (!$assignment->file_assignment) {
            return response()->json([
                'status' => 'error',
                'message' => 'No file attached to this assignment',
            ], 400);
        }

        
        $userRole = auth()->user()->role; 

        if ($userRole === 'student') {
            
            $enrolled = DB::table('enroll_courses')
                ->join('courses', 'enroll_courses.course_id', '=', 'courses.id')
                ->join('activities', 'courses.id', '=', 'activities.course_id')
                ->where('enroll_courses.student_id', auth()->id()) // Assuming student ID is available in enroll_courses
                ->where('activities.id', $assignment->activity_id)
                ->exists();

            if (!$enrolled) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not enrolled in the course related to this assignment',
                ], 403);
            }
        } elseif ($userRole === 'lecturer') {
            // Check if the lecturer is teaching the course related to this assignment
            $teaching = DB::table('enroll_courses')
                ->join('courses', 'enroll_courses.course_id', '=', 'courses.id')
                ->join('activities', 'courses.id', '=', 'activities.course_id')
                ->where('enroll_courses.lecturer_id', auth()->id()) // Assuming lecturer ID is available in enroll_courses
                ->where('activities.id', $assignment->activity_id)
                ->exists();

            if (!$teaching) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not teaching the course related to this assignment',
                ], 403);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized role',
            ], 403);
        }

        $filePath = storage_path('app/' . $assignment->file_assignment);

        if (!Storage::exists($assignment->file_assignment)) {
            return response()->json([
                'status' => 'error',
                'message' => 'File not found',
            ], 404);
        }

        return response()->download($filePath, $assignment->name_assignment);
    }
    

}
