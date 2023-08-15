<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function chat() : BelongsTo
    {
        return $this->BelongsTo(Chat::class,'chat_id');
    }
}
