<?php

namespace App\Models;

use Dyrynda\Database\Support\NullableFields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    use NullableFields;

    protected $nullable = [
        'remarks',
        'delivered_at',
    ];

    protected $dates = [
        'delivered_at',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function scopeOpen(Builder $builder)
    {
        $builder->whereNull('delivered_at');
    }

    public function scopeDone(Builder $builder)
    {
        $builder->whereNotNull('delivered_at');
    }
}
