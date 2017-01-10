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
Route::get('/youtube/deleteFile/{path?}',  'YoutubeController@deleteFile'); //刪除影片
Route::get('/youtube/deleteTsFile/{del_ts_cnt}',  'YoutubeController@deleteTsFile'); //刪除 TS 暫存影片
Route::get('/youtube/updFileName',  'YoutubeController@updateFileName'); //更換檔名
Route::get('/youtube/deleteRebuildFile/{del_file}',  'YoutubeController@deleteRebuildFile'); //刪除 轉換重制的原影片
