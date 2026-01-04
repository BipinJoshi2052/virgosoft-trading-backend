<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderMatched
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public $buyOrder;
    public $sellOrder;
    public $updatedData;  // Payload with updated balances, assets, orders
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Order $buyOrder, Order $sellOrder, $updatedData)
    {
        $this->buyOrder = $buyOrder;
        $this->sellOrder = $sellOrder;
        $this->updatedData = $updatedData;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // Broadcast to both users' private channels
        return [
            new PrivateChannel('user.' . $this->buyOrder->user_id),
            new PrivateChannel('user.' . $this->sellOrder->user_id),
        ];
        // return new PrivateChannel('channel-name');
    }
    public function broadcastAs()
    {
        return 'order.matched';  // Event name on frontend
    }

    public function broadcastWith()
    {
        // Customize payload per user if needed, but for simplicity, send full data (frontend can filter)
        return [
            'buyOrder' => $this->buyOrder->toArray(),
            'sellOrder' => $this->sellOrder->toArray(),
            'updatedData' => $this->updatedData,
        ];
    }
}
