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

        // DEBUG STEP 1: Check if Laravel sees the file
        if (!Storage::exists($path)) {
            dd("ERROR: File not found!", [
                'Looking for' => $path,
                'Real Path would be' => Storage::path($path),
                'Does it exist?' => file_exists(Storage::path($path))
            ]);
        }

        // DEBUG STEP 2: Check if we can read content
        $xmlString = Storage::get($path);
        if (empty($xmlString)) {
            dd("ERROR: File exists but is empty!", $path);
        }

        // DEBUG STEP 3: Check if XML loads
        $xml = @simplexml_load_string($xmlString);
        if ($xml === false) {
            dd("ERROR: XML Content is corrupted/invalid.", $xmlString);
        }

        // DEBUG STEP 4: Check Namespaces and Nodes
        $ns = 'http://schemas.xmlsoap.org/soap/envelope/';
        $xml->registerXPathNamespace('soap', $ns);
        $nodes = $xml->xpath('//soap:Body/*');

        if (empty($nodes)) {
            dd("ERROR: File loaded, but no nodes found inside <soap:Body>.", [
                'Raw Content' => $xmlString,
                'XPath Attempted' => '//soap:Body/*'
            ]);
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