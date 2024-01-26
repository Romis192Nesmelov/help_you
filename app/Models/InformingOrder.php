<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InformingOrder extends Model
{
    protected $fillable = [
        'message',
        'order_id'
    ];

    public function orders(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
