<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class BlockedPhoneNumber extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::creating(function (BlockedPhoneNumber $model) {
            $model->user()->associate(Auth::user());
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
