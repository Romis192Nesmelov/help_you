<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    protected $fillable = ['name'];

    public function points(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
