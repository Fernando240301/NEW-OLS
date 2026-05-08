<p>Berikut Work Assignment untuk project:</p>

<p>
    <strong>{{ $workflow['project_number'] ?? '-' }}</strong><br>
    {{ $workflow['projectname'] ?? '-' }}
</p>

<p>
    Silakan melakukan approval sesuai kewenangan Anda.
</p>

<p>
    <a href="{{ url('/verifikasi/work-assignment') }}"
        style="background:#28a745;color:#fff;padding:10px 15px;
              text-decoration:none;border-radius:4px;">
        Buka Halaman Verifikasi
    </a>
</p>

<p style="margin-top:20px;font-size:12px;color:#666;">
    Email ini dikirim otomatis oleh sistem.
</p>
