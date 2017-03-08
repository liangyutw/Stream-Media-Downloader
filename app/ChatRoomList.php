<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use DB;

class ChatRoomList extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'chat_room_list';

    /**
     * 取得聊天室與會員的資料
     *
     * @return mixed
     */
    public function get_chat_room_data()
    {
        return DB::table('chat_room_list')->where('status', 1)->join('chat_room_member', 'chat_room_list.id', '=', 'chat_room_member.chat_room_id')->get();
    }

    /**
     * 使用條件關係取聊天室資料
     *
     * @param array $param
     * @return bool
     */
    public function get_chat_room_data_from_condition($param = [])
    {
        if (!is_array($param) and count($param) <= 0) {
            return false;
        }

        $select_condition   = $param['select']; //純字串
        $where_condition    = $param['where'];  //陣列
        $update_condition   = isset($param['update']) ? $param['update'] : null;  //陣列

        if (!is_array($where_condition) or (isset($update_condition) and !is_array($update_condition)) ) {
            return false;
        }

        $ChatRoomList       = new ChatRoomList;

        if ($select_condition != "") {
            $data = $ChatRoomList->select($select_condition);
        }else{
            $data = $ChatRoomList;
        }

        foreach ($where_condition as $column_name => $value) {
            $data->where($column_name, $value);
        }


        if (is_array($update_condition) and count($update_condition) > 0) {
            return $data->update($update_condition);
        }else{
            return $data->get()->toArray();
        }

    }

    /**
     * 建立聊天室
     *
     * @param array $insert_arr
     * @return mixed
     */
    public function create_chat_room($insert_arr = [])
    {
        $ChatRoomList = new ChatRoomList;
        $ChatRoomList->name = $insert_arr['name'];
        $ChatRoomList->description = $insert_arr['description'];
        $ChatRoomList->limit = $insert_arr['limit'];
        $ChatRoomList->status = $insert_arr['status'];
        $ChatRoomList->create_user_id = $insert_arr['create_user_id'];
        $ChatRoomList->save();

        return $ChatRoomList->id;
    }

    /**
     * 所有聊天室
     *
     * @return mixed
     */
    public function get_chat_room_all()
    {
        return ChatRoomList::where("status", 1)->get()->toArray();
    }

    /**
     * 聊天室限制人數
     *
     * @param $chat_id
     * @param $user_ids
     * @return bool
     */
    public function get_chat_room_limit($chat_id, $user_ids)
    {
        $flag = true;

        $get_limit_info = DB::table('chat_room_list')
            ->select('chat_room_list.limit as member_limit','chat_room_member.user_id')
            ->join('chat_room_member', 'chat_room_member.chat_room_id', '=', 'chat_room_list.id')
            ->where('chat_room_list.id', $chat_id)
            ->get();
        //echo "<pre>";print_r($get_limit_info);exit;
        foreach ($get_limit_info as $limit_key => $limit_info) {

            if (count($user_ids) > $limit_info['member_limit']) {
                $flag = false;
            }
        }

        return $flag;
    }

    /**
     * 取的會員可建立聊天室的數量
     *
     * @return Response
     */
    public function get_user_create_room_limit($user_id = null)
    {
        if (is_null($user_id)) {
            return false;
        }

        $user_create_room_count = DB::table('chat_room_list')->where('create_user_id', $user_id)->count();
        $user_room_limit = DB::table('users')->select('chat_room_limit')->where('id', $user_id)->get()[0];

        if ($user_create_room_count < $user_room_limit['chat_room_limit']) {
            return true;
        }else{
            return false;
        }
    }
}
