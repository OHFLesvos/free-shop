<?php

namespace App\Models;

use donatj\UserAgent\UserAgentParser;
use Dyrynda\Database\Support\NullableFields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;
    use NullableFields;

    protected $nullable = [
        'remarks',
        'completed_at',
    ];

    protected $dates = [
        'completed_at',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('amount');
    }

    public function scopeOpen(Builder $qry)
    {
        $qry->whereNull('completed_at');
    }

    public function scopeCompleted(Builder $qry)
    {
        $qry->whereNotNull('completed_at');
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

    public function setCustomerPhoneAttribute($value)
    {
        $this->attributes['customer_phone'] = preg_replace("/\s+/", '', $value);
    }

    public function getUAAttribute()
    {
        $parser = new UserAgentParser();
        return $parser->parse($this->customer_user_agent);
    }

    public function getGeoIpLocationAttribute()
    {
        return geoip()->getLocation($this->user_ip_address);
    }
}
