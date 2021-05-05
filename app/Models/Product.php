<?php

namespace App\Models;

use Dyrynda\Database\Support\NullableFields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
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

    protected $casts = [
        'is_available' => 'boolean',
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
        return DB::table('orders')->whereIn('orders.status', ['new', 'ready'])
            ->join('order_product', function ($join) {
                $join->on('orders.id', '=', 'order_product.order_id')
                    ->where('order_product.product_id', '=', $this->id);
            })
            ->groupBy('order_product.product_id')
            ->selectRaw('sum(quantity) as reserved')
            ->first()
            ->reserved ?? 0;
    }

    public function getFreeQuantityAttribute()
    {
        return $this->stock - $this->reserved_quantity;
    }

    public function getQuantityAvailableForCustomerAttribute()
    {
        if ($this->limit_per_order !== null) {
            return min($this->limit_per_order, $this->free_quantity);
        }
        return max(0, $this->free_quantity);
    }

    public function scopeAvailable(Builder $qry)
    {
        $qry->where('is_available', DB::raw('true'));
    }

    public function scopeDisabled(Builder $qry)
    {
        $qry->where('is_available', DB::raw('false'));
    }
}
