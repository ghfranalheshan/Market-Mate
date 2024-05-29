<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'body',
        'is_read'
        ];

    public function user()
    {
        return $this->morphedByMany(User::class, 'notifiable');
    }

    public function market()
    {
        return $this->morphedByMany(Market::class, 'notifiable');
    }

}
