<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;
use App\Models\Message;
use App\Events\SendStatusUser;
use Clue\Redis\Protocol\Model\Request;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{chat_user_id}', function (User $user, int $chat_user_id) {
    return $user->id === $chat_user_id;
});

Broadcast::channel('message.viewed.user.{user_id}', function (User $user, int $user_id) {
    return $user->id === $user_id;
});

Broadcast::channel('main.user.conect', function (User $user) {
    return (bool) $user->is_super_user === true;
});

Broadcast::channel('user.confirmation.{user_id}', function (User $user, int $user_id) {
    return $user->is_super_user && $user_id === $user->id;
});

Broadcast::channel('user.status.transmition',
    function (User $user) {
            return ['id' => $user->id, 'name' => $user->name];
    }
);

Broadcast::channel('contact.us.{user_id}',
    function (User $user,int $user_id) {
        if($user->id === $user_id || $user->is_super_user)
            return ['id' => $user->id, 'name' => $user->name];
    }
);

Broadcast::channel('user.access.contact.us',
    function (User $user) {
            return ['id' => $user->id, 'name' => $user->name];
    }
);