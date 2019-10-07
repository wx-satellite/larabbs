<?php

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

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::get("test/{name?}","TestController@test");


//Route::get("/","PagesController@root")->name("root");
Route::get("/","TopicsController@index")->name("root");
// 后台权限校验失败跳转的路由
Route::get("permission-denied","PagesController@permissionDenied")->name("permission-denied");

Route::resource("users","UsersController",["only"=>["show","update","edit"]]);


/******  用户认证脚手架生成的语句是：Auth::routes()，实际等效于下面的路由： *****/

// 用户身份验证相关的路由
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// 用户注册相关路由
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');

// 密码重置相关路由
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

// Email 认证相关路由
Route::get('email/verify', 'Auth\VerificationController@show')->name('verification.notice'); // 验证页面
Route::get('email/verify/{id}', 'Auth\VerificationController@verify')->name('verification.verify'); // 验证邮箱，在具体操作在中间件中
Route::get('email/resend', 'Auth\VerificationController@resend')->name('verification.resend'); // 重发邮件

/*****  Auth::routes()结束    ************************/


Route::resource('topics', 'TopicsController', ['only' => ['index', 'create', 'store', 'update', 'edit', 'destroy']]);
Route::get("topics/{topic}/{slug?}","TopicsController@show")->name("topics.show");

Route::post("upload_image","TopicsController@uploadImage")->name("topics.upload_image");

Route::resource("categories","CategoriesController",["only"=>["show"]]);
Route::resource('replies', 'RepliesController', ['only' => ['store',  'destroy']]);

Route::resource("notifications", "NotificationsController",["only"=>["index"]]);