<?php

namespace App\Models;

use App\Models\Traits\NumberCompareScope;
use Dyrynda\Database\Support\NullableFields;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Propaganistas\LaravelPhone\Exceptions\NumberParseException;
use Propaganistas\LaravelPhone\PhoneNumber;

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
        'credit',
    ];

    protected $nullable = [
        'locale',
        'remarks',
    ];

    protected static function booted()
    {
        static::deleting(function (Customer $customer) {
            $customer->orders()
                ->whereIn('status', ['new', 'ready'])
                ->update(['status' => 'cancelled']);
            $customer->orders()
                ->update(['customer_id' => null]);
        });
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function scopeRegisteredInDateRange(Builder $qry, ?string $start, ?string $end)
    {
        if (filled($start) && filled($end)) {
            $qry->whereDate('created_at', '>=', $start)
                ->whereDate('created_at', '<=', $end);
        }
    }

    public function scopeFilter(Builder $qry, string $filter)
    {
        $qry->where('name', 'LIKE', '%' . $filter . '%')
            ->orWhere('id_number', 'LIKE', $filter . '%')
            ->orWhere(fn ($inner) => $inner->whereNumberCompare('id_number', $filter))
            ->orWhere('phone', 'LIKE', $filter . '%')
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

    public function getPhoneFormattedInternationalAttribute()
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

    public function getNextOrderIn()
    {
        $days = intval(setting()->get('customer.waiting_time_between_orders', 0));
        if ($days > 0) {
            $lastOrder = $this->orders()
                ->where('status', '!=', 'cancelled')
                ->orderBy('created_at', 'desc')
                ->first();
            if ($lastOrder != null) {
                if (now()->subDays($days)->lte($lastOrder->created_at)) {
                    return $lastOrder->created_at->clone()->addDays($days)->diffForHumans();
                }
            }
        }
        return null;
    }
}
