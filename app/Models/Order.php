<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_type_id',
        'city_id',
        'performers',
        'latitude',
        'longitude',
        'description',
        'approved',
        'active'
    ];

    public function orderType(): BelongsTo
    {
        return $this->belongsTo(OrderType::class);
    }

    public function subTypes(): BelongsToMany
    {
        return $this->belongsToMany(OrderSubType::class);
    }

    public function performers(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(OrderImage::class);
    }
}
