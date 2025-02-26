<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;
use App\Models\Message;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{chat_user_id}', function (User $user, int $chat_user_id) {
    return $user->id === $chat_user_id;
});

Broadcast::channel('message.viewed.user.{user_id}', function (User $user, int $user_id) {
    return $user->id === $user_id;
});

