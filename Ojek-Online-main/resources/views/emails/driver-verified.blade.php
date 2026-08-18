<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Akun Driver Diverifikasi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .content p {
            color: #333;
            line-height: 1.6;
        }
        .button {
            display: inline-block;
            background: #22c55e;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 8px;
            margin-top: 20px;
        }
        .footer {
            background: #f4f4f4;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Verifikasi Berhasil!</h1>
        </div>
        <div class="content">
            <p>Halo <strong>{{ $driver->name }}</strong>,</p>
            <p>Selamat! Akun driver Anda telah <strong>diverifikasi</strong> oleh admin OMK OJOL.</p>
            <p>Anda sekarang dapat login ke aplikasi dan mulai menerima pesanan.</p>
            <p>Berikut adalah detail akun Anda:</p>
            <ul>
                <li><strong>Email:</strong> {{ $driver->email }}</li>
                <li><strong>Status:</strong> Aktif</li>
            </ul>
            <a href="{{ url('/login/driver') }}" class="button">Login Sekarang</a>
            <p style="margin-top: 20px;">Terima kasih telah bergabung dengan OMK OJOL!</p>
            <p>Salam,<br><strong>Tim OMK OJOL</strong></p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} OMK OJOL - Ojek Mitra Mahasiswa. All rights reserved.</p>
        </div>
    </div>
</body>
</html>