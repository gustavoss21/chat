<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\IndexMessage;

class Message extends Model
{
    protected $fillable = ['message','uploud_message','index_message_id'];

    public function indexMessage(): HasMany
    {
        return $this->hasMany(IndexMessage::class);
    }
}
