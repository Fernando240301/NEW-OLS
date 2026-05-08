<!DOCTYPE html>
<html>
<head>
    <title>Penawaran {{ $penawaran->nosurat }}</title>
    <style>
        body { font-family: sans-serif; }
        .barcode { margin-top: 20px; }
    </style>
</head>
<body>
    <h1>Penawaran: {{ $penawaran->judul }}</h1>
    <p>Nomor Surat: {{ $penawaran->nosurat }}</p>
    <p>Client: {{ $penawaran->namaclient }}</p>

    @if(isset($barcode))
        <div class="barcode">
            <img src="{{ storage_path('app/public/' . $barcode) }}" alt="Barcode" width="120">
        </div>
    @endif
</body>
</html>
