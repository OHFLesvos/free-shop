<?php

namespace App\Models;

use App\Models\Traits\NumberCompareScope;
use Dyrynda\Database\Support\NullableFields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'costs',
        'ip_address',
        'user_agent',
        'remarks',
    ];

    protected $nullable = [
        'remarks',
    ];

    public const STATUSES = [
        'new',
        'ready',
        'completed',
        'cancelled',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('quantity');
    }

    public function numberOfProducts()
    {
        return $this->products()->sum('quantity');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function getIsOpenAttribute()
    {
        return in_array($this->status, ['new', 'ready']);
    }

    public function scopeRegisteredInDateRange(Builder $qry, ?string $start, ?string $end)
    {
        if (filled($start) && filled($end)) {
            $qry->whereDate('created_at', '>=', $start)
                ->whereDate('created_at', '<=', $end);
        }
    }

    public function scopeCompletedInDateRange(Builder $qry, ?string $start, ?string $end)
    {
        if (filled($start) && filled($end)) {
            $qry->status('completed')
                ->whereDate('completed_at', '>=', $start)
                ->whereDate('completed_at', '<=', $end);
        } else {
            $qry->status('completed');
        }
    }

    public function scopeOpen(Builder $qry)
    {
        $qry->whereIn('status', ['new', 'ready']);
    }

    public function scopeStatus(Builder $qry, string $status)
    {
        assert(in_array($status, self::STATUSES));

        $qry->where('status', $status);
    }

    public function scopeFilter(Builder $qry, string $filter)
    {
        $qry->where('id', is_numeric($filter) ? $filter : 0)
            ->orWhereHas('customer', function ($cqry) use ($filter) {
                $cqry->where(DB::raw('LOWER(name)'), 'LIKE', '%' . strtolower($filter) . '%')
                    ->orWhere('id_number', 'LIKE', $filter . '%')
                    ->orWhere(fn ($inner) => $inner->whereNumberCompare('id_number', $filter))
                    ->orWhere('phone', 'LIKE', $filter . '%')
                    ->orWhere(fn ($inner) => $inner->whereNumberCompare('phone', $filter));
            })
            ->orWhere('remarks', 'LIKE', '%' . $filter . '%');
    }
}
