<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subtype extends Model
{
    protected $fillable = [
        'name',
        'order_type_id',
        'active'
    ];

    public $timestamps = false;

    public function orderType(): BelongsTo
    {
        return $this->belongsTo(OrderType::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
