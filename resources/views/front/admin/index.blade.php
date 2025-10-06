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

            <nav class="sidebar">
                <a href="{{ route('admin.transaksi') }}"
                    class="nav-item @if (request()->routeIs('admin.transaksi')) active @endif">
                    <i data-lucide="credit-card"></i>
                    <span>Transaksi</span>
                </a>
                <a href="{{ route('admin.member') }}" class="nav-item @if (request()->routeIs('admin.member')) active @endif">
                    <i data-lucide="users"></i>
                    <span>Member</span>
                </a>
                {{-- <a href="{{ route('admin.customers') }}"
                    class="nav-item @if (request()->routeIs('admin.customers')) active @endif">
                    <i data-lucide="user-check"></i>
                    <span>Pelatih</span>
                </a> --}}
                <a href="{{ route('main') }}" class="nav-item">
                    <i data-lucide="home"></i>
                    <span>Home</span>
                </a>

                <form id="logout-form" action="{{ route('logout.fo') }}" method="POST" style="display:none;">
                    @csrf
                </form>

                <a href="#" class="nav-item"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i data-lucide="power"></i>
                    <span>Logout</span>
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

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();
</script>


</html>
