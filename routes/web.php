<?php

use App\Http\Controllers\Main\HomeController;
use App\Http\Controllers\Main\ProjectController;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Xml\Project;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', [HomeController::class, 'index']);

Route::prefix('project')->group(function () {
    Route::get('/', [ProjectController::class, 'index']);
    Route::get('/{id}/detail', [ProjectController::class, 'detail']);
    // Route::post('/validasi', controller_path('LoginController@validasi'));
    // Route::get('/captcha', ['uses' => controller_path('LoginController@getCaptcha')]);
});


Route::get('/about', function () {
    return view('about');
});

Route::get('/contact', function () {
    return view('contact');
});

// Route::get('/project', function () {
//     return view('project.project');
// });
// Route::get('/project/detail', function () {
//     return view('project.project-detail');
// });

Route::get('/blog', function () {
    return view('blog.blog');
});
Route::get('/blog/detail', function () {
    return view('blog.blog-detail');
});
