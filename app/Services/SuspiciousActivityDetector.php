<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class SuspiciousActivityDetector
{
    /**
     * Check for suspicious activity patterns and return alerts
     */
    public function detectSuspiciousActivity(AuditLog $log): array
    {
        $alerts = [];

        // Pattern 1: Bulk deletions (>5 deletes in 10 minutes)
        if ($bulkDeleteAlert = $this->detectBulkDeletions($log)) {
            $alerts[] = $bulkDeleteAlert;
        }

        // Pattern 2: Unusual IP address
        if ($ipAlert = $this->detectUnusualIP($log)) {
            $alerts[] = $ipAlert;
        }

        // Pattern 3: After-hours activity
        if ($afterHoursAlert = $this->detectAfterHoursActivity($log)) {
            $alerts[] = $afterHoursAlert;
        }

        // Pattern 4: Rapid succession actions (potential bot)
        if ($rapidActionsAlert = $this->detectRapidActions($log)) {
            $alerts[] = $rapidActionsAlert;
        }

        return $alerts;
    }

    /**
     * Detect bulk deletions (>5 deletes in 10 minutes)
     */
    private function detectBulkDeletions(AuditLog $log): ?array
    {
        if ($log->action !== 'deleted') {
            return null;
        }

        // Use session instead of cache to avoid database dependency
        $sessionKey = "delete_count_{$log->user_id}";
        $count = session($sessionKey, 0) + 1;
        
        // Store count in session for 10 minutes
        session([$sessionKey => $count]);

        if ($count >= 5) {
            return [
                'severity' => 'high',
                'type' => 'bulk_deletion',
                'message' => "User {$log->user->name} has deleted {$count} items in the last 10 minutes",
                'action_required' => 'Review and potentially freeze account',
            ];
        }

        return null;
    }

    /**
     * Detect unusual IP addresses (not in user's typical IP range)
     */
    private function detectUnusualIP(AuditLog $log): ?array
    {
        if (!$log->user_id) {
            return null;
        }

        // Get user's recent IPs (last 30 days)
        $recentIPs = AuditLog::where('user_id', $log->user_id)
            ->where('created_at', '>=', now()->subDays(30))
            ->where('id', '!=', $log->id)
            ->pluck('ip_address')
            ->unique()
            ->toArray();

        // If this is a new IP and user has history
        if (!empty($recentIPs) && !in_array($log->ip_address, $recentIPs)) {
            return [
                'severity' => 'medium',
                'type' => 'unusual_ip',
                'message' => "User {$log->user->name} accessed from new IP: {$log->ip_address}",
                'action_required' => 'Verify with user if this was expected',
                'previous_ips' => $recentIPs,
            ];
        }

        return null;
    }

    /**
     * Detect after-hours activity (outside 8 AM - 6 PM on weekdays)
     */
    private function detectAfterHoursActivity(AuditLog $log): ?array
    {
        $hour = $log->created_at->hour;
        $isWeekend = $log->created_at->isWeekend();
        $isOutsideHours = $hour < 8 || $hour >= 18;

        if (($isOutsideHours || $isWeekend) && $log->action === 'deleted') {
            return [
                'severity' => 'medium',
                'type' => 'after_hours',
                'message' => "User {$log->user->name} performed deletion outside business hours",
                'timestamp' => $log->created_at->format('Y-m-d H:i:s'),
                'action_required' => 'Review for unauthorized access',
            ];
        }

        return null;
    }

    /**
     * Detect rapid succession actions (>10 actions in 1 minute - potential bot)
     */
    private function detectRapidActions(AuditLog $log): ?array
    {
        $sessionKey = "action_count_{$log->user_id}";
        $count = session($sessionKey, 0) + 1;
        
        // Store count in session
        session([$sessionKey => $count]);

        if ($count >= 10) {
            return [
                'severity' => 'high',
                'type' => 'rapid_actions',
                'message' => "User {$log->user->name} performed {$count} actions in 1 minute (potential bot)",
                'action_required' => 'Enable CAPTCHA or rate limiting for this user',
            ];
        }

        return null;
    }

    /**
     * Get summary of recent suspicious activities
     */
    public function getRecentAlerts(int $hours = 24): array
    {
        $logs = AuditLog::with('user')
            ->where('created_at', '>=', now()->subHours($hours))
            ->orderBy('created_at', 'desc')
            ->get();

        $allAlerts = [];

        foreach ($logs as $log) {
            $alerts = $this->detectSuspiciousActivity($log);
            if (!empty($alerts)) {
                $allAlerts[] = [
                    'log_id' => $log->id,
                    'user' => $log->user?->name ?? 'System',
                    'action' => $log->action,
                    'model' => class_basename($log->model_type),
                    'timestamp' => $log->created_at->format('Y-m-d H:i:s'),
                    'alerts' => $alerts,
                ];
            }
        }

        return $allAlerts;
    }

    /**
     * Log security alert to separate channel
     */
    public function logSecurityAlert(array $alert, AuditLog $log): void
    {
        Log::channel('security')->warning('Security Alert Detected', [
            'alert_type' => $alert['type'],
            'severity' => $alert['severity'],
            'message' => $alert['message'],
            'user_id' => $log->user_id,
            'user_name' => $log->user?->name,
            'ip_address' => $log->ip_address,
            'action' => $log->action,
            'model_type' => $log->model_type,
            'model_id' => $log->model_id,
            'timestamp' => $log->created_at->toDateTimeString(),
        ]);
    }
}
