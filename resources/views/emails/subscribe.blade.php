@extends('emails.layout')

@section('content')
    <p>
        Halo <b>{{ $data['name'] }}</b>, <br><br>

        Terima kasih telah berlangganan di Jakarta Camera. nikmati promo dan informasi lainnya yang akan kamu dapatkan<br>

        <br><br>

        Salam,<br>
        <b>Tim Jakarta Camera</b>

        <br>
        <br>
        <span>berhenti berlangganan, <a href="{{ $data['link_unsub'] }}" style="font-size: 8px;">Unsubscribe</a></span>
    </p>
@endsection
