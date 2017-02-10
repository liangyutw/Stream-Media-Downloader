<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\MailContentModel;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Redis;
use App\Events\PushNotification;
use App\Events\RegisterMail;
use Event;

class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        echo "index";
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        //$user_obj = new User();
        //echo "<pre>";var_dump($user_obj);exit;

        $user_obj = (isset($request->all()['user_id']) and !is_null($request->all()['user_id'])) ? User::find($request->all()['user_id']) : null;

        Event::fire(new PushNotification($user_obj, $request->all()['content']));
        return view('boardcast.system_show', ["send_data" => $request->all()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register_store(Request $request)
    {
        $email = isset($request->all()['register_email']) ? $request->all()['register_email'] : null;
        $name = isset($request->all()['register_name']) ? $request->all()['register_name'] : null;
        if (is_null($email) and is_null($name)) {
            return 'email and name is not empty!!';
        }
        $user = new User;
        $user->name = $name;
        $user->email = $email;

        $mail_content = new MailContentModel;

        if ($user->save()) {
            Event::fire(new RegisterMail($user->id, $mail_content->member_register(env('BW_URL'))));
            return redirect('boardcast');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function boardcast_system_show()
    {
        return view('boardcast.system_show');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function boardcast_user_show($id)
    {
        $user = User::find($id);
        $token = sha1($user->id . '|' . $user->email);
        return view('boardcast.show', compact('token'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    //聊天室畫面
    public function chatroom()
    {
        return view('chat.home');
    }

    //聊天室對話儲存
    public function save_chatroom(Request $request)
    {
//        echo "<pre>";
        //print_r(htmlspecialchars($request->all()['msg_data']));
//        print_r($request->all());
//        exit;
        $all_data = '';
        $upload_status = isset($request->all()['upload_status']) ? $request->all()['upload_status'] : null;
        $msg_data = isset($request->all()['msg_data']) ? $request->all()['msg_data'] : null;
        $name_data = trim($request->all()['name']);

//        if(!is_null($upload_status)) {
//            $all_data = '"<div>'.$name_data.':</div> <div style=\'background:#eaeaea; padding:10px;margin:10px 0px 10px 20px;\'>' . trim($msg_data) . '</div>",';
//        }else {
//            $all_data = '"<div style=\'background:#eaeaea; padding:10px;margin:10px 0px 10px 20px;\'>' . trim($msg_data) . '</div>",';
//        }
        if(!is_null($upload_status)) {
            $all_data = '"<div>'.$name_data.':</div> <p class=\'bg-success\'>' . trim($msg_data) . '</p>",';
        }else {
            $all_data = '"<p class=\'bg-success\'>' . trim($msg_data) . '</p>",';
        }

        $file = fopen(public_path().'/chat_save/'.date("Ymd").".json", "a+"); //開啟檔案
        fwrite($file, $all_data);
        fclose($file);
    }

    //取聊天室對話
    public function get_chatroom()
    {
//        var_dump(file_exists(public_path().'/chat_save/'.date("Ymd").".json"));
//        var_dump(file_exists(public_path().'/chat_save/'.date('Ymd',strtotime(date("Ymd") . "-1 days")).".json"));
//        exit;

        $source = $yesterday_source = $new_source = [];
        //取出前一天記錄
        if (file_exists(public_path().'/chat_save/'.date("Ymd",strtotime(date("Ymd") . "-1 days")).".json") == true) {
            $yesterday_source = json_decode('{"data":[' . rtrim(file_get_contents(public_path() . '/chat_save/' . date('Ymd', strtotime(date("Ymd") . "-1 days")) . ".json"), ',') . ']}', true);
        }

        //取今天記錄
        if (file_exists(public_path().'/chat_save/'.date("Ymd").".json") == false) {
            $file = fopen(public_path().'/chat_save/'.date("Ymd").".json", "a+"); //開啟檔案
            fwrite($file, '');
            fclose($file);
        }else {
            $source = json_decode('{"data":[' . rtrim(file_get_contents(public_path() . '/chat_save/' . date("Ymd") . ".json"), ',') . ']}', true);
        }


        switch (true) {
            case (is_array($yesterday_source) == true and count($yesterday_source) > 0 and is_array($source) == true and count($source) > 0):
                $new_source = array_merge($yesterday_source['data'], $source['data']);
                break;
            case (is_array($yesterday_source) == true and count($yesterday_source) > 0 and count($source) <= 0):
                $new_source = $yesterday_source;
                break;
            case (is_array($source) == true and count($source) > 0 and count($yesterday_source) <= 0):
                $new_source = $source;
                break;
        }

//        echo "<pre>";print_r($yesterday_source);
//        echo "<pre>";print_r($new_source);
//        exit;

        return $new_source;
    }

    //聊天室上傳圖片
    public function upload_chat_pic(Request $request)
    {
//        echo "<pre>";print_r($request->all()['userImage']->getClientOriginalName());
//        print_r($request->all()['userImage']->getClientOriginalName());
//        print_r($request->all()['userImage']->getSize());
//        exit;

        $file_data = $request->file('userImage');
        $image_info = getimagesize($request->file('userImage'));

        // 上傳路徑
        $destinationPath = public_path().'/chat_upload_pic';


        // 取得檔案擴展名(副檔名)
        $extension = $file_data->getClientOriginalExtension();
        $originalname = $file_data->getClientOriginalName();
        $size = $file_data->getSize();

        // 檔案重新命名
        $fileName = time().'.'.$extension;

        // 複製移動到指定上傳路徑
        $return = $file_data->move($destinationPath, $fileName);

        if (gettype($return) == "object") {
            return [
                "originalname" => $originalname,
                "file_name" => '/chat_upload_pic/'.$fileName,
                "file_size" => ($size > 1024) ? number_format(round($size/1024)) : $size,
                "image_height" => $image_info[1],
                "extension" => $extension
            ];
        }
    }

    //聊天室圖片下載
    public function download_pic_from_chatroom($file_name = null)
    {
        if (is_null($file_name)) {
            return false;
        }

        header("Content-type:application");
        header("Content-Disposition: attachment; filename=".$file_name);
        readfile(env('BW_URL').'/chat_upload_pic/'.$file_name);
        exit(0);
    }
}
