<template>
    <AuthenticatedLayout>
        <div class="space-y-8">
            <header>
                <h1 class="text-3xl font-display font-bold text-primary tracking-tight">Análise de Inscrições</h1>
                <p class="text-white/50 mt-1">Valide as inscrições pendentes dos competidores.</p>
            </header>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- List of Pending Presentations -->
                <div class="lg:col-span-7 space-y-4">
                    <BaseCard v-for="pres in presentations" :key="pres.id" 
                        hoverable 
                        :class="selectedPresentation?.id === pres.id ? 'border-primary/50 bg-primary/5' : ''"
                        @click="selectPresentation(pres)"
                        class="cursor-pointer"
                    >
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-bold text-white">{{ pres.competitor?.name }}</h3>
                                <p class="text-white/50 text-sm italic">"{{ pres.work_title }}"</p>
                                <div class="mt-2 flex items-center space-x-2">
                                    <span class="px-2 py-0.5 rounded bg-white/10 text-[10px] text-white/40 uppercase font-bold">
                                        {{ pres.contest?.name }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-[10px] text-white/30 uppercase font-bold">
                                {{ formatDate(pres.created_at) }}
                            </div>
                        </div>
                    </BaseCard>

                    <div v-if="!loading && !presentations.length" class="text-center py-12 bg-white/5 rounded-2xl border border-dashed border-white/10">
                        <p class="text-white/20 italic">Nenhuma inscrição pendente para análise.</p>
                    </div>
                </div>

                <!-- Analysis Panel -->
                <div class="lg:col-span-5">
                    <div class="sticky top-8">
                        <BaseCard v-if="selectedPresentation">
                            <template #header>
                                <h3 class="text-lg font-display font-bold text-white">Analisar Inscrição</h3>
                            </template>

                            <div class="space-y-6">
                                <div class="p-4 bg-white/5 rounded-lg border border-outline-variant/10">
                                    <p class="text-[10px] text-white/30 uppercase font-bold mb-1">Competidor</p>
                                    <p class="text-white font-bold">{{ selectedPresentation.competitor?.name }}</p>
                                    <p class="text-white/50 text-xs">{{ selectedPresentation.competitor?.email }}</p>
                                </div>

                                <form @submit.prevent="handleEvaluate" class="space-y-4">
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-white/70">Veredito</label>
                                        <div class="flex space-x-2">
                                            <button 
                                                type="button"
                                                @click="form.status = 'APTO'"
                                                class="flex-1 py-3 rounded-lg border-2 transition-all font-bold text-xs uppercase tracking-widest"
                                                :class="form.status === 'APTO' ? 'border-secondary bg-secondary/10 text-secondary' : 'border-white/10 text-white/30 hover:border-white/20'"
                                            >
                                                APTO
                                            </button>
                                            <button 
                                                type="button"
                                                @click="form.status = 'INAPTO'"
                                                class="flex-1 py-3 rounded-lg border-2 transition-all font-bold text-xs uppercase tracking-widest"
                                                :class="form.status === 'INAPTO' ? 'border-error bg-error/10 text-error' : 'border-white/10 text-white/30 hover:border-white/20'"
                                            >
                                                INAPTO
                                            </button>
                                        </div>
                                    </div>

                                    <transition name="fade">
                                        <div v-if="form.status === 'INAPTO'" class="space-y-1.5">
                                            <label class="block text-sm font-medium text-white/70">Justificativa</label>
                                            <textarea 
                                                v-model="form.justification_inapto"
                                                rows="4"
                                                class="w-full bg-surface-container-highest border border-outline-variant/20 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-error/50 transition-all"
                                                placeholder="Informe o motivo da inaptidão..."
                                            ></textarea>
                                        </div>
                                    </transition>

                                    <BaseButton type="submit" full-width :loading="submitting" :variant="form.status === 'INAPTO' ? 'error' : 'primary'">
                                        Confirmar Avaliação
                                    </BaseButton>
                                </form>
                            </div>
                        </BaseCard>

                        <div v-else class="text-center py-24 text-white/20 bg-white/5 rounded-2xl border border-dashed border-white/10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-4 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            <p>Selecione uma inscrição para analisar</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';
import AuthenticatedLayout from '../../components/AuthenticatedLayout.vue';
import BaseCard from '../../components/BaseCard.vue';
import BaseButton from '../../components/BaseButton.vue';

const presentations = ref([]);
const selectedPresentation = ref(null);
const loading = ref(true);
const submitting = ref(false);

const form = reactive({
    status: 'APTO',
    justification_inapto: ''
});

onMounted(() => {
    fetchPresentations();
});

async function fetchPresentations() {
    loading.value = true;
    try {
        const response = await axios.get('/api/presentations?analyze=true');
        presentations.value = response.data;
    } catch (error) {
        console.error('Failed to fetch presentations', error);
    } finally {
        loading.value = false;
    }
}

function selectPresentation(pres) {
    selectedPresentation.value = pres;
    form.status = 'APTO';
    form.justification_inapto = '';
}

async function handleEvaluate() {
    if (!selectedPresentation.value) return;
    
    submitting.value = true;
    try {
        await axios.post(`/api/presentations/${selectedPresentation.value.id}/evaluate`, form);
        alert('Avaliação concluída!');
        selectedPresentation.value = null;
        fetchPresentations();
    } catch (error) {
        if (error.response?.data?.message) {
            alert(error.response.data.message);
        }
    } finally {
        submitting.value = false;
    }
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
}
</script>
