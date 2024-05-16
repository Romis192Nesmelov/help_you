<?php

namespace App\Models;

use App\Casts\Json;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
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
        'admin',
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

    public function getAgeAttribute(): int
    {
        return Carbon::parse($this->born)->age;
    }

    public function scopeFiltered(Builder $query): void
    {
        $query->when(request('filter'), function (Builder $q) {
            $filter = request('filter');
            foreach (['id','name','family','phone','email'] as $k => $field) {
                if (!$k) $q->where($field, 'LIKE', "%{$filter}%");
                else $q->orWhere($field, 'LIKE', "%{$filter}%");
            }
        });
    }

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
            ->where('status',1)
            ->with('user.ratings')
            ->with('performers.ratings')
            ->with('orderType')
            ->with('subType')
            ->orderByDesc('created_at');
    }

    public function orderArchivePerformer(): BelongsToMany
    {
        return $this->belongsToMany(Order::class)
            ->where('status',0)
            ->with('user.ratings')
            ->with('performers.ratings')
            ->with('orderType')
            ->with('subType')
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

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function readStatusOrder(): HasMany
    {
        return $this->hasMany(ReadStatusOrder::class);
    }

    public function incentives(): BelongsToMany
    {
        return $this->belongsToMany(Action::class);
    }

    public function incentivesActive(): BelongsToMany
    {
        return $this->belongsToMany(Action::class)->wherePivot('active',1);
    }
}
