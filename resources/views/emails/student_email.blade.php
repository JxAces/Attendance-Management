<!DOCTYPE html>
<html>
<head>
    <title>Student QR Code</title>
</head>
<body>
    <p>Dear {{ $student->full_name }},</p>
    <p>Here is your unique QR code:</p>
    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ $student->id_no }}" alt="QR Code">
    <p>Scan this QR code for attendance.</p>
    <p>Thank you,</p>
    <p>Your Institution</p>
</body>
</html>
