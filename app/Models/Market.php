<?php

namespace App\Models;

use App\Notifications\SendCheckCode;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Auth;

class Market extends Authenticatable
{

    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    public function sendCodeByEmail($verification_code)
    {
        $this->notify(new SendCheckCode($verification_code)); // my notification
    }

    protected $guard_name = 'market-api';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'location',
        'market_name',
        'photo',
        'm_category_id',
        'startTime',
        'endTime',
        'marketType'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $appends = ['FullTime'];

    public function Product()
    {
        return $this->belongsToMany(Product::class, 'markets_products')
            ->withPivot('price', 'quantity', 'details');
    }

    public function delivery()
    {
        return $this->hasMany(Delivery::class, 'market_id');
    }

    public function post()
    {
        return $this->hasMany(Post::class, 'market_id');
    }

    public function followup()
    {
        return $this->hasMany(FollowUpRequest::class, 'market_id');
    }

    public function m_category()
    {
        return $this->belongsTo(M_category::class, 'm_category_id');
    }

    public function linkedSocialAccounts(): MorphMany
    {
        return $this->morphMany(LinkedSocialAccount::class, 'linked');
    }
    public function buyingOrder()
    {
        return $this->hasMany(BuyingOrder::class, 'market_id');
    }
    public function notifiable()
    {
        return $this->morphToMany(Notification::class, 'notifiable');
    }
    public function deviceable(): MorphMany
    {
        return $this->morphMany(Device::class, 'deviceable');
    }
    public function receivable(): MorphMany
    {
        return $this->morphMany(Message_receipent::class, 'receivable');
    }
    public function sendable(): MorphMany
    {
        return $this->morphMany(Message::class, 'sendable');
    }

    public function getFullTimeAttribute(){
        return $this->startTime. ' Am to ' .$this->endTime .' Pm';
    }
    public function position()
    {
        return $this->hasOne(Position::class, 'market_id');
    }


}

