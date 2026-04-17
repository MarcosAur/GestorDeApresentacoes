<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Gestor de Apresentação Gemini') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@700&family=Manrope:wght@400;500;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-surface text-white font-body antialiased" x-data="{ sidebarOpen: true }">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside 
            :class="sidebarOpen ? 'w-64' : 'w-20'" 
            class="bg-surface-container-low transition-all duration-300 ease-in-out flex flex-col border-r border-outline-variant/20"
        >
            <div class="p-6 flex items-center justify-between">
                <span x-show="sidebarOpen" class="font-display text-xl font-bold tracking-tight text-primary">GEMINI</span>
                <button @click="sidebarOpen = !sidebarOpen" class="text-white hover:text-primary transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                    </svg>
                </button>
            </div>

            <nav class="flex-1 px-4 space-y-2 mt-4 overflow-y-auto">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-surface-container-high text-primary' : 'hover:bg-surface-container-high transition-colors group' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ request()->routeIs('dashboard') ? 'text-primary' : 'text-secondary' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span x-show="sidebarOpen" class="{{ !request()->routeIs('dashboard') ? 'group-hover:text-primary' : '' }}">Dashboard</span>
                </a>

                @if(auth()->user()->hasRole('admin'))
                <a href="{{ route('events.index') }}" class="flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('events.*') ? 'bg-surface-container-high text-primary' : 'hover:bg-surface-container-high transition-colors group' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ request()->routeIs('events.*') ? 'text-primary' : 'text-secondary' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span x-show="sidebarOpen" class="{{ !request()->routeIs('events.*') ? 'group-hover:text-primary' : '' }}">Eventos</span>
                </a>

                <a href="{{ route('contests.index') }}" class="flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('contests.*') ? 'bg-surface-container-high text-primary' : 'hover:bg-surface-container-high transition-colors group' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ request()->routeIs('contests.*') ? 'text-primary' : 'text-secondary' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <span x-show="sidebarOpen" class="{{ !request()->routeIs('contests.*') ? 'group-hover:text-primary' : '' }}">Concursos</span>
                </a>

                <a href="{{ route('jurors.index') }}" class="flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('jurors.*') ? 'bg-surface-container-high text-primary' : 'hover:bg-surface-container-high transition-colors group' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ request()->routeIs('jurors.*') ? 'text-primary' : 'text-secondary' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span x-show="sidebarOpen" class="{{ !request()->routeIs('jurors.*') ? 'group-hover:text-primary' : '' }}">Jurados</span>
                </a>
                @endif

                @if(auth()->user()->hasRole('competidor'))
                <a href="{{ route('competitor.enrollment') }}" class="flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('competitor.enrollment') ? 'bg-surface-container-high text-primary' : 'hover:bg-surface-container-high transition-colors group' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ request()->routeIs('competitor.enrollment') ? 'text-primary' : 'text-secondary' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span x-show="sidebarOpen" class="{{ !request()->routeIs('competitor.enrollment') ? 'group-hover:text-primary' : '' }}">Inscrições</span>
                </a>
                @endif
                <!-- Add other links here -->
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="h-16 bg-surface-container-low border-b border-outline-variant/20 flex items-center justify-between px-8">
                <div></div>
                <div class="flex items-center space-x-4" x-data="{ open: false }">
                    <div class="relative">
                        <button @click="open = !open" class="flex items-center space-x-2 font-admin text-sm font-medium hover:text-primary transition-colors">
                            <span>{{ Auth::user()->name }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-surface-container-high border border-outline-variant/20 rounded-lg shadow-xl py-2 z-50">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm hover:bg-surface-container-highest transition-colors text-error">
                                    Sair
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main area -->
            <main class="flex-1 overflow-y-auto p-8">
                {{ $slot ?? '' }}
                @yield('content')
            </main>
        </div>
    </div>
    @livewireScripts
</body>
</html>
