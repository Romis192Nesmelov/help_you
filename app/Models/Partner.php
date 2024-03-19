<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
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
}
