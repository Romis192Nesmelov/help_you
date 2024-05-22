<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Partner extends Model
{
    protected $fillable = [
        'logo',
        'name',
        'about',
        'info'
    ];

    public function actions(): HasMany
    {
        return $this->hasMany(Action::class);
    }

    public function scopeFiltered(Builder $query): void
    {
        $query->when(request('filter'), function (Builder $q) {
            $filter = request('filter');
            foreach (['id','name','about','info'] as $k => $field) {
                if (!$k) $q->where($field, 'LIKE', "%{$filter}%");
                else $q->orWhere($field, 'LIKE', "%{$filter}%");
            }
        });
    }
}
