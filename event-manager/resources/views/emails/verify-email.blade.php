<x-mail::message>
# Verifikasi alamat email Anda

Halo **{{ $userName }}**,

Terima kasih telah mendaftar di **{{ $appName }}**. Verifikasi alamat email Anda untuk mengamankan akun dan mulai mendaftar ke acara.

@if (!empty($isPreview))
<x-mail::panel>
Pratinjau email: tautan verifikasi di pesan ini tidak aktif.
</x-mail::panel>
@endif

<x-mail::button :url="$verificationUrl" color="primary">
@if (!empty($isPreview))
Contoh tombol (tautan tidak aktif)
@else
Verifikasi Email
@endif
</x-mail::button>

Jika tombol tidak berfungsi, buka tautan berikut di browser:

[Verifikasi email]({{ $verificationUrl }})

Tautan ini berlaku selama **{{ $expiresInMinutes }} menit**. Jika Anda tidak membuat akun ini, abaikan email ini. Akun tidak dapat mendaftar ke acara sebelum alamat email diverifikasi.

Salam hangat,  
Tim **{{ $appName }}**
</x-mail::message>
