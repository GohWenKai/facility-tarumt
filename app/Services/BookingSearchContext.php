<?php

namespace App\Services;

use App\Models\Booking;
use App\Filters\StatusFilter;
use App\Filters\DateFilter;

class BookingSearchContext
{
    public function applyFilters(array $filters)
    {
        // 1. Start the Query
        $query = Booking::query()
            ->where('user_id', auth()->id())
            ->with('facility');

        // 2. Define Available Strategies
        $strategies = [
            'status' => new StatusFilter(),
            'date'   => new DateFilter(),
        ];

        // 3. Loop through filters and apply Strategy if exists
        foreach ($filters as $key => $value) {
            if (array_key_exists($key, $strategies) && !empty($value)) {
                $strategies[$key]->apply($query, $value);
            }
        }

        return $query;
    }
}