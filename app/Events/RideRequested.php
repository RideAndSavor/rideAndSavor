<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RideRequested
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $current_location;
    public $destination;
    public $rider_id;
    public $driver_id;
    /**
     * Create a new event instance.
     */
    public function __construct($driver_id, $current_location, $destination, $rider_id)
    {
        $this->current_location = $current_location; // Should be an array or object with latitude and longitude
        $this->destination = $destination;
        $this->rider_id = $rider_id;
        $this->driver_id = $driver_id;
    }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return new PrivateChannel('driver.' . $this->driver_id);
    }

    /**
     * Customize the broadcast data.
     */
    public function broadcastWith()
    {
        return [
            'current_location' => $this->current_location,
            'destination' => $this->destination, 
            'rider_id' => $this->rider_id, 
        ];
    }

    // public function broadcastOn(): array
    // {
    //     return [
    //         new PrivateChannel('channel-name'),
    //     ];
    // }
}
