<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class IndexMessage extends Model
{
    protected $fillable = ['id','sender_user_id','recipient_user_id','index_message_id','z_index'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
