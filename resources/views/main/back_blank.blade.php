<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pacific Pool - Admin Dashboard</title>

    {{-- ✅ Tambahkan Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
          rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
          crossorigin="anonymous">

    @vite([
        'resources/css/back/back_blank.css',
        'resources/css/back/transaction.css',
        'resources/js/back/transaction-filter.js'
    ])
</head>

<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">Dashboard</div>
            <ul class="menu">
                <li class="menu-item {{ request()->is('transaction') ? 'active' : '' }}">
                    <a href="{{ route('transaction') }}" class="menu-link">Transaction</a>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="header">
                <h1 id="pageTitle">Transaction</h1>
                <div class="user-info">
                    <div class="avatar">AD</div>
                    <span>Admin</span>
                </div>
            </div>

            <div id="contentArea" class="content-area">
                @yield('content')
            </div>
        </main>
    </div>

    {{-- ✅ Tambahkan Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>
</html>
