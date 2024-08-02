@extends('emails.layout')

@section('content')
    <p>
        Halo <b>{{ $data['name'] }}</b>, <br><br>

        Terima kasih telah menggunakan jasa servis kamera di Jakarta Camera. Sebagai bentuk apresiasi, kami senang memberikan Anda kode referral yang dapat Anda bagikan kepada teman dan keluarga. Dengan menggunakan kode referral ini, mereka akan mendapatkan diskon untuk layanan kami, dan Anda juga akan menerima hadiah spesial dari kami setiap kali kode ini digunakan. <br>
        Berikut adalah kode referral Anda: <br><br>

        <b>VCR90909AB</b> <br><br>

        {{-- Silakan klik tombol di bawah ini untuk melihat detail lebih lanjut tentang program referral kami dan cara menggunakannya: <br><br>

        <a href="{{ $data['link_referral'] }}" class="button">Lihat Detail Referral</a> <br><br><br> --}}

        Kami sangat menghargai dukungan Anda dan berharap dapat terus memberikan layanan terbaik untuk Anda. Jika Anda memiliki pertanyaan lebih lanjut, jangan ragu untuk menghubungi tim kami. Kami siap membantu Anda. <br><br>

        Terima kasih atas kepercayaannya!<br><br>

        Salam,<br>
        <b>Tim Jakarta Camera</b>
    </p>
@endsection