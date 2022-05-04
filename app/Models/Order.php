<?php

namespace App\Models;

use App\Dto\CostsDto;
use App\Models\Traits\NumberCompareScope;
use Dyrynda\Database\Support\NullableFields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;

class Order extends Model implements Auditable
{
    use HasFactory;
    use NullableFields;
    use NumberCompareScope;
    use \OwenIt\Auditing\Auditable;

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::updating(function (Order $order) {
            if ($order->status == 'completed' && $order->completed_at == null) {
                $order->completed_at = now();
            }
        });
    }

    protected $fillable = [
        'ip_address',
        'user_agent',
        'remarks',
    ];

    /**
     * The attributes that should be set to null in the database
     * in case the value is an empty string.
     *
     * @var array
     */
    protected $nullable = [
        'remarks',
    ];

    public const STATUSES = [
        'new',
        'ready',
        'completed',
        'cancelled',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('quantity');
    }

    public function numberOfProducts(): int
    {
        return $this->products()->sum('quantity');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function getIsOpenAttribute(): bool
    {
        return in_array($this->status, ['new', 'ready']);
    }

    public function getIsCancellableAttribute(): bool
    {
        return $this->status == 'new';
    }

    public function scopeRegisteredInDateRange(Builder $qry, ?string $start, ?string $end): void
    {
        if (filled($start) && filled($end)) {
            $qry->whereDate('created_at', '>=', $start)
                ->whereDate('created_at', '<=', $end);
        }
    }

    public function scopeCompletedInDateRange(Builder $qry, ?string $start, ?string $end): void
    {
        $qry->where('status', 'completed');

        if (filled($start) && filled($end)) {
            $qry->whereDate('completed_at', '>=', $start)
                ->whereDate('completed_at', '<=', $end);
        }
    }

    public function scopeOpen(Builder $qry): void
    {
        $qry->whereIn('status', ['new', 'ready']);
    }

    public function scopeStatus(Builder $qry, string $status): void
    {
        assert(in_array($status, self::STATUSES));

        $qry->where('status', $status);
    }

    public function scopeFilter(Builder $qry, string $filter): void
    {
        $qry->where('id', intval($filter))
            ->orWhereHas('customer', function ($customerQry) use ($filter) {
                $customerQry->where(DB::raw('LOWER(name)'), 'LIKE', '%' . strtolower($filter) . '%')
                    ->orWhere('id_number', 'LIKE', $filter . '%')
                    ->orWhere(fn ($inner) => $inner->whereNumberCompare('id_number', $filter))
                    ->orWhere('phone', 'LIKE', $filter . '%')
                    ->orWhere(fn ($inner) => $inner->whereNumberCompare('phone', $filter));
            })
            ->orWhere('remarks', 'LIKE', '%' . $filter . '%');
    }

    public function calculateTotalPrice(): int
    {
        return $this->products()->get()
            ->map(fn (Product $product) => $product->price *  $product->pivot->quantity)
            ->sum();
    }

    /**
     * @return Collection<CostsDto>
     */
    public function getCosts(): Collection
    {
        $names = $this->products->mapWithKeys(fn (Product $product) => [$product->currency_id => $product->currency->name]);

        return $this->products
            ->filter(fn (Product $product) => $product->price > 0 && $product->currency_id !== null)
            ->map(fn (Product $product) => [
                'currency' => $product->currency_id,
                'value' => $product->price * $product->getRelationValue('pivot')->quantity,
            ])
            ->groupBy('currency')
            ->map(fn (Collection $v, int $k) => new CostsDto(
                currency_id: $k,
                currency_name: $names[$k],
                value: $v->sum('value'),
            ));
    }

    public function getCostsString(): string
    {
        $value = $this->getCosts()->map(fn (CostsDto $cost) => (string) $cost)->join(', ');

        return filled($value) ? $value : '-';
    }
}
