<?php

namespace App\Models;

use Dyrynda\Database\Support\NullableFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    use NullableFields;

    protected $nullable = [
        'description',
        'customer_limit',
    ];

    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }

    public function imageUrl($width, $height)
    {
        return 'https://picsum.photos/seed/' . md5($this->name) . '/' . $width . '/' . $height;
    }
}
