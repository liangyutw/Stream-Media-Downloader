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
        // echo "<pre>";var_dump($request);exit;

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
        if (is_null($user)) {
            return "無此帳號";
        }
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

}
