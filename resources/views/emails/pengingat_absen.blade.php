<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Pengingat Absen</title>
</head>
<body>
    <p>Halo {{ $nama ?? 'User' }},</p>
    <p>{!! nl2br(e($pesan ?? 'Anda belum melakukan absen atau izin hari ini.')) !!}</p>
</body>
</html>
