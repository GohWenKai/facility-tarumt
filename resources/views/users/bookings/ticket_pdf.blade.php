<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Booking Ticket</title>
</head>
<body style="font-family: sans-serif; padding: 20px; border: 4px double #333;">

    <!-- HEADER WITH LOGO -->
    <div style="text-align: center; border-bottom: 1px solid #ddd; padding-bottom: 20px; margin-bottom: 20px;">
        
        <!-- Image: Uses public_path() to get the real file location on the server -->
        <img src="{{ public_path('storage/icon/tarumt.png') }}" style="width: 120px; height: auto; margin-bottom: 10px;" alt="TAR UMT Logo">
        
        <h1 style="margin: 0; text-transform: uppercase; font-size: 24px;">Facility Ticket</h1>
        
        <br>
        
        <span style="background-color: #28a745; color: white; padding: 5px 15px; border-radius: 4px; font-weight: bold; font-size: 14px; text-transform: uppercase;">
            Approved
        </span>
    </div>

    <!-- TICKET DETAILS -->
    <div style="margin-top: 20px; font-size: 16px; line-height: 1.6;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="font-weight: bold; width: 140px; padding: 5px;">Ticket ID:</td>
                <td style="padding: 5px;">{{ $data['id'] }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; padding: 5px;">Name:</td>
                <td style="padding: 5px;">{{ $data['name'] }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; padding: 5px;">Role:</td>
                <td style="padding: 5px;">{{ $data['role'] }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; padding: 5px;">Facility:</td>
                <td style="padding: 5px;">{{ $data['facility'] }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; padding: 5px;">Schedule:</td>
                <td style="padding: 5px;">{{ $data['time'] }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; padding: 5px;">Issued At:</td>
                <td style="padding: 5px;">{{ $data['generated_at'] }}</td>
            </tr>
        </table>
    </div>

    <!-- FOOTER -->
    <div style="position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 12px; color: #777; border-top: 1px solid #eee; padding-top: 10px;">
        <em>Please present this ticket at the facility counter.</em>
    </div>

</body>
</html>