<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>@yield('title', 'Dashboard Admin')</title>
  @vite('resources/css/admin/admin.css')
  @stack('styles')
</head>
<body>
  <div class="app">
    <!-- Sidebar -->
    <aside class="sidebar" role="navigation" aria-label="Sidebar">
      <div class="brand">
        <div class="logo">PG</div>
        <div>
          <h1>Pacific Admin</h1>
          <p>Dashboard</p>
        </div>
      </div>

      <nav>
        <a href="{{ route('admin.transaksi') }}" class="nav-item @if(request()->routeIs('admin.transaksi')) active @endif">
          <span>Transaksi</span>
        </a>
        <a href="{{ route('admin.member')}}" class="nav-item @if(request()->routeIs('admin.member')) active @endif">
          <span>Member</span>
        </a>
        <a href="" class="nav-item @if(request()->routeIs('admin.customers')) active @endif">
          <span>Customers</span>
        </a>
        <a href="" class="nav-item @if(request()->routeIs('admin.settings')) active @endif">
          <span>Settings</span>
        </a>
        <a href="{{ route('main') }}" class="nav-item">
          <span>Kembali ke Home</span>
        </a>
      </nav>

      <div class="sidebar-footer">© Pacific Global</div>
    </aside>

    <!-- Main -->
    <main class="main" role="main">
      <div class="topbar">
        <h2>@yield('page-title')</h2>
        <div class="controls">
          @yield('top-controls')
        </div>
      </div>

      {{-- Konten halaman --}}
      @yield('content')
    </main>
  </div>

  @stack('scripts')
</body>
</html>
