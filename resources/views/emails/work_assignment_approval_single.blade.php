<p>Yth. Manager {{ $role }},</p>

<p>
    Mohon persetujuan Work Assignment berikut:
</p>

<ul>
    <li>Project Number: <b>{{ $workflow['project_number'] }}</b></li>
    <li>Project Name: <b>{{ $workflow['projectname'] }}</b></li>
</ul>

<p>
    Silakan klik tombol berikut untuk melakukan approval:
</p>

<p style="text-align:center">
    <a href="{{ $link }}"
        style="background:#28a745;color:#fff;padding:12px 20px;
              text-decoration:none;border-radius:5px;display:inline-block">
        ✅ APPROVE {{ $role }}
    </a>
</p>

<p>
    Email ini dikirim otomatis oleh sistem MARINDOTECH.
</p>
