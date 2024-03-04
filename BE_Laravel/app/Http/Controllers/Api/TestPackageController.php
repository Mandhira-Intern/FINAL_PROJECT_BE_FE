<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TestPackage;
use App\Models\QuestionTestPackage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TestPackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $keyCache = 'test_package_all';

            $testPackage = Cache::remember($keyCache, config('app.cache_time'), function () {
                return QuestionTestPackage::join('test_packages', 'question_test_packages.testPackage_id', '=', 'test_packages.id')
                ->join('questions', 'question_test_packages.question_id', '=', 'questions.id')
                ->whereNull('question_test_packages.deleted_at')
                ->select('question_test_packages.*', 'test_packages.title', 'questions.text_question', 'questions.type_question', 'questions.poin_question')
                ->get();
            }); 
            
            if(count($testPackage) > 0)
            {
                Log::info('Data Test Package Berhasil Ditampilkan');
                return response()->json([
                    'data' => $testPackage,
                    'status' => 'success',
                    'message' => 'Data Test Package Berhasil Ditampilkan',
                ], 200);
            }
    
            Log::info('Data Test Package Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Test Package Kosong',
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

            $validate = Validator::make($storeData, [
                'title' => 'required|max:255',
                'question_ids' => 'required|array',
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

            DB::beginTransaction();
            $testPackage = TestPackage::create($storeData);

            $storeData['testPackage_id'] = $testPackage->id;

            foreach($storeData['question_ids'] as $question_id){
                $storeData['question_id'] = $question_id;
                $questionTestPackage = QuestionTestPackage::create($storeData);
            }

            DB::commit();
            Log::info('Data Test Package Berhasil Ditambahakan');
            return response()->json([
                'data' => $storeData,
                'status' => 'success',
                'message' => 'Data Test Package Berhasil Ditambahakan',
            ], 200);
        }catch(\Exception $e){
            DB::rollBack();
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'exception' => $e->getMessage(),
                'status' => 'error',
                'message' => "Data Test Package Gagal Ditambahakan",
            ], 422);
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
            DB::beginTransaction();

            $responseFullyDestroy = $this->fullyDestroy($id);
            if ($responseFullyDestroy->getStatusCode() !== 200) {
                DB::rollBack();
                return $responseFullyDestroy;
            }

            $responseStore = $this->store($request);
            if($responseStore->getStatusCode() !== 200){
                DB::rollBack();
                return $responseStore;
            }

            DB::commit();
            Log::info('Data Test Package Berhasil Diupdate');
            return response()->json([
                'data' => $request->all(),
                'status' => 'success',
                'message' => 'Data Test Package Berhasil Diupdate',
            ], 200);
        }catch(\Exception $e){
            DB::rollBack();
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'exception' => $e->getMessage(),
                'status' => 'error',
                'message' => "Data Test Package Gagal Diupdate",
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            $testPackage = TestPackage::whereNull('deleted_at')->find($id);
            $questionTestPackage = QuestionTestPackage::where('testPackage_id', $id)->whereNull('deleted_at');

            if(!$testPackage){
                Log::error('Test Package Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Test Package Tidak Ditemukan',
                ], 404);
            }
    
            DB::beginTransaction();
            if($testPackage->delete() && $questionTestPackage->delete()){
                DB::commit();
                Log::info('Test Package Berhasil Dihapus');
                return response()->json([
                    'data' => $testPackage,
                    'status' => 'success',
                    'message' => 'Test Package Berhasil Dihapus',
                ], 200);
            }
        }catch(\Exception $e){
            DB::rollBack();
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'exception' => $e->getMessage(),
                'status' => 'error',
                'message' => "Data Test Package Gagal Dihapus",
            ], 422);
        }
    }

    public function fullyDestroy(string $id)
    {
        try{
            $testPackage = TestPackage::whereNull('deleted_at')->find($id);

            if(!$testPackage){
                Log::error('Test Package Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Test Package Tidak Ditemukan',
                ], 404);
            }

            DB::beginTransaction();

            $deleted = QuestionTestPackage::where('testPackage_id', $id)->whereNull('deleted_at')->forceDelete();

            if($testPackage->forceDelete() && $deleted){
                DB::commit();
                Log::info('Test Package Berhasil Dihapus');
                return response()->json([
                    'data' => $testPackage,
                    'status' => 'success',
                    'message' => 'Test Package Berhasil Dihapus',
                ], 200);
            }
            DB::rollBack();
            return response()->json([
                'data' => $testPackage,
                'status' => 'Error',
                'message' => 'Error',
            ], 400);
        }catch(\Exception $e){
            DB::rollBack();
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'exception' => $e->getMessage(),
                'status' => 'error',
                'message' => "Data Test Package Gagal Dihapus",
            ], 422);
        }
    }
}
