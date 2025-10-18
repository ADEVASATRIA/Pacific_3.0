{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Pacific Pool') }} - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite([
        'resources/css/app_back.css', 
        'resources/js/app_back.js', 
    ])
</head>
<body class="font-sans antialiased">
    <div x-data="{ 
        sidebarOpen: true, 
        activeMenu: '{{ request()->route()->getName() ?? 'dashboard' }}',
        isMobile: window.innerWidth < 768 
    }" 
    x-init="
        window.addEventListener('resize', () => {
            isMobile = window.innerWidth < 768;
            if (isMobile) sidebarOpen = false;
        });
        if (isMobile) sidebarOpen = false;
    "
    class="flex h-screen bg-gradient-to-br from-slate-50 to-slate-100 overflow-hidden">
        
        {{-- Overlay for mobile --}}
        <div x-show="sidebarOpen && isMobile" 
             x-transition.opacity
             @click="sidebarOpen = false"
             class="fixed inset-0 bg-black/50 z-20 md:hidden">
        </div>

        {{-- Sidebar --}}
        <aside 
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
            class="fixed md:relative z-30 h-screen bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 text-white transition-all duration-300 ease-in-out flex flex-col shadow-2xl"
            :style="sidebarOpen ? 'width: 288px' : 'width: 80px'">
            
            {{-- Header --}}
            <div class="p-6 flex items-center justify-between border-b border-slate-700/50">
                <div class="flex items-center gap-3" :class="!sidebarOpen && 'justify-center w-full'">
                    <div class="w-10 h-10 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-xl flex items-center justify-center shadow-lg shadow-cyan-500/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"></path>
                        </svg>
                    </div>
                    <div x-show="sidebarOpen" x-transition>
                        <h1 class="text-lg font-bold bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent">
                            Pacific Pool
                        </h1>
                        <p class="text-xs text-slate-400">Management Back Office</p>
                    </div>
                </div>
            </div>

            {{-- Search Bar --}}
            <div x-show="sidebarOpen" x-transition class="px-4 pt-6 pb-4">
                <div class="relative">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" placeholder="Search..." 
                           class="w-full bg-slate-800/50 border border-slate-700/50 rounded-lg pl-10 pr-4 py-2 text-sm text-white placeholder-slate-400 focus:outline-none focus:border-cyan-500/50 focus:bg-slate-800 transition-all">
                </div>
            </div>

            {{-- Main Menu --}}
            <nav class="flex-1 px-3 py-4 overflow-y-auto scrollbar-thin scrollbar-thumb-slate-700 scrollbar-track-transparent">
                <div class="space-y-1">
                    {{-- Dashboard --}}
                    <a href="" 
                       :class="activeMenu === 'dashboard' ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white'"
                       class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group relative"
                       :class="!sidebarOpen && 'justify-center'">
                        {{-- <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg> --}}
                        <span x-show="sidebarOpen" class="text-sm font-medium">Dashboard</span>
                    </a>

                    {{-- Management Staff --}}
                    <a href="{{ route('staff') }}" 
                       :class="activeMenu === 'tickets.index' ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white'"
                       class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group relative"
                       :class="!sidebarOpen && 'justify-center'">
                        {{-- <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                        </svg> --}}
                        <span x-show="sidebarOpen" class="flex-1 text-sm font-medium">Management Staff</span>
                    </a>

                    {{-- Customers --}}
                    <a href="{{ route('transaction') }}" 
                       :class="activeMenu === 'customers.index' ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white'"
                       class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group relative"
                       :class="!sidebarOpen && 'justify-center'">
                        {{-- <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg> --}}
                        <span x-show="sidebarOpen" class="text-sm font-medium">Transaction</span>
                    </a>

                    {{-- Pool Schedule --}}
                    <a href="{{ route('promo') }}" 
                       :class="activeMenu === 'schedule.index' ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white'"
                       class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group relative"
                       :class="!sidebarOpen && 'justify-center'">
                        {{-- <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg> --}}
                        <span x-show="sidebarOpen" class="text-sm font-medium">Promo</span>
                    </a>

                    {{-- Pool Management --}}
                    <a href="{{ route('ticket-types') }}" 
                       :class="activeMenu === 'pools.index' ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white'"
                       class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group relative"
                       :class="!sidebarOpen && 'justify-center'">
                        {{-- <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"></path>
                        </svg> --}}
                        <span x-show="sidebarOpen" class="text-sm font-medium">Ticket Types</span>
                    </a>

                    {{-- Payments --}}
                    <a href="{{ route('package-combo') }}" 
                       :class="activeMenu === 'payments.index' ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white'"
                       class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group relative"
                       :class="!sidebarOpen && 'justify-center'">
                        {{-- <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg> --}}
                        <span x-show="sidebarOpen" class="flex-1 text-sm font-medium">Package Combo</span>
                    </a>

                    {{-- Reports --}}
                    <a href="{{ route('member') }}" 
                       :class="activeMenu === 'reports.index' ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white'"
                       class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group relative"
                       :class="!sidebarOpen && 'justify-center'">
                        {{-- <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg> --}}
                        <span x-show="sidebarOpen" class="text-sm font-medium">Member</span>
                    </a>

                    {{-- Invoices --}}
                    <a href="{{ route('coach') }}" 
                       :class="activeMenu === 'invoices.index' ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white'"
                       class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group relative"
                       :class="!sidebarOpen && 'justify-center'">
                        {{-- <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg> --}}
                        <span x-show="sidebarOpen" class="text-sm font-medium">Coach</span>
                    </a>

                    {{-- Clubhouse --}}
                    <a href="{{ route('clubhouse') }}" 
                       :class="activeMenu === 'invoices.index' ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white'"
                       class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group relative"
                       :class="!sidebarOpen && 'justify-center'">
                        {{-- <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg> --}}
                        <span x-show="sidebarOpen" class="text-sm font-medium">Clubhouse</span>
                    </a>

                    {{-- Customer --}}
                    <a href="{{ route('customer') }}" 
                       :class="activeMenu === 'invoices.index' ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white'"
                       class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group relative"
                       :class="!sidebarOpen && 'justify-center'">
                        {{-- <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg> --}}
                        <span x-show="sidebarOpen" class="text-sm font-medium">Customer</span>
                    </a>
                </div>
            </nav>

            {{-- Bottom Menu --}}
            <div class="px-3 py-4 border-t border-slate-700/50">
                <div class="space-y-1 mb-4">
                    {{-- Notifications --}}
                    <a href="" 
                       :class="activeMenu === 'notifications.index' ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white'"
                       class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group relative"
                       :class="!sidebarOpen && 'justify-center'">
                        {{-- <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg> --}}
                        <span x-show="sidebarOpen" class="flex-1 text-sm font-medium">Notifications</span>
                    </a>

                    {{-- Settings --}}
                    <a href="" 
                       :class="activeMenu === 'settings.index' ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white'"
                       class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group relative"
                       :class="!sidebarOpen && 'justify-center'">
                        <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span x-show="sidebarOpen" class="text-sm font-medium">Settings</span>
                    </a>
                </div>

                {{-- User Profile --}}
                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-800/50 border border-slate-700/50" :class="!sidebarOpen && 'justify-center'">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold shadow-lg flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}
                    </div>
                    <div x-show="sidebarOpen" class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name ?? 'Admin User' }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ auth()->user()->email ?? 'admin@aquaticket.com' }}</p>
                    </div>
                    <form method="POST" action="" x-show="sidebarOpen">
                        @csrf
                        <button type="submit" class="text-slate-400 hover:text-red-400 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Toggle Button --}}
            <button @click="sidebarOpen = !sidebarOpen"
                    class="hidden md:flex absolute -right-3 top-20 w-6 h-6 bg-gradient-to-br from-cyan-500 to-blue-500 rounded-full items-center justify-center text-white shadow-lg hover:shadow-cyan-500/50 transition-all hover:scale-110">
                <svg x-show="!sidebarOpen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <svg x-show="sidebarOpen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
        </aside>

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            {{-- Top Bar --}}
            <header class="bg-white shadow-sm border-b border-slate-200 z-10">
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center gap-4">
                        {{-- Mobile Menu Toggle --}}
                        <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-slate-600 hover:text-slate-900">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        
                        <div>
                            <h1 class="text-2xl font-bold text-slate-800">@yield('page-title', 'Dashboard')</h1>
                            <p class="text-sm text-slate-600">@yield('page-subtitle', 'Welcome back!')</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        {{-- Quick Actions --}}
                        <button class="hidden sm:flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-cyan-500 to-blue-500 text-white rounded-lg hover:shadow-lg hover:shadow-cyan-500/30 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span class="text-sm font-medium">New Ticket</span>
                        </button>
                    </div>
                </div>
            </header>

            {{-- Page Content --}}
            <main class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>