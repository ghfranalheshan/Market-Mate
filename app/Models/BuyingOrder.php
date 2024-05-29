<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuyingOrder extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'market_id',
        'order_date',
        'total_price',
        'is_received',
        'request_status',
        'delivery_cost',
        'lat',
        'lang',
        ];

    protected $with = ['user','market','product'];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function market()
    {
        return $this->belongsTo(Market::class,'market_id');
    }
    public function product()
    {
        return $this->belongsToMany(Product::class,'buying_order_products')->withPivot('quantity','details');
    }
    public function withdelivery()
    {
        return $this->belongsTo(WithDelivery::class,' buying_order_id');

    }

}
