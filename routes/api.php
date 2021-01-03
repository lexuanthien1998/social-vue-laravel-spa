<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// post

Route::prefix('/post')->group(function() {
    Route::get('index', 'Api\PostsController@index');
    Route::get('{id}/details', 'Api\PostsController@show');
    Route::post('store', 'Api\PostsController@store');
    Route::post('{id}/update', 'Api\PostsController@update');
    Route::post('destroy', 'Api\PostsController@destroy');

    Route::post('likes', 'Api\PostsController@likes');
    Route::post('dislikes', 'Api\PostsController@dislikes');
    Route::post('comment', 'Api\PostsController@comment');
    Route::post('del-comment', 'Api\PostsController@deleteComment');

});
// user
Route::get('users', 'API\UserController@index');
Route::post('follow', 'API\UserController@follow');
Route::post('unfollow', 'API\UserController@unfollow');

Route::get('user/{username}', 'API\UserController@profile');
Route::post('user/{id}/profile/update', 'API\UserController@profileUpdate');
Route::post('login', 'API\UserController@login');
Route::post('register', 'API\UserController@register');
Route::middleware('auth:api')->group(function () {
    Route::post('logout', 'Api\UserController@logout');
});