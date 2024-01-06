<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReadStatusOrder extends Model
{
    protected $fillable = [
        'order_id',
        'status',
        'read'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
