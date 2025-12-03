<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class StatusFilter implements FilterStrategy
{
    public function apply(Builder $query, $value)
    {
        if ($value && $value !== 'All') {
            return $query->where('status', $value);
        }
        return $query;
    }
}