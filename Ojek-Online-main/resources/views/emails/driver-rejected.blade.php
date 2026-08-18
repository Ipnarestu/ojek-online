<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Status Pendaftaran Driver</title>
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
            background: linear-gradient(135deg, #ef4444, #dc2626);
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
        .reason-box {
            background: #fef2f2;
            border-left: 4px solid #ef4444;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
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
            <h1>❌ Verifikasi Ditolak</h1>
        </div>
        <div class="content">
            <p>Halo <strong>{{ $driver->name }}</strong>,</p>
            <p>Maaf, pendaftaran akun driver Anda <strong>belum dapat disetujui</strong> oleh admin OMK OJOL.</p>
            
            <div class="reason-box">
                <strong>📋 Alasan Penolakan:</strong><br>
                {{ $reason }}
            </div>
            
            <p>Silakan periksa kembali dokumen yang Anda upload dan pastikan:</p>
            <ul>
                <li>KTP jelas dan terbaca</li>
                <li>SIM masih berlaku</li>
                <li>STNK sesuai dengan kendaraan</li>
                <li>Foto diri terbaru</li>
            </ul>
            <p>Anda dapat mendaftar kembali setelah melengkapi persyaratan.</p>
            <a href="{{ url('/register/driver') }}" class="button">Daftar Ulang</a>
            <p style="margin-top: 20px;">Jika ada pertanyaan, silakan hubungi admin.</p>
            <p>Salam,<br><strong>Tim OMK OJOL</strong></p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} OMK OJOL - Ojek Mitra Mahasiswa. All rights reserved.</p>
        </div>
    </div>
</body>
</html>