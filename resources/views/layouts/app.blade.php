<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  
  {{-- Hanya judul dasar --}}
  <title>@yield('title','Welcome') | {{ config('app.name') }}</title>
</head>
<body>
  <header>

    <nav class="desktop-menu">
      <a href="{{ route('home') }}">Home</a>

      {{-- Logika Autentikasi di Navigasi Desktop --}}
      @guest
        {{-- Tampilkan jika pengguna BELUM login --}}
        <a href="{{ route('login.show') }}">Login</a>
      @else
        {{-- Tampilkan jika pengguna SUDAH login --}}
        <a href="{{ url('/profile') }}">Profil</a>
        {{-- Jika Anda ingin menambahkan tombol Logout, gunakan form POST --}}
        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
            @csrf
            <button type="submit">Logout</button>
        </form>
      @endguest
    </nav>

    <button id="menuToggle" type="button"></button>
  </header>

  <main id="content">
    @yield('content')
  </main>
</body>
</html>
