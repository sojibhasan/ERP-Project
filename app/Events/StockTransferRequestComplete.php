<?php

namespace App\Events;

use App\Product;
use App\StockTransferRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class StockTransferRequestComplete implements ShouldBroadcast 
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_id; // request location user id
    public $transfer_request; // request
    public $product; // request id
    public $location_id; // request location id
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user_id, StockTransferRequest $transfer_request, Product $product, $location_id)
    {
        $this->user_id = $user_id;
        $this->transfer_request = $transfer_request;
        $this->location_id = $location_id;
        $this->product = $product;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('stock-transfer-request-complete.'.$this->user_id);
    }
}
