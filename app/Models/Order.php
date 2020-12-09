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

    public function scopeDone(Builder $qry)
    {
        $qry->whereNotNull('delivered_at');
    }

    public function scopeFilter(Builder $qry, string $filter)
    {
        $qry->where('customer_name', 'LIKE', '%' . trim($filter) . '%')
            ->orWhere('customer_id_number', 'LIKE', '%' . trim($filter) . '%')
            ->orWhere('customer_phone', 'LIKE', '%' . trim(preg_replace("/\s+/", '', $filter)) . '%')
            ->orWhere('remarks', 'LIKE', '%' . trim($filter) . '%');
    }

    public function setCustomerPhoneAttribute($value)
    {
        $this->attributes['customer_phone'] = preg_replace("/\s+/", '', $value);
    }
}
