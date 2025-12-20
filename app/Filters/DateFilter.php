<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class DateFilter implements FilterStrategy
{
    public function apply(Builder $query, $value)
    {
        if ($value) {
            return $query->whereDate('start_time', $value);
        }
        return $query;
    }
}