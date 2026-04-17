<div class="min-h-screen bg-surface p-6 flex flex-col items-center justify-center">
    <div class="w-full max-w-4xl">
        <div class="text-center mb-12">
            <h1 class="font-display text-5xl md:text-7xl text-primary mb-4 tracking-tighter uppercase italic">
                {{ $contest->name }}
            </h1>
            <div class="h-1 w-32 bg-secondary mx-auto rounded-full shadow-[0_0_15px_rgba(0,238,252,0.6)]"></div>
        </div>

        <div class="relative group">
            <!-- Glassmorphism Container -->
            <div class="backdrop-blur-2xl bg-surface-container-high/40 border border-outline-variant/20 rounded-3xl p-8 md:p-12 shadow-2xl overflow-hidden relative">
                
                <!-- Background Decorative Gradient -->
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-primary/10 rounded-full blur-3xl group-hover:bg-primary/20 transition-all duration-700"></div>
                <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-secondary/10 rounded-full blur-3xl group-hover:bg-secondary/20 transition-all duration-700"></div>

                <div class="relative z-10 text-center">
                    @if($currentPresentation)
                        <span class="inline-block px-4 py-1 rounded-full bg-secondary/10 text-secondary text-xs font-bold tracking-widest uppercase mb-6 border border-secondary/20">
                            No Palco Agora
                        </span>
                        
                        <h2 class="font-display text-4xl md:text-6xl text-white mb-2 leading-none">
                            {{ $currentPresentation->work_title }}
                        </h2>
                        
                        <p class="text-xl text-white/60 font-body mb-8">
                            Competidor: <span class="text-white">{{ $currentPresentation->competitor->name }}</span>
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-left">
                            <div class="p-4 rounded-2xl bg-surface-container-highest/30 border border-outline-variant/10">
                                <p class="text-xs text-white/40 uppercase tracking-wider mb-1">Status do Concurso</p>
                                <p class="text-lg text-secondary">{{ $contest->status }}</p>
                            </div>
                        </div>
                    @else
                        <div class="py-20">
                            <h2 class="font-display text-3xl text-white/20 uppercase tracking-widest">
                                Aguardando Início
                            </h2>
                            <p class="text-white/40 mt-4">A próxima apresentação aparecerá aqui em tempo real.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Fake Border Outline (Phantom Borders) -->
            <div class="absolute inset-0 rounded-3xl border border-outline-variant/5 pointer-events-none"></div>
        </div>

        <div class="mt-12 text-center">
            <p class="text-white/30 text-sm flex items-center justify-center gap-2">
                <span class="w-2 h-2 bg-secondary rounded-full animate-pulse"></span>
                Sincronizado via Digital Stage Core
            </p>
        </div>
    </div>
</div>
