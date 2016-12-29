<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test{id?}.html',                                                 ['as' => 'test_index', 'uses'=>"TestController@index"]);

//youtube 影片下載器
Route::get('/youtube', 				'YoutubeController@index'); //主頁面
Route::get('/youtube/getFiles', 	'YoutubeController@getFiles'); //取得檔案列表
Route::post('/youtube/deleteFile',  'YoutubeController@deleteFile'); //刪除影片
