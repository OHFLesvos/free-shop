<?php

namespace App\Models;

use App\Events\UserDeleted;
use Dyrynda\Database\Support\NullableFields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use NullableFields;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'timezone',
        'provider',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'notify_via_email' => 'boolean',
        'notify_via_phone' => 'boolean',
    ];

    protected $nullable = [
        'timezone',
        'phone',
    ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'deleted' => UserDeleted::class,
    ];

    public function scopeNotifiable(Builder $query)
    {
        $query->where('notify_via_email', true)
            ->orWhere(fn ($inner) => $inner->where('notify_via_phone', true)
                ->whereNotNull('phone')
            );
    }

    public function routeNotificationForTwilio()
    {
        return $this->phone;
    }

    public function scopeFilter(Builder $qry, string $filter)
    {
        $qry->where('name', 'LIKE', '%' . $filter . '%')
            ->orWhere('email', 'LIKE', '%' . $filter . '%');
    }
}
