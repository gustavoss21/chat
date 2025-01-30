<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
 
use App\Models\IndexMessage;


class Message extends Model
{
    protected $fillable = ['sender_user_id','recipient_user_id','message','uploud_message','index_message_id','z_index'];
}
