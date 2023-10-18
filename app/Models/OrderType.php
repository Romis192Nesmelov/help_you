<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Casts\Json;

class OrderType extends Model
{
    protected $fillable = ['name','subtypes','active'];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function ordersActive(): HasMany
    {
        return $this->hasMany(Order::class)->where('active',1)->where('approved',1);
    }

    protected $casts = [
        'subtypes' => Json::class,
    ];
}
