<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    protected $enum = [
        'status' =>['archived', 'pending', 'booked'],
    ];

    protected $fillable = [
        'user_id',
        'clinic_id',
        'doctor_id',
        'full_name',
        'age',
        'gender',
        'description',
        'hide_user',
        'date',
        'status'
    ];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class , 'user_id');

    }

    public function clinic() : BelongsTo
    {
        return $this->belongsTo(Clinic::class , 'clinic_id');

    }
}
