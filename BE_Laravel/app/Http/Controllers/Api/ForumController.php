<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Forum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ForumController extends Controller
{
    public function index()
    {
        $forums = Forum::with('activity')
            ->whereNull('deleted_at')
            ->get();
        if(count($forums) > 0)
        {
            return response()->json([
                'data' => $forums,
                'status' => 'success',
                'message' => 'Forum Data Displayed Successfully',
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Forum Data is Empty',
            'data' => null
        ], 200);
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'forum_title' => 'required|max:100',
            'description' => 'required|max:255',
            'activity_id' => 'required',
            'lecture_id' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validate->errors(),
            ], 400);
        }

        $forum = Forum::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Forum data added successfully',
            'data' => $forum,
        ], 201);
    }

    public function show($id)
    {
        $forum = Forum::whereNull('deleted_at')->find($id);

        if (!$forum) {
            return response()->json([
                'status' => 'error',
                'message' => 'Forum Not Found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $forum,
        ], 200);
    }

    public function update(Request $request, string $id)
    {
        $forum = Forum::whereNull('deleted_at')->find($id);

        if (!$forum) {
            return response()->json([
                'status' => 'error',
                'message' => 'Forum Not Found',
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'forum_title' => 'required|max:100',
            'description' => 'required|max:255',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => $validate->errors(),
            ], 400);
        }
        
        $forum->forum_title = $request->forum_title;
        $forum->description = $request->description;

        $forum->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Forum data updated successfully',
            'data' => $forum,
        ], 200);
    }

    public function destroy($id)
    {
        $forum = Forum::whereNull('deleted_at')->find($id);

        if (!$forum) {
            return response()->json([
                'status' => 'error',
                'message' => 'Forum Not Found',
            ], 404);
        }

        $forum->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Forum Data Deleted Successfully',
        ], 200);
    }
}

