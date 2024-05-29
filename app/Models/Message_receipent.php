<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Message_receipent extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id'
    ];


    public function receivable(): MorphTo
    {
        return $this->morphTo();
    }
}
