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

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function getIsOpenAttribute()
    {
        return in_array($this->status, ['new', 'ready']);
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
                $cqry->where('name', 'LIKE', '%' . $filter . '%')
                    ->orWhere('id_number', 'LIKE', $filter . '%')
                    ->orWhere(fn ($inner) => $inner->whereNumberCompare('id_number', $filter))
                    ->orWhere('phone', 'LIKE', $filter . '%')
                    ->orWhere(fn ($inner) => $inner->whereNumberCompare('phone', $filter));
            })
            ->orWhere('remarks', 'LIKE', '%' . $filter . '%');
    }
}
