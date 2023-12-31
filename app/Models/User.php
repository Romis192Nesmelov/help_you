<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Casts\Json;
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
        'avatar_props',
        'name',
        'family',
        'born',
        'phone',
        'email',
        'password',
        'code',
        'info_about',
        'mail_notice',
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
//        'born' => 'datetime:d-m-Y',
        'avatar_props' => Json::class,
        'password' => 'hashed',
    ];

    public function subscriptions(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'subscriptions', 'subscriber_id');
    }

    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'subscriptions', 'user_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function orderPerformer(): BelongsToMany
    {
        return $this->belongsToMany(Order::class)->orderByDesc('created_at');
    }

    public function orderActivePerformer(): BelongsToMany
    {
        return $this->belongsToMany(Order::class)
            ->where('status','>',0)
            ->orderByDesc('created_at');
    }

    public function orderArchivePerformer(): BelongsToMany
    {
        return $this->belongsToMany(Order::class)
            ->where('status',0)
            ->orderByDesc('created_at');
    }

    public function ordersApproving(): HasMany
    {
        return $this->hasMany(Order::class)
            ->where('approved',1)
            ->orderByDesc('created_at');
    }

    public function ordersActive(): HasMany
    {
        return $this->hasMany(Order::class)
            ->where('status','>',0)
            ->orderByDesc('created_at');
    }

    public function orderApproving(): HasMany
    {
        return $this->hasMany(Order::class)
            ->where('approved',0)
            ->orderByDesc('created_at');
    }

    public function ordersActiveAndApproving(): HasMany
    {
        return $this->hasMany(Order::class)
            ->where('status','>',0)
            ->where('approved',1)
            ->orderByDesc('created_at');
    }

    public function ordersArchive(): HasMany
    {
        return $this->hasMany(Order::class)
            ->where('status',0)
            ->where('approved',1)
            ->orderByDesc('created_at');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function unreadMessages(): HasMany
    {
        return $this->hasMany(MessageUser::class)->where('read',null);
    }

    public function years(): int
    {
        return Carbon::parse($this->born)->age;
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function readStatusOrder(): HasMany
    {
        return $this->hasMany(ReadStatusOrder::class);
    }
}
