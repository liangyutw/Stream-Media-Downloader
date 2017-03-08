<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use DB;

class ChatRoomMember extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'chat_room_member';

    public function create_chat_room_member($insert_arr = [])
    {
//        $ChatRoomMember = new ChatRoomMember;
//        $ChatRoomMember->chat_room_id = $insert_arr['chat_room_id'];
//        $ChatRoomMember->user_id = $insert_arr['user_id'];
//        $ChatRoomMember->set_time = $insert_arr['set_time'];
//        return $ChatRoomMember->save();

        return DB::table('chat_room_member')->insert(
            [
                'chat_room_id' => $insert_arr['chat_room_id'],
                'user_id' => $insert_arr['user_id'],
                'set_time' => $insert_arr['set_time']
            ]
        );
    }

    public function get_chat_room_member_data($param = [])
    {

        if (!is_array($param)) {
            return false;
        }

        $select_condition = $param['select'];
        $where_condition = $param['where'];

        if (!is_array($where_condition) or (isset($param['update']) and !is_array($param['update'])) ) {
            return false;
        }

        $update_condition = isset($param['update']) ? $param['update'] : null;

        //DB::enableQueryLog();
        $data = DB::table('chat_room_member');
        if ($select_condition != "") {
            $data->select($select_condition);
        }

        foreach ($where_condition as $column_name => $value) {
            $data->where($column_name, $value);
        }

        if (is_array($update_condition) and count($update_condition) > 0) {

            if (isset($param['flag']) and $param['flag'] == 'add_member') {
                $update_condition = $this->_add_member_to_room($data, $param);
            }

            return $data->where($column_name, $value)->update($update_condition);
        }else{
            return $data->get();
        }


    }

    private function _add_member_to_room($data, $param)
    {
        $get_chat_room_member_data  = $data->get()[0];
        $decode_member              = json_decode($get_chat_room_member_data['user_id'], true);
        $decode_member[]            = $param['update']['login_user'];
        return $update_arr          = ['user_id' => json_encode($decode_member)];
    }
}
