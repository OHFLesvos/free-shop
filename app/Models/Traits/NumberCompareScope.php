<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait NumberCompareScope
{
    public function scopeWhereNumberCompare(Builder $qry, string $field, string $value)
    {
        $qry->where(DB::raw('TRIM(LEADING \'0\' FROM (REGEXP_REPLACE(' . $field . ', \'[^0-9]+\', \'\')))'), ltrim(preg_replace('/[^0-9]/', '', $value), '0'));
    }
}
