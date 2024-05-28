<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'subject',
        'text',
        'user_id',
        'status',
        'read_admin'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function scopeWithUserId(Builder $query): void
    {
        $query->when(request('parent_id'), function (Builder $q) {
            $q->where('user_id',request('parent_id'));
        });
    }

    public function scopeFiltered(Builder $query): void
    {
        $query->when(request('filter'), function (Builder $q) {
            $filter = request('filter');
            foreach (['id','subject','user_id'] as $k => $field) {
                if (!$k) $q->where($field, 'LIKE', "%{$filter}%");
                else $q->orWhere($field, 'LIKE', "%{$filter}%");
            }
        });
    }
}
