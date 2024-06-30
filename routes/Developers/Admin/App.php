<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Admin\{
    CategoriesController,
    StudentController

};
use App\Http\Controllers\Api\V1\{
    CoursesController,
    CourseVideosController,
    CourseQuizzesController,
    QuizQuestionsController,

};


Route::prefix('admin/')

    ->middleware(['auth:sanctum','admin'])
    ->group(function () {

        Route::apiResource('categories',CategoriesController::class);

        Route::apiResource('courses', CoursesController::class);
        Route::apiResource('courses.videos', CourseVideosController::class);
        Route::apiResource('courses.quizzess', CourseQuizzesController::class);
        Route::apiResource('quizzes.questions', QuizQuestionsController::class);

        Route::apiResource('students',StudentController::class);
    });
