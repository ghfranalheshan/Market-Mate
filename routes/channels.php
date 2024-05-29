<?php

use App\Models\Message;
use App\Models\Message_receipent;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


Broadcast::channel('chat.{message->sendable_id}_{message_receipent->receivable_id}',
    function ($user ,Message $message , Message_receipent $message_receipent) {

        $recipient=$message_receipent->receivable();
        $sender=$message->sendable();

        return [
            'message_content' =>$this->message->message,
            'sender' => $sender,
            'recipient'=> $recipient,
        ];

    });
