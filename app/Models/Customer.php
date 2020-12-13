<?php

namespace App\Models;

use App\Models\Traits\NumberCompareScope;
use Dyrynda\Database\Support\NullableFields;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Customer extends Model implements HasLocalePreference
{
    use HasFactory;
    use Notifiable;
    use NullableFields;
    use NumberCompareScope;

    protected $fillable = [
        'name',
        'id_number',
        'phone',
        'locale',
        'remarks',
    ];

    protected $nullable = [
        'locale',
        'remarks',
    ];

    protected static function booted()
    {
        static::deleting(function (Customer $customer) {
            $customer->orders()->delete();
        });
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function scopeFilter(Builder $qry, string $filter)
    {
        $qry->where('name', 'LIKE', '%' . $filter . '%')
            ->orWhere(fn ($inner) => $inner->whereNumberCompare('id_number', $filter))
            ->orWhere(fn ($inner) => $inner->whereNumberCompare('phone', $filter))
            ->orWhere('remarks', 'LIKE', '%' . $filter . '%');
    }

    public function routeNotificationForTwilio()
    {
        return $this->phone;
    }

    public function preferredLocale()
    {
        return $this->locale;
    }
}
