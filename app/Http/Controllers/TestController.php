<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        echo "this is test Controller".$id;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
//        echo "<pre>";print_r(htmlspecialchars($request->all()['msg_data']));
//        exit;
        $name_data = trim($request->all()['name']);
        $msg_data = '"<div>'.$name_data.':</div> <div style=\'background:#eaeaea; padding:10px;margin:10px 0px 10px 20px;\'>'.trim(htmlspecialchars($request->all()['msg_data'])).'<span style=font-size:10pt;color:#aaa;> '.date("Y-m-d H:i:s").'</span></div>",';

        $file = fopen(public_path().'/chat_save/'.date("Ymd").".json", "a+"); //開啟檔案
        fwrite($file, $msg_data);
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
//        echo "<pre>";print_r($request->file('userImage'));
//        print_r($request->all()['userImage']->getClientOriginalName());
//        print_r($request->all()['userImage']->getSize());
//        exit;


        $file_data = $request->file('userImage');

        // 上傳路徑
        $destinationPath = public_path().'/chat_upload_pic';


        // 取得檔案擴展名(副檔名)
        $extension = $file_data->getClientOriginalExtension();
        $size = $file_data->getSize();

        // 檔案重新命名
        $fileName = time().'.'.$extension;

        // 複製移動到指定上傳路徑
        $return = $file_data->move($destinationPath, $fileName);

        if (gettype($return) == "object") {
            return ["file_name" => '/chat_upload_pic/'.$fileName, "file_size" => number_format(round($size/1024))];
        }

    }
}
