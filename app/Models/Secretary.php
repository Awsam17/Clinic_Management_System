<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Secretary extends Authenticatable implements JWTSubject
{
    use HasFactory;
    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function clinic() : BelongsTo
    {
        return $this->BelongsTo(Clinic::class,'clinic_id');
    }

    public function getJWTIdentifier() {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims() {
        return [];
    }
}
