<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CustomerLimitApproved implements ShouldBroadcast 
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $customer_id;
    public $requested_user;
    public $customer_name;
    public $limit;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($customer_id, $requested_user, $customer_name, $limit )
    {
        $this->customer_id = $customer_id;
        $this->requested_user =  $requested_user;
        $this->customer_name = $customer_name;
        $this->limit = $limit;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('customer-limit-approved.'.$this->requested_user);
    }
}
