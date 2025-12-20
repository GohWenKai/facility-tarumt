<?php

namespace App\Adapters;

use Illuminate\Http\Request;

class SearchQueryAdapter
{
    public function parseCriteria(Request $request): array
    {
        $content = $request->getContent();
        
        // Use @ to suppress warnings if content isn't XML
        $xml = @simplexml_load_string($content);

        if ($xml) {
            // XML DETECTED
            return [
                'type' => 'xml',
                'keyword' => (string)$xml->keyword,
                'min_capacity' => (int)$xml->min_capacity,
                'building_id' => (int)$xml->building_id,
                'requires_projector' => ((string)$xml->requires_projector === 'true'),
            ];
        }

        // STANDARD SEARCH
        return [
            'type' => 'standard',
            'keyword' => $request->get('search') ?? $request->get('q'),
            'min_capacity' => null,
            'building_id' => null,
            'requires_projector' => false,
        ];
    }
}