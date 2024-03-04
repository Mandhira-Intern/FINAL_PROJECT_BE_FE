<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcement = Announcement::whereNull('deleted_at')->get();
        if(count($announcement) > 0)
        {
            return response()->json([
                'data' => $announcement,
                'status' => 'success',
                'message' => 'Announcement Data Displayed Successfully',
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Announcement Data is Empty',
            'data' => null
        ], 200);
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name_announcement' => 'required|max:100',
            'text_announcement' => 'required|max:255',
            'activity_id' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validate->errors(),
            ], 400);
        }

        $announcement = Announcement::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Announcement data added successfully',
            'data' => $announcement,
        ], 201);
    }

    public function show($id)
    {
        $announcement = Announcement::whereNull('deleted_at')->find($id);

        if (!$announcement) {
            return response()->json([
                'status' => 'error',
                'message' => 'Announcement Not Found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $announcement,
        ], 200);
    }

    public function update(Request $request, string $id)
    {
        $announcement = Announcement::whereNull('deleted_at')->find($id);

        if (!$announcement) {
            return response()->json([
                'status' => 'error',
                'message' => 'Announcement Not Found',
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'name_announcement' => 'required|max:100',
            'text_announcement' => 'required|max:255',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => $validate->errors(),
            ], 400);
        }
        
        $announcement->name_announcement = $request->name_announcement;
        $announcement->text_announcement = $request->text_announcement;

        $announcement->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Announcement data updated successfully',
            'data' => $announcement,
        ], 200);
    }

    public function destroy($id)
    {
        $announcement = Announcement::whereNull('deleted_at')->find($id);

        if (!$announcement) {
            return response()->json([
                'status' => 'error',
                'message' => 'Announcement Not Found',
            ], 404);
        }

        $announcement->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Announcement Data Deleted Successfully',
        ], 200);
    }
}
