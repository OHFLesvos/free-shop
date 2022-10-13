<?php

namespace App\Models;

use Dyrynda\Database\Support\NullableFields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Translatable\HasTranslations;

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
        'sequence',
        'stock',
        'limit_per_order',
        'is_available',
        'price',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class)
            ->withPivot('quantity');
    }

    /**
     * @return Attribute<?string,never>
     */
    protected function pictureUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->picture !== null) {
                    if (preg_match('#^http[s]?://#', $this->picture)) {
                        return $this->picture;
                    }
                    if (Storage::exists($this->picture)) {
                        return Storage::url($this->picture);
                    }
                }

                return null;
            },
        );
    }

    /**
     * @return Attribute<int,never>
     */
    protected function reservedQuantity(): Attribute
    {
        return Attribute::make(
            get: fn () => DB::table('orders')
                ->whereIn('orders.status', ['new', 'ready'])
                ->join('order_product', function (JoinClause $join) {
                    $join->on('orders.id', '=', 'order_product.order_id')
                        ->where('order_product.product_id', '=', $this->id);
                })
                ->groupBy('order_product.product_id')
                ->sum('quantity'),
        );
    }

    /**
     * @return Attribute<int,int>
     */
    protected function freeQuantity(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->stock - $this->reserved_quantity,
            set: fn (int $value) => [
                'stock' => $value + $this->reserved_quantity,
            ],
        );
    }

    /**
     * @return Attribute<int,never>
     */
    protected function quantityAvailableForCustomer(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->limit_per_order !== null
                ? min($this->limit_per_order, $this->free_quantity)
                : max(0, $this->free_quantity)
        );
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
