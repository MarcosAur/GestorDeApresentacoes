<div class="p-6 bg-surface min-h-screen text-white">
    @if (session()->has('message'))
        <div class="bg-primary/20 text-primary p-4 rounded-lg border border-primary/30 mb-6 backdrop-blur-md">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- List -->
        <div class="lg:col-span-2 bg-surface-container-low p-6 rounded-2xl border border-outline-variant/20 shadow-xl">
            <h2 class="text-2xl font-bold mb-6 font-manrope">Aguardando Análise</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="text-outline-variant uppercase text-xs font-bold">
                        <tr>
                            <th class="p-4">Competidor</th>
                            <th class="p-4">Concurso</th>
                            <th class="p-4">Trabalho</th>
                            <th class="p-4">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/10">
                        @foreach($presentations as $presentation)
                            <tr class="{{ $selectedPresentation?->id === $presentation->id ? 'bg-primary/5' : '' }}">
                                <td class="p-4">{{ $presentation->competitor->name }}</td>
                                <td class="p-4">{{ $presentation->contest->name }}</td>
                                <td class="p-4 font-bold">{{ $presentation->work_title }}</td>
                                <td class="p-4">
                                    <button wire:click="selectPresentation({{ $presentation->id }})" class="bg-primary/20 text-primary px-3 py-1 rounded-lg text-xs font-bold border border-primary/30 hover:bg-primary/30 transition-colors">
                                        Analisar
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Analyzer Details -->
        <div class="bg-surface-container-low p-6 rounded-2xl border border-outline-variant/20 shadow-xl">
            @if($selectedPresentation)
                <h2 class="text-2xl font-bold mb-6 font-manrope">Detalhes da Apresentação</h2>
                
                <div class="space-y-4 mb-8">
                    <div>
                        <p class="text-xs text-outline-variant font-bold">COMPETIDOR</p>
                        <p>{{ $selectedPresentation->competitor->name }} ({{ $selectedPresentation->competitor->email }})</p>
                    </div>
                    <div>
                        <p class="text-xs text-outline-variant font-bold">CONCURSO</p>
                        <p>{{ $selectedPresentation->contest->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-outline-variant font-bold">TRABALHO</p>
                        <p class="font-bold text-lg text-primary">{{ $selectedPresentation->work_title }}</p>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-sm font-bold mb-3 uppercase text-outline-variant">Documentos Anexados</h3>
                    <div class="space-y-2">
                        @foreach($selectedPresentation->competitor->documents as $doc)
                            <div class="p-3 bg-surface-container-highest rounded-lg border border-outline-variant/20 flex justify-between items-center">
                                <span class="text-sm">{{ $doc->type }}</span>
                                <a href="{{ route('documents.download', $doc->id) }}" class="text-primary hover:text-primary-container">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>

                <hr class="border-outline-variant/20 mb-8">

                <form wire:submit.prevent="evaluate" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Avaliação Final</label>
                        <select wire:model.live="form.status" class="w-full bg-surface-container-highest border-none rounded-lg p-3 focus:ring-2 focus:ring-primary">
                            <option value="EM_ANALISE">Em Análise</option>
                            <option value="APTO">APTO</option>
                            <option value="INAPTO">INAPTO</option>
                        </select>
                    </div>

                    @if($form->status === 'INAPTO')
                        <div>
                            <label class="block text-sm font-medium mb-1">Justificativa da Reprovação</label>
                            <textarea wire:model="form.justification_inapto" rows="4" class="w-full bg-surface-container-highest border-none rounded-lg p-3 focus:ring-2 focus:ring-primary" placeholder="Descreva o motivo pelo qual o competidor não está apto..."></textarea>
                            @error('form.justification_inapto') <span class="text-error text-xs">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    <button type="submit" class="w-full bg-gradient-to-r from-primary to-primary-container p-3 rounded-lg font-bold text-surface shadow-lg hover:opacity-90 transition-opacity">
                        Salvar Avaliação
                    </button>
                </form>
            @else
                <div class="flex flex-col items-center justify-center h-full text-outline-variant opacity-50 space-y-4">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <p>Selecione uma apresentação para iniciar a análise.</p>
                </div>
            @endif
        </div>
    </div>
</div>
