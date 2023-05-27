<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function user() : BelongsTo
    {
        return $this->BelongsTo(User::class,'user_id');
    }

    public function worked_times() : HasMany
    {
        return $this->hasMany(Worked_time::class);
    }

    public function doctor_clinics() : HasMany
    {
        return $this->hasMany(Doc_clinic::class);
    }

    public function doctor_applies() : HasMany
    {
        return $this->hasMany(Doc_apply::class);
    }

    public function speciality_doctors() : HasMany
    {
        return $this->hasMany(Spec_doc::class);
    }

    public function appointments() : HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
