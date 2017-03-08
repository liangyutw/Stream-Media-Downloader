<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\ChatRoomList;
use App\ChatRoomMember;
use App\Notice;
//use App\MailContentModel;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session;
use Illuminate\Http\Response;
use DB;

//use Redis;
//use App\Events\PushNotification;
//use App\Events\RegisterMail;
//use Event;

class ChatController extends Controller
{
    private $today_json_path;
    private $day;
    private $login_user;
    private $date_bar_start;
    private $date_bar_end;

    function __construct()
    {
        $this->day = 4;
        if (!session::has('user_info')) {
            return redirect('/chat/login');
        }
        $this->login_user       = session::get('user_info')['id'];
        $this->date_bar_start   = "<div style='background-color: #eaeaea;height: 20px;line-height: 20px;'><div style='font-size:10pt;color:#888;margin-left: 50%;font-weight: bold;'>";
        $this->date_bar_end     = "</div></div>";
    }

    //登入聊天室
    public function chat_login(Request $request)
    {
        //echo "<pre>";print_r($request->all());exit;
        if ($request->method() == 'GET') {
            return view('chat.login');
        }elseif ($request->method() == 'POST') {

            $email      = isset($request->all()['email']) ? $request->all()['email'] : null;
            $password   = isset($request->all()['password']) ? $request->all()['password'] : null;

            if (is_null($email) or is_null($password)) {
                return false;
            }
            $user_count = User::where(["email" => $email, "password" => $password])->count();
            $user_data  = User::where(["email" => $email, "password" => $password])->get()->toArray()[0];

            if ($user_count == 1) {
                Session::put('user_info', $user_data);

                return redirect('chat/list');
            }
            return $user_data;
        }
    }

    //登出聊天室
    public function chat_logout()
    {
        Session::forget('user_info');
        return view('chat.login');
    }

    //聊天室列表
    public function chatroom_list()
    {
        if (!Session::has('user_info')) {
            session()->flash('nologin_msg','未登入無法列出聊天室');
            return view('chat.room_list');
        }

        $chat_room_data = $user_ids = [];
        $del_status_arr = [];

        $ChatRoomList   = new ChatRoomList;
        $chat_room_data = $ChatRoomList->get_chat_room_data();

        $Notice         = new Notice;
        $notice_count   = $Notice->get_notice_count($this->login_user);

        foreach ($chat_room_data as $chat_key => &$chat_info) {
            $user_ids = json_decode($chat_info['user_id'], true);
            if (!in_array($this->login_user, $user_ids)) {
                unset($chat_room_data[$chat_key]);
            }
            if (isset($chat_info['create_user_id'])) {
                $chat_info['create_user_name'] = $this->_user_data_array()[$chat_info['create_user_id']];
            }
            if ($this->login_user == $chat_info['create_user_id']) {
                $del_status_arr[$chat_info['chat_room_id']] = ["del_status" => true];
            }
        }

        $return_arr = [
            "notice_count"      => $notice_count,
            "chat_list_result"  => $chat_room_data,
            "del_status_arr"    => $del_status_arr
        ];

        return view('chat.room_list', $return_arr);
    }

    //建立聊天室
    public function add_chatroom(Request $request)
    {
//        echo "<pre>";print_r($request->all());exit;
        if ($request->method() == 'GET') {
            if (!Session::has('user_info')) {
                session()->flash('nologin_msg','未登入無法新增聊天室');
                return view('chat.add_room');
            }

            $Notice         = new Notice;
            $notice_count   = $Notice->get_notice_count($this->login_user);
            $return_arr = [
                "notice_count" => $notice_count
            ];

            return view('chat.add_room', $return_arr);

        }elseif ($request->method() == 'POST') {
            $user_id        = isset($this->login_user) ? $this->login_user : null;
            $name           = isset($request->all()['name']) ? $request->all()['name'] : null;
            $description    = isset($request->all()['description']) ? $request->all()['description'] : null;
            $limit          = isset($request->all()['limit']) ? $request->all()['limit'] : null;
            $status         = isset($request->all()['status']) ? $request->all()['status'] : null;

            if (is_null($name) or
                !is_numeric($user_id) or is_null($user_id) or
                !is_numeric($limit) or is_null($limit) or
                !is_numeric($status) or is_null($status)
            ) {
                session()->flash('error_msg','建立失敗，請檢查傳遞格式');
                return view('chat.add_room');
            }
            $ChatRoomList   = new ChatRoomList;
            $return_result  = $ChatRoomList->get_user_create_room_limit($user_id);

            if ($return_result == false) {
                session()->flash('error_msg','建立聊天室失敗，已達限制數量');
                return view('chat.add_room');
            }

            //建立聊天室資訊
            $chat_room_param = [
                "name"              => $name,
                "limit"             => $limit,
                "description"       => $description,
                "status"            => $status,
                "create_user_id"    => $user_id
            ];

            $ChatRoomList_id = $ChatRoomList->create_chat_room($chat_room_param);

            if ($ChatRoomList_id > 0) {

                $chat_room_user = [];
                array_push($chat_room_user, $user_id);

                // 建立聊天會員
                $member_param = [
                    "chat_room_id"  => $ChatRoomList_id,
                    "user_id"       => json_encode($chat_room_user),
                    "set_time"      => date('Y-m-d H:i:s')
                ];
                $ChatRoomMember     = new ChatRoomMember;
                $insert_member_result = $ChatRoomMember->create_chat_room_member($member_param);

                if ($insert_member_result == true) {
                    return redirect('chat/list');
                }
            }
        }
    }

    //單一聊天室畫面
    public function chatroom($chat_id = null)
    {
        if (Session::has('user_info') and is_null($chat_id)) {
            return redirect('chat/list');
        } else if (!Session::has('user_info') or is_null($chat_id)) {
            return redirect('chat/login');
        } else {

            $ChatRoomList           = new ChatRoomList;
            $chat_room_info         = $ChatRoomList->get_chat_room_data()[0];
            $user_id                = json_decode($chat_room_info['user_id'], true);

            //不在此聊天室，直接跳往聊天室列表
            if (!in_array($this->login_user, $user_id)) {
                return redirect('chat/list');
            }

            $param = [
                "select"            => "name",
                "where" => [
                    "id"            => $chat_id
                ]
            ];
            $chat_room_data         = $ChatRoomList->get_chat_room_data_from_condition($param)[0];

            $Notice                 = new Notice;
            $notice_count           = $Notice->get_notice_count($this->login_user);
            $chat_member_list       = $this->chat_room_member_list($chat_id);

            $user_name              = Session::get('user_info')['name'];

            $return_data = [
                'chat_id'           => $chat_id,
                "user_id"           => $this->login_user,
                "user_name"         => $user_name,
                "room_name"         => $chat_room_data['name'],
                "notice_count"      => $notice_count,
                "chat_member_list"  => $chat_member_list
            ];

            return view('chat.home', $return_data);
        }
    }

    //聊天室對話儲存
    public function save_chatroom(Request $request)
    {
        $upload_status      = isset($request->all()['upload_status']) ? trim($request->all()['upload_status']) : null;
        $msg_data           = isset($request->all()['msg_data']) ? trim($request->all()['msg_data']) : null;
        $name_data          = isset($request->all()['name']) ? trim($request->all()['name']) : null;
        $chat_id            = isset($request->all()['chat_id']) ? trim($request->all()['chat_id']) : null;
        $msg_token          = isset($request->all()['msg_token']) ? str_replace("/", "|", trim($request->all()['msg_token'])) : null;

        $del_link           = ' <a href="/chat/del_msg/'.$chat_id.'/'.$msg_token.'" style="font-size:10pt;">刪除</a>';
        $save_msg           = ($upload_status != 1) ? '<div style="padding: 2px 5px 2px 10px; margin-left: 15px; background-color: #FFF8DC;font-size:12pt;">'.nl2br($msg_data).$del_link.' <span style="font-size:10pt;color:#aaa;">'.date('Y-m-d H:i:s').'</span></div>': $msg_data;
        $today_path         = $this->_json_path($chat_id);

        $param = [
            "today_path"    => $today_path,
            "name_data"     => $name_data,
            "save_msg"      => $save_msg,
        ];
        $this->_write_to_json_file($param);
    }

    //取聊天室對話
    public function get_chatroom(Request $request)
    {
        if (empty($request->all()['chat_id']) or is_null($request->all()['chat_id'])) {
            return false;
        }

        $chat_id                = $request->all()['chat_id'];
        $return_arr             = [];
        $init_json_cnt          = 2;    //預設倒數兩個json檔案
        $json_arr               = glob(public_path() . '/chat_save/'.$chat_id.'_*.json');

        //在檔案裡找最後一個、倒數第二個檔案路徑
        foreach ($json_arr as $key => $name) {
            $second_last_path   = $json_arr[(count($json_arr)-$init_json_cnt)];
            $lastest_path       = $name;
        }

        //倒數第二個檔案聊天記錄
        if (!empty($second_last_path)) {
            $today              = str_replace([public_path() . '/chat_save/'.$chat_id.'_', '.json'], ["", ""], $second_last_path);
            $source             = json_decode(file_get_contents($second_last_path), true);
            $date_bar           = $this->date_bar_start . date('Y-m-d', strtotime($today)) . $this->date_bar_end;
            $before_source      = array_merge([["data_bar" => $date_bar]], $source);
            if (is_array($before_source) and count($before_source) > 0) {
                $return_arr     = array_merge($return_arr, $before_source);
            }
        }

        //倒數第一個檔案聊天記錄
        if (!empty($lastest_path)) {
            $today              = str_replace([public_path() . '/chat_save/'.$chat_id.'_', '.json'], ["", ""], $lastest_path);
            $source             = json_decode(file_get_contents($lastest_path), true);
            $date_bar           = $this->date_bar_start . date('Y-m-d', strtotime($today)) . $this->date_bar_end;
            $before_source      = array_merge([["data_bar" => $date_bar]], $source);
            if (is_array($before_source) and count($before_source) > 0) {
                $return_arr     = array_merge($return_arr, $before_source);
            }
        }

        if ((count($json_arr)-$init_json_cnt) <= 0) {
            return ["room_msg" => $return_arr, "history_path" => "", "init_json_cnt" => 0];
        }

        //取預設歷史訊息的檔案名
        $history_msg_path   = $json_arr[(count($json_arr)-($init_json_cnt+1))];
        $replace_history_msg_path = str_replace([public_path() . '/chat_save/'.$chat_id.'_', '.json'], ["", ""], $history_msg_path);

        return ["room_msg" => $return_arr, "history_path" => $replace_history_msg_path, "init_json_cnt" => ($init_json_cnt+1)];
    }

    //取得歷史訊息
    public function get_chat_room_history_msg(Request $request)
    {

        $date           = isset($request->all()['date']) ? $request->all()['date'] : null;
        $init_json_cnt  = isset($request->all()['init_json_cnt']) ? $request->all()['init_json_cnt'] : 0;
        $chat_id        = isset($request->all()['chat_id']) ? $request->all()['chat_id'] : 0;
        if (is_null($date) or $init_json_cnt == 0 or $chat_id == 0) {
            return false;
        }

        $return_arr             = [];
        $json_arr               = glob(public_path() . '/chat_save/'.$chat_id.'_*.json');

        //取傳值進來的歷史訊息
        $json_path              = public_path() . '/chat_save/'.$chat_id.'_'.$date.'.json';
        if (!empty($json_path)) {
            $source             = json_decode(file_get_contents($json_path), true);
            $date_bar           = $this->date_bar_start . date('Y-m-d', strtotime($date)) . $this->date_bar_end;
            $before_source      = array_merge([["data_bar" => $date_bar]], $source);
            if (is_array($before_source) and count($before_source) > 0) {
                $return_arr     = array_merge($return_arr, $before_source);
            }
        }

        if ((count($json_arr)-$init_json_cnt) <= 0) {
            return ["room_msg" => $return_arr, "history_path" => "", "init_json_cnt" => 0];
        }

        //取下一筆的預設歷史訊息的檔案名
        $history_msg_path       = $json_arr[(count($json_arr)-($init_json_cnt+1))];
        $replace_history_msg_path = str_replace([public_path() . '/chat_save/'.$chat_id.'_', '.json'], ["", ""], $history_msg_path);

        return ["room_msg" => $return_arr, "history_path" => $replace_history_msg_path, "init_json_cnt" => ($init_json_cnt+1)];
    }

    //聊天室上傳圖片(透過 ajax傳送)
    public function upload_chat_pic(Request $request)
    {
        $file_data          = $request->file('userImage');
        $image_info         = getimagesize($request->file('userImage'));

        // 上傳路徑
        $destinationPath    = public_path() . '/chat_upload_pic';


        // 取得檔案擴展名(副檔名)
        $extension          = $file_data->getClientOriginalExtension();
        $originalname       = $file_data->getClientOriginalName();
        $size               = round($file_data->getSize()/1024, 2);
        $unit               = ($size > 0) ? 'KB' : 'Byte';
        if ($size > 1024) {
            $size           = round($size / 1024, 2);
            $unit           = 'MB';
        }


        // 檔案重新命名
        $fileName           = time() . '.' . $extension;

        // 複製移動到指定上傳路徑
        $return             = $file_data->move($destinationPath, $fileName);

        if (gettype($return) == "object") {
            return [
                "originalname"   => $originalname,
                "file_name"      => '/chat_upload_pic/' . $fileName,
                "file_size"      => $size.$unit,
                "image_height"   => $image_info[1],
                "extension"      => $extension,
                "msg_token"      => str_replace("/","|",base64_encode($this->login_user.'_'.$fileName.'_'.date("YmdHis").microtime(true)))
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
        header("Content-Disposition: attachment; filename=" . $file_name);
        readfile(env('BW_URL') . '/chat_upload_pic/' . $file_name);
        exit(0);
    }

    //刪除聊天室圖片
    public function del_pic_from_chatroom($chat_id = null, $msg_token = null)
    {
        if (is_null($msg_token) or is_null($chat_id)) {
            return false;
        }

        $param = [
            "chat_id"       => $chat_id,
            "msg_token"     => $msg_token,
            "del_kind"      => 'file'
        ];

        $return = $this->_del_msg_shared($param);

        //刪除訊息更新完成後，刪除檔案
        if ($return == true) {

            // 上傳路徑
            $destinationPath        = public_path() . '/chat_upload_pic/';
            $flag                   = false;

            $decode_token           = base64_decode(str_replace("|","/",(urldecode($msg_token))));
            $decode_data_to_explode = explode("_", $decode_token);
            $file_name              = $decode_data_to_explode[1];

            // 檔案已在資料夾內進入
            if (in_array($destinationPath.$file_name, glob($destinationPath.'*'))) {

                //找出陣列的 key
                $unset_key          = array_search($destinationPath.$file_name, glob($destinationPath.'*'));

                //檔案存在
                if (file_exists(glob($destinationPath.'*')[$unset_key])) {

                    //刪除實體檔案
                    unlink(glob($destinationPath . '*')[$unset_key]);
                    $flag = true;
                }
            }

            if ($flag == true) {
                return redirect('/chat/' . $chat_id);
            }

        }
    }

    //刪除聊天室單一訊息
    public function del_msg_from_chatroom($chat_id = null, $msg_token = null)
    {
        if (is_null($msg_token) or is_null($chat_id)) {
            return false;
        }

        $param = [
            "chat_id"       => $chat_id,
            "msg_token"     => $msg_token,
            "del_kind"      => 'text'
        ];

        $return = $this->_del_msg_shared($param);

        if ($return == true) {
            return redirect('/chat/' . $chat_id);
        }
    }

    //刪除訊息、檔案共用
    private function _del_msg_shared($param = [])
    {
        $decode_token           = base64_decode(str_replace("|","/",($param['msg_token'])));
        $decode_data_to_explode = explode("_", $decode_token);
        $user_id                = $decode_data_to_explode[0];
        $datetime               = mb_substr($decode_data_to_explode[2], 0, 8);

        $return_msg = ($param['del_kind'] == 'text') ? '此訊息已被刪除' : '此檔案已被刪除';

        if ($datetime != date("Ymd")) {
            session()->flash('error_msg','只能刪除'.date("Y-m-d").'的檔案');
            return redirect('/chat/' . $param['chat_id']);
        }

        if ($this->login_user != $user_id) {
            session()->flash('error_msg','非本人不能刪除');
            return redirect('/chat/' . $param['chat_id']);
        }

        //取今天聊天訊息
        $source = json_decode(file_get_contents($this->_json_path($param['chat_id'])['today']), true);

        //取會員名稱
        $user_data = User::select('name')->where('id', $user_id)->get()->toArray()[0];

        // 聊天訊息陣列
        foreach ($source as $source_key => &$user_msg) {

            //只找此會員訊息陣列
            if (array_key_exists($user_data['name'], $user_msg) == true) {

                //將此會員的訊息做迴圈列出
                foreach ($user_msg[$user_data['name']] as $msg_key => $msg_data) {

                    //去檢查訊息裡的 $msg_token (唯一性)
                    if (strpos($msg_data, $param['msg_token']) != false) {
                        $user_msg[$user_data['name']][$msg_key] = '<div style="padding: 0px 0px 0px 5px;margin-left: 15px;font-size:10pt;">'.$return_msg.'</div>';
                        //unset($user_msg[$user_data['name']][$msg_key]);
                    }
                }
            }
        }

        //覆寫檔案
        $file = fopen($this->_json_path($param['chat_id'])['today'], "w");
        fwrite($file, json_encode($source));
        fclose($file);

        return true;
    }

    //邀請會員加入聊天室
    public function invite_member(Request $request, $chat_id = null)
    {
        $user_data = [];
        $ChatRoomList   = new ChatRoomList;
        $User           = new User;

        //聊天室與會員的資料
        $chat_room_all  = $ChatRoomList->get_chat_room_all()[0];

        //列出可邀請會員，排除自己
        $user_data      = $User->get_user_data_notIn_loginuser($this->login_user);

        if ($request->method() == 'GET') {

            //邀請通知數字
            $Notice         = new Notice;
            $notice_count   = $Notice->get_notice_count($this->login_user);

            $return_arr = [
                "user_list_result"      => $user_data,
                "chat_id"               => $chat_id,
                "notice_count"          => $notice_count,
                "chat_room_all"         => $chat_room_all
            ];

            return view('chat.invite_member', $return_arr);

        } elseif ($request->method() == 'POST') {

            $invite_user_ids    = isset($request->all()['member']) ? $request->all()['member'] : null;
            $chat_id            = isset($request->all()['chat_id']) ? $request->all()['chat_id'] : null;

            if ((!is_array($invite_user_ids) and count($invite_user_ids) <= 0) or is_null($chat_id)) {
                return false;
            }

            //檢查是否達聊天室上限
            $judge_limit_result = $this->_judge_chat_room_limit($chat_id, $invite_user_ids);

            if ($judge_limit_result == false) {
                session()->flash('error_msg', '邀請人數已達到聊天室限制人數，無法邀請');
                return redirect('/chat/invite/'.$chat_id);
            }

            //檢查是否邀請過
            $judge_invite_result = $this->_judge_invite_already($chat_id, $invite_user_ids);
            if (isset($judge_invite_result['error_msg']) and $judge_invite_result['error_msg'] != '' and isset($judge_invite_result['notice_data']) and is_array($judge_invite_result['notice_data'])) {
                session()->flash('error_msg', $judge_invite_result['error_msg']);
                $return_arr = [
                    "user_list_result"  => $user_data,
                    "chat_id"           => $chat_id,
                    "notice_count"      => count($judge_invite_result['notice_data']),
                    "chat_room_all"     => $chat_room_all
                ];
                return view('chat.invite_member', $return_arr);
            }


            if (is_array($invite_user_ids) and count($invite_user_ids) > 0) {

                $condition_param = [
                    "select" => "",
                    "where" => [
                        "id" => $chat_id
                    ]
                ];
                $chat_room_data = $ChatRoomList->get_chat_room_data_from_condition($condition_param)[0];

                foreach ($invite_user_ids as $user_key => $user_id) {
                    $invite_param = [
                        "name"          => $chat_room_data['name'],
                        "description"   => $chat_room_data['description'],
                        "user_id"       => $user_id,
                        "login_user"    => $this->login_user,
                        "chat_id"       => $chat_id,
                    ];
                    $notice = new Notice;
                    $save_result[$user_key] = $notice->insert_notice($notice, $invite_param);
                }

                if (count($save_result) > 0) {
                    session()->flash('error_msg', '已經邀請成功');
                    return redirect('/chat/list');
                }
            }
        }
    }

    //判斷聊天室是否超過人數
    private function _judge_chat_room_limit($chat_id = null, $user_ids = [])
    {
        if (isset($this->login_user)) {
            $user_ids[] = $this->login_user;
        }

        $ChatRoomList = new ChatRoomList;
        return $ChatRoomList->get_chat_room_limit($chat_id, $user_ids);
    }

    //判斷是否邀請過會員
    private function _judge_invite_already($chat_id = null, $invite_user_ids = [])
    {
        $is_member          = $error_msg = '';
        $member_ids         = $already_invite_user = [];
        $change_name        = $this->_user_data_array();

        //去通知裡撈邀請資料出來
        $notice             = new Notice;
        $ChatRoomMember     = new ChatRoomMember;

        $param = [
            "select"        => "to",
            "where" => [
                "from"      => $this->login_user,
                "chat_room" => $chat_id,
                "status"    => 1
            ]
        ];
        //取通知資料
        $notice_data        = $notice->get_notice_data_condition($param);

        $get_member_param = [
            "select"        => "user_id",
            "where" => [
                "chat_room_id" => $chat_id,
            ]
        ];
        //取聊天室會員資料
        $get_member_data    = $ChatRoomMember->get_chat_room_member_data($get_member_param)[0];
        $member_ids         = json_decode($get_member_data['user_id']);
        //邀請陣列與聊天室會員陣列做交集
        $intersect_result   = array_intersect($invite_user_ids, $member_ids);

        //交集陣列有值，表示已存在聊天室
        if (count($intersect_result) > 0) {
            foreach ($intersect_result as $key => $user_id) {
                $is_member .= $change_name[$user_id] . '、';
            }

            //已存在就存在 $error_msg
            if (!empty($is_member)) {
                $error_msg .= '<b>'.rtrim($is_member, '、').'</b> 已在此聊天室<BR>';
            }
        }

        if (is_array($notice_data) and count($notice_data) > 0) {
            $notice_user_ids = $notice_data[0];

            //從邀請表單做迴圈檢查是否已存在通知的db裡
            foreach ($invite_user_ids as $invite_key => $invite_user_id) {
                if (in_array($invite_user_id, $notice_user_ids)) {
                    $already_invite_user[] = $invite_user_id;
                }
            }

            //若有邀請過
            if (count($already_invite_user) > 0) {
                $user_name = '';
                foreach ($already_invite_user as $key => $user) {
                    $user_name .= $change_name[$user] . '、';
                }
                $error_msg = '已經邀請過 <b>' . rtrim($user_name, '、') . '</b>';
            }
        }

        return ["notice_data" => $notice_data, "error_msg" => $error_msg];
    }

    //邀請列表
    public function invite_list()
    {
        if (!Session::has('user_info')) {
            session()->flash('nologin_msg','未登入無法列出聊天室邀請');
            return view('chat.invite_list');
        }

        $user_data                  = $this->_user_data_array();

        $param = [
            "login_user"            => $this->login_user,
            "status"                => 1
        ];
        $notice                     = new Notice;
        $notice_data                = $notice->get_combind_notice_data($param);
        foreach ($notice_data as $key => &$row) {
            $row['from']            = $user_data[$row['from']];
            $row['create_user_id']  = $user_data[$row['create_user_id']];
        }

        $return_arr = [
            "invite_list_data"      => $notice_data,
            "notice_count"          => count($notice_data)
        ];

        //echo "<pre>";print_r($notice_data);exit;

        return view('chat.invite_list', $return_arr);
    }

    //組成新陣列，id 轉換 name
    private function _user_data_array()
    {
        $return_arr = [];
        $user_data = User::all()->toArray();
        foreach ($user_data as $key => $row) {
            $return_arr[$row['id']] = $row['name'];
        }
        return $return_arr;
    }

    //處理同意/拒絕 邀請結果
    public function judge_invite_result(Request $request)
    {
        $accept         = isset($request->all()['accept']) ? $request->all()['accept'] : null;
        $reject         = isset($request->all()['reject']) ? $request->all()['reject'] : null;
        $notice_id      = isset($request->all()['notice_id']) ? $request->all()['notice_id'] : null;
        $chat_room      = isset($request->all()['chat_room']) ? $request->all()['chat_room'] : null;

        if ((is_null($accept) or is_null($reject)) and is_null($notice_id) and is_null($chat_room)) {
            return false;
        }

        if (!empty($accept)) {
            $notice = new Notice;
            $update_notice = $notice->upd_notice_status($notice_id);

            if ($update_notice == 1) {
                $param = [
                    "select" => "user_id",
                    "where" => [
                        "chat_room_id" => $chat_room
                    ],
                    "update" => [
                        'login_user' => $this->login_user
                    ],
                    "flag" => "add_member"
                ];
                $ChatRoomMember = new ChatRoomMember;
                $update_room_member         = $ChatRoomMember->get_chat_room_member_data($param);

                if ($update_room_member == 1) {
                    $date_bar               = "<div style='background-color: #eaeaea;height: 20px;line-height: 20px;'><div style='font-size:10pt;color:#aaa;margin-left: 45%;'>".date('Y-m-d H:i:s').' '.$this->_user_data_array()[$this->login_user]."加入聊天室</div></div>";

                    $param = [
                        "today_path"        => $this->_json_path($chat_room),
                        "name_data"         => '系統訊息',
                        "save_msg"          => $date_bar,
                    ];
                    $this->_write_to_json_file($param);

                    return redirect('chat/list');
                }
            }
        }else{
            return redirect('chat/invite_list');
        }
    }

    //回傳不同聊天室的檔案名稱
    private function _json_path($chat_id = 0)
    {
        return [
            'yesterday'     => public_path() . '/chat_save/' . $chat_id.'_'.date("Ymd", strtotime(date("Ymd") . "-1 days")) . ".json",
            'today'         => public_path() . '/chat_save/' . $chat_id.'_'.date("Ymd") . ".json"
        ];
    }

    //共用寫入json檔案
    private function _write_to_json_file($param = [])
    {
        $insert_data = $origin_data = [];
        //沒有聊天訊息檔案 或 檔案沒訊息
        if (file_exists($param['today_path']['today']) == false or empty(file_get_contents($param['today_path']['today'])) == true)
        {
            $insert_data[] = [
                $param['name_data'] => [$param['save_msg']]
            ];

        }else {

            //取出檔案並解碼
            $origin_data = json_decode(file_get_contents($param['today_path']['today']), true);

            //抓陣列的最後一筆資料的 key值是否為新訊息為同一人
            //同一人就新增在同一陣列裡
            if (array_key_exists($param['name_data'], $origin_data[(count($origin_data)-1)])) {

                //最後一個陣列走迴圈
                foreach ($origin_data[(count($origin_data)-1)] as $user => &$user_msg) {
                    if ($user == $param['name_data']) {
                        $user_msg[] = $param['save_msg'];
                    }
                }
            }else{

                //不同人就新增一筆陣列
                $origin_data[] = [
                    $param['name_data'] => [$param['save_msg']]
                ];
            }
            $insert_data = $origin_data;
        }

        $file = fopen($param['today_path']['today'], "w+"); //開啟檔案
        fwrite($file, json_encode($insert_data));
        fclose($file);
    }

    //聊天室會員列表
    public function chat_room_member_list($chat_id = null)
    {
        $param = [
            "select" => "user_id",
            "where" => [
                "chat_room_id" => $chat_id
            ]
        ];
        $ChatRoomMember = new ChatRoomMember;
        $chat_room_member_data  = $ChatRoomMember->get_chat_room_member_data($param)[0];

        $chat_room_member_data_decode   = json_decode($chat_room_member_data['user_id'], true);
        foreach ($chat_room_member_data_decode as $key => $user_id) {
            $return_data[] = [
                "user_id"       => $user_id,
                "user_name"     => $this->_user_data_array()[$user_id],
            ];
        }
        return $return_data;
        //echo "<pre>";print_r($return_data);exit;
    }

    //刪除聊天室
    public function del_chat_room($chat_id = null)
    {
        if (is_null($chat_id)) {
            return false;
        }

        $param = [
            "select"        => "name",
            "where" => [
                "id"        => $chat_id
            ],
            "update" => [
                'status'    => 0
            ]
        ];
        $ChatRoomList       = new ChatRoomList;
        $update_result      = $ChatRoomList->get_chat_room_data_from_condition($param);

        if ($update_result == 1) {
            session()->flash('error_msg','刪除聊天室完成');
            return redirect('chat/list');
        }
    }
}
