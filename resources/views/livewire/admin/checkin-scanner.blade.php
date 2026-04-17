<div class="p-6 min-h-screen bg-surface text-white">
    <div class="max-w-2xl mx-auto">
        <header class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold font-space-grotesk tracking-tight text-primary">Check-in / Credenciamento</h1>
                <p class="text-outline-variant text-sm">Insira o Hash ou utilize o scanner de QR Code.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="p-2 bg-surface-container-high rounded-lg hover:bg-surface-bright transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </a>
        </header>

        <!-- Mensagens de Feedback -->
        @if (session()->has('success'))
            <div class="mb-6 p-4 bg-secondary/10 border border-secondary/30 rounded-xl flex items-center gap-3 animate-pulse">
                <div class="w-10 h-10 rounded-full bg-secondary/20 flex items-center justify-center text-secondary">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <p class="font-bold text-secondary">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 p-4 bg-error/10 border border-error/30 rounded-xl flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-error/20 flex items-center justify-center text-error">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
                <div>
                    <p class="font-bold text-error">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if (session()->has('warning'))
            <div class="mb-6 p-4 bg-yellow-500/10 border border-yellow-500/30 rounded-xl flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-yellow-500/20 flex items-center justify-center text-yellow-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <p class="font-bold text-yellow-500">{{ session('warning') }}</p>
                </div>
            </div>
        @endif

        <!-- Entrada Principal -->
        <div class="bg-surface-container-low rounded-3xl overflow-hidden border border-outline-variant/20 shadow-2xl mb-6">
            <div class="p-8">
                <label for="manualHash" class="block text-sm font-bold text-outline-variant uppercase tracking-wider mb-4">Entrada Manual / Leitor USB</label>
                <div class="flex gap-4">
                    <div class="relative flex-1">
                        <input 
                            type="text" 
                            id="manualHash"
                            wire:model="manualHash" 
                            wire:keydown.enter="processCheckin(manualHash)"
                            placeholder="Aguardando leitura..." 
                            class="w-full bg-surface-container-highest border-none rounded-xl py-4 pl-12 pr-4 text-white focus:ring-2 focus:ring-secondary transition-all"
                            autofocus
                        >
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-outline-variant">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A10.003 10.003 0 0012 21a10.003 10.003 0 008.384-4.562l.054.09m-3.333-4.41V11a6.003 6.003 0 00-12 0v2.581m6.708 3.976l.142.143m.454.454a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    
                    <button 
                        wire:click="toggleCamera"
                        class="p-4 rounded-xl font-bold transition-all flex items-center gap-2 {{ $isCameraActive ? 'bg-error text-white shadow-[0_0_15px_rgba(255,110,132,0.4)]' : 'bg-surface-container-highest text-secondary border border-secondary/20 hover:bg-secondary/10' }}"
                        title="Ativar/Desativar Câmera"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </button>

                    <button 
                        wire:click="processCheckin(manualHash)"
                        class="bg-secondary px-6 rounded-xl font-bold text-surface hover:bg-secondary/90 transition-colors shadow-[0_0_20px_rgba(0,238,252,0.3)]"
                    >
                        Validar
                    </button>
                </div>
            </div>

            <!-- Área da Câmera Condicional -->
            <div x-data="{ active: @entangle('isCameraActive') }" x-show="active" x-transition.opacity.duration.500ms class="relative">
                <div class="aspect-video w-full bg-black relative flex items-center justify-center border-t border-outline-variant/10">
                    <div id="reader" class="w-full h-full"></div>
                    
                    <!-- Viewfinder Overlay -->
                    <div class="absolute inset-0 pointer-events-none border-[40px] border-black/60 z-10 flex items-center justify-center">
                        <div class="w-48 h-48 border-2 border-outline-variant/20 relative">
                            <div class="absolute -top-2 -left-2 w-8 h-8 border-t-4 border-l-4 border-secondary shadow-[0_0_15px_rgba(0,238,252,0.5)]"></div>
                            <div class="absolute -top-2 -right-2 w-8 h-8 border-t-4 border-r-4 border-secondary shadow-[0_0_15px_rgba(0,238,252,0.5)]"></div>
                            <div class="absolute -bottom-2 -left-2 w-8 h-8 border-b-4 border-l-4 border-secondary shadow-[0_0_15px_rgba(0,238,252,0.5)]"></div>
                            <div class="absolute -bottom-2 -right-2 w-8 h-8 border-b-4 border-r-4 border-secondary shadow-[0_0_15px_rgba(0,238,252,0.5)]"></div>
                            
                            <div class="absolute top-0 left-0 w-full h-0.5 bg-gradient-to-r from-transparent via-secondary to-transparent animate-scan-line shadow-[0_0_10px_rgba(0,238,252,0.8)]"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        let html5QrCode = null;

        const startScanner = async () => {
            const reader = document.getElementById('reader');
            if (!reader) return;

            html5QrCode = new Html5Qrcode("reader");
            const config = { fps: 15, qrbox: { width: 200, height: 200 } };
            
            try {
                await html5QrCode.start(
                    { facingMode: "environment" }, 
                    config, 
                    (decodedText) => {
                        html5QrCode.pause();
                        @this.processCheckin(decodedText);
                    }
                );
            } catch (err) {
                console.error("Erro ao iniciar câmera:", err);
            }
        };

        const stopScanner = async () => {
            if (html5QrCode && html5QrCode.isScanning) {
                await html5QrCode.stop();
                html5QrCode = null;
            }
        };

        document.addEventListener('camera-activated', startScanner);
        document.addEventListener('camera-deactivated', stopScanner);

        window.addEventListener('checkin-completed', () => {
            if (html5QrCode) {
                setTimeout(() => html5QrCode.resume(), 2000);
            }
        });

        // Garantir limpeza ao sair da página
        document.addEventListener('livewire:navigating', stopScanner);
    </script>

    <style>
        @keyframes scan-line {
            0% { top: 0; }
            100% { top: 100%; }
        }
        .animate-scan-line {
            animation: scan-line 2s ease-in-out infinite;
        }
        #reader { border: none !important; }
        #reader video { object-fit: cover !important; }
    </style>
</div>
