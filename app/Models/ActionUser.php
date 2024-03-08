<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActionUser extends Model
{
    protected $table = 'action_user';

    protected $fillable = [
        'action_id',
        'user_id',
        'read',
        'active'
    ];

    public $timestamps = false;

    public function action(): BelongsTo
    {
        return $this->belongsTo(Action::class)->select(['id','name']);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
