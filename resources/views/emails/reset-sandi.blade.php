@extends('emails.layout')

@section('content')
    <p>
        Kami menerima permintaan untuk mereset kata sandi akun Jakarta Camera Anda. <br>
        Jika Anda yang melakukan permintaan ini, silakan klik tombol di bawah ini untuk mereset kata sandi Anda:. <br><br>

        <a href="{{ $data['link_reset'] }}" class="button">Reset Kata Sandi</a> <br><br><br>

        Jika Anda tidak meminta untuk mereset kata sandi, harap abaikan email ini. 
        Kata sandi Anda akan tetap aman dan tidak akan diubah. <br><br>

        Terima kasih atas perhatiannya!<br><br>

        Salam,<br>
        <b>Tim Jakarta Camera</b>
    </p>
@endsection