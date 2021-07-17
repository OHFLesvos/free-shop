<?php

namespace App\Models;

use App\Models\Traits\NumberCompareScope;
use Carbon\Carbon;
use Dyrynda\Database\Support\NullableFields;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\Access\Authorizable;
use libphonenumber\NumberParseException;
use Propaganistas\LaravelPhone\PhoneNumber;
use OwenIt\Auditing\Contracts\Auditable;

class Customer extends Model implements
    HasLocalePreference,
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract,
    Auditable
{
    use HasFactory;
    use Notifiable;
    use NullableFields;
    use NumberCompareScope;
    use Authenticatable;
    use Authorizable;
    use CanResetPassword;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'name',
        'id_number',
        'phone',
        'locale',
        'remarks',
        'credit',
    ];

    /**
     * The attributes that should be set to null in the database
     * in case the value is an empty string.
     *
     * @var array
     */
    protected $nullable = [
        'locale',
        'remarks',
        'disabled_reason',
    ];

    protected $casts = [
        'is_disabled' => 'boolean',
        'topped_up_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function (Customer $customer) {
            if ($customer->topped_up_at == null) {
                $customer->topped_up_at = now();
            }
        });
        static::deleting(function (Customer $customer) {
            $customer->orders()
                ->whereIn('status', ['new', 'ready'])
                ->update(['status' => 'cancelled']);
            $customer->orders()
                ->update(['customer_id' => null]);
            $customer->comments()
                ->delete();
        });
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function comments(): HasOneOrMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function tags(): BelongsToMany
    {
        return $this->morphToMany(Tag::class, 'taggable')
            ->using(Taggable::class);
    }

    public function scopeRegisteredInDateRange(Builder $qry, ?string $start, ?string $end): void
    {
        if (filled($start) && filled($end)) {
            $qry->whereDate('created_at', '>=', $start)
                ->whereDate('created_at', '<=', $end);
        }
    }

    public function scopeFilter(Builder $qry, string $filter): void
    {
        $qry->where(DB::raw('LOWER(name)'), 'LIKE', '%' . strtolower($filter) . '%')
            ->orWhere('id_number', 'LIKE', $filter . '%')
            ->orWhere(fn ($inner) => $inner->whereNumberCompare('id_number', $filter))
            ->orWhere('phone', 'LIKE', $filter . '%')
            ->orWhere(fn ($inner) => $inner->whereNumberCompare('phone', $filter))
            ->orWhere('remarks', 'LIKE', '%' . $filter . '%');
    }

    public function routeNotificationForTwilio(): ?string
    {
        return $this->phone;
    }

    public function preferredLocale(): ?string
    {
        return $this->locale;
    }

    public function getPhoneFormattedInternationalAttribute(): ?string
    {
        if ($this->phone !== null) {
            try {
                return PhoneNumber::make($this->phone)->formatInternational();
            } catch (NumberParseException $ignored) {
                return $this->phone;
            }
        }
        return null;
    }

    public function getNextOrderIn(): ?Carbon
    {
        $days = intval(setting()->get('customer.waiting_time_between_orders', 0));
        if ($days > 0) {
            $lastOrder = $this->orders()
                ->where('status', '!=', 'cancelled')
                ->orderBy('created_at', 'desc')
                ->first();
            if ($lastOrder != null) {
                if (now()->subDays($days)->lte($lastOrder->created_at)) {
                    return $lastOrder->created_at->clone()->addDays($days);
                }
            }
        }
        return null;
    }

    public function getNextTopUpDateAttribute(): ?Carbon
    {
        $days = setting()->get('customer.credit_top_up.days');
        if ($days > 0) {
            $startingCredit = setting()->get('customer.starting_credit', config('shop.customer.starting_credit'));
            $amount = setting()->get('customer.credit_top_up.amount', $startingCredit);
            $maximum = setting()->get('customer.credit_top_up.maximum', $startingCredit);
            if ($this->credit < min($this->credit + $amount, $maximum)) {
                $date = $this->topped_up_at ? $this->topped_up_at->clone()->addDays($days) : today();
                if ($date->isBefore(today())) {
                    return today();
                }
                return $date;
            }
        }
        return null;
    }
}
