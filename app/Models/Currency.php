<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'initial_value',
        'top_up_amount',
        'top_up_maximum',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function customers(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class)
            ->withPivot('value');
    }
}
