<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class LinkedSocialAccount extends Model
{
    use HasFactory;
    protected $table = 'linked_social_accounts';

    protected $fillable = [
        'provider_name',
        'provider_id',
        'user_id'

    ];

//    public function user()
//    {
//        return $this->belongsTo(User::class);
//    }

    public function linked(): MorphTo
    {
        return $this->morphTo();
    }
}
