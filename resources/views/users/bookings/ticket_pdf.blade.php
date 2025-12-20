<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Facility Booking Confirmation</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 20px;
            background: #fff;
            color: #333;
            font-size: 12px;
        }
        
        .ticket {
            max-width: 650px;
            margin: 0 auto;
            border: 2px solid #2c5282;
        }
        
        /* Header */
        .header {
            background: #2c5282;
            color: white;
            padding: 15px 20px;
            display: table;
            width: 100%;
            box-sizing: border-box;
        }
        
        .header-left {
            display: table-cell;
            vertical-align: middle;
            width: 70%;
        }
        
        .header-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
        }
        
        .header h1 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
        }
        
        .header p {
            margin: 3px 0 0 0;
            font-size: 10px;
            opacity: 0.9;
        }
        
        .booking-ref {
            background: white;
            color: #2c5282;
            padding: 8px 12px;
            font-weight: bold;
            font-size: 11px;
        }
        
        /* Main Content */
        .content {
            padding: 20px;
        }
        
        /* Status Banner */
        .status-banner {
            background: #c6f6d5;
            border: 1px solid #9ae6b4;
            color: #22543d;
            padding: 10px 15px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
        }
        
        /* Two Column Layout */
        .two-col {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        
        .col {
            display: table-cell;
            vertical-align: top;
            width: 50%;
            padding-right: 15px;
        }
        
        .col:last-child {
            padding-right: 0;
            padding-left: 15px;
        }
        
        /* Section */
        .section {
            margin-bottom: 15px;
        }
        
        .section-title {
            font-size: 9px;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .section-value {
            font-size: 13px;
            font-weight: bold;
            color: #1a202c;
        }
        
        .section-value.large {
            font-size: 16px;
        }
        
        /* Highlight Box */
        .highlight-box {
            background: #ebf8ff;
            border-left: 4px solid #2c5282;
            padding: 12px 15px;
            margin: 15px 0;
        }
        
        .highlight-box .label {
            font-size: 9px;
            color: #2c5282;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        
        .highlight-box .value {
            font-size: 14px;
            font-weight: bold;
            color: #1a202c;
        }
        
        /* Divider */
        hr {
            border: none;
            border-top: 1px dashed #e2e8f0;
            margin: 20px 0;
        }
        
        /* Instructions */
        .instructions {
            background: #fffaf0;
            border: 1px solid #fbd38d;
            padding: 12px 15px;
            margin: 15px 0;
        }
        
        .instructions-title {
            font-weight: bold;
            color: #c05621;
            margin-bottom: 8px;
            font-size: 11px;
        }
        
        .instructions ul {
            margin: 0;
            padding-left: 18px;
            color: #744210;
        }
        
        .instructions li {
            margin-bottom: 4px;
            font-size: 10px;
        }
        
        /* Footer */
        .footer {
            background: #f7fafc;
            padding: 15px 20px;
            border-top: 1px solid #e2e8f0;
        }
        
        .footer-row {
            display: table;
            width: 100%;
        }
        
        .footer-col {
            display: table-cell;
            vertical-align: top;
        }
        
        .footer-col.left {
            width: 60%;
        }
        
        .footer-col.right {
            text-align: right;
        }
        
        .contact-title {
            font-size: 9px;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 3px;
        }
        
        .contact-value {
            font-size: 10px;
            color: #4a5568;
        }
        
        .validity {
            font-size: 9px;
            color: #e53e3e;
            font-weight: bold;
        }
        
        .issued {
            font-size: 9px;
            color: #a0aec0;
            margin-top: 5px;
        }
        
        /* Terms */
        .terms {
            font-size: 8px;
            color: #a0aec0;
            margin-top: 15px;
            line-height: 1.4;
        }
    </style>
</head>
<body>
    <div class="ticket">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <h1>FACILITY BOOKING CONFIRMATION</h1>
                <p>Tunku Abdul Rahman University of Management and Technology</p>
            </div>
            <div class="header-right">
                <div class="booking-ref">{{ strtoupper(substr($data['id'], 0, 8)) }}</div>
            </div>
        </div>
        
        <!-- Content -->
        <div class="content">
            <!-- Status -->
            <div class="status-banner">
                BOOKING CONFIRMED - APPROVED FOR USE
            </div>
            
            <!-- User & Facility Info -->
            <div class="two-col">
                <div class="col">
                    <div class="section">
                        <div class="section-title">Booked By</div>
                        <div class="section-value">{{ $data['name'] }}</div>
                    </div>
                    <div class="section">
                        <div class="section-title">User Type</div>
                        <div class="section-value">{{ ucfirst($data['role']) }}</div>
                    </div>
                </div>
                <div class="col">
                    <div class="section">
                        <div class="section-title">Facility Reserved</div>
                        <div class="section-value large">{{ $data['facility'] }}</div>
                    </div>
                </div>
            </div>
            
            <!-- Schedule Highlight -->
            <div class="highlight-box">
                <div class="label">Authorized Usage Period</div>
                <div class="value">{{ $data['time'] }}</div>
            </div>
            
            <hr>
            
            <!-- Check-in Instructions -->
            <div class="instructions">
                <div class="instructions-title">CHECK-IN PROCEDURE</div>
                <ul>
                    <li>Arrive at least <strong>10 minutes</strong> before your scheduled time</li>
                    <li>Present this confirmation at the <strong>facility reception desk</strong></li>
                    <li>Staff will verify your booking and provide access</li>
                    <li>Late arrivals beyond <strong>15 minutes</strong> may result in booking cancellation</li>
                    <li>Report any facility issues to reception immediately</li>
                </ul>
            </div>
            
            <hr>
            
            <!-- Reference -->
            <div class="section">
                <div class="section-title">Full Booking Reference</div>
                <div class="section-value" style="font-family: Courier New, monospace; font-size: 10px; color: #718096;">
                    {{ $data['id'] }}
                </div>
            </div>
            
            <!-- QR Code Section -->
            <div style="text-align: center; margin-top: 20px; padding: 15px; background: #f7fafc; border-radius: 8px;">
                <div style="font-size: 10px; color: #718096; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px;">
                    Scan to Verify Booking
                </div>
                <!-- QR Code -->
                <div class="qr-code">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ route('admin.bookings.checkin', $data['id']) }}" alt="QR Code">
                    <p>Scan to Check-in</p>
                </div>
                <div style="font-size: 9px; color: #a0aec0; margin-top: 8px;">
                    Booking ID: {{ strtoupper(substr($data['id'], 0, 8)) }}
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div class="footer-row">
                <div class="footer-col left">
                    <div class="contact-title">Need Help?</div>
                    <div class="contact-value">
                        Facilities Office: Level 3, Main Building<br>
                        Tel: 03-4145 0123 Ext. 456 | Email: facilities@tarumt.edu.my
                    </div>
                </div>
                <div class="footer-col right">
                    <div class="validity">Valid only for scheduled date</div>
                    <div class="issued">Issued: {{ $data['generated_at'] }}</div>
                </div>
            </div>
            
            <div class="terms">
                <strong>Terms:</strong> This booking is non-transferable. Cancellation must be made 24 hours in advance. 
                Misuse of facilities may result in suspension of booking privileges. 
                By using this facility, you agree to follow all university safety guidelines and regulations.
            </div>
        </div>
    </div>
</body>
</html>