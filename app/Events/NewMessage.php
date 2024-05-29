<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;
use App\Models\Message_receipent;


class NewMessage implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;
    public Message_receipent $message_receipent;
    /**
     * Create a new event instance.
     *
     * @return void
   */
    public function __construct(Message $message , Message_receipent $message_receipent)
    {
        $this->message = $message ;
        $this->message_receipent=$message_receipent;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel("chat.{$this->message->sendable_id}.{$this->message_receipent->receivable_id}");
    }

    public function broadcastWith(){

        $recipient=$this->message_receipent->receivable();
        $sender=$this->message->sendable();
//
//        $recipient = $this->message_receipent->receivable;
//        $sender = $this->message->sendable;

        return [
            'message_content' =>$this->message->message,
            'sender' => $sender,
            'recipient'=> $recipient,
        ];
    }
}
