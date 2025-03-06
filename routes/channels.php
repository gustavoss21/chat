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

Broadcast::channel('user.conect.{user_id}', function (User $user, int $user_id) {
    return $user->id === $user_id;
});

Broadcast::channel('main.user.conect', function (User $user) {
    return (bool) $user->is_super_user === true;
});

Broadcast::channel('call.closed.{user_id}', function (User $user, int $user_id) {
    return $user_id === $user->id;
});

Broadcast::channel('user.confirmation.{user_id}', function (User $user, int $user_id) {
    return $user->is_super_user && $user_id === $user->id;
});