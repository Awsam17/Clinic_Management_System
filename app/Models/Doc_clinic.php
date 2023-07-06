<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Doc_clinic extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function doctor() : BelongsTo
    {
        return $this->BelongsTo(Doctor::class,'doctor_id');
    }

    public function clinic() : BelongsTo
    {
        return $this->BelongsTo(Clinic::class,'clinic_id');
    }
}
