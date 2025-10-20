@extends('layouts.auth')

@section('title','Masuk')
@section('subtitle','Masuk lewat pipa warp!')

@section('content')
  {{-- Menampilkan Error Validasi (Penting) --}}
  @if ($errors->any())
    <div style="border: 1px solid red; padding: 10px; margin-bottom: 15px;">
      <strong>Uh-oh!</strong>
      <ul>
        @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
      </ul>
    </div>
  @endif

  {{-- Form Login Utama --}}
  <form method="POST" action="{{ route('login') }}">
    @csrf
    
    {{-- Field Email --}}
    <div>
      <label for="email">Email</label>
      <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
    </div>

    {{-- Field Password --}}
    <div>
      <label for="password">Password</label>
      <input id="password" type="password" name="password" required autocomplete="current-password">
    </div>

    {{-- Ingat Saya (Remember Me) --}}
    <div style="margin: 8px 0 14px">
      <label>
          <input type="checkbox" name="remember" value="1"> Ingat saya
      </label>
    </div>

    {{-- Tombol Aksi --}}
    <div>
      <button type="submit">MASUK</button>
      
      {{-- Link Login Google (Socialite) --}}
      <a href="{{ route('google.redirect') }}">Dengan Google</a>
    </div>

    {{-- Link Daftar (Register) --}}
    <p style="text-align: center; margin-top: 15px;">Belum punya akun?
      <a href="{{ route('register.show') }}">Daftar</a>
    </p>
  </form>
@endsection