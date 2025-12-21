<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * IFA Compliance Middleware
 * 
 * Ensures all API requests/responses meet IFA (Integration Framework Architecture) requirements:
 * - Request MUST have: timestamp (YYYY-MM-DD HH:MM:SS), requestID (unique string)
 * - Response MUST have: status (S/F/E format), timestamp (YYYY-MM-DD HH:MM:SS)
 */
class IFACompliance
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Validate IFA request fields for POST/PUT/PATCH requests
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH'])) {
            $errors = [];
            
            if (!$request->has('requestID') || empty($request->input('requestID'))) {
                $errors['requestID'] = 'The requestID field is required.';
            }
            
            if (!$request->has('timestamp') || empty($request->input('timestamp'))) {
                $errors['timestamp'] = 'The timestamp field is required.';
            } else {
                // Validate timestamp format (YYYY-MM-DD HH:MM:SS)
                $timestamp = $request->input('timestamp');
                if (!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $timestamp)) {
                    $errors['timestamp'] = 'The timestamp must be in YYYY-MM-DD HH:MM:SS format.';
                }
            }
            
            if (!empty($errors)) {
                return response()->json([
                    'status' => 'E',
                    'timestamp' => now()->format('Y-m-d H:i:s'),
                    'message' => 'IFA validation failed',
                    'errors' => $errors
                ], 422);
            }
        }

        // Process the request
        $response = $next($request);

        // Transform response to IFA format
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $data = $response->getData(true);
            
            // Add timestamp if not present
            if (!isset($data['timestamp'])) {
                $data['timestamp'] = now()->format('Y-m-d H:i:s');
            }
            
            // Convert status to IFA format (S/F/E)
            if (isset($data['status'])) {
                $data['status'] = match(strtolower($data['status'])) {
                    'success' => 'S',
                    'error' => 'E',
                    'fail', 'failed' => 'F',
                    default => $data['status'], // Keep if already S/F/E
                };
            } else {
                // Determine status from HTTP code
                $httpCode = $response->getStatusCode();
                $data['status'] = match(true) {
                    $httpCode >= 200 && $httpCode < 300 => 'S',
                    $httpCode >= 400 && $httpCode < 500 => 'F',
                    default => 'E',
                };
            }
            
            $response->setData($data);
        }

        return $response;
    }
}
