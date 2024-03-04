<?php

namespace App\Http\Controllers\Api;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $keyCache = 'event_all';

            $events = Cache::remember($keyCache, config('app.cache_time'), function () {
                return Event::whereNull('deleted_at')->get();
            });

            if(count($events) > 0)
            {
                Log::info('Data Event Berhasil Ditampilkan');
                return response()->json([
                    'data' => $events,
                    'status' => 'success',
                    'message' => 'Data Event Berhasil Ditampilkan',
                ], 200);
            }
            
            Log::info('Data Event Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Event Kosong',
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

            $validate = Validator::make($request->all(), [
                'event_name' => 'required|max:255',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'description' => 'required|max:255',
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
    
            $event = Event::create($storeData);
    
            Log::info('Data Event Berhasil Ditambahakan');
            return response()->json([
                'data' => $event,
                'status' => 'success',
                'message' => 'Data Event Berhasil Ditambahakan',
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
            $event = Event::whereNull('deleted_at')->find($id);

            if(!$event){
                Log::error('Data Event Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Event Tidak Ditemukan',
                ], 404);
            }
    
            $validate = Validator::make($request->all(), [
                'event_name' => 'required|max:255',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'description' => 'required|max:255',
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
    
            $event->event_name = $request->event_name;
            $event->start_date = $request->start_date;
            $event->end_date = $request->end_date;
            $event->description = $request->description;
            
            $event->save();
    
            Log::info('Data Event Berhasil Diupdate');
            return response()->json([
                'data' => $event,
                'status' => 'success',
                'message' => 'Data Event Berhasil Diupdate',
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
            $event = Event::whereNull('deleted_at')->find($id);

            if(!$event){
                Log::error('Data Event Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Event Tidak Ditemukan',
                ], 404);
            }
    
            if($event->delete()){
                Log::info('Data Event Berhasil Dihapus');
                return response()->json([
                    'data' => $event,
                    'status' => 'success',
                    'message' => 'Data Event Berhasil Dihapus',
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
