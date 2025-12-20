<?php

namespace App\Adapters;

use Illuminate\Support\Collection;

class ChartJsAdapter
{
    /**
     * Adapts a list of Booking objects into Chart.js format
     * Input: Collection of [facility_id, total, facility->name]
     * Output: ['labels' => ['Gym', 'Lab'], 'data' => [50, 20]]
     */
    public function adaptPopularFacilities(Collection $data): array
    {
        return [
            'labels' => $data->map(function ($item) {
                // Handle cases where facility might be deleted (null check)
                return $item->facility->name ?? 'Unknown Facility';
            })->toArray(),
            
            'data'   => $data->pluck('total')->toArray(),
        ];
    }
}