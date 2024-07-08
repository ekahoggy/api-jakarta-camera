@extends('emails.layout')

@section('content')
    <p>
        Halo <b>{{ $data['name'] }}</b>, <br><br>

        ada voucher baru cuma untukmu<br>
        Silakan gunakan voucher ini untuk membeli produk dari jakarta camera sampai {{ $data['tanggal_selesai'] }} {{ $data['jam_selesai'] }}<br><br>

        <a href="#" class="button">{{$data['redeem_code']}}</a>
        <br>
        <span>Nominal Voucher : {{ $data['voucher'] }}</span>
        <br><br><br>
        Salam,<br>
        <b>Tim Jakarta Camera</b>
    </p>
@endsection
