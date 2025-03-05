<?php

namespace App\Events;

use Illuminate\Support\Facades\Log;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class trackingDriverCurrentLocation implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $location;

    public function __construct($location)
    {
        // dd($location);
        $this->location = $location;
        // dd($this->location['driver_id']);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // dd('ok');
        return [
            new Channel('tracking-current-location'. $this->location['driver_id']),
        ];
    }

     /**
     * Customize the broadcast data.
     */
    public function broadcastWith()
    {
        // dd('ok');
        return [
            'driver_id' => $this->location['driver_id'],
            'lat' => $this->location['current_location']['lat'],
            'long' => $this->location['current_location']['long'],
        ];
    }
}
