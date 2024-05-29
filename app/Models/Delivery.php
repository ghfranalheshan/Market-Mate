<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Delivery extends Authenticatable
{
    use HasFactory , HasRoles , HasApiTokens;
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'hour_number',
        'market_id'
    ];
    protected $guard_name='delivery-api';

    public function market()
    {
        return $this->belongsTo(User::class,' market_id');
    }
    public function withdelivery()
    {
        return $this->hasMany(WithDelivery::class,'delivery_id');
    }
}
