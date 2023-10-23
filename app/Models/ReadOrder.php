<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReadOrder extends Model
{
    protected $fillable = [
        'subscription_id',
        'order_id',
        'read'
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class)->where('approved',1)->where('active',1);
    }
}
