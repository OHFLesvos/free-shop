<?php

namespace App\Models;

use App\Models\Traits\NumberCompareScope;
use Dyrynda\Database\Support\NullableFields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Order extends Model implements Auditable
{
    use HasFactory;
    use NullableFields;
    use NumberCompareScope;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'ip_address',
        'user_agent',
        'remarks',
    ];

    protected $nullable = [
        'remarks',
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
        $qry->whereIn('status', ['new', 'ready']);
    }

    public function scopeCompleted(Builder $qry)
    {
        $qry->where('status', 'completed');
    }

    public function scopeCancelled(Builder $qry)
    {
        $qry->where('status', 'cancelled');
    }

    public function scopeFilter(Builder $qry, string $filter)
    {
        $qry->where('id', is_numeric($filter) ? $filter : 0)
            ->orWhereHas('customer', function ($cqry) use ($filter) {
                $cqry->where('name', 'LIKE', '%' . $filter . '%')
                    ->orWhere(fn ($inner) => $inner->whereNumberCompare('id_number', $filter))
                    ->orWhere(fn ($inner) => $inner->whereNumberCompare('phone', $filter));
            })
            ->orWhere('remarks', 'LIKE', '%' . $filter . '%');
    }
}
