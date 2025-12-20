@extends('layouts.admin')

@section('content')
<style>
/* 
   REAL WORLD IT DESIGN - JUSTIFICATION
   ------------------------------------
   1. Purpose: Provide immediate, unambiguous feedback on the check-in attempt.
   2. Hierarchy: Status Icon -> Status Text -> Booking Details -> Next Action.
   3. Color Theory:
      - Success (Green-500): Universally recognized signal for "Go".
      - Error (Red-500): Universally recognized signal for "Stop/Attention".
      - Brand (Blue-600): Consistent identifying color for primary actions.
   4. HCI Principle (Visibility): The status is the largest element, impossible to miss.
   5. HCI Principle (Recovery): "Scan Next" is prominent to allow rapid processing of a queue.
*/

.result-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    overflow: hidden;
    margin-top: 2rem;
    animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

.result-header {
    background: #f8fafc;
    padding: 3rem 1.5rem;
    text-align: center;
    border-bottom: 1px solid #e2e8f0;
}

.status-icon-wrapper {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 96px;
    height: 96px;
    border-radius: 50%;
    margin-bottom: 1.5rem;
    animation: scaleIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
}

/* Success State Styles */
.status-success .status-icon-wrapper {
    background-color: #d1fae5; /* Green-100 */
    color: #059669; /* Green-600 */
}
.status-success .status-title { color: #059669; }

/* Error State Styles */
.status-error .status-icon-wrapper {
    background-color: #fee2e2; /* Red-100 */
    color: #dc2626; /* Red-600 */
}
.status-error .status-title { color: #dc2626; }

.status-title {
    font-size: 1.875rem;
    font-weight: 800;
    letter-spacing: -0.025em;
    margin-bottom: 0.5rem;
}

.status-message {
    color: #64748b; /* Slate-500 */
    font-size: 1.1rem;
    max-width: 400px;
    margin: 0 auto;
    line-height: 1.5;
}

.result-body {
    padding: 2rem;
    background: #ffffff;
}

.details-grid {
    display: grid;
    gap: 1.5rem;
    max-width: 500px;
    margin: 0 auto;
}

.detail-item {
    display: flex;
    flex-direction: column;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 12px;
    border: 1px solid #f1f5f9;
}

.detail-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #64748b;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.detail-value {
    font-size: 1rem;
    font-weight: 600;
    color: #1e293b;
}

.result-footer {
    padding: 1.5rem 2rem;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    text-align: center;
}

.btn-scan-next {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.875rem 2rem;
    background-color: #2563eb; /* Blue-600 */
    color: white;
    font-weight: 600;
    border-radius: 9999px; /* Pill shape */
    transition: all 0.2s;
    text-decoration: none;
    box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
}

.btn-scan-next:hover {
    background-color: #1d4ed8; /* Blue-700 */
    transform: translateY(-1px);
    box-shadow: 0 6px 8px -1px rgba(37, 99, 235, 0.3);
    color: white;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes scaleIn {
    from { transform: scale(0.8); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}
</style>

<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="result-card {{ $status === 'success' ? 'status-success' : 'status-error' }}">
                
                <!-- 1. Header with Visual Feedback -->
                <div class="result-header">
                    <div class="status-icon-wrapper">
                        @if($status === 'success')
                            <i class="bi bi-check-lg" style="font-size: 3.5rem;"></i>
                        @else
                            <i class="bi bi-x-lg" style="font-size: 3.5rem;"></i>
                        @endif
                    </div>
                    <h1 class="status-title">
                        {{ $status === 'success' ? 'Check-in Confirmed' : 'Check-in Failed' }}
                    </h1>
                    <p class="status-message">
                        {{ $message }}
                    </p>
                </div>

                <!-- 2. Booking Context (Only shown if booking exists) -->
                @if($booking)
                <div class="result-body">
                    <div class="details-grid">
                        <div class="detail-item">
                            <span class="detail-label">Student Name</span>
                            <span class="detail-value text-primary">
                                <i class="bi bi-person-fill me-1"></i> {{ $booking->user->name }}
                            </span>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="detail-item h-100">
                                    <span class="detail-label">Facility</span>
                                    <span class="detail-value">{{ $booking->facility->name }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="detail-item h-100">
                                    <span class="detail-label">Booking ID</span>
                                    <span class="detail-value" style="font-family: monospace;">#{{ substr($booking->id, 0, 8) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="detail-item">
                            <span class="detail-label">Scheduled Time</span>
                            <span class="detail-value">
                                {{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} - 
                                {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}
                            </span>
                        </div>
                    </div>
                </div>
                @endif

                <!-- 3. Primary Action -->
                <div class="result-footer">
                    <a href="{{ route('admin.scanner') }}" class="btn-scan-next">
                        <i class="bi bi-qr-code-scan me-2"></i> Scan Next Ticket
                    </a>
                    <div class="mt-3">
                        <a href="{{ route('dashboard') }}" class="text-muted small text-decoration-none">
                            <i class="bi bi-arrow-left"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
