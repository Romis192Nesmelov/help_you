<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'avatar',
        'name',
        'family',
        'born',
        'phone',
        'email',
        'password',
        'code',
        'info_about',
        'active'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'born' => 'datetime',
        'password' => 'hashed',
    ];

    public function getBornAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function orderPerformer(): BelongsToMany
    {
        return $this->belongsToMany(Order::class);
    }

    public function ordersActive(): HasMany
    {
        return $this->hasMany(Order::class)->where('active',1);
    }
}
