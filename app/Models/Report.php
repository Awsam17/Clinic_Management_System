<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function clinic() : BelongsTo
    {
        return $this->BelongsTo(Clinic::class,'clinic_id');
    }

    public function user() : BelongsTo
    {
        return $this->BelongsTo(User::class,'user_id');
    }
}
