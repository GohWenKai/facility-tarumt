<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

interface FilterStrategy
{
    public function apply(Builder $query, $value);
}