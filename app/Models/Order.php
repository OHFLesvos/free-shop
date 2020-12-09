<?php

namespace App\Models;

use Dyrynda\Database\Support\NullableFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    use NullableFields;

    protected $nullable = [
        'remarks',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
