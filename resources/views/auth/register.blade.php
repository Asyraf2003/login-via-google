@extends('layouts.auth')

@section('title','Daftar')
@section('subtitle','Ambil jamur & mulai petualangan!')

@section('content')
  {{-- Menampilkan Error Validasi (Penting) --}}
  @if ($errors->any())
    <div style="border: 1px solid red; padding: 10px; margin-bottom: 15px;">
      <strong>Periksa lagi:</strong>
      <ul>
        @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
      </ul>
    </div>
  @endif

  {{-- Form Registrasi Utama --}}
  <form method="POST" action="{{ route('register') }}">
    @csrf
    
    {{-- Field Nama --}}
    <div>
      <label for="name">Nama</label>
      <input id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name">
    </div>

    {{-- Field Email --}}
    <div>
      <label for="email">Email</label>
      <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
    </div>

    {{-- Field Password --}}
    <div>
      <label for="password">Password</label>
      <input id="password" type="password" name="password" required autocomplete="new-password">
    </div>

    {{-- Field Konfirmasi Password --}}
    <div>
      <label for="password_confirmation">Konfirmasi Password</label>
      <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
    </div>

    {{-- Tombol Aksi --}}
    <div style="margin-top: 15px;">
      <button type="submit">Daftar</button>
      
      {{-- Link Login Google (Socialite) --}}
      <a href="{{ route('google.redirect') }}" style="margin-left: 10px;">Dengan Google</a>
    </div>
    
    {{-- Link Login --}}
    <p style="text-align:center; margin-top: 15px;">Sudah punya akun?
      <a href="{{ route('login.show') }}">Kembali</a>
    </p>
  </form>
@endsection