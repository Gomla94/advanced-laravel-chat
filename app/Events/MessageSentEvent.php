<?php

namespace App\Events;

use App\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;


class MessageSentEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $message;
    public $authUser;
    public $toUser;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Message $message, $authUser, $toUser)
    {
        $this->message = $message;
        $this->authUser = $authUser;
        $this->toUser = $toUser;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // return new PrivateChannel('joinChat-channel');
        return new PrivateChannel("messages." . $this->authUser . "." . $this->toUser);
    }

    public function broadcastWith()
    {
        return ['message' => $this->message];
    }
}
