<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Auth;

class CustomerLimitApproval implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_id;
    public $customer_id;
    public $requested_user;
    public $customer_name;
    public $created_at;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user_id, $customer_id, $customer_name)
    {
        $this->user_id = $user_id;
        $this->customer_id = $customer_id;
        $this->requested_user = Auth::user()->id;
        $this->customer_name = $customer_name;
        $this->created_at = date('Y-m-d H:i:s');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {   
        return new Channel('customer-limit-approval-channel.'. $this->user_id);
    }
}
