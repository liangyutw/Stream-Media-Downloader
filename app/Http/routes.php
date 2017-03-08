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
Route::any('/chat/login',  ['as' => 'chat_login', 'uses'=>'ChatController@chat_login']);
Route::any('/chat/add',  ['as' => 'chat_room_add', 'uses'=>'ChatController@add_chatroom']);
Route::get('/chat/logout',  ['as' => 'chat_logout', 'uses'=>'ChatController@chat_logout']);
Route::get('/chat/list',  ['as' => 'chat_room_list', 'uses'=>'ChatController@chatroom_list']);
Route::get('/chat/get_massage', ['as' => 'chat_get_msg', 'uses'=>"ChatController@get_chatroom"]);
Route::get('/chat/{chat_id?}',  ['as' => 'chat_room', 'uses'=>'ChatController@chatroom'])->where('chat_id', '[0-9]+');
Route::get('/chat/del_pic/{chat_id}/{msg_token}',  ['as' => 'chat_del_pic', 'uses'=>'ChatController@del_pic_from_chatroom']);
Route::get('/chat/del_msg/{chat_id}/{msg_token}',  ['as' => 'chat_del_msg', 'uses'=>'ChatController@del_msg_from_chatroom']);
Route::get('/chat/dl_chat_pic/{file_name}',  ['as' => 'dl_chat_pic', 'uses'=>"ChatController@download_pic_from_chatroom"]);
Route::any('/chat/invite/{chat_id?}',  ['as' => 'chat_invite_member', 'uses'=>"ChatController@invite_member"]);
Route::get('/chat/invite_list',  ['as' => 'chat_invite_list', 'uses'=>"ChatController@invite_list"]);
Route::get('/chat/del_chat_room/{chat_id}',  ['as' => 'del_chat_room', 'uses'=>"ChatController@del_chat_room"])->where('chat_id', '[0-9]+');
Route::post('/chat/history_msg',  ['as' => 'chat_room_history', 'uses'=>"ChatController@get_chat_room_history_msg"]);

Route::post('/chat/judge_invite',  ['as' => 'chat_invite_judge', 'uses'=>"ChatController@judge_invite_result"]);
Route::post('/chat/upload', ['as' => 'chat_upload', 'uses'=>"ChatController@upload_chat_pic"]);
Route::post('/chat/save', ['as' => 'chat_save', 'uses'=>"ChatController@save_chatroom"]);

Route::get('/home/{id?}', 'ChatController@boardcast_user_show');




//網頁推播
Route::get('/boardcast', 'TestController@boardcast_system_show');
Route::post('/boardcast', ['as' => 'boardcast_create','uses'=>'TestController@create']);

//註冊信發送
Route::post('/register_store', ['as' => 'register_store','uses'=>'TestController@register_store']);

