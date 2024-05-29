<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithDelivery extends Model
{
    use HasFactory;
    protected $fillable = [
        'delivery_type',
        'delivery_id',
        'order_id',
        'startTime',
        'endTime'
        ];
    public function delivery()
    {
        return $this->belongsTo(Delivery::class,'delivery_id');
    }
    public function buyingOrder()
    {
        return $this->belongsTo(BuyingOrder::class,'order_id');
    }

}
