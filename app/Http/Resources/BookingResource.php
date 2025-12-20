<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'start_time' => $this->start_time, // format this if needed, e.g. ->format('Y-m-d H:i')
            'end_time' => $this->end_time,
            // Eager loaded relationship
            'facility' => $this->whenLoaded('facility', function () {
                return [
                    'id' => $this->facility->id,
                    'name' => $this->facility->name,
                ];
            }),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}