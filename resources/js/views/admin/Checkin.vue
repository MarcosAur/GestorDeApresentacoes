<template>
    <AuthenticatedLayout>
        <div class="max-w-2xl mx-auto space-y-8">
            <header>
                <h1 class="text-3xl font-display font-bold text-primary tracking-tight">Check-in / Credenciamento</h1>
                <p class="text-white/50 mt-1">Valide o QR Code do competidor para confirmar sua presença.</p>
            </header>

            <BaseCard>
                <div class="space-y-6">
                    <!-- Scanner Container -->
                    <div class="relative overflow-hidden rounded-2xl bg-black aspect-video border border-outline-variant/20 flex items-center justify-center">
                        <div id="reader" class="w-full"></div>
                        <div v-if="!isCameraActive" class="text-center space-y-4 p-8">
                            <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto text-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <BaseButton @click="startScanner">Ativar Câmera</BaseButton>
                        </div>
                    </div>

                    <!-- Manual Input -->
                    <div class="flex items-end space-x-2">
                        <div class="flex-1">
                            <BaseInput 
                                v-model="manualHash" 
                                label="Entrada Manual (Hash)" 
                                placeholder="Digite o código manualmente..."
                                @keyup.enter="handleManualCheckin"
                            />
                        </div>
                        <BaseButton @click="handleManualCheckin" variant="secondary">Validar</BaseButton>
                    </div>

                    <!-- Status / Feedback -->
                    <transition name="fade">
                        <div v-if="feedback" :class="feedbackClass" class="p-4 rounded-lg border flex items-center space-x-3">
                            <svg v-if="feedbackType === 'success'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="font-bold text-sm">{{ feedback }}</span>
                        </div>
                    </transition>
                </div>
            </BaseCard>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onUnmounted } from 'vue';
import { Html5Qrcode } from 'html5-qrcode';
import axios from 'axios';
import AuthenticatedLayout from '../../components/AuthenticatedLayout.vue';
import BaseCard from '../../components/BaseCard.vue';
import BaseButton from '../../components/BaseButton.vue';
import BaseInput from '../../components/BaseInput.vue';

const isCameraActive = ref(false);
const manualHash = ref('');
const feedback = ref('');
const feedbackType = ref('');
const feedbackClass = ref('');
let html5QrCode = null;

onUnmounted(() => {
    stopScanner();
});

async function startScanner() {
    isCameraActive.value = true;
    html5QrCode = new Html5Qrcode("reader");
    
    try {
        await html5QrCode.start(
            { facingMode: "environment" },
            {
                fps: 10,
                qrbox: { width: 250, height: 250 }
            },
            (decodedText) => {
                processCheckin(decodedText);
            },
            (errorMessage) => {
                // Ignore scanner noise
            }
        );
    } catch (err) {
        console.error("Unable to start scanner", err);
        isCameraActive.value = false;
        showFeedback('Erro ao acessar a câmera.', 'error');
    }
}

async function stopScanner() {
    if (html5QrCode && html5QrCode.isScanning) {
        await html5QrCode.stop();
    }
    isCameraActive.value = false;
}

async function handleManualCheckin() {
    if (!manualHash.value) return;
    await processCheckin(manualHash.value);
}

async function processCheckin(hash) {
    try {
        const response = await axios.post('/api/checkin', { hash });
        showFeedback(response.data.message, 'success');
        manualHash.value = '';
        
        // Brief delay before re-enabling scanner or processing next
        if (isCameraActive.value) {
            // Optional: pause or highlight success
        }
    } catch (error) {
        const msg = error.response?.data?.message || 'Erro ao processar check-in.';
        showFeedback(msg, 'error');
    }
}

function showFeedback(message, type) {
    feedback.value = message;
    feedbackType.value = type;
    feedbackClass.value = type === 'success' 
        ? 'bg-secondary/10 border-secondary/20 text-secondary' 
        : 'bg-error/10 border-error/20 text-error';
    
    setTimeout(() => {
        feedback.value = '';
    }, 5000);
}
</script>
