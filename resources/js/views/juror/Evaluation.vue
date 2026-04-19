<template>
    <AuthenticatedLayout>
        <div v-if="loading" class="flex items-center justify-center h-64">
            <p class="text-secondary animate-pulse font-display text-xl tracking-widest uppercase">Aguardando Palco...</p>
        </div>

        <div v-else class="max-w-4xl mx-auto space-y-8">
            <header class="text-center">
                <h1 class="text-3xl font-display font-bold text-secondary tracking-tight uppercase">{{ contest.name }}</h1>
                <p class="text-white/40 text-sm mt-1 uppercase font-admin tracking-widest">Painel de Avaliação</p>
            </header>

            <transition name="fade" mode="out-in">
                <div v-if="currentPresentation" :key="currentPresentation.id" class="space-y-8">
                    <!-- Presentation Info -->
                    <BaseCard class="border-secondary/20 bg-secondary/5 text-center relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-full h-1 bg-secondary shadow-[0_0_15px_rgba(0,238,252,0.5)]"></div>
                        <p class="text-secondary font-admin text-xs font-bold tracking-widest uppercase mb-2">Apresentação Atual</p>
                        <h2 class="text-4xl font-display font-bold text-white">{{ currentPresentation.competitor?.name }}</h2>
                        <p class="text-xl text-white/60 italic mt-1">"{{ currentPresentation.work_title }}"</p>
                    </BaseCard>

                    <!-- Evaluation Form -->
                    <div v-if="!hasVoted">
                        <BaseCard>
                            <template #header>
                                <h3 class="text-lg font-display font-bold text-white">Atribuir Notas</h3>
                            </template>
                            
                            <form @submit.prevent="handleSubmit" class="space-y-6">
                                <div v-for="criterion in contest.evaluation_criteria" :key="criterion.id" class="space-y-3">
                                    <div class="flex justify-between items-end">
                                        <label class="block text-sm font-bold text-white/70">{{ criterion.name }}</label>
                                        <span class="text-2xl font-display font-bold text-secondary">{{ scores[criterion.id] }}<span class="text-white/20 text-sm">/{{ criterion.max_score }}</span></span>
                                    </div>
                                    <input 
                                        type="range" 
                                        v-model.number="scores[criterion.id]" 
                                        :min="0" 
                                        :max="criterion.max_score" 
                                        step="0.5"
                                        class="w-full h-2 bg-surface-container-highest rounded-lg appearance-none cursor-pointer accent-secondary"
                                    />
                                    <div class="flex justify-between text-[10px] text-white/30 font-bold">
                                        <span>0</span>
                                        <span>{{ criterion.max_score }}</span>
                                    </div>
                                </div>

                                <div class="pt-6 border-t border-white/5">
                                    <BaseButton type="submit" variant="primary" full-width :loading="submitting" class="bg-gradient-to-r from-secondary to-secondary/70 text-surface">
                                        ENVIAR AVALIAÇÃO
                                    </BaseButton>
                                </div>
                            </form>
                        </BaseCard>
                    </div>

                    <!-- Voted State -->
                    <BaseCard v-else class="text-center py-12 border-secondary/20">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-secondary/10 text-secondary mb-4 border border-secondary/20">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-display font-bold text-white">Notas Enviadas!</h3>
                        <p class="text-white/50 mt-2">Aguardando a próxima apresentação ser chamada no palco.</p>
                    </BaseCard>
                </div>

                <div v-else class="text-center py-20 space-y-4">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-secondary"></div>
                    <p class="text-white/30 italic">Aguardando o início das apresentações no palco...</p>
                </div>
            </transition>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import AuthenticatedLayout from '../../components/AuthenticatedLayout.vue';
import BaseCard from '../../components/BaseCard.vue';
import BaseButton from '../../components/BaseButton.vue';

const route = useRoute();
const router = useRouter();
const contestId = route.params.contestId;

const contest = ref(null);
const currentPresentation = ref(null);
const hasVoted = ref(false);
const scores = reactive({});
const loading = ref(true);
const submitting = ref(false);

onMounted(async () => {
    await fetchData();
    setupWebSocket();
});

onUnmounted(() => {
    if (window.Echo) {
        window.Echo.leave(`concurso.${contestId}`);
    }
});

async function fetchData() {
    try {
        const response = await axios.get(`/api/contests/${contestId}/evaluation`);
        contest.value = response.data.contest;
        currentPresentation.value = response.data.currentPresentation;
        hasVoted.value = response.data.hasVoted;
        
        if (currentPresentation.value) {
            initializeScores();
        }
        
        loading.value = false;
    } catch (error) {
        console.error('Failed to fetch evaluation data', error);
        router.push('/contests');
    }
}

function initializeScores() {
    contest.value.evaluation_criteria.forEach(c => {
        if (scores[c.id] === undefined) {
            scores[c.id] = 0;
        }
    });
}

function setupWebSocket() {
    if (window.Echo) {
        window.Echo.channel(`concurso.${contestId}`)
            .listen('.ApresentacaoAlterada', (e) => {
                // Reset local state for new presentation
                hasVoted.value = false;
                Object.keys(scores).forEach(key => scores[key] = 0);
                fetchData();
            });
    }
}

async function handleSubmit() {
    submitting.ref = true;
    try {
        await axios.post(`/api/contests/${contestId}/evaluation`, { scores });
        hasVoted.value = true;
        alert('Avaliação enviada com sucesso!');
    } catch (error) {
        if (error.response?.data?.message) {
            alert(error.response.data.message);
        }
    } finally {
        submitting.ref = false;
    }
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>
