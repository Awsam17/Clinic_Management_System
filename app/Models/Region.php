<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function city() : BelongsTo
    {
        return $this->BelongsTo(City::class,'city_id');
    }

    public function addresses() : HasMany
    {
        return $this->hasMany(Address::class);
    }
}
