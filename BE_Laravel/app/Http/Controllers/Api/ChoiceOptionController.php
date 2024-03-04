<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChoiceOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChoiceOptionController extends Controller
{
    public function index()
    {
        $option = ChoiceOption::whereNull('deleted_at')->get();
        if(count($option) > 0)
        {
            return response()->json([
                'data' => $option,
                'status' => 'success',
                'message' => 'Choice Option Data Displayed Successfully',
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Choice Option Data is Empty',
            'data' => null
        ], 200);
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'option_text' => 'required|max:255',
            'is_correct' => 'required|boolean',
            'question_id' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validate->errors(),
            ], 400);
        }

        $option = ChoiceOption::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Choice Option data added successfully',
            'data' => $option,
        ], 201);
    }

    public function show($id)
    {
        $option = ChoiceOption::whereNull('deleted_at')->find($id);

        if (!$option) {
            return response()->json([
                'status' => 'error',
                'message' => 'Choice Option Not Found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $option,
        ], 200);
    }

    public function update(Request $request, string $id)
    {
        $option = ChoiceOption::whereNull('deleted_at')->find($id);

        if (!$option) {
            return response()->json([
                'status' => 'error',
                'message' => 'Choice Option Not Found',
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'option_text' => 'required|max:255',
            'is_correct' => 'required|boolean'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => $validate->errors(),
            ], 400);
        }
        
        $option->option_text = $request->option_text;
        $option->is_correct = $request->is_correct;

        $option->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Choice Option Data Updated Successfully',
            'data' => $option,
        ], 200);
    }

    public function destroy($id)
    {
        $option = ChoiceOption::whereNull('deleted_at')->find($id);

        if (!$option) {
            return response()->json([
                'status' => 'error',
                'message' => 'Choice Option Not Found',
            ], 404);
        }

        $option->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Choice Option Data Deleted Successfully',
        ], 200);
    }
}
