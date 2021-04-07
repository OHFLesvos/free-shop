<?php

namespace App\Models;

use Dyrynda\Database\Support\NullableFields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Translatable\HasTranslations;

class Product extends Model
{
    use HasFactory;
    use NullableFields;
    use HasTranslations;

    protected $nullable = [
        'limit_per_order',
    ];

    public $translatable = [
        'name',
        'category',
        'description',
    ];

    public $fillable = [
        'sequence',
        'stock',
        'limit_per_order',
        'is_available',
        'price',
    ];

    public function orders()
    {
        return $this->belongsToMany(Order::class)
            ->withPivot('quantity');
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

    public function getReservedQuantityAttribute()
    {
        return $this->orders
            ->where('isOpen', true)
            ->map(fn ($order) => $order->pivot->quantity)
            ->sum();
    }

    public function getFreeQuantityAttribute()
    {
        return $this->stock - $this->reserved_quantity;
    }

    public function getQuantityAvailableForCustomerAttribute()
    {
        // TODO fix calculation of free and available orders
        if ($this->limit_per_order !== null) {
            return min($this->limit_per_order, $this->stock);
        }
        return $this->stock;
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
