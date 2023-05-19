<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Clinic extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function incoming_app() : HasMany
    {
        return $this->hasMany(Incoming_app::class);
    }

    public function booked_app() : HasMany
    {
        return $this->hasMany(Booked_app::class);
    }

    public function archive() : HasMany
    {
        return $this->hasMany(Archive::class);
    }

    public function medical_report() : HasMany
    {
        return $this->hasMany(Medical_report::class);
    }

    public function address() : BelongsTo
    {
        return $this->BelongsTo(Address::class,'address_id');
    }

    public function report() : HasMany
    {
        return $this->HasMany(Report::class);
    }

    public function secretary() : HasMany
    {
        return $this->hasMany(Secretary::class);
    }

    public function doctor_apply() : HasMany
    {
        return $this->hasMany(Doc_apply::class);
    }

    public function doctor_clinic() : HasMany
    {
        return $this->hasMany(Doc_clinic::class);
    }

    public function worked_time() : HasMany
    {
        return $this->hasMany(Worked_time::class);
    }
}
