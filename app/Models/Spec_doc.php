<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Spec_doc extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function doctor() : BelongsTo
    {
        return $this->BelongsTo(Doctor::class,'doctor_id');
    }

    public function specialty() : BelongsTo
    {
        return $this->BelongsTo(Specialty::class,'specialty_id');
    }
}
