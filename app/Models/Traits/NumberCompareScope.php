<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait NumberCompareScope
{
    public function scopeWhereNumberCompare(Builder $qry, string $field, string $value): Builder
    {
        return $qry->whereRaw(
            'TRIM(LEADING \'0\' FROM (REGEXP_REPLACE(' . $field . ', \'[^0-9]+\', \'\'))) = ?',
            [ltrim(preg_replace('/[^0-9]/', '', $value), '0')]
        );
    }
}
