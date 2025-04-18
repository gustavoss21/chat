<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model
{
    const STATUS_VIEWED = 2;
    protected $fillable = ['sender_user_id','recipient_user_id','message','uploud_message','index_message_id','status'];
}
