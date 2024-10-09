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
    /**
     * Create a new event instance.
     */
    public function __construct($current_location, $destination)
    {
        $this->current_location = $current_location; // Should be an array or object with latitude and longitude
        $this->destination = $destination;
    }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return new PrivateChannel('drivers-nearby');
    }

    /**
     * Customize the broadcast data.
     */
    public function broadcastWith()
    {
        return [
            'current_location' => $this->current_location,
            'destination' => $this->destination, 
        ];
    }

    // public function broadcastOn(): array
    // {
    //     return [
    //         new PrivateChannel('channel-name'),
    //     ];
    // }
}
