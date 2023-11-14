<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Casts\Json;
use Illuminate\Support\Facades\Auth;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_type_id',
        'subtype_id',
        'city_id',
        'need_performers',
        'address',
        'latitude',
        'longitude',
        'description_short',
        'description_full',
        'approved',
        'status'
    ];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->select('id','avatar','avatar_props','family','name');
    }

    public function orderType(): BelongsTo
    {
        return $this->belongsTo(OrderType::class);
    }

    public function subType(): BelongsTo
    {
        return $this->belongsTo(SubType::class);
    }

    public function performers(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(OrderImage::class);
    }

    public function readSubscribers(): HasMany
    {
        return $this->hasMany(ReadOrder::class);
    }

    public function scopeDefault(Builder $query): void
    {
        $query
            ->where('status',2)
            ->where('approved',1)
            ->where('user_id','!=',Auth::id());
    }

    public function scopeFiltered(Builder $query): void
    {
        $query->when(request('order_type'), function (Builder $q) {
            $q->where('order_type_id',request('order_type'));
        });

        $query->when(request('performers'), function (Builder $q) {
            $q->whereBetween('need_performers',[request('performers_from'),request('performers_to')]);
        });
    }
}
