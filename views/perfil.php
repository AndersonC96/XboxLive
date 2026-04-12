<main class="py-12 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto animate-fade-in">
    <div class="glass-card rounded-[3rem] overflow-hidden">
        <!-- Hero Header -->
        <div class="h-64 relative overflow-hidden bg-xbox-green/20">
            <div class="absolute inset-0 bg-gradient-to-t from-xbox-dark to-transparent"></div>
            <div class="absolute inset-0 opacity-20 bg-[url('img/gs.png')] bg-repeat rotate-12 scale-150"></div>
        </div>

        <div class="px-12 pb-12 relative">
            <!-- Avatar Floating -->
            <div class="flex flex-col md:flex-row items-center md:items-end gap-8 -mt-24 mb-12">
                <div class="relative group">
                    <img src="<?= $userProfile['gamerpic']; ?>" alt="Avatar" class="w-48 h-48 rounded-[2.5rem] border-8 border-xbox-dark shadow-2xl group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute bottom-4 right-4 w-8 h-8 bg-green-500 rounded-full border-4 border-xbox-dark shadow-lg"></div>
                </div>
                <div class="flex-1 text-center md:text-left mb-4">
                    <h1 class="text-5xl font-black text-white tracking-tighter mb-2 italic uppercase"><?= htmlspecialchars($userProfile['gamertag']); ?></h1>
                    <div class="flex flex-wrap justify-center md:justify-start gap-6 text-secondary font-black uppercase tracking-widest text-[10px]">
                        <span class="flex items-center gap-2"><i class="fas fa-id-badge text-xbox-green"></i> <?= $userProfile['tier'] ?></span>
                        <span class="flex items-center gap-2"><i class="fas fa-star text-xbox-green"></i> Rep: <?= $userProfile['reputation'] ?></span>
                        <span class="flex items-center gap-2"><i class="fas fa-map-marker-alt text-xbox-green"></i> <?= $userProfile['location'] ?></span>
                    </div>
                </div>
                <div class="flex gap-4 mb-4">
                    <button class="btn-xbox px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest">Editar Perfil</button>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
                <div class="p-8 rounded-[2.5rem] bg-white/[0.03] border border-white/5 text-center group hover:border-xbox-green/30 transition-all">
                    <p class="text-[10px] font-black text-secondary uppercase tracking-widest mb-4">Gamerscore Total</p>
                    <div class="flex items-center justify-center gap-3">
                        <img src="img/gs.png" class="w-8 h-8" alt="GS">
                        <span class="text-4xl font-black text-white italic tracking-tighter"><?= $userProfile['gamerscore'] ?></span>
                    </div>
                </div>
                <div class="p-8 rounded-[2.5rem] bg-white/[0.03] border border-white/5 text-center group hover:border-xbox-green/30 transition-all md:col-span-2">
                    <p class="text-[10px] font-black text-secondary uppercase tracking-widest mb-4">Bio do Jogador</p>
                    <p class="text-gray-300 font-medium text-lg italic leading-relaxed">"<?= htmlspecialchars($userProfile['bio']) ?>"</p>
                </div>
            </div>

            <!-- Presence Detail -->
            <div class="glass-card p-10 rounded-[2.5rem] border-l-4 border-xbox-green bg-gradient-to-r from-xbox-green/5 to-transparent">
                <h3 class="text-xl font-black mb-6 uppercase italic tracking-tighter">Status de Presença</h3>
                <div class="flex items-center gap-6">
                    <div class="w-16 h-16 rounded-2xl bg-white/5 flex items-center justify-center text-xbox-green border border-white/5">
                        <i class="fas fa-signal text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-white italic tracking-tighter mb-1 uppercase"><?= $presence['people'][0]['presenceState'] ?? 'Offline' ?></p>
                        <p class="text-gray-400 font-medium"><?= $presence['people'][0]['presenceText'] ?? 'Nenhuma atividade no momento' ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
