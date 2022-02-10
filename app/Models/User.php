<?php

namespace App\Models;

use App\Events\UserCreated;
use App\Events\UserDeleted;
use Dyrynda\Database\Support\NullableFields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use NullableFields;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
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
    ];

    /**
     * The attributes that should be set to null in the database
     * in case the value is an empty string.
     *
     * @var array
     */
    protected $nullable = [
        'timezone',
    ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => UserCreated::class,
        'deleted' => UserDeleted::class,
    ];

    public static function boot()
    {
        parent::boot();
        static::deleted(function ($user) {
            if ($user->avatar !== null && Storage::exists($user->avatar)) {
                Storage::delete($user->avatar);
            }
        });
    }

    public function scopeFilter(Builder $qry, string $filter): void
    {
        $qry->where('name', 'LIKE', '%'.$filter.'%')
            ->orWhere('email', 'LIKE', '%'.$filter.'%');
    }
}
