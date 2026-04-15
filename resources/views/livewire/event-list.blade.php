<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="font-display text-4xl font-bold tracking-tight text-white uppercase">Gestão de Eventos</h1>
        <button wire:click="openModal" class="bg-gradient-to-br from-primary to-primary-container p-3 px-6 rounded-xl font-bold text-surface hover:opacity-90 transition-all shadow-lg shadow-primary/20 flex items-center space-x-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Novo Evento</span>
        </button>
    </div>

    <div class="bg-surface-container-low rounded-2xl overflow-hidden border border-outline-variant/10 shadow-2xl">
        <table class="w-full text-left font-admin">
            <thead>
                <tr class="bg-surface-container-high/50 text-white/60 text-xs uppercase tracking-widest">
                    <th class="px-8 py-4 font-bold">Nome do Evento</th>
                    <th class="px-8 py-4 font-bold">Data</th>
                    <th class="px-8 py-4 font-bold">Descrição</th>
                    <th class="px-8 py-4 font-bold text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/5">
                @forelse($events as $event)
                    <tr class="group hover:bg-surface-container-high/30 transition-all duration-300">
                        <td class="px-8 py-6">
                            <span class="block text-white font-bold group-hover:text-primary transition-colors">{{ $event->name }}</span>
                        </td>
                        <td class="px-8 py-6">
                            <span class="bg-secondary/10 text-secondary text-xs px-3 py-1 rounded-full border border-secondary/20">
                                {{ \Carbon\Carbon::parse($event->event_date)->format('d/m/Y') }}
                            </span>
                        </td>
                        <td class="px-8 py-6 text-white/60 text-sm truncate max-w-xs">
                            {{ $event->description ?: 'Sem descrição' }}
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex items-center justify-end space-x-3">
                                <button wire:click="openModal({{ $event->id }})" class="text-white/40 hover:text-primary transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button wire:click="delete({{ $event->id }})" wire:confirm="Tem certeza que deseja deletar este evento?" class="text-white/40 hover:text-error transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-8 py-12 text-center text-white/40 italic">Nenhum evento cadastrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="p-6 border-t border-outline-variant/10">
            {{ $events->links() }}
        </div>
    </div>

    <!-- Modal (Glassmorphism) -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-6 backdrop-blur-md bg-surface/40">
            <div class="bg-surface-container-high w-full max-w-lg rounded-2xl border border-outline-variant/20 shadow-2xl p-8 transform transition-all">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="font-display text-2xl font-bold tracking-tight text-white uppercase italic">
                        {{ $editingEvent ? 'Editar Evento' : 'Novo Evento' }}
                    </h3>
                    <button wire:click="$set('showModal', false)" class="text-white/40 hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="space-y-6">
                    <div>
                        <label class="block text-xs font-admin font-bold text-white/40 uppercase tracking-widest mb-2">Nome do Evento</label>
                        <input wire:model="name" type="text" class="w-full bg-surface-container-highest border-none rounded-lg p-3 text-white focus:ring-2 focus:ring-primary transition-all outline-none">
                        @error('name') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-admin font-bold text-white/40 uppercase tracking-widest mb-2">Data do Evento</label>
                        <input wire:model="event_date" type="date" class="w-full bg-surface-container-highest border-none rounded-lg p-3 text-white focus:ring-2 focus:ring-primary transition-all outline-none">
                        @error('event_date') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-admin font-bold text-white/40 uppercase tracking-widest mb-2">Descrição</label>
                        <textarea wire:model="description" rows="4" class="w-full bg-surface-container-highest border-none rounded-lg p-3 text-white focus:ring-2 focus:ring-primary transition-all outline-none"></textarea>
                        @error('description') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-4 flex items-center justify-end space-x-4">
                        <button type="button" wire:click="$set('showModal', false)" class="text-white/60 hover:text-white font-bold transition-colors">Cancelar</button>
                        <button type="submit" class="bg-gradient-to-br from-primary to-primary-container px-8 py-3 rounded-xl font-bold text-surface hover:opacity-90 transition-all shadow-lg shadow-primary/20">
                            {{ $editingEvent ? 'Salvar Alterações' : 'Criar Evento' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
