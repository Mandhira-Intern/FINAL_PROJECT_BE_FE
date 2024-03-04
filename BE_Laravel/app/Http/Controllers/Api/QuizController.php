<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes  = Quiz::whereNull('deleted_at')->get();
        if(count($quizzes) > 0)
        {
            return response()->json([
                'data' => $quizzes,
                'status' => 'success',
                'message' => 'Quiz Data Displayed Successfully',
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Quiz Data is Empty',
            'data' => null
        ], 404);
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'title_quiz' => 'required|max:255',
            'description_quiz' => 'required|max:255',
            'open_quiz' => 'required|date',
            'close_quiz' => 'required|date',
            'time_limit' => 'required|integer',
            'attempts_allowed' => 'required|integer',
            'activity_id'=> 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validate->errors(),
            ], 400);
        }

        $quiz = Quiz::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Quiz Data Added Successfully',
            'data' => $quiz,
        ], 201);
    }

    public function show($id)
    {
        $quiz = Quiz::whereNull('deleted_at')->find($id);

        if (!$quiz) {
            return response()->json([
                'status' => 'error',
                'message' => 'Quiz Not Found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $quiz,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $quiz = Quiz::whereNull('deleted_at')->find($id);

        if (!$quiz) {
            return response()->json([
                'status' => 'error',
                'message' => 'Quiz Not Found',
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'title_quiz' => 'required|max:255',
            'description_quiz' => 'required|max:255',
            'open_quiz' => 'required|date',
            'close_quiz' => 'required|date|after:open_date',
            'time_limit' => 'required|integer|gt:0',
            'attempts_allowed' => 'required|integer|gt:0',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validate->errors(),
            ], 400);
        }

        $quiz->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Quiz Data Updated Successfully',
            'data' => $quiz,
        ], 200);
    }

    public function destroy($id)
    {
        $quiz = Quiz::whereNull('deleted_at')->find($id);

        if (!$quiz) {
            return response()->json([
                'status' => 'error',
                'message' => 'Quiz Not Found',
            ], 404);
        }

        $quiz->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Quiz Data Deleted Successfully',
        ], 200);
    }
}
