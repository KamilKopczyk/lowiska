<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie - Wędkarski Radar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg?v=2">
    <link rel="apple-touch-icon" href="/favicon.svg?v=2">
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center relative overflow-hidden">

    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1506125840744-167167210587?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" alt="Wędkarstwo tło" class="w-full h-full object-cover" />
        <div class="absolute inset-0 bg-emerald-950/85 backdrop-blur-[2px]"></div>
    </div>

    <div class="relative z-10 w-full max-w-md px-6">
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-emerald-900/20">

            <div class="bg-emerald-900 py-8 text-center rounded-t-3xl border-b-4 border-amber-400 relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-emerald-800 rounded-full opacity-50"></div>
                <div class="absolute -bottom-10 -left-10 w-24 h-24 bg-emerald-950 rounded-full opacity-50"></div>

                <a href="/" class="relative z-10 inline-block transition transform hover:scale-105">
                    <i class="fa-solid fa-fish-fins text-4xl text-amber-400 mb-2 drop-shadow-md"></i>
                    <h1 class="text-2xl font-black text-white tracking-tight">Wędkarski Radar</h1>
                </a>
                <p class="relative z-10 text-emerald-200 text-sm mt-1 font-medium">Witaj z powrotem na łowisku!</p>
            </div>

            <div class="p-8">
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-bold text-gray-700 mb-1 uppercase tracking-wide">Adres e-mail</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-envelope text-gray-400"></i>
                            </div>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus placeholder="np. jan.kowalski@email.com"
                                class="pl-10 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors bg-gray-50 placeholder-gray-400">
                        </div>
                        @error('email')
                            <p class="text-red-500 text-xs mt-1.5 font-bold"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="password" class="block text-sm font-bold text-gray-700 uppercase tracking-wide">Hasło</label>
                            @if (Route::has('password.request'))

                            @endif
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-lock text-gray-400"></i>
                            </div>
                            <input type="password" name="password" id="password" required placeholder="••••••••"
                                class="pl-10 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors bg-gray-50 placeholder-gray-400">
                        </div>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1.5 font-bold"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-bold py-3.5 rounded-xl transition-all shadow-[0_4px_14px_0_rgba(4,120,87,0.39)] flex items-center justify-center mt-2">
                        <i class="fa-solid fa-arrow-right-to-bracket mr-2"></i> Zaloguj się
                    </button>
                </form>

                @if (Route::has('register'))
                    <div class="mt-8 text-center border-t border-gray-100 pt-6">
                        <p class="text-sm text-gray-500 font-medium">
                            Nie masz jeszcze konta?
                            <a href="{{ route('register') }}" class="font-bold text-amber-500 hover:text-amber-600 transition ml-1">Dołącz do nas!</a>
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <div class="text-center mt-6">
            <a href="/" class="text-emerald-200/60 hover:text-white transition text-sm font-medium flex items-center justify-center">
                <i class="fa-solid fa-arrow-left mr-2 text-xs"></i> Powrót na stronę główną
            </a>
        </div>
    </div>

</body>
</html>
