<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { background-color: #ffffff; padding: 30px; border-radius: 8px; max-width: 600px; margin: 0 auto; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header { border-bottom: 2px solid #28a745; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { color: #28a745; margin: 0; font-size: 24px; }
        .details { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .details th, .details td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; }
        .details th { background-color: #f8f9fa; color: #555; width: 40%; }
        .footer { font-size: 12px; color: #777; text-align: center; margin-top: 30px; }
        .btn { background-color: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 10px; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Booking Approved! ✅</h1>
    </div>

    <p>Dear <strong>{{ $booking->user->name }}</strong>,</p>
    
    <p>We are pleased to inform you that your request to book the <strong>{{ $booking->facility->name }}</strong> has been officially <span style="color: #28a745; font-weight: bold;">APPROVED</span>.</p>

    <h3>Booking Details:</h3>
    <table class="details">
        <tr>
            <th>Booking ID</th>
            <td>#{{ $booking->id }}</td>
        </tr>
        <tr>
            <th>Facility</th>
            <td>{{ $booking->facility->name }}</td>
        </tr>
        <tr>
            <th>Start Time</th>
            <td>{{ \Carbon\Carbon::parse($booking->start_time)->format('d M Y, h:i A') }}</td>
        </tr>
        <tr>
            <th>End Time</th>
            <td>{{ \Carbon\Carbon::parse($booking->end_time)->format('d M Y, h:i A') }}</td>
        </tr>
    </table>

    <p>Please ensure you arrive on time. If you need to cancel, please log in to the system.</p>

    <div style="text-align: center; margin-top: 20px;">
        <a href="{{ url('/bookings') }}" class="btn">View My Bookings</a>
    </div>

    <div class="footer">
        <p>Facility Booking System &copy; {{ date('Y') }}</p>
    </div>
</div>

</body>
</html>