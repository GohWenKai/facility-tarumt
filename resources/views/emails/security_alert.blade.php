<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Alert</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 20px;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        .header {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            padding: 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: repeating-linear-gradient(
                45deg,
                transparent,
                transparent 10px,
                rgba(255, 255, 255, 0.05) 10px,
                rgba(255, 255, 255, 0.05) 20px
            );
            animation: slide 20s linear infinite;
        }
        
        @keyframes slide {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }
        
        .alert-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            position: relative;
            z-index: 1;
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 0 0 10px rgba(255, 255, 255, 0);
            }
        }
        
        .header h1 {
            color: #ffffff;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }
        
        .severity-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            z-index: 1;
        }
        
        .severity-high {
            background: rgba(255, 255, 255, 0.3);
            color: #ffffff;
            border: 2px solid rgba(255, 255, 255, 0.5);
        }
        
        .severity-medium {
            background: rgba(251, 191, 36, 0.3);
            color: #ffffff;
            border: 2px solid rgba(251, 191, 36, 0.5);
        }
        
        .content {
            padding: 40px 30px;
        }
        
        .alert-message {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border-left: 4px solid #ef4444;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .alert-message p {
            color: #991b1b;
            font-size: 16px;
            line-height: 1.6;
            font-weight: 500;
        }
        
        .details-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .detail-item {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        
        .detail-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 6px;
        }
        
        .detail-value {
            font-size: 14px;
            color: #0f172a;
            font-weight: 600;
            font-family: 'SF Mono', 'Consolas', monospace;
        }
        
        .action-required {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            border-left: 4px solid #f59e0b;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .action-required h3 {
            color: #92400e;
            font-size: 14px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .action-required p {
            color: #78350f;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }
        
        .btn {
            display: inline-block;
            padding: 14px 28px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            text-align: center;
            transition: all 0.3s ease;
            flex: 1;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
        }
        
        .btn-primary:hover {
            box-shadow: 0 8px 20px rgba(239, 68, 68, 0.6);
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #ffffff;
            color: #64748b;
            border: 2px solid #e2e8f0;
        }
        
        .btn-secondary:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }
        
        .footer {
            background: #f8fafc;
            padding: 25px 30px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
        }
        
        .footer p {
            color: #64748b;
            font-size: 13px;
            line-height: 1.6;
        }
        
        .footer strong {
            color: #0f172a;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="alert-icon">🚨</div>
            <h1>Security Alert Detected</h1>
            <span class="severity-badge severity-{{ $alert['severity'] }}">
                {{ strtoupper($alert['severity']) }} PRIORITY
            </span>
        </div>
        
        <!-- Content -->
        <div class="content">
            <!-- Alert Message -->
            <div class="alert-message">
                <p><strong>Alert Type:</strong> {{ str_replace('_', ' ', ucwords($alert['type'], '_')) }}</p>
                <p style="margin-top: 10px;">{{ $alert['message'] }}</p>
            </div>
            
            <!-- Details Grid -->
            <div class="details-grid">
                <div class="detail-item">
                    <div class="detail-label">User</div>
                    <div class="detail-value">{{ $log->user->name ?? 'System' }}</div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Action</div>
                    <div class="detail-value">{{ ucfirst($log->action) }}</div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">IP Address</div>
                    <div class="detail-value">{{ $log->ip_address }}</div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Timestamp</div>
                    <div class="detail-value">{{ $log->created_at->format('M d, Y H:i:s') }}</div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Model</div>
                    <div class="detail-value">{{ class_basename($log->model_type) }}</div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Model ID</div>
                    <div class="detail-value">#{{ $log->model_id }}</div>
                </div>
            </div>
            
            <!-- Action Required -->
            @if(isset($alert['action_required']))
            <div class="action-required">
                <h3>⚠️ Action Required</h3>
                <p>{{ $alert['action_required'] }}</p>
            </div>
            @endif
            
            <!-- Action Buttons -->
            <div class="button-group">
                <a href="{{ route('admin.audit_logs.index') }}" class="btn btn-primary">
                    View Audit Logs
                </a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    Manage Users
                </a>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p><strong>TARUMT Facility Booking System</strong></p>
            <p>Security Monitoring & Compliance</p>
            <p style="margin-top: 10px; font-size: 11px;">
                This is an automated security alert. Please do not reply to this email.
            </p>
        </div>
    </div>
</body>
</html>
