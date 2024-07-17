@extends('emails.layout')

@section('content')
    <h2>Promo Spesial untuk Anda!</h2>

    <p>Dapatkan diskon hingga {{ $data['promo']['diskon_besar'] }}% untuk {{ $data['promo']['type'] }} dari tanggal {{ $data['promo']['tanggal_mulai'] }} hingga
        {{ $data['promo']['tanggal_selesai'] }}.</p>

    <ul class="product-list">
        @foreach ($data['detail'] as $item)
            <li>
                <img src="{{ $item->image }}" alt="{{ $item->name }}">
                <h3>{{ $item->name }}</h3>
                <p>Harga diskon: {{ $item->persen }} - <span class="discount">{{ $item->harga }}</span></p>
            </li>
        @endforeach
    </ul>

    <p>Klik di sini untuk berbelanja sekarang:
        <a href="{{ $data['link_cta'] }}" class="button">Belanja</a>
    </p>

    <h2>Promo ini hanya berlaku untuk waktu yang terbatas, jadi jangan sampai Anda ketinggalan!</h2>

    <br>
    <br>
    <br>
    <span>berhenti berlangganan, <a href="{{ $data['link_unsub'] }}" style="font-size: 8px;">Unsubscribe</a></span>
@endsection
