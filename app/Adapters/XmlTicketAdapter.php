<?php

namespace App\Adapters;

use Illuminate\Support\Facades\Storage;

class XmlTicketAdapter
{
    /**
     * Adapts a SOAP XML file into a standard PHP Array
     */
    public function parseTicket(string $bookingId): array
    {
        $path = "xml/{$bookingId}.xml";

        if (!Storage::exists($path)) {
            return []; // Or throw exception
        }

        $xmlString = Storage::get($path);
        
        // Load XML
        $xml = simplexml_load_string($xmlString);
        
        // Register SOAP Namespace
        $xml->registerXPathNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');

        // Helper to extract
        $getValue = function($field) use ($xml) {
            $result = $xml->xpath("//soap:ticket/soap:{$field}");
            return (!empty($result)) ? (string)$result[0] : 'N/A';
        };

        // Return standardized array
        return [
            'id'           => $getValue('id'),
            'name'         => $getValue('student_name'),
            'role'         => $getValue('role'),
            'facility'     => $getValue('facility'),
            'time'         => $getValue('start_time') . ' to ' . $getValue('end_time'),
            'generated_at' => $getValue('generated_at'),
        ];
    }
}