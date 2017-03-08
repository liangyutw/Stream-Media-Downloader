<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use DB;

class Notice extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'notice';

    public function get_notice_count($login_user = null)
    {
        return Notice::where('to', $login_user)->where('status', 1)->count();
    }

    public function insert_notice($notice, $param = [])
    {
        if (!is_array($param)) {
            return false;
        }

        if (!is_object($notice)) {
            $notice = new Notice;
        }

        $notice->subject        = $param['name'];
        $notice->content        = $param['description'];
        $notice->to             = $param['user_id'];
        $notice->from           = $param['login_user'];
        $notice->set_time       = date('Y-m-d H:i:s');
        $notice->chat_room      = $param['chat_id'];
        $notice->status         = 1;
        return $notice->save();
    }

    public function get_notice_data_condition($param = [])
    {
        $select_condition = $param['select'];
        $where_condition = $param['where'];

        if (!is_array($where_condition)) {
            return false;
        }

        $notice = new Notice;
        if ($select_condition != "") {
            $data = $notice->select($select_condition);
        }

        if (!is_object($data)) {
            $data = $notice;
        }

        foreach ($where_condition as $column_name => $value) {
            $data->where($column_name, $value);
        }

        return $data->get()->toArray();
    }

    public function get_combind_notice_data($param = [])
    {
        if (!is_array($param)) {
            return false;
        }

        $notice = new Notice;
        return $notice
            ->select(
                "chat_room_list.name as chat_room_name",
                "chat_room_list.create_user_id",
                "notice.*"
            )
            ->join('chat_room_list', 'chat_room_list.id', '=', 'notice.chat_room')
            ->where('notice.to', $param['login_user'])
            ->where('notice.status', $param['status'])->get();
    }

    public function upd_notice_status($notice_id = null)
    {
        return DB::table('notice')->where('id', $notice_id)->update(['status' => 0]);
    }
}
