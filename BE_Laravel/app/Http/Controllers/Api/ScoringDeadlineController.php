<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ScoringDeadline;
use App\Models\Quiz;
use App\Models\Assignment;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ScoringDeadlineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $keyCache = 'scoring_deadline_all';

            $scoringDeadlines = Cache::remember($keyCache, config('app.cache_time'), function () {
                return ScoringDeadline::whereNull('deleted_at')->get();
            });
        
            if(count($scoringDeadlines) > 0)
            {
                Log::info('Data Scoring Deadline Berhasil Ditampilkan');
                return response()->json([
                    'data' => $scoringDeadlines,
                    'status' => 'success',
                    'message' => 'Data Scoring Deadline Berhasil Ditampilkan',
                ], 200);
            }
    
            Log::info('Data Scoring Deadline Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Scoring Deadline Kosong',
            ], 200);
        }catch(\Exception $e){
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);   
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
      try{
            $storeData = $request->all();

            $isFilled_quiz_title = $request->filled('quiz_title') ? true : false;
            $isFilled_assignment_name = $request->filled('assignment_name') ? true : false;
    
            $validate = Validator::make($request->all(), [
                'type_scroring' => 'required|max:255|in:quiz,assignment',
                'deadline' => 'required|date',
                'quiz_title' => 'max:255' . ($isFilled_quiz_title ? '|exists:quizzes,quiz_title' : ''),
                'assignment_name' => 'max:255' . ($isFilled_assignment_name ? '|exists:assignments,name_assignment' : ''),
            ]);
    
            if($validate->fails())
            {
                Log::error('validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }
    
            $quiz = Quiz::whereNull('deleted_at')->where('quiz_title', $request['quiz_title'])->first();
            $assignment = Assignment::whereNull('deleted_at')->where('name_assingment', $request['assignment_name'])->first();
    
            if(!$quiz && $assignment && $isFilled_quiz_title){
                Log::error('The selected quiz name is invalid.');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'The selected quiz name is invalid.',
                ], 400);
            }else if(!$assignment && $quiz && $isFilled_assignment_name){
                Log::error('The selected assignment name is invalid.');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'The selected assignment name is invalid.',
                ], 400);
            }else if(!$quiz && !$assignment){
                Log::error('The selected quiz tittle and/or assignment name is invalid.');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'The selected quiz tittle and/or assignment name is invalid.',
                ], 400);
            }
    
            $storeData['quiz_id'] = $isFilled_quiz_title ? $quiz->id : null;
            $storeData['assignment_id'] = $isFilled_assignment_name ? $assignment->id : null;
    
            $scoringDeadline = ScoringDeadline::create($storeData);
    
            Log::info('Data Scoring Deadline Berhasil Ditambahakan');
            return response()->json([
                'data' => $scoringDeadline,
                'status' => 'success',
                'message' => 'Data Scoring Deadline Berhasil Ditambahakan',
            ], 200);
        }catch(\Exception $e){
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);   
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try{
            $scoringDeadline = ScoringDeadline::whereNull('deleted_at')->find($id);

            if(!$scoringDeadline){
                Log::error('Data Scoring Deadline Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Scoring Deadline Tidak Ditemukan',
                ], 404);
            }
    
            $isFilled_quiz_title = $request->filled('quiz_title') ? true : false;
            $isFilled_assignment_name = $request->filled('assignment_name') ? true : false;
    
            $validate = Validator::make($request->all(), [
                'type_scroring' => 'required|max:255|in:quiz,assignment',
                'deadline' => 'required|date',
                'quiz_title' => 'max:255' . ($isFilled_quiz_title ? '|exists:quizzes,quiz_title' : ''),
                'assignment_name' => 'max:255' . ($isFilled_assignment_name ? '|exists:assignments,name_assignment' : ''),
            ]);
    
            if($validate->fails())
            {
                Log::error('validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }
    
            $quiz = Quiz::whereNull('deleted_at')->where('quiz_title', $request['quiz_title'])->first();
            $assignment = Assignment::whereNull('deleted_at')->where('name_assignment', $request['assignment_name'])->first();
    
            if(!$quiz && $assignment && $isFilled_quiz_title){
                Log::error('The selected quiz name is invalid.');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'The selected quiz name is invalid.',
                ], 400);
            }else if(!$assignment && $quiz && $isFilled_assignment_name){
                Log::error('The selected assignment name is invalid.');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'The selected assignment name is invalid.',
                ], 400);
            }else if(!$quiz && !$assignment){
                Log::error('The selected quiz tittle and/or assignment name is invalid.');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'The selected quiz tittle and/or assignment name is invalid.',
                ], 400);
            }
    
            $scoringDeadline->type_scroring = $request->type_scroring;
            $scoringDeadline->deadline = $request->deadline;
            $scoringDeadline->quiz_id = $isFilled_quiz_title ? $quiz->id : null;
            $scoringDeadline->assignment_id = $isFilled_assignment_name ? $assignment->id : null;
            
            $scoringDeadline->save();
    
            Log::info('Data Scoring Deadline Berhasil Diupdate');
            return response()->json([
                'data' => $scoringDeadline,
                'status' => 'success',
                'message' => 'Data Scoring Deadline Berhasil Diupdate',
            ], 200);
        }catch(\Exception $e){
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);   
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            $scoringDeadline = ScoringDeadline::whereNull('deleted_at')->find($id);

            if(!$scoringDeadline){
                Log::error('Data Scoring Deadline Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Scoring Deadline Tidak Ditemukan',
                ], 404);
            }
    
            if($scoringDeadline->delete()){
                Log::info('Data Scoring Deadline Berhasil Dihapus');
                return response()->json([
                    'data' => $scoringDeadline,
                    'status' => 'success',
                    'message' => 'Data Scoring Deadline Berhasil Dihapus',
                ], 200);
            }
        }catch(\Exception $e){
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);   
        }
    }
}
