<?php

namespace App\Events;

use App\Account;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CanDanMessage implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $account;

    public function __construct(Account $account, $message)
    {
        $this->account = $account;
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('can-dan.' . $this->account->user_id);
    }

    public function broadcastAs()
    {
        return 'can-dan-event';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'id' => $this->account->id,
            'account_id' => $this->account->account_id,
            'message' => $this->message,
        ];
    }
}
