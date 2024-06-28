@extends('emails.layout')

@section('content')
    <p>
        Halo <b>{{ $data['name'] }}</b>, <br><br>

        Terima kasih telah mendaftar di Jakarta Camera. Untuk melanjutkan proses pendaftaran dan mengaktifkan akun Anda, kami perlu memverifikasi alamat email Anda. <br>
        Silakan klik tombol di bawah ini untuk memverifikasi email Anda: <br><br>

        <a href="{{ $data['link_verif'] }}" class="button">Verifikasi</a> <br><br><br>

        Jika Anda tidak mendaftar di Jakarta Camera, harap abaikan email ini. <br><br>

        Terima kasih atas kerjasamanya!<br><br>

        Salam,<br>
        <b>Tim Jakarta Camera</b>
    </p>
@endsection