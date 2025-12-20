<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Approved</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8fafc; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; background: white; }
        .header { background: linear-gradient(135deg, #059669 0%, #10b981 100%); padding: 2rem; text-align: center; }
        .header h1 { color: white; font-size: 1.5rem; margin-bottom: 0.5rem; }
        .header p { color: rgba(255,255,255,0.9); font-size: 0.9rem; }
        .icon { font-size: 3rem; margin-bottom: 1rem; }
        .content { padding: 2rem; }
        .greeting { font-size: 1.1rem; color: #1e293b; margin-bottom: 1rem; }
        .booking-card { background: #f8fafc; border-radius: 12px; padding: 1.5rem; margin: 1.5rem 0; border-left: 4px solid #10b981; }
        .booking-card h3 { color: #1e293b; margin-bottom: 1rem; font-size: 1rem; }
        .detail-row { display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #e2e8f0; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: #64748b; font-size: 0.875rem; }
        .detail-value { color: #1e293b; font-weight: 600; font-size: 0.875rem; }
        .cta-button { display: inline-block; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white; padding: 0.875rem 2rem; border-radius: 8px; text-decoration: none; font-weight: 600; margin: 1rem 0; }
        .footer { background: #f8fafc; padding: 1.5rem; text-align: center; color: #64748b; font-size: 0.8rem; border-top: 1px solid #e2e8f0; }
        .status-badge { display: inline-block; background: #d1fae5; color: #047857; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">✅</div>
            <h1>Booking Approved!</h1>
            <p>Your facility reservation has been confirmed</p>
        </div>
        
        <div class="content">
            <p class="greeting">Hi {{ $booking->user->name }},</p>
            <p>Great news! Your booking request has been <strong>approved</strong> by the administrator.</p>
            
            <div class="booking-card">
                <h3>📋 Booking Details</h3>
                
                <div class="detail-row">
                    <span class="detail-label">Facility</span>
                    <span class="detail-value">{{ $booking->facility->name }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Location</span>
                    <span class="detail-value">{{ $booking->facility->building->name ?? 'Main Campus' }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Date</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($booking->start_time)->format('l, d M Y') }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Time</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Credits Used</span>
                    <span class="detail-value">{{ $booking->total_cost }} pts</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Status</span>
                    <span class="status-badge">Approved</span>
                </div>
            </div>
            
            <p>You can download your booking ticket from your profile or booking history page.</p>
            
            <center>
                <a href="{{ url('/history') }}" class="cta-button">View My Bookings</a>
            </center>
            
            <p style="margin-top: 1.5rem; color: #64748b; font-size: 0.875rem;">
                <strong>Reminder:</strong> Please arrive on time for your booking. Late arrivals may result in cancellation.
            </p>
        </div>
        
        <div class="footer">
            <p>This is an automated message from TARUMT Facility Booking System.</p>
            <p>© {{ date('Y') }} TARUMT FBS. All rights reserved.</p>
        </div>
    </div>
</body>
</html>