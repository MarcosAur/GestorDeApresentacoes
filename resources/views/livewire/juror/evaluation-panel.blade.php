<div class="p-6">
    <div class="max-w-4xl mx-auto">
        <header class="mb-8">
            <h1 class="font-display text-3xl text-primary uppercase italic tracking-tighter">Painel do Jurado</h1>
            <p class="text-white/60 font-body">{{ $contest->name }}</p>
        </header>

        @if(session()->has('info'))
            <div class="mb-6 p-4 rounded-2xl bg-secondary/10 border border-secondary/20 text-secondary text-sm flex items-center gap-3 animate-bounce">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('info') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Sidebar: Competitor Info -->
            <div class="lg:col-span-1">
                <div class="bg-surface-container-low border border-outline-variant/10 rounded-3xl p-6 sticky top-6">
                    <h2 class="text-xs font-bold text-white/40 uppercase tracking-widest mb-4">Em Apresentação</h2>
                    
                    @if($currentPresentation)
                        <div class="space-y-4">
                            <div>
                                <p class="text-2xl font-display text-white leading-tight">{{ $currentPresentation->work_title }}</p>
                                <p class="text-sm text-secondary">{{ $currentPresentation->competitor->name }}</p>
                            </div>
                        </div>
                    @else
                        <div class="py-10 text-center">
                            <p class="text-white/20 italic">Aguardando chamada do Admin...</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Main: Evaluation Form Skeleton -->
            <div class="lg:col-span-2">
                <div class="bg-surface-container-high border border-outline-variant/20 rounded-3xl p-8 relative overflow-hidden">
                    @if($currentPresentation)
                        <h3 class="font-display text-xl text-white mb-6 uppercase tracking-wider">Critérios de Avaliação</h3>
                        
                        <form wire:submit.prevent="submit" class="space-y-6">
                            @foreach($contest->evaluationCriteria as $criterion)
                                <div class="p-4 rounded-2xl bg-surface-container-highest/50 border border-outline-variant/5">
                                    <div class="flex justify-between items-center mb-3">
                                        <label class="text-white font-medium">{{ $criterion->name }}</label>
                                        <div class="flex items-center space-x-2">
                                            <span class="text-[10px] font-bold text-white/40 uppercase tracking-widest">Peso {{ $criterion->weight }}x</span>
                                        </div>
                                    </div>
                                    
                                    <div class="relative">
                                        <input type="number" step="0.1" min="0" max="{{ $criterion->max_score }}" 
                                               class="w-full bg-surface-container-highest border border-outline-variant/20 rounded-xl py-4 px-6 text-2xl font-bold text-primary focus:ring-2 focus:ring-primary transition-all outline-none disabled:opacity-50 disabled:cursor-not-allowed"
                                               wire:model.live="scores.{{ $criterion->id }}"
                                               {{ $hasVoted ? 'disabled' : '' }}
                                               placeholder="0.0">
                                        <div class="absolute right-6 top-1/2 -translate-y-1/2 text-white/20 font-bold uppercase text-[10px] tracking-widest">
                                            Máx: {{ $criterion->max_score }}
                                        </div>
                                    </div>
                                    
                                    @error("scores.{$criterion->id}") <p class="text-error text-[10px] mt-2 font-bold uppercase tracking-tighter">{{ $message }}</p> @enderror
                                </div>
                            @endforeach

                            <div class="pt-6">
                                @if(!$hasVoted)
                                    <button type="submit" class="w-full py-4 bg-gradient-to-br from-primary to-primary-container text-white font-display uppercase tracking-widest rounded-2xl shadow-lg shadow-primary/20 hover:scale-[1.02] transition-transform flex items-center justify-center space-x-2 group">
                                        <span>Enviar Avaliação</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                @else
                                    <div class="w-full py-4 bg-secondary/10 border border-secondary/20 text-secondary text-center rounded-2xl font-display uppercase tracking-widest text-sm flex items-center justify-center space-x-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>Voto Registrado</span>
                                    </div>
                                    <p class="text-center text-[10px] text-white/20 mt-4 uppercase tracking-tighter">Aguarde o administrador chamar o próximo competidor.</p>
                                @endif
                            </div>
                        </form>
                    @else
                        <div class="flex flex-col items-center justify-center py-20 text-center">
                            @if($contest->status === 'FINALIZADO')
                                <div class="w-20 h-20 bg-primary/10 rounded-full flex items-center justify-center mb-6 border border-primary/20 shadow-[0_0_20px_rgba(237,134,255,0.2)]">
                                    <svg class="w-10 h-10 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <h3 class="font-display text-2xl text-white uppercase italic tracking-tighter mb-2">Concurso Encerrado</h3>
                                <p class="text-white/40 font-body max-w-xs mx-auto">Obrigado pela sua contribuição! As notas foram consolidadas.</p>
                                <a href="{{ route('contests.index') }}" class="mt-8 text-primary text-xs font-bold uppercase tracking-widest hover:underline">Voltar aos Concursos</a>
                            @else
                                <div class="w-16 h-16 border-4 border-secondary/20 border-t-secondary rounded-full animate-spin mb-6"></div>
                                <p class="text-white/40 font-body">Sincronizando com o palco...</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
