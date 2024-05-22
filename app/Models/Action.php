<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Action extends Model
{
    protected $fillable = [
        'name',
        'html',
        'start',
        'end',
        'rating',
        'partner_id'
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function usersAwardedActive(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->wherePivot('active',1);
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function scopeFiltered(Builder $query): void
    {
        $query->when(request('filter'), function (Builder $q) {
            $filter = request('filter');
            foreach (['id','name','start','end','rating','partner_id'] as $k => $field) {
                if (!$k) $q->where($field, 'LIKE', "%{$filter}%");
                else $q->orWhere($field, 'LIKE', "%{$filter}%");
            }
        });
    }
}
