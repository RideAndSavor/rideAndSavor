<?php
 
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('online-users', function ($user) {
    // Authorize user to join the channel
    if ($user) {
        // Here you could log the user's status if you wish to store it
        // For example, store the user's online status in the database or session.
        return ['id' => $user->id, 'name' => $user->name];  // Send additional user data to the frontend
    }

    // If user is not authenticated, return false to prevent joining
    return false;
});

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
