<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Medical_report extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function patient() : BelongsTo
    {
        return $this->BelongsTo(Patient::class,'patient_id');
    }

    public function clinic() : BelongsTo
    {
        return $this->BelongsTo(Clinic::class,'clinic_id');
    }
}
