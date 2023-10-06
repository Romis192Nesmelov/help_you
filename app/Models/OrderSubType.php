<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class OrderSubType extends Model
{
    protected $fillable = ['name','active','order_type_id'];

    public function orderType(): BelongsTo
    {
        return $this->belongsTo(OrderType::class);
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class);
    }
}
