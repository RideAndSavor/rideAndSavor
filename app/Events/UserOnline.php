<?php

namespace App\Events; 
use Illuminate\Support\Facades\Cache; 
use Illuminate\Broadcasting\PresenceChannel; 
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserOnline implements ShouldBroadcast
{
    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function broadcastOn()
    {
        return new PresenceChannel('online-users');
    }

    public function broadcastWith()
    {
        // Cache the user as online
        Cache::put('user-online-' . $this->user->id, true, now()->addMinutes(5));  // Expire after 5 mins
        return [
            'name' => $this->user->name,
            'id' => $this->user->id,
        ];
    }
}

