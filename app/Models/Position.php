<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $fillable = [
        'market_id',
        'lang',
        'lat',
    ];

    public function market(){
        return $this->belongsTo(Market::class,'market_id');
    }
}
