<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="font-display text-4xl font-bold tracking-tight text-white uppercase">Gestão de Concursos</h1>
        <button wire:click="openModal" class="bg-gradient-to-br from-primary to-primary-container p-3 px-6 rounded-xl font-bold text-surface hover:opacity-90 transition-all shadow-lg shadow-primary/20 flex items-center space-x-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Novo Concurso</span>
        </button>
    </div>

    <div class="bg-surface-container-low rounded-2xl overflow-hidden border border-outline-variant/10 shadow-2xl">
        <table class="w-full text-left font-admin">
            <thead>
                <tr class="bg-surface-container-high/50 text-white/60 text-xs uppercase tracking-widest">
                    <th class="px-8 py-4 font-bold">Concurso</th>
                    <th class="px-8 py-4 font-bold">Evento Pai</th>
                    <th class="px-8 py-4 font-bold">Status</th>
                    <th class="px-8 py-4 font-bold text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/5">
                @forelse($contests as $contest)
                    <tr class="group hover:bg-surface-container-high/30 transition-all duration-300">
                        <td class="px-8 py-6">
                            <span class="block text-white font-bold group-hover:text-primary transition-colors">{{ $contest->name }}</span>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-white/60 font-medium">{{ $contest->event->name }}</span>
                        </td>
                        <td class="px-8 py-6">
                            @php
                                $statusClasses = [
                                    'AGENDADO' => 'bg-secondary/10 text-secondary border-secondary/20',
                                    'EM_ANDAMENTO' => 'bg-primary/10 text-primary border-primary/20 animate-pulse',
                                    'FINALIZADO' => 'bg-error/10 text-error border-error/20'
                                ];
                            @endphp
                            <span class="{{ $statusClasses[$contest->status] }} text-[10px] font-bold px-3 py-1 rounded-full border uppercase tracking-widest">
                                {{ $contest->status }}
                            </span>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex items-center justify-end space-x-3">
                                <button wire:click="openModal({{ $contest->id }})" class="text-white/40 hover:text-primary transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button wire:click="delete({{ $contest->id }})" wire:confirm="Tem certeza que deseja deletar este concurso?" class="text-white/40 hover:text-error transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-8 py-12 text-center text-white/40 italic">Nenhum concurso cadastrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-6 border-t border-outline-variant/10">
            {{ $contests->links() }}
        </div>
    </div>

    <!-- Modal (Barema Editor) -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-6 backdrop-blur-md bg-surface/40 overflow-y-auto">
            <div class="bg-surface-container-high w-full max-w-2xl rounded-2xl border border-outline-variant/20 shadow-2xl p-8 transform transition-all my-auto">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="font-display text-2xl font-bold tracking-tight text-white uppercase italic">
                        {{ $editingContest ? 'Editar Concurso' : 'Novo Concurso' }}
                    </h3>
                    <button wire:click="$set('showModal', false)" class="text-white/40 hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="space-y-8">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <label class="block text-xs font-admin font-bold text-white/40 uppercase tracking-widest mb-2">Evento Pai</label>
                            <select wire:model="form.event_id" class="w-full bg-surface-container-highest border-none rounded-lg p-3 text-white focus:ring-2 focus:ring-primary transition-all outline-none">
                                <option value="">Selecione um evento</option>
                                @foreach($events as $event)
                                    <option value="{{ $event->id }}">{{ $event->name }}</option>
                                @endforeach
                            </select>
                            @error('form.event_id') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-admin font-bold text-white/40 uppercase tracking-widest mb-2">Nome do Concurso</label>
                            <input wire:model="form.name" type="text" class="w-full bg-surface-container-highest border-none rounded-lg p-3 text-white focus:ring-2 focus:ring-primary transition-all outline-none">
                            @error('form.name') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-admin font-bold text-white/40 uppercase tracking-widest mb-2">Status Inicial</label>
                            <select wire:model="form.status" class="w-full bg-surface-container-highest border-none rounded-lg p-3 text-white focus:ring-2 focus:ring-primary transition-all outline-none">
                                <option value="AGENDADO">AGENDADO</option>
                                <option value="EM_ANDAMENTO">EM_ANDAMENTO</option>
                                <option value="FINALIZADO">FINALIZADO</option>
                            </select>
                        </div>
                    </div>

                    <!-- Jurors Section -->
                    <div class="space-y-4">
                        <div class="border-b border-outline-variant/10 pb-4">
                            <h4 class="font-display text-lg font-bold text-primary uppercase tracking-tighter">Jurados do Concurso</h4>
                        </div>
                        <div class="grid grid-cols-2 gap-3 max-h-32 overflow-y-auto pr-2">
                            @foreach($availableJurors as $juror)
                                <label class="flex items-center space-x-3 p-2 bg-surface-container-low rounded-lg border border-outline-variant/5 cursor-pointer hover:bg-surface-container-high transition-colors">
                                    <input type="checkbox" wire:model="form.selectedJurors" value="{{ $juror->id }}" class="w-4 h-4 rounded border-outline-variant bg-surface-container-highest text-primary focus:ring-primary">
                                    <span class="text-xs font-bold text-white">{{ $juror->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        @if($availableJurors->isEmpty())
                            <p class="text-[10px] text-white/30 italic">Nenhum jurado cadastrado no sistema.</p>
                        @endif
                    </div>

                    <!-- Critérios Section -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between border-b border-outline-variant/10 pb-4">
                            <h4 class="font-display text-lg font-bold text-secondary uppercase tracking-tighter">Critérios de Avaliação</h4>
                            <button type="button" wire:click="addCriterion" class="text-xs font-bold bg-secondary/10 text-secondary px-3 py-1 rounded-full border border-secondary/20 hover:bg-secondary/20 transition-all">
                                + Adicionar Critério
                            </button>
                        </div>
                        @error('criteria_tiebreak') <p class="text-error text-xs font-bold text-center italic">{{ $message }}</p> @enderror

                        @if(!empty($form->criteria))
                            <div class="grid grid-cols-12 gap-3 px-4 text-[10px] font-bold text-white/40 uppercase tracking-widest">
                                <div class="col-span-5">Nome do Critério</div>
                                <div class="col-span-2 text-center">Nota Máx.</div>
                                <div class="col-span-2 text-center">Peso</div>
                                <div class="col-span-2 text-center">Prioridade</div>
                                <div class="col-span-1"></div>
                            </div>
                        @endif

                        <div class="space-y-3">
                            @foreach($form->criteria as $index => $criterion)
                                <div class="grid grid-cols-12 gap-3 items-center bg-surface-container-low p-4 rounded-xl border border-outline-variant/5">
                                    <div class="col-span-5">
                                        <input wire:model="form.criteria.{{ $index }}.name" type="text" placeholder="Ex: Originalidade" class="w-full bg-surface-container-highest border-none rounded-lg p-2 text-sm text-white focus:ring-1 focus:ring-secondary transition-all outline-none">
                                    </div>
                                    <div class="col-span-2">
                                        <input wire:model="form.criteria.{{ $index }}.max_score" type="number" step="0.5" placeholder="10.0" class="w-full bg-surface-container-highest border-none rounded-lg p-2 text-sm text-center text-white focus:ring-1 focus:ring-secondary transition-all outline-none">
                                    </div>
                                    <div class="col-span-2">
                                        <input wire:model="form.criteria.{{ $index }}.weight" type="number" step="0.1" placeholder="1.0" class="w-full bg-surface-container-highest border-none rounded-lg p-2 text-sm text-center text-white focus:ring-1 focus:ring-secondary transition-all outline-none">
                                    </div>
                                    <div class="col-span-2">
                                        <input wire:model="form.criteria.{{ $index }}.tiebreak_priority" type="number" placeholder="1" class="w-full bg-surface-container-highest border-none rounded-lg p-2 text-sm text-center text-white focus:ring-1 focus:ring-secondary transition-all outline-none">
                                    </div>
                                    <div class="col-span-1 text-right">
                                        <button type="button" wire:click="removeCriterion({{ $index }})" class="text-white/20 hover:text-error transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                            @if(empty($form->criteria))
                                <p class="text-center text-xs text-white/30 italic py-4">Nenhum critério definido. Adicione ao menos um.</p>
                            @endif
                        </div>
                    </div>

                    <div class="pt-4 flex items-center justify-end space-x-4 border-t border-outline-variant/10 pt-8">
                        <button type="button" wire:click="$set('showModal', false)" class="text-white/60 hover:text-white font-bold transition-colors">Cancelar</button>
                        <button type="submit" class="bg-gradient-to-br from-primary to-primary-container px-10 py-3 rounded-xl font-bold text-surface hover:opacity-90 transition-all shadow-lg shadow-primary/20">
                            {{ $editingContest ? 'Salvar Tudo' : 'Criar Concurso' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
