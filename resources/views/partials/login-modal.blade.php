<div id="login-modal" class="fixed inset-0 z-[9999] hidden opacity-0 transition-all duration-300 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-emerald-950/70 backdrop-blur-md cursor-pointer" onclick="closeLoginModal()"></div>
    <div class="relative z-10 w-full max-w-[400px] transform scale-95 transition-all duration-300 flex flex-col items-center" id="login-card">
        <div class="bg-white rounded-[2.5rem] shadow-2xl overflow-hidden w-full text-center">
            <div class="bg-emerald-900 pt-10 pb-6 text-center relative overflow-hidden">
                <div class="absolute inset-0 z-0">
                    <img src="https://images.unsplash.com/photo-1518314115147-36b132808069?q=80&w=2000" class="w-full h-full object-cover blur-[3px] opacity-40 scale-110">
                </div>
                <div class="relative z-10">
                    <i class="fa-solid fa-fish text-4xl text-amber-400 drop-shadow-md mb-2"></i>
                    <h2 class="text-2xl font-bold text-white tracking-tight">Wędkarski Radar</h2>
                    <p class="text-emerald-200 text-sm font-medium opacity-90 mt-1 italic">Witaj z powrotem na łowisku!</p>
                </div>
                <div class="flex justify-center space-x-8 mt-6 relative z-10">
                    <button onclick="switchTab('login')" id="tab-login" class="text-white text-xs font-black uppercase tracking-widest border-b-4 border-amber-400 pb-2 transition-all">Logowanie</button>
                    <button onclick="switchTab('register')" id="tab-register" class="text-emerald-300 hover:text-white text-xs font-black uppercase tracking-widest border-b-4 border-transparent pb-2 transition-all">Rejestracja</button>
                </div>
            </div>

            <div class="p-10">
                <form id="form-login" method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf
                    <div class="space-y-1">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 text-left">Adres E-mail</label>
                        <div class="relative">
                            <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-sm"></i>
                            <input type="email" name="email" placeholder="np. jan.kowalski@email.com" required
                                class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border-2 border-emerald-500/20 rounded-2xl focus:border-emerald-500 focus:ring-0 outline-none transition-all text-sm placeholder:text-gray-300">
                        </div>
                    </div>
                    <div class="space-y-1">
                        <div class="flex justify-between items-center px-1">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Hasło</label>

                        </div>
                        <div class="relative">
                            <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-sm"></i>
                            <input type="password" name="password" placeholder="••••••••" required
                                class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border-2 border-emerald-500/10 rounded-2xl focus:border-emerald-500 focus:ring-0 outline-none transition-all text-sm placeholder:text-gray-300">
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-black py-4 rounded-2xl shadow-xl transition-all transform hover:-translate-y-1 flex items-center justify-center gap-2 tracking-widest text-sm">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i> ZALOGUJ SIĘ
                    </button>
                    <div class="mt-8 text-center">
                        <p class="text-xs text-gray-400 font-bold">Nie masz jeszcze konta? <button type="button" onclick="switchTab('register')" class="text-amber-500 hover:text-amber-600 ml-1">Dołącz do nas!</button></p>
                    </div>
                </form>

                <form id="form-register" method="POST" action="{{ route('register') }}" class="space-y-5 hidden">
                    @csrf
                    <div class="space-y-1 text-left">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Imię i Nazwisko</label>
                        <input type="text" name="name" required class="w-full px-4 py-3.5 bg-gray-50 border-2 border-emerald-500/10 rounded-2xl focus:border-emerald-500 outline-none text-sm">
                    </div>
                    <div class="space-y-1 text-left">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Adres E-mail</label>
                        <input type="email" name="email" required class="w-full px-4 py-3.5 bg-gray-50 border-2 border-emerald-500/10 rounded-2xl focus:border-emerald-500 outline-none text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-left">
                        <div class="space-y-1">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Hasło</label>
                            <input type="password" name="password" required class="w-full px-4 py-3 bg-gray-50 border-2 border-emerald-500/10 rounded-2xl focus:border-emerald-500 outline-none text-sm">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Powtórz</label>
                            <input type="password" name="password_confirmation" required class="w-full px-4 py-3 bg-gray-50 border-2 border-emerald-500/10 rounded-2xl focus:border-emerald-500 outline-none text-sm">
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-black py-4 rounded-2xl shadow-xl transition-all transform hover:-translate-y-1 tracking-widest text-sm mt-2">DOŁĄCZ DO NAS!</button>
                </form>
            </div>
        </div>
        <div class="mt-6 w-full flex justify-center">
            <button onclick="closeLoginModal()" class="flex items-center text-emerald-100/80 hover:text-white transition-colors text-xs font-bold gap-2 drop-shadow-md">
                <i class="fa-solid fa-arrow-left"></i> Powrót na stronę główną
            </button>
        </div>
    </div>
</div>
