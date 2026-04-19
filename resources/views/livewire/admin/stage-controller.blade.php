<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-display text-4xl font-bold tracking-tight text-white uppercase italic">Controle de Palco</h1>
            <p class="text-white/40 font-admin">{{ $contest->name }}</p>
        </div>
        <div class="flex items-center space-x-4">
            <span class="bg-surface-container-high px-4 py-2 rounded-lg text-xs font-bold text-white/60 border border-outline-variant/20 uppercase tracking-widest">
                Status: {{ $contest->status }}
            </span>
            @if($contest->status !== 'FINALIZADO')
                <button wire:click="finishContest" wire:confirm="Tem certeza que deseja encerrar o concurso?" class="bg-error/20 text-error border border-error/30 p-2 px-6 rounded-xl font-bold hover:bg-error/30 transition-all shadow-lg shadow-error/10">
                    Encerrar Concurso
                </button>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Esquerda: Lista de Candidatos Aptos -->
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-surface-container-low rounded-2xl border border-outline-variant/10 overflow-hidden">
                <div class="p-6 border-b border-outline-variant/10 flex justify-between items-center">
                    <h2 class="font-display text-lg text-white font-bold uppercase tracking-tight">Candidatos no Backstage</h2>
                    <span class="text-xs text-white/40 italic">Apto + Check-in realizado</span>
                </div>
                <div class="divide-y divide-outline-variant/5">
                    @forelse($presentations as $p)
                        <div class="p-6 flex items-center justify-between group hover:bg-surface-container-high/30 transition-all">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-surface-container-highest rounded-xl flex items-center justify-center font-display text-xl text-primary font-bold">
                                    {{ $loop->iteration }}
                                </div>
                                <div>
                                    <p class="text-white font-bold">{{ $p->work_title }}</p>
                                    <p class="text-xs text-white/40 uppercase font-admin tracking-wider">{{ $p->competitor->name }}</p>
                                </div>
                            </div>
                            
                            @if($contest->current_presentation_id === $p->id)
                                <span class="bg-primary/20 text-primary border border-primary/30 px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-widest animate-pulse">
                                    No Palco
                                </span>
                            @elseif($p->checkin_realizado)
                                <button wire:click="setOnStage({{ $p->id }})" class="bg-secondary/10 text-secondary border border-secondary/20 px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-secondary/20 transition-all">
                                    Mandar ao Palco
                                </button>
                            @else
                                <span class="bg-white/5 text-white/20 border border-white/10 px-4 py-2 rounded-xl text-[10px] font-bold uppercase tracking-widest cursor-not-allowed" title="Competidor ainda não fez check-in">
                                    Offline
                                </span>
                            @endif
                        </div>
                    @empty
                        <div class="p-12 text-center text-white/20 italic">
                            Nenhum candidato pronto para o palco encontrado.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Direita: Status dos Jurados -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-surface-container-low rounded-3xl p-6 border border-outline-variant/10 sticky top-6">
                <h3 class="font-display text-white text-lg font-bold uppercase mb-6 border-b border-outline-variant/10 pb-4">
                    Status da Votação
                </h3>

                @if($contest->current_presentation_id)
                    <div class="space-y-4">
                        @foreach($contest->jurors as $juror)
                            <div class="flex items-center justify-between p-4 rounded-2xl bg-surface-container-highest/30 border border-outline-variant/5">
                                <div class="flex items-center space-x-3">
                                    <div class="w-2 h-2 rounded-full {{ in_array($juror->id, $votedJurors) ? 'bg-secondary animate-pulse shadow-[0_0_10px_rgba(0,238,252,0.5)]' : 'bg-white/10' }}"></div>
                                    <span class="text-sm font-bold {{ in_array($juror->id, $votedJurors) ? 'text-white' : 'text-white/40' }}">{{ $juror->name }}</span>
                                </div>
                                <span class="text-[10px] font-bold uppercase tracking-tighter {{ in_array($juror->id, $votedJurors) ? 'text-secondary' : 'text-white/20' }}">
                                    {{ in_array($juror->id, $votedJurors) ? 'VOTOU' : 'AGUARDANDO' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-8 p-4 rounded-2xl bg-primary/5 border border-primary/10">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[10px] font-bold text-white/40 uppercase tracking-widest">Progresso</span>
                            <span class="text-[10px] font-bold text-primary">{{ count($votedJurors) }} / {{ count($contest->jurors) }}</span>
                        </div>
                        <div class="h-1 w-full bg-white/5 rounded-full overflow-hidden">
                            <div class="h-full bg-primary transition-all duration-500" style="width: {{ count($contest->jurors) > 0 ? (count($votedJurors) / count($contest->jurors)) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-10 opacity-20 italic">
                        Inicie o concurso para acompanhar os votos.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
