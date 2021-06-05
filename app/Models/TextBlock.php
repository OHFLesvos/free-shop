<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Translatable\HasTranslations;

class TextBlock extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasTranslations;

    protected $fillable = [
        'name',
        'content',
    ];

    /**
     * The attributes that should have translations
     *
     * @var array
     */
    public $translatable = [
        'content',
    ];

    public function getRouteKeyName()
    {
        return 'name';
    }
}
