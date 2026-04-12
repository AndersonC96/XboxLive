<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Xbox Live Dashboard' ?></title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind & Config -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        xbox: {
                            green: '#107C10',
                            'green-light': '#1db91d',
                            dark: '#0a0a0a',
                            surface: '#121212',
                            border: '#2a2a2a'
                        }
                    },
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                    animation: {
                        'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    }
                }
            }
        }
    </script>
    
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/png" href="img/logo2.png"/>
</head>
<body class="bg-xbox-dark text-gray-100 font-sans min-h-full flex flex-col">

<?php if (isset($showNavbar) && $showNavbar): ?>
<nav class="glass sticky top-0 z-50 py-3">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Brand -->
            <div class="flex items-center gap-8">
                <a href="dashboard" class="flex items-center gap-3 group">
                    <img src="img/logo2.png" alt="Xbox" class="w-10 h-10 group-hover:scale-110 transition-transform duration-300">
                    <span class="hidden md:block font-bold text-xl tracking-tight text-white">XBOX LIVE</span>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden lg:flex items-center gap-1">
                    <div class="relative group py-4">
                        <button class="px-4 py-2 rounded-lg hover:bg-white/5 transition-colors flex items-center gap-2">
                            <span>Social</span>
                            <i class="fas fa-chevron-down text-[10px] opacity-50"></i>
                        </button>
                        <div class="absolute top-[80%] left-0 w-56 glass rounded-xl overflow-hidden invisible group-hover:visible opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-200 z-[100] shadow-2xl border border-white/10">
                            <div class="py-2">
                                <a href="amigos" class="flex items-center gap-3 px-4 py-3 hover:bg-xbox-green/20 transition-colors">
                                    <i class="fas fa-users w-4 text-xbox-green"></i> Amigos
                                </a>
                                <a href="seguidores" class="flex items-center gap-3 px-4 py-3 hover:bg-xbox-green/20 transition-colors">
                                    <i class="fas fa-user-plus w-4 text-xbox-green"></i> Seguidores
                                </a>
                                <a href="feed" class="flex items-center gap-3 px-4 py-3 hover:bg-xbox-green/20 transition-colors">
                                    <i class="fas fa-rss w-4 text-xbox-green"></i> Feed de Atividade
                                </a>
                                <a href="recentes" class="flex items-center gap-3 px-4 py-3 hover:bg-xbox-green/20 transition-colors">
                                    <i class="fas fa-history w-4 text-xbox-green"></i> Jogadores Recentes
                                </a>
                                <a href="bloqueados" class="flex items-center gap-3 px-4 py-3 hover:bg-xbox-green/20 transition-colors">
                                    <i class="fas fa-ban w-4 text-xbox-green"></i> Bloqueados
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="relative group py-4">
                        <button class="px-4 py-2 rounded-lg hover:bg-white/5 transition-colors flex items-center gap-2">
                            <span>Game Pass</span>
                            <i class="fas fa-chevron-down text-[10px] opacity-50"></i>
                        </button>
                        <div class="absolute top-[80%] left-0 w-56 glass rounded-xl overflow-hidden invisible group-hover:visible opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-200 z-[100] shadow-2xl border border-white/10">
                            <div class="py-2">
                                <a href="todos_os_jogos" class="flex items-center gap-3 px-4 py-3 hover:bg-xbox-green/20 transition-colors">
                                    <i class="fas fa-list w-4 text-xbox-green"></i> Todos os Jogos
                                </a>
                                <a href="ea_play" class="flex items-center gap-3 px-4 py-3 hover:bg-xbox-green/20 transition-colors">
                                    <i class="fas fa-play-circle w-4 text-xbox-green"></i> EA Play
                                </a>
                                <a href="gamepass_pc" class="flex items-center gap-3 px-4 py-3 hover:bg-xbox-green/20 transition-colors">
                                    <i class="fas fa-laptop w-4 text-xbox-green"></i> PC Gamepass
                                </a>
                            </div>
                        </div>
                    </div>

                    <a href="conquistas" class="px-4 py-2 rounded-lg hover:bg-white/5 transition-colors">Conquistas</a>
                    
                    <div class="relative group py-4">
                        <button class="px-4 py-2 rounded-lg hover:bg-white/5 transition-colors flex items-center gap-2">
                            <span>Loja</span>
                            <i class="fas fa-chevron-down text-[10px] opacity-50"></i>
                        </button>
                        <div class="absolute top-[80%] left-0 w-56 glass rounded-xl overflow-hidden invisible group-hover:visible opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-200 z-[100] shadow-2xl border border-white/10">
                            <div class="py-2">
                                <a href="mais_jogados" class="flex items-center gap-3 px-4 py-3 hover:bg-xbox-green/20 transition-colors">
                                    <i class="fas fa-fire w-4 text-xbox-green"></i> Mais Jogados
                                </a>
                                <a href="promocao" class="flex items-center gap-3 px-4 py-3 hover:bg-xbox-green/20 transition-colors">
                                    <i class="fas fa-tags w-4 text-xbox-green"></i> Promoções
                                </a>
                                <a href="novos_jogos" class="flex items-center gap-3 px-4 py-3 hover:bg-xbox-green/20 transition-colors">
                                    <i class="fas fa-plus-circle w-4 text-xbox-green"></i> Novos Jogos
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile & Search -->
            <div class="flex items-center gap-4">
                <form action="search" method="GET" class="hidden md:flex items-center bg-white/5 border border-white/10 rounded-full px-4 py-1.5 focus-within:border-xbox-green transition-all">
                    <i class="fas fa-search text-gray-500 text-sm"></i>
                    <input type="text" name="gamertag_search" placeholder="Buscar Gamertag" class="bg-transparent border-none outline-none px-3 py-1 text-sm w-40 lg:w-48 placeholder:text-gray-600">
                </form>

                <div class="relative group">
                    <button class="flex items-center gap-3 pl-1 pr-3 py-1 rounded-full hover:bg-white/5 transition-all">
                        <div class="relative">
                            <img src="<?= $userProfile['gamerpic'] ?? 'img/default_avatar.jpg' ?>" alt="Profile" class="w-10 h-10 rounded-full border-2 border-xbox-green shadow-[0_0_15px_rgba(16,124,16,0.5)]">
                            <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-xbox-dark rounded-full"></div>
                        </div>
                        <div class="hidden sm:block text-left">
                            <p class="text-xs font-bold leading-none"><?= htmlspecialchars($userProfile['gamertag'] ?? 'Usuário') ?></p>
                            <span class="text-[10px] text-gray-500 uppercase tracking-widest font-bold">Online</span>
                        </div>
                        <i class="fas fa-chevron-down text-[10px] opacity-50 hidden sm:block"></i>
                    </button>
                    
                    <div class="absolute top-full right-0 mt-2 w-48 glass rounded-xl overflow-hidden hidden group-hover:block animate-fade-in shadow-2xl">
                        <div class="nav-dropdown-item font-black uppercase text-[10px] tracking-widest text-xbox-green/50 px-4 py-2 border-b border-white/5 mb-1">
                            Conta
                        </div>
                        <a href="perfil" class="nav-dropdown-item flex items-center justify-between group/link px-4 py-3 hover:bg-white/5 transition-colors">
                            Meu Perfil
                            <i class="fas fa-id-card text-[10px] text-gray-700 group-hover/link:text-xbox-green transition-colors"></i>
                        </a>
                        <a href="capturas" class="nav-dropdown-item flex items-center justify-between group/link px-4 py-3 hover:bg-white/5 transition-colors">
                            Minhas Capturas
                            <i class="fas fa-camera text-[10px] text-gray-700 group-hover/link:text-xbox-green transition-colors"></i>
                        </a>
                        <a href="logout" class="nav-dropdown-item flex items-center justify-between group/link px-4 py-3 hover:bg-red-500/20 text-red-400 transition-colors">
                            Sair
                            <i class="fas fa-sign-out-alt text-[10px] text-gray-700 group-hover/link:text-red-500 transition-colors"></i>
                        </a>
                    </div>
                </div>

                <!-- Mobile menu toggle -->
                <button class="lg:hidden p-2 text-gray-400 hover:text-white">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </div>
</nav>
<?php endif; ?>

<?= $content ?>

<footer class="mt-auto py-8 border-t border-white/5 bg-xbox-dark/50 backdrop-blur-md">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <div class="flex flex-col items-center gap-4">
            <img src="img/logo2.png" alt="Xbox" class="w-8 h-8 opacity-50 grayscale hover:grayscale-0 transition-all duration-500">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-gray-600">
                Xbox Live Dashboard &bull; Portfolio Case
            </p>
            <p class="text-[10px] text-gray-700 font-medium">
                Desenvolvido com PHP, MySQL e Tailwind CSS para demonstração técnica.
            </p>
        </div>
    </div>
</footer>
</body>
</html>
