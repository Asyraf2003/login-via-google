<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">

  {{-- Judul Halaman --}}
  <title>@yield('title', 'Auth') | {{ config('app.name') }}</title>

  {{-- Placeholder untuk script/style tambahan jika ada yang di-push --}}
  @stack('head')
</head>
<body>
  <div class="auth-shell">
    <main class="auth-card" role="main">
      <div class="brand">
        <h1 class="brand-title">{{ config('app.name') }}</h1>
      </div>
      
      {{-- Subtitle (misalnya: Login atau Register) --}}
      <p class="title-sub">@yield('subtitle')</p>
      
      <div class="divider" aria-hidden="true"></div>
      
      {{-- Konten Utama (Form Login/Register/Reset Password) --}}
      @yield('content')
    </main>
  </div>
  
  {{-- Placeholder untuk scripts --}}
  @stack('scripts')
</body>
</html>