<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Casts\Json;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_type_id',
        'city_id',
        'subtypes',
        'need_performers',
        'address',
        'latitude',
        'longitude',
        'description',
        'approved',
        'active'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderType(): BelongsTo
    {
        return $this->belongsTo(OrderType::class);
    }

    public function performers(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(OrderImage::class);
    }

    protected $casts = [
        'subtypes' => Json::class,
    ];

    public function getOrderSubTypesAttribute($value)
    {
        $orderSubTypes = OrderType::find($this->order_type_id)->subtypes;
        $subTypes = [];
        if ($this->subtypes) {
            foreach ($this->subtypes as $id) {
                foreach ($orderSubTypes as $orderSubType) {
                    if ($orderSubType['id'] == $id) {
                        $subTypes[] = $orderSubType;
                        break;
                    }
                }
            }
            return $subTypes;
        } else return null;
    }
}
