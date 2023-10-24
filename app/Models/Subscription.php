<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Subscription extends Model
{
    protected $table = 'subscriptions';

    protected $fillable = [
        'subscriber_id',
        'user_id',
    ];

    public $timestamps = false;

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(User::class,'subscriber_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function readOrders(): HasMany
    {
        return $this->hasMany(ReadOrder::class)->where('read',null)->orderByDesc('created_at');
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'read_orders');
    }

    public function scopeDefault(Builder $query): void
    {
        $query->where('subscriber_id',Auth::id());
    }
}
