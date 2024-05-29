<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Translatable\HasTranslations;

class Post extends Model
{
    use HasFactory;
    use HasTranslations;
    protected $fillable = [
        'market_id',
        'text',
        'numberOfLikes',

    ];
    public $translatable = ['text'];
    public function like()
    {
        return $this->hasMany(Like::class,'post_id');
    }
    public function market()
    {
        return $this->belongsTo(Market::class,'market_id');
    }
    public function photo(): MorphMany
    {
        return $this->morphMany(Photo::class, 'photoable');
    }
}
