<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketAccept extends Model
{
    use HasFactory;
    protected $fillable =['market_id' , 'status'] ;

    public function market()
    {
        return $this->belongsTo(User::class,' market_id');
    }

}
