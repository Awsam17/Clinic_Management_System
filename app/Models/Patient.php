<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function medical_report() : HasMany
    {
        return $this->hasMany(Medical_report::class);
    }
}
