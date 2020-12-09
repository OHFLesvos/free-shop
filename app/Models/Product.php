<?php

namespace App\Models;

use Dyrynda\Database\Support\NullableFields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    use NullableFields;

    protected $nullable = [
        'description',
        'customer_limit',
    ];

    public function orders()
    {
        return $this->belongsToMany(Order::class)
            ->withPivot('amount');
    }

    public function imageUrl($width, $height)
    {
        return 'https://picsum.photos/seed/' . md5($this->name) . '/' . $width . '/' . $height;
    }

    public function getReservedAmountAttribute()
    {
        return $this->orders
            ->map(fn ($order) => $order->pivot->amount)
            ->sum();
    }

    public function getFreeAmountAttribute()
    {
        return $this->stock_amount - $this->reserved_amount;
    }

    public function getAvailableForCustomerAmountAttribute()
    {
        if ($this->customer_limit !== null) {
            return min($this->customer_limit, $this->free_amount);
        }
        return $this->free_amount;
    }

    public function scopeAvailable(Builder $qry)
    {
        $qry->where('is_available', true);
    }

    public function scopeDisabled(Builder $qry)
    {
        $qry->where('is_available', false);
    }
}
