<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\Forum;
use App\Models\Media;
use App\Models\Quiz;
use App\Models\Video;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ActivityController extends Controller
{
    
    public function index()
    {
        $activities = Activity::leftJoin('assignments', 'activities.id', '=', 'assignments.activity_id')
            ->leftJoin('forums', 'activities.id', '=', 'forums.activity_id')
            ->leftJoin('quizzes', 'activities.id', '=', 'quizzes.activity_id')
            ->leftJoin('announcements', 'activities.id', '=', 'announcements.activity_id')
            ->leftJoin('videos', 'activities.id', '=', 'videos.activity_id')
            ->leftJoin('media', 'activities.id', '=', 'media.activity_id')
            ->whereNull('activities.deleted_at')
            ->select(
                'activities.id',
                'activities.activity_name',
                'assignments.name_assignment as assignment_name',
                'forums.forum_title',
                'quizzes.title_quiz',
                'announcements.name_announcement',
                'videos.name_video',
                'media.name_media'
            )
            ->whereNull('activities.deleted_at')->get();
    
        if (count($activities) > 0) {
            return response()->json([
                'data' => $activities,
                'status' => 'success',
                'message' => 'Activity Data Displayed Successfully',
            ], 200);
        }
    
        return response()->json([
            'data' => null,
            'status' => 'error',
            'message' => 'Activity Data is Empty',
        ], 200);
    }
    

    
    public function store(Request $request)
    {
        
        try {
            DB::beginTransaction();
            // Validasi input untuk Activity
            $validate = Validator::make($request->all(), [
                'activity_name' => 'required|max:255',
                'description' => 'required|max:255',
                'learning_media_type' => 'required|in:media,forum,question,assignment,quiz,announcement,video', // Pastikan pilihan sesuai
                'course_id'=> 'required',
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            // Buat aktivitas (Activity)
            $activity = Activity::create([
                'activity_name' => $request->input('activity_name'),
                'description' => $request->input('description'),
                'learning_media_type' => $request->input('learning_media_type'),
                'course_id' => $request->input('course_id'),

            ]);

            $learningMediaType = $request->input('learning_media_type');

            if ($learningMediaType === 'media') {
                $file = $request->file('file');
                $filePath = $file->store('MediaFiles');
            
                $media = Media::updateOrCreate(
                    ['activity_id' => $activity->id],
                    [
                        'name_media' => $request->input('name_media'),
                        'type_media' => $request->input('type_media'),
                        'size_media' => $request->input('size_media'),
                        'file' => $filePath,
                    ]
                );
                DB::commit();
        
                return response()->json([
                    'status' => 'success',
                    'message' => 'Activity and Media Data Added Successfully',
                    'data' => $media,
                ], 201);
            } elseif ($learningMediaType === 'forum') {
                $forum = Forum::Create([
                    'activity_id' => $activity->id,
                    'forum_title' => $request->input('forum_title'),
                    'description' => $request->input('description'),
                ]);
                DB::commit();
        
                return response()->json([
                    'status' => 'success',
                    'message' => 'Activity and Forum Data Added Successfully',
                    'data' => $forum,
                ], 201);

                
            } elseif ($learningMediaType === 'assignment') {
                if ($request->hasFile('file_assignment')) {
                    $file = $request->file('file_assignment');
                    $filePath = $file->store('AssignmentFiles');
                    $request->merge(['file_assignment' => $filePath]);
                }
        
                $assignment = Assignment::create([
                    'activity_id' => $activity->id,
                    'name_assignment' => $request->input('name_assignment'),
                   'description' => $request->input('description'),
                    'file_assignment' => $request->input('file_assignment'),
                    'type_assignment' => $request->input('type_assignment'),
                    'allow_submission' => $request->input('allow_submission'),
                    'due_date' => $request->input('due_date'),
                    'cut_off' => $request->input('cut_off'),
                    'remind_grade' => $request->input('remind_grade'),
                    'max_file' => $request->input('max_file'),
                    'max_size' => $request->input('max_size'),
                ]);
        
                DB::commit();
        
                return response()->json([
                    'status' => 'success',
                    'message' => 'Activity and Assignment Data Added Successfully',
                    'data' => $assignment,
                ], 201);
        
            } elseif ($learningMediaType === 'quiz') {
                $quiz = Quiz::create([
                    'activity_id' => $activity->id,
                    'title_quiz' => $request->input('title_quiz'),
                    'description_quiz' => $request->input('description_quiz'),
                    'open_quiz' => $request->input('open_quiz'),
                    'close_quiz' => $request->input('close_quiz'),
                    'attempts_allowed' => $request->input('attempts_allowed'),
                    'time_limit' => $request->input('time_limit'),

                    
                ]);
                DB::commit();
        
                return response()->json([
                    'status' => 'success',
                    'message' => 'Activity and Quiz Data Added Successfully',
                    'data' => $quiz,
                ], 201);
            }elseif ($learningMediaType === 'announcement') {
                $announcement = Announcement::Create([
                    'activity_id' => $activity->id,
                    'name_announcement' => $request->input('name_announcement'),
                    'text_announcement' => $request->input('text_announcement'),
                ]);
                DB::commit();
        
                return response()->json([
                    'status' => 'success',
                    'message' => 'Activity and Announcement Data Added Successfully',
                    'data' => $announcement,
                ], 201);
            }elseif ($learningMediaType === 'video') {
                $video = Video::Create([
                    'activity_id' => $activity->id,
                    'name_video' => $request->input('name_video'),
                    'description_link' => $request->input('description_link'),
                    'link' => $request->input('link'),
                ]);

                DB::commit();
        
                return response()->json([
                    'status' => 'success',
                    'message' => 'Activity and Video Data Added Successfully',
                    'data' => $video,
                ], 201);
            } 

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Activity and Learning Media Data Added Successfully',
                'data' => $activity,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to store data. ' . $e->getMessage(),
            ], 500);
        }
        
    }

  
    public function show(string $id)
    {
        $activity = Activity::with(['assignment', 'forum', 'quizzes', 'announcement', 'video', 'media'])
            ->whereNull('deleted_at')
            ->find($id);

        if (!$activity) {
            return response()->json([
                'status' => 'error',
                'message' => 'Activity Data Not Found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $activity,
            'message' => 'Activity Data is Successfully Displayed',
        ], 200);
    }

    
    public function update(Request $request, string $id)
    {
        try {
            DB::beginTransaction();
            $activity = Activity::whereNull('deleted_at')->find($id);

            if (!$activity) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Activity Data Not Found',
                ], 404);
            }

            // Validasi input
            $validate = Validator::make($request->all(), [
                'activity_name' => 'required|max:255',
                'description' => 'required|max:255',
                'learning_media_type' => 'required|in:media,forum,question,assignment,quiz,announcement,video',

            ]);

            

            // Periksa apakah validasi gagal
            if ($validate->fails()) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            // Perbarui data aktivitas
            $activity->update([
                'activity_name' => $request->input('activity_name'),
                'description' => $request->input('description'),
                'learning_media_type' => $request->input('learning_media_type'),

            ]);

            $learningMediaType = $request->input('learning_media_type');

            if ($learningMediaType === 'media') {
                if ($request->hasFile('file')) {
                    $file = $request->file('file');
                    $filePath = $file->store('MediaFiles');
            
                    Media::updateOrCreate(
                        ['activity_id' => $activity->id],
                        [
                            'name_media' => $request->input('name_media'),
                            'type_media' => $request->input('type_media'),
                            'size_media' => $request->input('size_media'),
                            'file' => $filePath,
                        ]
                    );
                } else {
                    Media::updateOrCreate(
                        ['activity_id' => $activity->id],
                        [
                            'name_media' => $request->input('name_media'),
                            'type_media' => $request->input('type_media'),
                            'size_media' => $request->input('size_media'),
                        ]
                    );
                }
            } elseif ($learningMediaType === 'forum') {
                Forum::updateOrCreate(
                    ['activity_id' => $activity->id],
                    [
                        'forum_title' => $request->input('forum_title'), // Menambahkan forum_title
                        'description_forum' => $request->input('description_forum'), // Menambahkan description_forum

                    ]
                );
            } elseif ($learningMediaType === 'assignment') {
                if ($request->hasFile('file_assignment')) {
                    $file = $request->file('file_assignment');
                    $filePath = $file->store('AssignmentFiles');
                    $request->merge(['file_assignment' => $filePath]);
                }
    
                
                Assignment::updateOrCreate(
                    ['activity_id' => $activity->id],
                    [
                        'name_assignment' => $request->input('name_assignment'),
                        'description' => $request->input('description'),
                        'file_assignment' => $request->input('file_assignment'),
                        'type_assignment' => $request->input('type_assignment'),
                        'allow_submission' => $request->input('allow_submission'),
                        'due_date' => $request->input('due_date'),
                        'cut_off' => $request->input('cut_off'),
                        'remind_grade' => $request->input('remind_grade'),
                        'max_file' => $request->input('max_file'),
                        'max_size' => $request->input('max_size'),
                    ]
                );
            } elseif ($learningMediaType === 'quiz') {
                Quiz::updateOrCreate(
                    ['activity_id' => $activity->id],
                    [

                        'title_quiz' => $request->input('title_quiz'),
                        'description_quiz' => $request->input('description_quiz'),
                        'open_quiz' => $request->input('open_quiz'),
                        'close_quiz' => $request->input('close_quiz'),
                        'time_limit' => $request->input('time_limit'),
                        'attempts_allowed' => $request->input('attempts_allowed'),

                    ]
                );
            } elseif ($learningMediaType === 'announcement') {
                Announcement::updateOrCreate(
                    ['activity_id' => $activity->id],
                    [
                        'name_announcement' => $request->input('name_announcement'),
                        'text_announcement' => $request->input('text_announcement'),
                    ]
                );
            }elseif ($learningMediaType === 'video') {
                Video::updateOrCreate(
                    ['activity_id' => $activity->id],
                    [
                        'name_video' => $request->input('name_video'),
                        'description_link' => $request->input('description_link'),
                        'link' => $request->input('link'),
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Activity Data Updated Successfully',
                'data' => $activity, // Jika Anda ingin mengembalikan data yang diperbarui
            ], 200);

        } catch(\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update data. ' . $e->getMessage(),
            ], 500);
        }
    }

  
    public function destroy(string $id)
    {

        
        try {
            DB::beginTransaction();

            $activity = Activity::whereNull('deleted_at')->find($id);

            if (!$activity) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Activity Data Not Found',
                ], 404);
            }

            // Hapus terkait Forum atau Media (sesuaikan dengan relasi yang sesuai)
            if ($activity->learning_media_type === 'forum') {
                $activity->forum()->delete();
            } elseif ($activity->learning_media_type === 'media') {
                $activity->media()->delete();
            }

            $activity->delete();
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Activity Data Deleted Successfully',
            ], 200);



        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete data. ' . $e->getMessage(),
            ], 500);
        }
    }
}