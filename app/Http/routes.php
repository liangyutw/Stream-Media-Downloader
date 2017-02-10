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
Route::get('/test', 'TestController@create');



//Route::get('/test{id?}.html',                                                 ['as' => 'test_index', 'uses'=>"TestController@index"]);

//youtube 影片下載器
Route::get('/youtube', 				'YoutubeController@index'); //主頁面
Route::get('/youtube/getFiles', 	'YoutubeController@getFiles'); //取得檔案列表
Route::get('/youtube/deleteFile/{path?}',  'YoutubeController@deleteFile'); //刪除影片
Route::get('/youtube/deleteTsFile/{del_ts_cnt}',  'YoutubeController@deleteTsFile'); //刪除 TS 暫存影片
Route::get('/youtube/updFileName',  'YoutubeController@updateFileName'); //更換檔名
Route::get('/youtube/deleteRebuildFile/{del_file}',  'YoutubeController@deleteRebuildFile'); //刪除 轉換重制的原影片

//聊天室
Route::get('/chat',  'TestController@chatroom');
Route::get('/chat/get_massage', ['as' => 'chat_get_msg', 'uses'=>"TestController@get_chatroom"]);
Route::get('/home/{id?}', 'TestController@boardcast_user_show');
Route::get('/dl_chat_pic/{file_name}',  ['as' => 'dl_chat_pic', 'uses'=>"TestController@download_pic_from_chatroom"]);
Route::post('/chat/upload', ['as' => 'chat_upload', 'uses'=>"TestController@upload_chat_pic"]);
Route::post('/chat/save', ['as' => 'chat_save', 'uses'=>"TestController@save_chatroom"]);


//網頁推播
Route::get('/boardcast', 'TestController@boardcast_system_show');
Route::post('/boardcast', ['as' => 'boardcast_create','uses'=>'TestController@create']);

//註冊信發送
Route::post('/register_store', ['as' => 'register_store','uses'=>'TestController@register_store']);

