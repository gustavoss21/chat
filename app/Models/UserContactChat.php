<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserContactChat extends Model
{
    protected $table = 'users_contact_chat';
    protected $fillable = ['id','user_id'];
}
