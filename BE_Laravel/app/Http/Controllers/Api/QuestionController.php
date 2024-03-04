<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuestionController extends Controller
{
    public function index()
    {
        $questions  = Question::whereNull('deleted_at')->get();
        if(count($questions) > 0)
        {
            return response()->json([
                'data' => $questions,
                'status' => 'success',
                'message' => 'Question Data Displayed Successfully',
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Question Data is Empty',
            'data' => null
        ], 404);
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'text_question' => 'required|max:255',
            'type_question' => 'required|in:multiple,essay',
            'poin_question' => 'required|integer',
            'quiz_id' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validate->errors(),
            ], 400);
        }

        $question = Question::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Question Data Added Successfully',
            'data' => $question,
        ], 201);
    }

    public function show($id)
    {
        $question = Question::whereNull('deleted_at')->find($id);

        if (!$question) {
            return response()->json([
                'status' => 'error',
                'message' => 'Question Not Found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $question,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $question = Question::whereNull('deleted_at')->find($id);

        if (!$question) {
            return response()->json([
                'status' => 'error',
                'message' => 'Question Not Found',
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'text_question' => 'required|max:255',
            'type_question' => 'required|in:multiple,essay',
            'poin_question' => 'required|integer',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validate->errors(),
            ], 400);
        }

        $question->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Question Data Updated Successfully',
            'data' => $question,
        ], 200);
    }

    public function destroy($id)
    {
        $question = Question::whereNull('deleted_at')->find($id);

        if (!$question) {
            return response()->json([
                'status' => 'error',
                'message' => 'Question Not Found',
            ], 404);
        }

        $question->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Question Data Deleted Successfully',
        ], 200);
    }
}
