<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AnswerController extends Controller
{
    public function index()
    {
        $answer = Answer::whereNull('deleted_at')->get();
        if(count($answer) > 0)
        {
            return response()->json([
                'data' => $answer,
                'status' => 'success',
                'message' => 'Answer Data Displayed Successfully',
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Answer Data is Empty',
            'data' => null
        ], 200);
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'score' => 'required|numeric|min:0',
            'answer_text' => 'max:255',
            'question_id' => 'required',
            'quiz_attempt_id' => 'required',
            'choice_options_id' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validate->errors(),
            ], 400);
        }

        $answer = Answer::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Answer data added successfully',
            'data' => $answer,
        ], 201);
    }

    public function show($id)
    {
        $answer = Answer::whereNull('deleted_at')->find($id);

        if (!$answer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Answer Not Found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $answer,
        ], 200);
    }

    public function update(Request $request, string $id)
    {
        $answer = Answer::whereNull('deleted_at')->find($id);

        if (!$answer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Answer Not Found',
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'score' => 'required|numeric|min:0',
            'answer_text' => 'required|max:255',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => $validate->errors(),
            ], 400);
        }
        
        $answer->score = $request->score;
        $answer->answer_text = $request->answer_text;

        $answer->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Answer Data Updated Successfully',
            'data' => $answer,
        ], 200);
    }

    public function destroy($id)
    {
        $answer = Answer::whereNull('deleted_at')->find($id);

        if (!$answer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Answer Not Found',
            ], 404);
        }

        $answer->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Answer Data Deleted Successfully',
        ], 200);
    }
}
