<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('online-users', function ($user) {
    if (!$user) {
        Log::error('Broadcasting failed: user not authenticated');
    }
    return [
        // 'id' => $user->id,
        'name' => "Test",
    ];
});

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
