<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="font-display text-4xl font-bold tracking-tight text-white uppercase">Gestão de Jurados</h1>
        <button wire:click="openModal" class="bg-gradient-to-br from-primary to-primary-container p-3 px-6 rounded-xl font-bold text-surface hover:opacity-90 transition-all shadow-lg shadow-primary/20 flex items-center space-x-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Novo Jurado</span>
        </button>
    </div>

    <div class="bg-surface-container-low rounded-2xl overflow-hidden border border-outline-variant/10 shadow-2xl">
        <table class="w-full text-left font-admin">
            <thead>
                <tr class="bg-surface-container-high/50 text-white/60 text-xs uppercase tracking-widest">
                    <th class="px-8 py-4 font-bold">Jurado</th>
                    <th class="px-8 py-4 font-bold">Email</th>
                    <th class="px-8 py-4 font-bold">Concursos Vinculados</th>
                    <th class="px-8 py-4 font-bold text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/5">
                @forelse($jurors as $juror)
                    <tr class="group hover:bg-surface-container-high/30 transition-all duration-300">
                        <td class="px-8 py-6">
                            <span class="block text-white font-bold group-hover:text-primary transition-colors">{{ $juror->name }}</span>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-white/60 font-medium">{{ $juror->email }}</span>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex flex-wrap gap-2">
                                @forelse($juror->contests as $contest)
                                    <span class="bg-secondary/10 text-secondary text-[10px] font-bold px-3 py-1 rounded-full border border-secondary/20">
                                        {{ $contest->name }}
                                    </span>
                                @empty
                                    <span class="text-white/20 text-xs italic">Nenhum vínculo</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex items-center justify-end space-x-3">
                                <button wire:click="openModal({{ $juror->id }})" class="text-white/40 hover:text-primary transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button wire:click="delete({{ $juror->id }})" wire:confirm="Tem certeza que deseja remover este jurado?" class="text-white/40 hover:text-error transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-8 py-12 text-center text-white/40 italic">Nenhum jurado cadastrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-6 border-t border-outline-variant/10">
            {{ $jurors->links() }}
        </div>
    </div>

    <!-- Modal (Juror Editor) -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-6 backdrop-blur-md bg-surface/40 overflow-y-auto">
            <div class="bg-surface-container-high w-full max-w-xl rounded-2xl border border-outline-variant/20 shadow-2xl p-8 transform transition-all my-auto">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="font-display text-2xl font-bold tracking-tight text-white uppercase italic">
                        {{ $editingJuror ? 'Editar Jurado' : 'Novo Jurado' }}
                    </h3>
                    <button wire:click="$set('showModal', false)" class="text-white/40 hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="space-y-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-admin font-bold text-white/40 uppercase tracking-widest mb-2">Nome Completo</label>
                            <input wire:model="form.name" type="text" class="w-full bg-surface-container-highest border-none rounded-lg p-3 text-white focus:ring-2 focus:ring-primary transition-all outline-none">
                            @error('form.name') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-admin font-bold text-white/40 uppercase tracking-widest mb-2">E-mail</label>
                            <input wire:model="form.email" type="email" class="w-full bg-surface-container-highest border-none rounded-lg p-3 text-white focus:ring-2 focus:ring-primary transition-all outline-none">
                            @error('form.email') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-span-2">
                            <label class="block text-xs font-admin font-bold text-white/40 uppercase tracking-widest mb-2">Senha {{ $editingJuror ? '(Deixe em branco para não alterar)' : '' }}</label>
                            <input wire:model="form.password" type="password" class="w-full bg-surface-container-highest border-none rounded-lg p-3 text-white focus:ring-2 focus:ring-primary transition-all outline-none">
                            @error('form.password') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-admin font-bold text-white/40 uppercase tracking-widest mb-4">Concursos Vinculados</label>
                        <div class="grid grid-cols-2 gap-3 max-h-48 overflow-y-auto pr-2">
                            @foreach($contests as $contest)
                                <label class="flex items-center space-x-3 p-3 bg-surface-container-low rounded-xl border border-outline-variant/5 cursor-pointer hover:bg-surface-container-high transition-colors">
                                    <input type="checkbox" wire:model="form.selectedContests" value="{{ $contest->id }}" class="w-5 h-5 rounded border-outline-variant bg-surface-container-highest text-primary focus:ring-primary">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-white">{{ $contest->name }}</span>
                                        <span class="text-[10px] text-white/40">{{ $contest->event->name }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('form.selectedContests') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-4 flex items-center justify-end space-x-4">
                        <button type="button" wire:click="$set('showModal', false)" class="text-white/60 hover:text-white font-bold transition-colors">Cancelar</button>
                        <button type="submit" class="bg-gradient-to-br from-primary to-primary-container px-10 py-3 rounded-xl font-bold text-surface hover:opacity-90 transition-all shadow-lg shadow-primary/20">
                            {{ $editingJuror ? 'Salvar Alterações' : 'Cadastrar Jurado' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
