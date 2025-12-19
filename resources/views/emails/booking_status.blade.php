<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 10px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            padding: 20px;
            background-color: #f9f9f9;
        }
        .status {
            font-weight: bold;
            font-size: 18px;
            color: #007bff;
        }
        .approved { color: #28a745; }
        .rejected { color: #dc3545; }
        .pending { color: #ffc107; }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #777;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Booking Status Update</h2>
        </div>
        <div class="content">
            <p>Dear {{ $booking->user->name }},</p>
            
            <p>Your booking status has been updated:</p>
            
            <p class="status {{ strtolower($status) }}">
                Status: {{ ucfirst($status) }}
            </p>
            
            <p><strong>Booking Details:</strong></p>
            <ul>
                <li><strong>Facility:</strong> {{ $booking->facility->name }}</li>
                <li><strong>Date:</strong> {{ $booking->booking_date }}</li>
                <li><strong>Time:</strong> {{ $booking->start_time }} - {{ $booking->end_time }}</li>
            </ul>
            
            @if($message)
            <p><strong>Message:</strong><br>{{ $message }}</p>
            @endif
            
            @if($status === 'approved')
            <p style="color: green;">✓ Your booking has been approved! You can download your ticket from your booking history.</p>
            @elseif($status === 'rejected')
            <p style="color: red;">✗ Your booking has been rejected. Your credits have been refunded.</p>
            @endif
        </div>
        <div class="footer">
            <p>This is an automated email. Please do not reply.</p>
            <p>&copy; {{ date('Y') }} TARUMT Facility Booking System</p>
        </div>
    </div>
</body>
</html>
