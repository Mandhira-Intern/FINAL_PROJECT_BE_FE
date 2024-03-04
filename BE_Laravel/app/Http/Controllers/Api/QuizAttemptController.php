<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QuizAttempt;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuizAttemptController extends Controller
{
    public function index()
    {
        $attempt  = QuizAttempt::whereNull('deleted_at')->get();
        if(count($attempt) > 0)
        {
            return response()->json([
                'data' => $attempt,
                'status' => 'success',
                'message' => 'Quiz Attempt Data Displayed Successfully',
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Quiz Attempt Data is Empty',
            'data' => null
        ], 404);
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'total_score' => 'required|numeric|min:0|max:100',
            'student_id' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validate->errors(),
            ], 400);
        }
        $attempt = new QuizAttempt();
        $attempt->student_id = $request->student_id;
        $attempt->total_score = $request->total_score;
        $attempt->attempt_date = Carbon::now();

        $attempt->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Quiz Attempt Data Added Successfully',
            'data' => $attempt,
        ], 201);
    }

    public function show($id)
    {
        $attempt = QuizAttempt::whereNull('deleted_at')->find($id);

        if (!$attempt) {
            return response()->json([
                'status' => 'error',
                'message' => 'Quiz Attempt Not Found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $attempt,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $attempt = QuizAttempt::whereNull('deleted_at')->find($id);

        if (!$attempt) {
            return response()->json([
                'status' => 'error',
                'message' => 'Quiz Attempt Not Found',
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'total_score' => 'required|numeric|min:0|max:100',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validate->errors(),
            ], 400);
        }
        
        $attempt->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Quiz Attempt Data Updated Successfully',
            'data' => $attempt,
        ], 200);
    }

    public function destroy($id)
    {
        $attempt = QuizAttempt::whereNull('deleted_at')->find($id);

        if (!$attempt) {
            return response()->json([
                'status' => 'error',
                'message' => 'Quiz Attempt Not Found',
            ], 404);
        }

        $attempt->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Quiz Attempt Data Deleted Successfully',
        ], 200);
    }
}
