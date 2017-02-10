<?php

namespace App\Events;

use App\Events\Event;
use App\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RegisterMail extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $register_subject;
    public $register_message;
    public $register_email;
    public $register_name;
    public $token;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user_id, $mail_content)
    {
        $user_data = User::find($user_id);
        $this->register_email = $user_data->email;
        $this->register_name = $user_data->name;
        $this->register_subject = $mail_content['subject'];
        $this->register_message = $mail_content['content'];

        $this->token = sha1($user_data->id . '|' . $user_data->email);
//        echo "<pre>";print_r($user_data);
//        print_r($mail_content);
//        exit;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['register_mail'];
    }
}
