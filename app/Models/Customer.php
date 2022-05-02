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
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Support\Collection;
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
        'email',
        'topped_up_at',
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
        'email',
    ];

    protected $casts = [
        'is_disabled' => 'boolean',
        'topped_up_at' => 'datetime',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function currencies(): BelongsToMany
    {
        return $this->belongsToMany(Currency::class)
            ->withPivot('value');
    }

    /**
     * @param ?Collection<Currency> $currencies
     * @return void
     */
    public function initializeBalances(?Collection $currencies = null): void
    {
        $currencies = $currencies ?? Currency::all();
        $existingIds = $this->currencies->pluck('id');
        $ids = $currencies->whereNotIn('id', $existingIds)
            ->mapWithKeys(fn (Currency $currency) => [$currency->id => [
                'value' => $currency->top_up_amount,
            ]]);

        $this->currencies()->sync($ids, false);
    }

    public function getBalance(int|Currency $currency): int
    {
        if (is_int($currency)) {
            $currency = $this->currencies->firstWhere('id', $currency);
        }

        return $currency !== null ? $currency->getRelationValue('pivot')->value : 0;
    }

    public function setBalance(int $currencyId, int $value): void
    {
        if ($this->currencies->firstWhere('id', $currencyId) === null) {
            $this->currencies()->attach($currencyId, [
                'value' => $value,
            ]);
        } else {
            $this->currencies()->updateExistingPivot($currencyId, [
                'value' => $value,
            ]);
        }
    }

    /**
     * @param Collection<int,int> $balances
     * @return void
     */
    public function setBalances(Collection $balances): void
    {
        $ids = $balances->mapWithKeys(fn ($v, $k) => [$k => [
            'value' => $v,
        ]]);

        $this->currencies()->sync($ids);
    }

    /**
     * @return Collection<string,int>
     */
    public function balance(): Collection
    {
        return $this->currencies
            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
            ->mapWithKeys(fn (Currency $currency) => [$currency->name => $this->getBalance($currency)]);
    }

    public function totalBalance(): string
    {
        return $this->balance()
            ->map(fn ($v, $k) => "$v $k")
            ->join(', ');
    }

    public function getNextTopUpDateAttribute(): ?Carbon
    {
        $days = setting()->get('customer.credit_top_up.days');
        if ($days <= 0) {
            return null;
        }

        // TODO: Handle non-assigned currencies
        $needsTopUp = $this->currencies->contains(fn (Currency $currency) => $this->getBalance($currency) < $currency->top_up_amount);
        if (!$needsTopUp) {
            return null;
        }

        $date = $this->topped_up_at ? $this->topped_up_at->clone()->addDays($days) : today();
        return $date->isBefore(today()) ? today() : $date;
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
}
