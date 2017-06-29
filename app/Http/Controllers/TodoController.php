<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// use App\User;
// use App\MailContentModel;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Redis;
// use App\Events\PushNotification;
// use App\Events\RegisterMail;
// use Event;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('todolist.index');
    }

}
