<div class="space-y-8 p-6 bg-surface min-h-screen text-white">
    @if (session()->has('message'))
        <div class="bg-primary/20 text-primary p-4 rounded-lg border border-primary/30 backdrop-blur-md">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Enrollment Form -->
        <div class="bg-surface-container-low p-6 rounded-2xl border border-outline-variant/20 shadow-xl">
            <h2 class="text-2xl font-bold mb-6 font-manrope">Nova Inscrição</h2>
            <form wire:submit.prevent="enroll" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Concurso</label>
                    <select wire:model="contest_id" class="w-full bg-surface-container-highest border-none rounded-lg p-3 focus:ring-2 focus:ring-primary">
                        <option value="">Selecione um concurso</option>
                        @foreach($contests as $contest)
                            <option value="{{ $contest->id }}">{{ $contest->name }}</option>
                        @endforeach
                    </select>
                    @error('contest_id') <span class="text-error text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Título do Trabalho</label>
                    <input type="text" wire:model="work_title" placeholder="Ex: Cosplay de Naruto" class="w-full bg-surface-container-highest border-none rounded-lg p-3 focus:ring-2 focus:ring-primary">
                    @error('work_title') <span class="text-error text-xs">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-primary to-primary-container p-3 rounded-lg font-bold text-surface shadow-lg hover:opacity-90 transition-opacity">
                    Inscrever-se
                </button>
            </form>
        </div>

        <!-- Document Upload -->
        <div class="bg-surface-container-low p-6 rounded-2xl border border-outline-variant/20 shadow-xl">
            <h2 class="text-2xl font-bold mb-6 font-manrope">Gestão de Documentos</h2>
            <form wire:submit.prevent="uploadDocument" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Tipo de Documento</label>
                    <input type="text" wire:model="document_type" placeholder="Ex: Termo de Responsabilidade" class="w-full bg-surface-container-highest border-none rounded-lg p-3 focus:ring-2 focus:ring-primary">
                    @error('document_type') <span class="text-error text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Arquivo (PDF, PNG, JPG)</label>
                    <input type="file" wire:model="document_file" class="w-full bg-surface-container-highest border-none rounded-lg p-2 file:bg-surface-container-highest file:text-white file:border-none file:px-4 file:py-2 file:rounded-lg file:mr-4">
                    @error('document_file') <span class="text-error text-xs">{{ $message }}</span> @enderror
                </div>

                <div wire:loading wire:target="document_file" class="text-primary text-xs">Enviando arquivo...</div>

                <button type="submit" class="w-full bg-gradient-to-r from-secondary to-primary p-3 rounded-lg font-bold text-surface shadow-lg hover:opacity-90 transition-opacity">
                    Enviar Documento
                </button>
            </form>
        </div>
    </div>

    <!-- My Presentations -->
    <div class="bg-surface-container-low p-6 rounded-2xl border border-outline-variant/20 shadow-xl">
        <h2 class="text-2xl font-bold mb-6 font-manrope">Minhas Apresentações</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="text-outline-variant uppercase text-xs font-bold">
                    <tr>
                        <th class="p-4">Concurso</th>
                        <th class="p-4">Trabalho</th>
                        <th class="p-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/10">
                    @foreach($presentations as $presentation)
                        <tr>
                            <td class="p-4">{{ $presentation->contest->name }}</td>
                            <td class="p-4 font-bold">{{ $presentation->work_title }}</td>
                            <td class="p-4">
                                @if($presentation->status === 'APTO')
                                    <span class="px-3 py-1 bg-secondary/10 text-secondary rounded-full text-xs font-bold border border-secondary/20">APTO</span>
                                @elseif($presentation->status === 'INAPTO')
                                    <span class="px-3 py-1 bg-error/10 text-error rounded-full text-xs font-bold border border-error/20" title="{{ $presentation->justification_inapto }}">INAPTO</span>
                                @else
                                    <span class="px-3 py-1 bg-yellow-500/10 text-yellow-500 rounded-full text-xs font-bold border border-yellow-500/20">EM ANÁLISE</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- My Documents -->
    <div class="bg-surface-container-low p-6 rounded-2xl border border-outline-variant/20 shadow-xl">
        <h2 class="text-2xl font-bold mb-6 font-manrope">Documentos Enviados</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($documents as $doc)
                <div class="p-4 bg-surface-container-highest rounded-lg border border-outline-variant/20 flex justify-between items-center">
                    <div>
                        <p class="font-bold">{{ $doc->type }}</p>
                        <p class="text-xs text-outline-variant">{{ $doc->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <a href="{{ route('documents.download', $doc->id) }}" class="text-primary hover:text-primary-container">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>
