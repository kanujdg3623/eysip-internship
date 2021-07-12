<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// Cookies Routes
Route::get('/data/visitorcookie','VisitorController@index')->name('visitor.index');
Route::get('/data/visitorcookie/show','VisitorController@show')->name('visitor.show');
Route::get('/data/visitorcookie/update','VisitorController@update')->name('visitor.update');
Route::get('/data/visitorcookie/analysis','VisitorController@analysis')->name('visitor.analysis')->middleware('auth:admin');
Route::get('/data/clientcookie','ClientController@index')->name('client.index');
Route::get('/data/clientcookie/analysis','ClientController@analysis')->name('client.analysis')->middleware('auth:admin');

// User login and logout
Route::post('/password/check','Auth\ForgotPasswordController@check')->name('password.check');
Route::post('/check','Auth\LoginController@check')->name('check');
Route::get('/user/logout','Auth\LoginController@userLogout')->name('user.logout');

// Initiative page
Route::prefix('videos')->group(function(){
	Route::get('/year','VideosController@getYear')->name('videos.get-year');
	Route::get('/{initiative}','VideosController@index')->name('videos.index');
	Route::get('/','VideosController@getVideos')->name('videos.get-videos');
	Route::get('/id/{id}','VideosController@getVideo');
});

// pubsubhubbub
Route::get('/youtube-notification','YoutubeController@subscribeYoutube');
Route::post('/youtube-notification','YoutubeController@youtubeNotification');

// User home
Route::prefix('home')->group(function(){
	Route::get('/', 'HomeController@index');
	Route::get('/edit','HomeController@edit')->name('user.edit');
	Route::put('/','HomeController@update')->name('user.update');
});

// Admin
Route::prefix('course')->group(function(){
	Route::get('/', 'CourseController@index')->name('course.index')->middleware('auth');
	Route::get('/{id}', 'CourseController@show')->name('course.show')->middleware('auth');
	Route::get('/{id}/{video_id}/token', 'CourseVideoController@get_access_token')->name('course-video.video-token')->middleware('auth');
});

Route::prefix('admin')->group(function(){
	Route::get('/login','Auth\AdminLoginController@showLoginForm')->name('admin.login');
	Route::post('/login','Auth\AdminLoginController@login')->name('admin.login.submit');
	Route::get('/', 'AdminController@index')->name('admin.dashboard');
	Route::get('/edit','AdminController@edit')->name('admin.edit');
	Route::get('/approve/{user}','AdminController@approve');
	Route::get('/logout','Auth\AdminLoginController@logout')->name('admin.logout');
	Route::put('/','AdminController@update')->name('admin.update');
	
	Route::get('/analytics','AdminController@analytics')->name('admin.youtube-analysis');
	Route::get('/get-analytics','AdminController@getAnalytics');
	
	Route::get('/youtube-page','VideosController@getYoutubeView')->name('admin.youtube')->middleware('auth:admin');
	Route::get('/youtube-videos','VideosController@getYoutubeVideos')->middleware('auth:admin');
	Route::put('/youtube-update','VideosController@updateYoutubeVideo')->middleware('auth:admin');
	Route::delete('/youtube-delete','VideosController@deleteYoutubeVideo')->middleware('auth:admin');
	
	Route::get('/initiatives/{crud}','VideosController@crudInitiatives')->name('admin.initiatives')->middleware('auth:admin');	
	
	Route::get('/visitoranalysis',function () {
		return view('visitor-analysis.visitor-analysis');
	})->name('admin.visitor-analysis')->middleware('auth:admin');
	Route::get('/clientanalysis',function () {
		return view('client-analysis.client-analysis');
	})->name('admin.client-analysis')->middleware('auth:admin');
	Route::get('/analytics','AdminController@analytics')->name('admin.youtube-analysis');
	// Admin Courses
	Route::prefix('course')->group(function(){
		Route::get('/', 'CourseController@index')->name('admin-course.index')->middleware('auth:admin');
		Route::get('/create', 'CourseController@create')->name('admin-course.create')->middleware('auth:admin');
		Route::post('/create', 'CourseController@store')->name('admin-course.store')->middleware('auth:admin');
		Route::get('/{id}', 'CourseController@show')->name('admin-course.show')->middleware('auth:admin');
		Route::get('/{id}/edit', 'CourseController@edit')->name('admin-course.edit')->middleware('auth:admin');
		Route::get('/{id}/addVideo', 'CourseVideoController@create')->name('admin-course-video.create')->middleware('auth:admin');
		Route::post('/{id}/addVideo', 'CourseVideoController@store')->name('admin-course-video.store')->middleware('auth:admin');
		Route::get('/{id}/{video_id}', 'CourseVideoController@show')->name('admin-course-video.show')->middleware('auth:admin');
		Route::get('/{id}/{video_id}/edit', 'CourseVideoController@edit')->name('admin-course-video.edit')->middleware('auth:admin');
		Route::get('/{id}/{video_id}/token', 'CourseVideoController@get_access_token')->name('admin-course-video.video-token')->middleware('auth:admin');
	});
});

Auth::routes();

Route::get('/', function (Request $request) {
    return view('welcome');
})->name('landing');
