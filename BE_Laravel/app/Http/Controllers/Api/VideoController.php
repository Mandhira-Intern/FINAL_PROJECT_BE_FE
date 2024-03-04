<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VideoController extends Controller
{
    public function index()
    {
        $video = Video::whereNull('deleted_at')->get();
        if(count($video) > 0)
        {
            return response()->json([
                'data' => $video,
                'status' => 'success',
                'message' => 'Video Data Displayed Successfully',
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Video Data is Empty',
            'data' => null
        ], 200);
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name_video' => 'required|max:100',
            'description_link' => 'required|max:255',
            'link' => 'required|max:255',
            'activity_id' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validate->errors(),
            ], 400);
        }

        $video = Video::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Video data added successfully',
            'data' => $video,
        ], 201);
    }

    public function show($id)
    {
        $video = Video::whereNull('deleted_at')->find($id);

        if (!$video) {
            return response()->json([
                'status' => 'error',
                'message' => 'Video Not Found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $video,
        ], 200);
    }

    public function update(Request $request, string $id)
    {
        $video = Video::whereNull('deleted_at')->find($id);

        if (!$video) {
            return response()->json([
                'status' => 'error',
                'message' => 'Video Not Found',
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'name_video' => 'required|max:100',
            'description_link' => 'required|max:255',
            'link' => 'required|max:255',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => $validate->errors(),
            ], 400);
        }
        
        $video->name_video = $request->name_video;
        $video->description_link = $request->description_link;
        $video->link = $request->link;

        $video->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Video data updated successfully',
            'data' => $video,
        ], 200);
    }

    public function destroy($id)
    {
        $video = Video::whereNull('deleted_at')->find($id);

        if (!$video) {
            return response()->json([
                'status' => 'error',
                'message' => 'Video Not Found',
            ], 404);
        }

        $video->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Video Data Deleted Successfully',
        ], 200);
    }
}
