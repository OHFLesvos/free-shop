<?php

namespace App\Models;

use Dyrynda\Database\Support\NullableFields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    use NullableFields;

    protected $nullable = [
        'remarks',
        'delivered_at',
    ];

    protected $dates = [
        'delivered_at',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('amount');
    }

    public function scopeOpen(Builder $qry)
    {
        $qry->whereNull('delivered_at');
    }

    public function scopeCompleted(Builder $qry)
    {
        $qry->whereNotNull('delivered_at');
    }

    public function scopeFilter(Builder $qry, string $filter)
    {
        $qry->where('id', $filter)
            ->orWhere('customer_name', 'LIKE', '%' . $filter . '%')
            ->orWhere('customer_id_number', $filter)
            ->orWhere('customer_phone', preg_replace("/\s+/", '', $filter))
            ->orWhere('remarks', 'LIKE', '%' . $filter . '%');
    }

    public function setCustomerPhoneAttribute($value)
    {
        $this->attributes['customer_phone'] = preg_replace("/\s+/", '', $value);
    }

    public function getUserAgentInfoAttribute()
    {
        $parser = new UserAgentParser();
        return $parser->parse($this->customer_user_agent);
        // $ua->platform();
        // $ua->browser();
        // $ua->browserVersion();
    }

    public function getGeoIpAttribute()
    {
        return geoip($this->user_ip_address);
    }
}
