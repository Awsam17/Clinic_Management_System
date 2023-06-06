<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Address extends Model
{
    use HasFactory;
    //protected $fillable
    protected $guarded = [];

    public function region() : BelongsTo
    {
        return $this->belongsTo(Region::class , 'region_id');
    }

    public function clinic() : HasOne
    {
        return $this->HasOne(Clinic::class );
    }
}
