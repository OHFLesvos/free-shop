<?php

namespace App\Models;

use Dyrynda\Database\Support\NullableFields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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

    public function getPictureUrlAttribute()
    {
        if ($this->picture !== null) {
            if (preg_match('#^http[s]?://#', $this->picture)) {
                return $this->picture;
            }
            if (Storage::exists($this->picture)) {
                return Storage::url($this->picture);
            }
        }
        return null;
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
