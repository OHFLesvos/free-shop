<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

class Tag extends Model
{
    use HasFactory;
    use Sluggable;
    use SluggableScopeHelpers;

    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    public function customers()
    {
        return $this->morphedByMany(Customer::class, 'taggable');
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
