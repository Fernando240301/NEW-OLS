<!DOCTYPE html>
<html>
<head>
    <title>Verifikasi Dokumen</title>
    <style>
        body {
            font-family: Arial;
            text-align: center;
            margin-top: 50px;
        }
        .box {
            border: 1px solid #ddd;
            padding: 20px;
            display: inline-block;
            border-radius: 10px;
        }
    </style>
</head>
<body>

<div class="box">
    <h2>✅ Dokumen Valid</h2>

    <p><strong>Nomor Surat:</strong> {{ $penawaran->nosurat }}</p>
    <p><strong>Client:</strong> {{ $penawaran->namaclient }}</p>
    <p><strong>Status:</strong> ASLI / TERVALIDASI</p>
    <p><strong>Diverifikasi pada:</strong> {{ now() }}</p>

    <hr>

    <p style="font-size:12px; color:gray;">
        ID: {{ $penawaran->hash }}
    </p>
</div>

</body>
</html>