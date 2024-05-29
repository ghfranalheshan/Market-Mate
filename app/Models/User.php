<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\SendCheckCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    public function sendCodeByEmail($verification_code)
    {
        $this->notify(new SendCheckCode( $verification_code)); // my notification
    }

    protected $guard_name = 'api';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'location',
        'photo'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function followup()
    {
        return $this->hasMany(FollowUpRequest::class,'user_id');
    }
    public function buyingorder()
    {
        return $this->hasMany(BuyingOrder::class,'user_id');
    }
    public function notification()
    {
        return $this->hasMany(Notification::class,'user_id');
    }
    public function linkedSocialAccounts(): MorphMany
    {
        return $this->morphMany(LinkedSocialAccount::class, 'linked');
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
}
