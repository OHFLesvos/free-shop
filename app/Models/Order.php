<?php

namespace App\Models;

use donatj\UserAgent\UserAgentParser;
use Dyrynda\Database\Support\NullableFields;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class Order extends Model implements HasLocalePreference
{
    use HasFactory;
    use NullableFields;
    use Notifiable;

    protected $fillable = [
        'customer_name',
        'customer_id_number',
        'customer_phone',
        'remarks',
        'customer_ip_address',
        'customer_user_agent',
        'locale',
    ];

    protected $nullable = [
        'remarks',
        'completed_at',
        'cancelled_at',
        'locale',
    ];

    protected $dates = [
        'completed_at',
        'cancelled_at',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('quantity');
    }

    public function scopeOpen(Builder $qry)
    {
        $qry->whereNull('completed_at')
            ->whereNull('cancelled_at');
    }

    public function scopeCompleted(Builder $qry)
    {
        $qry->whereNotNull('completed_at');
    }

    public function scopeCancelled(Builder $qry)
    {
        $qry->whereNotNull('cancelled_at');
    }

    public function scopeFilter(Builder $qry, string $filter)
    {
        $qry->where('id', $filter)
            ->orWhere('customer_name', 'LIKE', '%' . $filter . '%')
            ->orWhere(fn ($inner) => $inner->whereNumberCompare('customer_id_number', $filter))
            ->orWhere(fn ($inner) => $inner->whereNumberCompare('customer_phone', $filter))
            ->orWhere('remarks', 'LIKE', '%' . $filter . '%');
    }

    public function scopeWhereNumberCompare(Builder $qry, string $field, string $value)
    {
        $qry->where(DB::raw('TRIM(LEADING \'0\' FROM (REGEXP_REPLACE(' . $field . ', \'[^0-9]+\', \'\')))'), ltrim(preg_replace('/[^0-9]/', '', $value), '0'));
    }

    public function getUAAttribute()
    {
        $parser = new UserAgentParser();
        return $parser->parse($this->customer_user_agent);
    }

    public function getGeoIpLocationAttribute()
    {
        return geoip()->getLocation($this->customer_ip_address);
    }

    public function routeNotificationForTwilio()
    {
        return $this->customer_phone;
    }

    public function preferredLocale()
    {
        return $this->locale;
    }
}
