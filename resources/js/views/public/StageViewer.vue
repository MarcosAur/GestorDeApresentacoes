<template>
    <div class="min-h-screen bg-surface flex flex-col p-8">
        <header class="text-center mb-12">
            <h1 class="text-5xl font-display font-bold text-primary tracking-tighter uppercase mb-2 animate-pulse">GEMINI STAGE</h1>
            <div class="h-1 w-24 bg-primary mx-auto rounded-full"></div>
        </header>

        <div v-if="loading" class="flex-1 flex items-center justify-center">
            <p class="text-white/20 font-display text-2xl tracking-widest uppercase animate-pulse">Estabelecendo Conexão...</p>
        </div>

        <div v-else class="flex-1 max-w-6xl mx-auto w-full">
            <transition name="slide-up" mode="out-in">
                <div v-if="contest?.status === 'FINALIZADO'" class="space-y-12 w-full max-w-4xl mx-auto">
                    <div class="text-center space-y-4 mb-12">
                        <p class="text-primary font-admin text-sm font-bold tracking-widest uppercase">Concurso Finalizado</p>
                        <h2 class="text-6xl font-display font-bold text-white leading-none tracking-tight">RESULTADO FINAL</h2>
                    </div>

                    <div class="space-y-4">
                        <div v-for="(item, index) in rankingData" :key="item.presentation.id" 
                            class="flex items-center justify-between p-6 rounded-2xl border transition-all duration-500"
                            :class="index === 0 ? 'bg-primary/10 border-primary/30 scale-105 shadow-[0_0_40px_-10px_rgba(237,134,255,0.3)]' : 'bg-white/5 border-white/10'"
                        >
                            <div class="flex items-center space-x-6">
                                <div class="w-12 h-12 flex items-center justify-center font-display font-bold text-2xl">
                                    <span v-if="index === 0">🥇</span>
                                    <span v-else-if="index === 1">🥈</span>
                                    <span v-else-if="index === 2">🥉</span>
                                    <span v-else class="text-white/20">#{{ index + 1 }}</span>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-white">{{ item.presentation.competitor?.name }}</div>
                                    <div class="text-white/40 italic">"{{ item.presentation.work_title }}"</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-3xl font-display font-bold text-secondary">{{ item.total_score.toFixed(2) }}</div>
                                <div class="text-[10px] text-white/30 uppercase font-bold tracking-widest">Pontos</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else-if="currentPresentation" :key="currentPresentation.id" class="space-y-12">
                    <div class="text-center space-y-4">
                        <p class="text-secondary font-admin text-sm font-bold tracking-widest uppercase">Apresentação Atual</p>
                        <h2 class="text-7xl md:text-9xl font-display font-bold text-white leading-none tracking-tight">
                            {{ currentPresentation.competitor?.name }}
                        </h2>
                        <div class="text-3xl md:text-4xl text-white/50 italic font-body">
                            "{{ currentPresentation.work_title }}"
                        </div>
                    </div>

                    <div class="flex justify-center pt-12">
                        <div class="px-8 py-3 bg-white/5 border border-white/10 rounded-full backdrop-blur-xl">
                            <span class="text-white/40 text-sm font-bold uppercase tracking-widest">{{ contest.name }}</span>
                        </div>
                    </div>
                </div>

                <div v-else class="h-full flex flex-col items-center justify-center space-y-6 text-center">
                    <div class="p-8 bg-surface-container-low border border-outline-variant/10 rounded-3xl shadow-2xl">
                        <h2 class="text-3xl font-display font-bold text-white uppercase tracking-wider mb-2">Aguardando Próxima Atração</h2>
                        <p class="text-white/40 text-lg">O espetáculo começará em instantes.</p>
                    </div>
                    
                    <div class="flex space-x-2">
                        <div v-for="i in 3" :key="i" class="w-3 h-3 rounded-full bg-primary/20 animate-bounce" :style="{ animationDelay: (i * 0.2) + 's' }"></div>
                    </div>
                </div>
            </transition>
        </div>

        <footer class="text-center mt-12 py-6 border-t border-white/5">
            <p class="text-white/20 text-[10px] font-bold tracking-[0.3em] uppercase">Powered by Gemini CLI Platform</p>
        </footer>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const contestId = route.params.contestId;

const contest = ref(null);
const currentPresentation = ref(null);
const rankingData = ref([]);
const loading = ref(true);

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
        const response = await axios.get(`/api/public/contests/${contestId}/stage`);
        contest.value = response.data.contest;
        currentPresentation.value = response.data.currentPresentation;
        
        if (contest.value.status === 'FINALIZADO') {
            await fetchRanking();
        }
        
        loading.value = false;
    } catch (error) {
        console.error('Failed to fetch public stage data', error);
    }
}

async function fetchRanking() {
    try {
        const response = await axios.get(`/api/public/contests/${contestId}/ranking`);
        rankingData.value = response.data.ranking;
    } catch (error) {
        console.error('Failed to fetch public ranking', error);
    }
}

function setupWebSocket() {
    if (window.Echo) {
        window.Echo.channel(`concurso.${contestId}`)
            .listen('.ApresentacaoAlterada', (e) => {
                fetchData();
            });
    }
}
</script>

<style scoped>
.slide-up-enter-active, .slide-up-leave-active {
    transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}
.slide-up-enter-from {
    opacity: 0;
    transform: translateY(40px);
}
.slide-up-leave-to {
    opacity: 0;
    transform: translateY(-40px);
}
</style>
