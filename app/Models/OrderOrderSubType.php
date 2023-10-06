<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderOrderSubType extends Model
{
    protected $table = 'order_order_sub_type';

    protected $fillable = [
        'order_id',
        'order_sub_type_id'
    ];

    public $timestamps = false;

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderSubType(): BelongsTo
    {
        return $this->belongsTo(OrderSubType::class);
    }
}
