<?php

namespace App\Models;

use App\Models\Traits\NumberCompareScope;
use Dyrynda\Database\Support\NullableFields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    use NullableFields;
    use NumberCompareScope;

    protected $fillable = [
        'ip_address',
        'user_agent',
        'remarks',
    ];

    protected $nullable = [
        'remarks',
        'completed_at',
        'cancelled_at',
    ];

    protected $dates = [
        'completed_at',
        'cancelled_at',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('quantity');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function scopeOpen(Builder $qry)
    {
        $qry->whereNull('completed_at')
            ->whereNull('cancelled_at');
    }

    public function scopeCompleted(Builder $qry)
    {
        $qry->whereNotNull('completed_at');
    }

    public function scopeCancelled(Builder $qry)
    {
        $qry->whereNotNull('cancelled_at');
    }

    public function scopeFilter(Builder $qry, string $filter)
    {
        $qry->where('id', $filter)
            ->orWhereHas('customer', function ($cqry) use ($filter) {
                $cqry->where('name', 'LIKE', '%' . $filter . '%')
                    ->orWhere(fn ($inner) => $inner->whereNumberCompare('id_number', $filter))
                    ->orWhere(fn ($inner) => $inner->whereNumberCompare('phone', $filter));
            })
            ->orWhere('remarks', 'LIKE', '%' . $filter . '%');
    }
}
