<?php

use App\Http\Controllers\Api\LectureController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('login', 'App\Http\Controllers\Api\AuthController@login');

Route::middleware('auth:api')->group(function () {
    Route::apiResource('user', 'App\Http\Controllers\Api\UserController');
    Route::post('logout', 'App\Http\Controllers\Api\AuthController@logout');
    Route::apiResource('role', 'App\Http\Controllers\Api\RoleController');
    Route::apiResource('userRole', 'App\Http\Controllers\Api\UserRoleController');
    Route::apiResource('student', 'App\Http\Controllers\Api\StudentController');
    Route::apiResource('program', 'App\Http\Controllers\Api\ProgramController');
    Route::apiResource('faculty', 'App\Http\Controllers\Api\FacultyController');
    Route::apiResource('studyProgram', 'App\Http\Controllers\Api\StudyProgramController');
    Route::apiResource('course', 'App\Http\Controllers\Api\CourseController');
    Route::apiResource('activity', 'App\Http\Controllers\Api\ActivityController');
    Route::apiResource('schedule', 'App\Http\Controllers\Api\ScheduleController');
    Route::apiResource('media', 'App\Http\Controllers\Api\MediaController');
    Route::apiResource('forum', 'App\Http\Controllers\Api\ForumController');
    Route::apiResource('quiz', 'App\Http\Controllers\Api\QuizController');
    Route::apiResource('question', 'App\Http\Controllers\Api\QuestionController');
    Route::apiResource('quizAttempt', 'App\Http\Controllers\Api\QuizAttemptController');
    Route::apiResource('choiceOption', 'App\Http\Controllers\Api\ChoiceOptionController');
    Route::apiResource('assignment', 'App\Http\Controllers\Api\AssignmentController');
    Route::apiResource('answer', 'App\Http\Controllers\Api\AnswerController');
    Route::apiResource('comment', 'App\Http\Controllers\Api\CommentController');
    Route::apiResource('uploader', 'App\Http\Controllers\Api\UploaderController');
    Route::apiResource('event', 'App\Http\Controllers\Api\EventController');
    Route::apiResource('scoringDeadline', 'App\Http\Controllers\Api\ScoringDeadlineController');
    Route::apiResource('announcement', 'App\Http\Controllers\Api\AnnouncementController');
    Route::apiResource('video', 'App\Http\Controllers\Api\VideoController');
    Route::apiResource('attendance', 'App\Http\Controllers\Api\AttendanceController');
    Route::apiResource('testPackage', 'App\Http\Controllers\Api\TestPackageController');
    Route::get('lectureAttendance', 'App\Http\Controllers\Api\MonitoringController@getLectureAttendance');
    Route::get('studentAttendance', 'App\Http\Controllers\Api\MonitoringController@getStudentAttendance');
    
    

    Route::get('media/download/{id}', 'App\Http\Controllers\Api\MediaController@download')->name('media.download');
    Route::get('assignment/download/{id}', 'App\Http\Controllers\Api\AssignmentController@download')->name('assignment.download');
    
    Route::get('uploader/download/{id}', 'App\Http\Controllers\Api\UploaderController@download')->name('uploader.download');
    Route::get('student/schedule/{id}',  'App\Http\Controllers\Api\StudentController@showSchedules');
    Route::get('lecture/showCourse/{id}',  'App\Http\Controllers\Api\LectureController@showCourse');
    Route::get('attendance/students/{courseCode}', 'App\Http\Controllers\Api\AttendanceController@showStudentsInCourse');
    Route::post('uploader/update/{id}', 'App\Http\Controllers\Api\UploaderController@updatee')->name('uploader.updatee');


    route::controller(LectureController::class)
    ->prefix('lecture')
    ->group(function(){
        Route::post('/', 'store');
        Route::put('/{id}}', 'update');
        Route::delete('/{id}', 'destroy');
        Route::get('/paginate', 'index');
        Route::get('/showCourse', 'showCourse');

    });


});
