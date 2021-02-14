<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Translatable\HasTranslations;

class TextBlock extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use HasTranslations;

    protected $fillable = [
        'name',
        'content',
    ];

    public $translatable = [
        'content',
    ];

    public function getRouteKeyName()
    {
        return 'name';
    }

    public static function getAsMarkdown(string $name)
    {
        $textBlock = TextBlock::whereName($name)->first();
        if ($textBlock != null && filled($textBlock->content)) {
            return Str::of($textBlock->content)->markdown();
        }
        return null;
    }
}
