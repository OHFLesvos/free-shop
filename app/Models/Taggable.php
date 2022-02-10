<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphPivot;

class Taggable extends MorphPivot
{
    protected $table = "taggables";

    public static function boot()
    {
        parent::boot();
        static::deleted(function ($item) {
            if (self::where('tag_id', $item->tag_id)->count() == 0) {
                Tag::destroy($item->tag_id);
            }
        });
    }
}
