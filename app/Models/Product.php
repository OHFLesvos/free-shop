<?php

namespace App\Models;

use Dyrynda\Database\Support\NullableFields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Translatable\HasTranslations;
use OwenIt\Auditing\Contracts\Auditable;

class Product extends Model implements Auditable
{
    use HasFactory;
    use NullableFields;
    use HasTranslations;
    use \OwenIt\Auditing\Auditable;

    /**
     * The attributes that should be set to null in the database
     * in case the value is an empty string.
     *
     * @var array
     */
    protected $nullable = [
        'limit_per_order',
    ];

    /**
     * The attributes that should have translations
     *
     * @var array
     */
    public $translatable = [
        'name',
        'category',
        'description',
    ];

    public $fillable = [
        'name',
        'sequence',
        'stock',
        'limit_per_order',
        'is_available',
        'price',
        'currency_id',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class)
            ->withPivot('quantity');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function getPictureUrlAttribute(): ?string
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

    public function getReservedQuantityAttribute(): int
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

    public function getFreeQuantityAttribute(): int
    {
        return $this->stock - $this->reserved_quantity;
    }

    public function setFreeQuantityAttribute(int $value): void
    {
        $this->stock = $value + $this->reserved_quantity;
    }

    public function getAvailableQuantityPerOrder(): int
    {
        $free_quantity = max(0, $this->free_quantity);

        if ($this->limit_per_order !== null) {
            return min($this->limit_per_order, $free_quantity);
        }

        return $free_quantity;
    }

    public function scopeAvailable(Builder $qry): void
    {
        $qry->where('is_available', true);
    }

    public function scopeDisabled(Builder $qry): void
    {
        $qry->where('is_available', false);
    }
}
