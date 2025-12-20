<?php

/**
 * Audit Log Controller
 * Author: [Your Name Here]
 * 
 * Purpose: Display audit logs in admin panel for accountability and security monitoring
 * Design Pattern: MVC Pattern
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use App\Services\SuspiciousActivityDetector;
use App\Mail\SecurityAlertMail;
use Illuminate\Support\Facades\Mail;

class AuditLogController extends Controller
{
    /**
     * Display audit logs with filters and pagination
     */
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by model type
        if ($request->filled('model_type')) {
            $query->where('model_type', 'like', '%' . $request->model_type . '%');
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(20);

        // Get distinct model types for filter dropdown
        $modelTypes = AuditLog::select('model_type')
            ->distinct()
            ->pluck('model_type')
            ->map(function($type) {
                return class_basename($type);
            });

        return view('admin.audit_logs.index', compact('logs', 'modelTypes'));
    }

    /**
     * Get new audit logs since a specific ID (for real-time polling)
     */
    public function getNewLogs(Request $request)
    {
        $sinceId = $request->input('since_id', 0);
        
        $newLogs = AuditLog::with('user')
            ->where('id', '>', $sinceId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($log) {
                return [
                    'id' => $log->id,
                    'user_name' => $log->user?->name ?? 'System',
                    'user_role' => $log->user?->role ?? 'system',
                    'action' => $log->action,
                    'model' => class_basename($log->model_type),
                    'model_id' => $log->model_id,
                    'ip_address' => $log->ip_address,
                    'created_at' => $log->created_at->format('M d, Y'),
                    'created_time' => $log->created_at->format('H:i:s'),
                    'relative_time' => $log->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'success' => true,
            'logs' => $newLogs,
            'latest_id' => $newLogs->isNotEmpty() ? $newLogs->first()['id'] : $sinceId,
        ]);
    }

    /**
     * Check for suspicious activity and send alerts
     */
    public function checkSuspiciousActivity(AuditLog $log)
    {
        $detector = new SuspiciousActivityDetector();
        $alerts = $detector->detectSuspiciousActivity($log);

        foreach ($alerts as $alert) {
            // Log to security channel
            $detector->logSecurityAlert($alert, $log);

            // Send email alert for high severity
            if ($alert['severity'] === 'high') {
                try {
                    Mail::to(config('mail.admin_email', env('MAIL_FROM_ADDRESS')))
                        ->send(new SecurityAlertMail($alert, $log));
                } catch (\Exception $e) {
                    \Log::error('Failed to send security alert email', [
                        'error' => $e->getMessage(),
                        'alert' => $alert
                    ]);
                }
            }
        }
    }

    /**
     * Restore a deleted asset from audit log data
     */
    public function restore(Request $request, $id)
    {
        // 1. Validate Password
        $request->validate([
            'password' => 'required|string',
        ]);

        if (!\Illuminate\Support\Facades\Hash::check($request->password, auth()->user()->password)) {
            return response()->json(['success' => false, 'message' => 'Invalid admin password'], 403);
        }

        $log = AuditLog::findOrFail($id);

        // 2. Validate Restoration Eligibility
        if ($log->action !== 'deleted' || $log->model_type !== \App\Models\Asset::class) {
            return response()->json(['success' => false, 'message' => 'This item cannot be restored'], 400);
        }

        if (empty($log->old_values)) {
             return response()->json(['success' => false, 'message' => 'No backup data found for this item'], 400);
        }

        // 3. Restore (Create New Asset)
        try {
            $data = $log->old_values;
            // Remove ID and timestamps to let DB handle them
            unset($data['id'], $data['created_at'], $data['updated_at']);
            
            $asset = \App\Models\Asset::create($data);

            // 4. Log the Restoration
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'restored',
                'model_type' => \App\Models\Asset::class,
                'model_id' => $asset->id,
                'new_values' => $asset->toArray(),
                'ip_address' => request()->ip(),
            ]);

            return response()->json(['success' => true, 'message' => 'Asset restored successfully!']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Restoration failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Export Audit Logs to PDF
     */
    public function exportPdf(Request $request)
    {
        // Reuse the index query logic for consistency
        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        // Apply same filters
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('model_type')) {
            $query->where('model_type', 'like', '%' . $request->model_type . '%');
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->get(); // Get all matching records, no pagination

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.audit_logs.pdf', compact('logs'));
        
        return $pdf->download('audit-log-report-' . now()->format('Y-m-d') . '.pdf');
    }
}

