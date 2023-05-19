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

    public function worked_time() : HasMany
    {
        return $this->hasMany(Worked_time::class);
    }

    public function doctor_clinic() : HasMany
    {
        return $this->hasMany(Doc_clinic::class);
    }

    public function doctor_apply() : HasMany
    {
        return $this->hasMany(Doc_apply::class);
    }

    public function speciality_doctor() : HasMany
    {
        return $this->hasMany(Spec_doc::class);
    }
}
