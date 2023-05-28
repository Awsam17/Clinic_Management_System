<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Specialty extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table ='specialties';
    public function specialty_doctors() : HasMany
    {
        return $this->hasMany(Spec_doc::class , 'specialty_id');
    }
}
