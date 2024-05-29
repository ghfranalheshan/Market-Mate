<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class M_category extends Model
{
    protected $fillable = [
        'name'
    ];
    use HasFactory;

    public function Market()
    {
        return $this->hasMany(Market::class,'M_category_id');
    }
}
