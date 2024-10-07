<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class trackingDriverCurrentLocation implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $location;

    public function __construct($location)
    {
        $this->location = $location;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('tracking-current-location'. $this->location['driver_id']),
        ];
    }

     /**
     * Customize the broadcast data.
     */
    public function broadcastWith()
    {
        return [
            'driver_id' => $this->location['driver_id'],
            'lat' => $this->location['current_location']['lat'],
            'long' => $this->location['current_location']['long'],
        ];
    }
}
