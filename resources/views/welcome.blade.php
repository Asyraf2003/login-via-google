@extends('layouts.app')

@section('title', 'Index')

@section('content')
  <h1>hello world!</h1>

  <p>Akses cepat:</p>
  <ul>
    <li><a href="{{ route('login.show') }}">Login (manual)</a></li>
    <li><a href="{{ route('register.show') }}">Register</a></li>
    <li><a href="{{ route('google.redirect') }}">Login via Google</a></li>
  </ul>
@endsection