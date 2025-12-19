<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use SimpleXMLElement;

class AssetHistoryService
{
    public function getHistory(int $id): array
    {
        // 1. Define the Path
        $path = "assets/{$id}.xml";
        $xmlRecords = [];

        // 1. Check if file exists - return empty array if not found
        if (!Storage::exists($path)) {
            \Log::warning("Asset history XML file not found: {$path}");
            return []; // Return empty history instead of crashing
        }

        // 2. Check if we can read content
        $xmlString = Storage::get($path);
        if (empty($xmlString)) {
            \Log::warning("Asset history XML file is empty: {$path}");
            return [];
        }

        // 3. Check if XML loads
        $xml = @simplexml_load_string($xmlString);
        if ($xml === false) {
            \Log::error("Asset history XML is corrupted: {$path}");
            return [];
        }

        // 4. Check Namespaces and Nodes
        $ns = 'http://schemas.xmlsoap.org/soap/envelope/';
        $xml->registerXPathNamespace('soap', $ns);
        $nodes = $xml->xpath('//soap:Body/*');

        if (empty($nodes)) {
            \Log::warning("Asset history XML has no records: {$path}");
            return [];
        }

        // If we get here, everything is working, proceed to extract
        foreach ($nodes as $node) {
            $node->registerXPathNamespace('soap', $ns);

            $get = function($field) use ($node) {
                $result = $node->xpath("soap:{$field}");
                return (!empty($result)) ? (string)$result[0] : '';
            };

            $date = $get('logged_at');
            if (empty($date)) $date = $get('generated_at');
            if (empty($date)) $date = $get('created_at');

            $xmlRecords[] = [
                'name'             => $get('name'),
                'type'             => $get('type'),
                'serial_number'    => $get('serial_number'),
                'condition'        => $get('condition'),
                'maintenance_note' => $get('maintenance_note'),
                'created_at'       => $date ?: 'N/A',
                'action'           => $node->getName() == 'asset_update' ? 'Update' : 'Create'
            ];
        }

        return array_reverse($xmlRecords);
    }
}