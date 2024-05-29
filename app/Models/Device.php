<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_token'
    ];

    public function deviceable(): MorphTo
    {
        return $this->morphTo();
    }



}
