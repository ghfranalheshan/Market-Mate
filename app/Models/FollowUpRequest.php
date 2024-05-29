<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowUpRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'market_id',
        'request_status'
    ];
    protected $with = ['user','market'];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function market()
    {
        return $this->belongsTo(Market::class,'market_id');
    }
}
