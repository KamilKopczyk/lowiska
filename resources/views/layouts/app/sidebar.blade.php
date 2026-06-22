<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('home') }}" />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Platform')" class="grid">
                    <flux:sidebar.item icon="home" :href="route('home')" :current="request()->routeIs('home')">
                        {{ __('Strona Główna') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="map" href="/mapa">
                        {{ __('Mapa Łowisk') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav>
                @guest
                    <a href="javascript:void(0)" onclick="openLoginModal()" class="flex items-center gap-3 px-3 py-2 rounded-lg text-emerald-600 dark:text-emerald-400 hover:bg-zinc-200 dark:hover:bg-zinc-800 transition-colors font-bold text-sm mx-2">
                        <i class="fa-solid fa-arrow-right-to-bracket text-lg"></i>
                        <span>Zaloguj się</span>
                    </a>
                @endguest

                <flux:sidebar.item icon="book-open-text" href="https://laravel.com/docs" target="_blank">
                    {{ __('Dokumentacja') }}
                </flux:sidebar.item>
            </flux:sidebar.nav>

            @auth
                <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
            @endauth
        </flux:sidebar>

        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
            <flux:spacer />

            @auth
                <flux:dropdown position="top" align="end">
                    <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />
                    <flux:menu>
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">{{ __('Wyloguj') }}</flux:menu.item>
                        </form>
                    </flux:menu>
                </flux:dropdown>
            @else
                <button onclick="openLoginModal()" class="flex items-center gap-2 px-3 py-1.5 text-sm font-bold text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 rounded-lg">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                    <span>Zaloguj</span>
                </button>
            @endauth
        </flux:header>

        <flux:main>
            {{ $slot }}
        </flux:main>

        <div id="login-modal" class="fixed inset-0 z-[9999] hidden opacity-0 transition-all duration-300 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-emerald-950/60 backdrop-blur-md cursor-pointer" onclick="closeLoginModal()"></div>

            <div class="relative z-10 w-full max-w-md transform scale-90 transition-all duration-300" id="login-card">
                <div class="bg-white dark:bg-zinc-900 rounded-[2.5rem] shadow-2xl overflow-hidden border border-emerald-900/20">
                    <div class="bg-emerald-900 py-8 text-center border-b-4 border-amber-400">
                        <i class="fa-solid fa-fish-fins text-4xl text-amber-400 mb-2"></i>
                        <h2 class="text-2xl font-black text-white uppercase tracking-tighter italic">Wędkarski Radar</h2>
                    </div>

                    <div class="p-8">
                        <form method="POST" action="{{ route('login') }}" class="space-y-5">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1 ml-1">E-mail</label>
                                <input type="email" name="email" required class="w-full px-4 py-3 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-2xl focus:ring-2 focus:ring-emerald-500 outline-none text-black dark:text-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1 ml-1">Hasło</label>
                                <input type="password" name="password" required class="w-full px-4 py-3 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-2xl focus:ring-2 focus:ring-emerald-500 outline-none text-black dark:text-white">
                            </div>
                            <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-black py-4 rounded-2xl shadow-lg transition transform hover:-translate-y-1 active:scale-95 uppercase tracking-wider">
                                Wejdź na łowisko
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Funkcje przypisane globalnie do window, by ominąć izolację Livewire
            window.openLoginModal = function(e) {
                if (e) e.preventDefault();
                const modal = document.getElementById('login-modal');
                const card = document.getElementById('login-card');

                modal.classList.remove('hidden');
                setTimeout(() => {
                    modal.classList.remove('opacity-0');
                    card.classList.replace('scale-90', 'scale-100');
                }, 20);
                document.body.style.overflow = 'hidden';
            };

            window.closeLoginModal = function() {
                const modal = document.getElementById('login-modal');
                const card = document.getElementById('login-card');

                modal.classList.add('opacity-0');
                card.classList.replace('scale-100', 'scale-90');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                }, 300);
            };

            // Jeśli po zalogowaniu są błędy, otwórz modal
            @if($errors->any())
                window.addEventListener('load', () => {
                    setTimeout(window.openLoginModal, 500);
                });
            @endif
        </script>

        @fluxScripts
    </body>
</html>
