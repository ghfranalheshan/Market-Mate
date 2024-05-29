<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
    ];
public $with = ['photo'];
    public function Market()
    {
        return $this->belongsToMany(Market::class,'markets_products')
            ->withPivot('price','quantity','details');
    }
    public function buyingorder()
    {
        return $this->belongsToMany(BuyingOrder::class,'buying_order_products')->withPivot('quantity','details');
    }

    public function photo(): MorphMany
    {
        return $this->morphMany(Photo::class, 'photoable');
    }

}
