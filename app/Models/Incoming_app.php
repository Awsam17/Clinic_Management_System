<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Incoming_app extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class , 'user_id');

    }

    public function clinic() : BelongsTo
    {
        return $this->belongsTo(Clinic::class , 'clinic_id');

    }
}
