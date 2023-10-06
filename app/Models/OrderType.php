<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderType extends Model
{
    protected $fillable = ['name','active'];

    public function subTypes(): HasMany
    {
        return $this->hasMany(OrderSubType::class);
    }

    public function subTypesActive(): HasMany
    {
        return $this->hasMany(OrderSubType::class)->where('active',1);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function ordersActive(): HasMany
    {
        return $this->hasMany(Order::class)->where('active',1)->where('approved',1);
    }
}
