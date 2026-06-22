<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>@yield('title', 'Wędkarski Radar')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg?v=2">
    <link rel="apple-touch-icon" href="/favicon.svg?v=2">
    @yield('styles')
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-800 flex flex-col min-h-screen">

    <!-- NAWIGACJA -->
    <nav id="main-navbar" class="sticky top-0 z-50 bg-emerald-900/95 backdrop-blur-md text-white shadow-md transition-all duration-300">
        <div id="main-navbar-container" class="container mx-auto flex flex-wrap justify-between items-center px-4 py-4 transition-all duration-300">

            <!-- Logo & navbar -->
            <div class="flex items-center justify-between w-full md:w-auto">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-fish-fins text-2xl text-amber-400"></i>
                    <a href="/" class="text-xl font-bold tracking-tight">Wędkarski Radar</a>
                </div>
                <button type="button" id="mobile-menu-btn" onclick="toggleMobileMenu()" class="md:hidden text-emerald-200 hover:text-white focus:outline-none">
                    <i class="fa-solid fa-bars text-2xl"></i>
                </button>
            </div>

            <!-- Linki i Profil -->
            <div id="mobile-menu" class="hidden md:flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-6 font-medium text-sm w-full md:w-auto mt-4 md:mt-0 pb-4 md:pb-0 transition-all duration-300">

                <a href="/lowiska" class="{{ request()->is('lowiska') ? 'text-amber-400 border-b-2 border-amber-400 pb-1' : 'hover:text-amber-400 transition' }} flex items-center">
                    <i class="fa-solid fa-water mr-2"></i> Wszystkie łowiska
                </a>
                <a href="/mapa" class="{{ request()->is('mapa') ? 'text-amber-400 border-b-2 border-amber-400 pb-1' : 'hover:text-amber-400 transition' }} flex items-center">
                    <i class="fa-solid fa-map mr-2"></i> Mapa
                </a>

                <!-- Widok dla niezalogowanego -->
                @guest
                    <button type="button" onclick="openLoginModal()" class="bg-amber-500 hover:bg-amber-600 text-emerald-950 font-bold px-5 py-2 rounded-lg transition flex items-center shadow-md border border-amber-400/50 w-full md:w-auto justify-center">
                        <i class="fa-solid fa-arrow-right-to-bracket mr-2 text-emerald-950"></i> Zaloguj się
                    </button>
                @endguest

                <!-- Widok dla zalogowanego -->
                @auth
                    <div class="flex items-center justify-between w-full md:w-auto space-x-4">
                        <a href="/profil" class="bg-emerald-700 hover:bg-emerald-600 text-white px-4 py-2 rounded-lg transition flex items-center shadow-sm border border-emerald-600/50 w-full justify-center">
                            @if(Auth::user()->isAdmin())
                                <i class="fa-solid fa-user-shield mr-2 text-amber-400 text-lg"></i>
                            @else
                                <i class="fa-solid fa-user-astronaut mr-2 text-amber-400 text-lg"></i>
                            @endif
                            <span class="font-bold tracking-wide">{{ Auth::user()->name }}</span>
                        </a>

                        <form method="POST" action="{{ route('logout') }}" class="m-0 p-0 flex shrink-0">
                            @csrf
                            <button type="submit" class="text-emerald-200 hover:text-red-400 transition text-sm font-medium flex items-center bg-emerald-800 md:bg-transparent px-3 py-2 md:p-0 rounded-lg md:rounded-none" title="Wyloguj się">
                                <i class="fa-solid fa-power-off text-xl"></i>
                            </button>
                        </form>
                    </div>
                @endauth

            </div>
        </div>
    </nav>

    @yield('content')

    <footer class="bg-emerald-950 text-emerald-200 py-6 mt-auto">
        <div class="container mx-auto px-4 text-center">
            <div class="flex items-center justify-center space-x-2 text-white mb-2">
                <i class="fa-solid fa-fish-fins text-xl text-amber-500"></i>
                <h4 class="text-lg font-bold">Wędkarski Radar</h4>
            </div>
            <p class="text-sm opacity-80 mb-2">Projekt aplikacji internetowej zrealizowany na potrzeby zajęć akademickich. Wykonał Kamil Kopczyk</p>
            <div class="text-xs opacity-60">
                &copy; 2026 Wędkarski Radar. Wszelkie prawa zastrzeżone.
            </div>
        </div>
    </footer>

    @guest
        @include('partials.login-modal')
    @endguest

    <script>
        window.openLoginModal = function() {
            const modal = document.getElementById('login-modal');
            const card = document.getElementById('login-card');
            modal.classList.remove('hidden');
            setTimeout(() => { modal.classList.remove('opacity-0'); card.classList.replace('scale-95', 'scale-100'); }, 10);
            document.body.style.overflow = 'hidden';
        }
        window.closeLoginModal = function() {
            const modal = document.getElementById('login-modal');
            const card = document.getElementById('login-card');
            modal.classList.add('opacity-0'); card.classList.replace('scale-100', 'scale-95');
            setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = 'auto'; }, 300);
        }
        function switchTab(type) {
            const loginForm = document.getElementById('form-login');
            const regForm = document.getElementById('form-register');
            const loginTab = document.getElementById('tab-login');
            const regTab = document.getElementById('tab-register');
            if (type === 'login') {
                loginForm.classList.remove('hidden'); regForm.classList.add('hidden');
                loginTab.classList.add('border-amber-400', 'text-white'); loginTab.classList.remove('text-emerald-300', 'border-transparent');
                regTab.classList.remove('border-amber-400', 'text-white'); regTab.classList.add('text-emerald-300', 'border-transparent');
            } else {
                loginForm.classList.add('hidden'); regForm.classList.remove('hidden');
                regTab.classList.add('border-amber-400', 'text-white'); regTab.classList.remove('text-emerald-300', 'border-transparent');
                loginTab.classList.remove('border-amber-400', 'text-white'); loginTab.classList.add('text-emerald-300', 'border-transparent');
            }
        }

        function toggleUserMenu() {
            document.getElementById('user-dropdown').classList.toggle('hidden');
        }

        // Zamknij menu profilu, gdy klikniesz gdziekolwiek indziej na stronie
        window.addEventListener('click', function(e) {
            const wrapper = document.getElementById('user-menu-wrapper');
            const dropdown = document.getElementById('user-dropdown');
            if (wrapper && !wrapper.contains(e.target)) {
                dropdown?.classList.add('hidden');
            }
        });

        // Płynna nawigacja przy przewijaniu strony
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('main-navbar');
            const container = document.getElementById('main-navbar-container');
            if (navbar && container) {
                if (window.scrollY > 20) {
                    navbar.classList.replace('bg-emerald-900/95', 'bg-emerald-950/90');
                    navbar.classList.add('shadow-lg');
                    container.classList.replace('py-4', 'py-2.5');
                } else {
                    navbar.classList.replace('bg-emerald-950/90', 'bg-emerald-900/95');
                    navbar.classList.remove('shadow-lg');
                    container.classList.replace('py-2.5', 'py-4');
                }
            }
        });

        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }

    </script>
    @yield('scripts')
</body>
</html>
