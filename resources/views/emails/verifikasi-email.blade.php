@extends('emails.layout')

@section('content')
    <h2>{{ $data['subject'] }}</h2>
    <p>{{ $data['message'] }}</p>
    <a href="#" class="button">Call to Action</a>
@endsection