<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function index()
    {
        $comment = Comment::with('student')
        ->whereNull('deleted_at')
        ->get();
        if(count($comment) > 0)
        {
            return response()->json([
                'data' => $comment,
                'status' => 'success',
                'message' => 'Comment Data Displayed Successfully',
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Comment Data is Empty',
            'data' => null
        ], 200);
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'comment_input' => 'required|max:255',
            'forum_id' => 'required',
            'student_id' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validate->errors(),
            ], 400);
        }

        $comment = Comment::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Comment data added successfully',
            'data' => $comment,
        ], 201);
    }

    public function show($id)
    {
        $comment = Comment::whereNull('deleted_at')->find($id);

        if (!$comment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Comment Not Found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $comment,
        ], 200);
    }

    public function update(Request $request, string $id)
    {
        $comment = Comment::whereNull('deleted_at')->find($id);

        if (!$comment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Comment Not Found',
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'comment_input' => 'required|max:255',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => $validate->errors(),
            ], 400);
        }
        
        $comment->comment_input = $request->comment_input;

        $comment->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Comment Data Updated Successfully',
            'data' => $comment,
        ], 200);
    }

    public function destroy($id)
    {
        $comment = Comment::whereNull('deleted_at')->find($id);

        if (!$comment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Comment Not Found',
            ], 404);
        }

        $comment->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Comment Data Deleted Successfully',
        ], 200);
    }
}
