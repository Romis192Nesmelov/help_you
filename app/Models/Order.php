<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_type_id',
        'subtype_id',
        'city_id',
        'name',
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
        return $this->belongsTo(User::class)->select('id','avatar','avatar_props','name','family','born','info_about');
    }

    public function userCredentials(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->select('id','avatar','name','family','phone','email','mail_notice');
    }

    public function orderType(): BelongsTo
    {
        return $this->belongsTo(OrderType::class);
    }

    public function subType(): BelongsTo
    {
        return $this->belongsTo(Subtype::class,'subtype_id');
    }

    public function performers(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(OrderImage::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function readSubscriptions(): HasMany
    {
        return $this->hasMany(ReadOrder::class);
    }

    public function readPerformers(): HasMany
    {
        return $this->hasMany(ReadPerformer::class);
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

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }
}
