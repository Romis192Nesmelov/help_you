<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function actionUsers(): HasMany
    {
        return $this->hasMany(ActionUser::class,'action_id');
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function scopeWithPartnerId(Builder $query): void
    {
        $query->when(request('parent_id'), function (Builder $q) {
            $q->where('partner_id',request('parent_id'));
        });
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

        $query->when(request('partners_ids'), function (Builder $q) {
            if (!request('filter')) $q->whereIn('partner_id',request('partners_ids'));
            else $q->orWhereIn('partner_id',request('partners_ids'));
        });
    }
}
