<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pacific Pool - Admin Dashboard</title>

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    @vite(['resources/css/back/back_blank.css', 'resources/css/back/transaction.css', 'resources/js/back/transaction-filter.js'])
</head>

<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <!-- Logo Section -->
            <div class="sidebar-header">
                <div class="logo-container">
                    <div class="logo-icon">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M16 4L4 10L16 16L28 10L16 4Z" fill="url(#gradient1)" />
                            <path d="M4 16L16 22L28 16" stroke="url(#gradient2)" stroke-width="2"
                                stroke-linecap="round" />
                            <path d="M4 22L16 28L28 22" stroke="url(#gradient3)" stroke-width="2"
                                stroke-linecap="round" />
                            <defs>
                                <linearGradient id="gradient1" x1="4" y1="4" x2="28"
                                    y2="16" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#3B82F6" />
                                    <stop offset="1" stop-color="#8B5CF6" />
                                </linearGradient>
                                <linearGradient id="gradient2" x1="4" y1="16" x2="28"
                                    y2="22" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#3B82F6" />
                                    <stop offset="1" stop-color="#8B5CF6" />
                                </linearGradient>
                                <linearGradient id="gradient3" x1="4" y1="22" x2="28"
                                    y2="28" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#3B82F6" />
                                    <stop offset="1" stop-color="#8B5CF6" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                    <div class="logo-text">
                        <h2>Pacific Pool</h2>
                        <span>Admin Dashboard</span>
                    </div>
                </div>
            </div>

            <!-- Navigation Menu -->
            <nav class="sidebar-nav">
                <ul class="menu">
                    <!-- Management Staff -->
                    <li class="menu-item {{ request()->is('staff') ? 'active' : '' }}">
                        <a href="{{ route('staff') }}" class="menu-link">
                            <i data-feather="users" class="menu-icon"></i>
                            <span class="menu-text">Management Staff</span>
                        </a>
                    </li>

                    <!-- Transaction -->
                    <li class="menu-item {{ request()->is('transaction') ? 'active' : '' }}">
                        <a href="{{ route('transaction') }}" class="menu-link">
                            <i data-feather="credit-card" class="menu-icon"></i>
                            <span class="menu-text">Transaction</span>
                        </a>
                    </li>

                    <!-- Promo -->
                    <li class="menu-item {{ request()->is('promo') ? 'active' : '' }}">
                        <a href="{{ route('promo') }}" class="menu-link">
                            <i data-feather="tag" class="menu-icon"></i>
                            <span class="menu-text">Promo</span>
                        </a>
                    </li>

                    <!-- Divider -->
                    <li class="menu-divider"></li>

                    <!-- Management Ticket Group -->
                    <li
                        class="menu-group {{ request()->is('ticket-types') || request()->is('package-combo') ? 'open' : '' }}">
                        <div class="menu-group-header">
                            <div class="menu-group-title">
                                <i data-feather="tag" class="menu-icon"></i>
                                <span class="menu-text">Management Ticket</span>
                            </div>
                            <i data-feather="chevron-down" class="arrow-icon"></i>
                        </div>
                        <ul class="submenu">
                            <li class="submenu-item {{ request()->is('ticket-types') ? 'active' : '' }}">
                                <a href="{{ route('ticket-types') }}" class="menu-link">
                                    <span class="submenu-dot"></span>
                                    <span class="menu-text">Ticket Types</span>
                                </a>
                            </li>
                            <li class="submenu-item {{ request()->is('package-combo') ? 'active' : '' }}">
                                <a href="{{ route('package-combo') }}" class="menu-link">
                                    <span class="submenu-dot"></span>
                                    <span class="menu-text">Package Combo</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Management Member, Coach & Clubhouse Group -->
                    <li
                        class="menu-group {{ request()->is('member') || request()->is('coach') || request()->is('clubhouse') ? 'open' : '' }}">
                        <div class="menu-group-header">
                            <div class="menu-group-title">
                                <i data-feather="briefcase" class="menu-icon"></i>
                                <span class="menu-text">Member, Coach & Clubhouse</span>
                            </div>
                            <i data-feather="chevron-down" class="arrow-icon"></i>
                        </div>
                        <ul class="submenu">
                            <li class="submenu-item {{ request()->is('member') ? 'active' : '' }}">
                                <a href="{{ route('member') }}" class="menu-link">
                                    <span class="submenu-dot"></span>
                                    <span class="menu-text">Member</span>
                                </a>
                            </li>
                            <li class="submenu-item {{ request()->is('coach') ? 'active' : '' }}">
                                <a href="{{ route('coach') }}" class="menu-link">
                                    <span class="submenu-dot"></span>
                                    <span class="menu-text">Coach</span>
                                </a>
                            </li>
                            <li class="submenu-item {{ request()->is('clubhouse') ? 'active' : '' }}">
                                <a href="{{ route('clubhouse') }}" class="menu-link">
                                    <span class="submenu-dot"></span>
                                    <span class="menu-text">Clubhouse</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Management Customer Group -->
                    <li class="menu-group {{ request()->is('customer') ? 'open' : '' }}">
                        <div class="menu-group-header">
                            <div class="menu-group-title">
                                <i data-feather="user-check" class="menu-icon"></i>
                                <span class="menu-text">Management Customer</span>
                            </div>
                            <i data-feather="chevron-down" class="arrow-icon"></i>
                        </div>
                        <ul class="submenu">
                            <li class="submenu-item {{ request()->is('customer') ? 'active' : '' }}">
                                <a href="{{ route('customer') }}" class="menu-link">
                                    <span class="submenu-dot"></span>
                                    <span class="menu-text">Customer</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </nav>

            <!-- Sidebar Footer -->
            <div class="sidebar-footer">
                <div class="footer-card">
                    <i data-feather="help-circle" class="footer-icon"></i>
                    <h4>Need Help?</h4>
                    <p>Contact support team</p>
                    <button class="btn-support">Get Support</button>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Header -->
            <header class="top-header">
                <div class="header-left">
                    <button class="btn-menu-toggle" id="menuToggle">
                        <i data-feather="menu"></i>
                    </button>
                    <h1 class="page-title" id="pageTitle">Halaman Back Office Pacific Pool</h1>
                </div>
                <div class="header-right">
                    <form action="{{ route('logout.bo') }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn-icon" title="Logout">
                            <i data-feather="log-out"></i>
                        </button>
                    </form>


                    {{-- User Menu --}}
                    @php
                        $user = \Illuminate\Support\Facades\Auth::guard('bo')->user();
                    @endphp


                    @if ($user)
                        <div class="user-menu">
                            <div class="user-avatar">
                                {{ strtoupper(substr($user->username, 0, 2)) }}
                            </div>
                            <div class="user-details">
                                <span class="user-name">{{ $user->name ?? $user->username }}</span>
                                <span class="user-role">
                                    @if ($user->is_root)
                                        Super Admin
                                    @elseif($user->is_admin)
                                        Administrator
                                    @else
                                        Staff
                                    @endif
                                </span>
                            </div>
                            <i data-feather="chevron-down" class="user-arrow"></i>
                        </div>
                    @else
                        <div class="user-menu">
                            <div class="user-avatar">??</div>
                            <div class="user-details">
                                <span class="user-name">Guest</span>
                                <span class="user-role">No Access</span>
                            </div>
                            <i data-feather="chevron-down" class="user-arrow"></i>
                        </div>
                    @endif
                </div>
            </header>

            <!-- Content Area -->
            <div id="contentArea" class="content-area">
                @yield('content')
            </div>
        </main>

    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

    <script defer src="https://unpkg.com/feather-icons@4.29.0/dist/feather.min.js?module"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Pastikan Feather sudah siap
            if (window.feather) {
                feather.replace();
            }

            // Menu Group Toggle
            document.querySelectorAll('.menu-group-header').forEach(header => {
                header.addEventListener('click', () => {
                    const parent = header.parentElement;
                    parent.classList.toggle('open');

                    // Gunakan try-catch agar tidak crash jika icon belum siap
                    try {
                        feather.replace();
                    } catch (err) {
                        console.warn('Feather replace failed:', err);
                    }
                });
            });

            // Mobile Menu Toggle
            const menuToggle = document.getElementById('menuToggle');
            const sidebar = document.querySelector('.sidebar');
            const dashboardWrapper = document.querySelector('.dashboard-wrapper');

            if (menuToggle) {
                menuToggle.addEventListener('click', () => {
                    sidebar.classList.toggle('mobile-open');
                    dashboardWrapper.classList.toggle('sidebar-mobile-open');
                });
            }

            // Close sidebar on mobile when clicking outside
            document.addEventListener('click', (e) => {
                if (window.innerWidth <= 992) {
                    if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
                        sidebar.classList.remove('mobile-open');
                        dashboardWrapper.classList.remove('sidebar-mobile-open');
                    }
                }
            });
        });
    </script>

</body>

</html>
@stack('scripts')
