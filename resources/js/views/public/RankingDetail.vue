<template>
    <div class="min-h-screen bg-surface p-8 flex flex-col">
        <header class="max-w-4xl mx-auto w-full mb-12 flex justify-between items-center">
            <router-link to="/rankings" class="flex items-center space-x-2 text-white/30 hover:text-primary transition-colors group">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                </svg>
                <span class="text-[10px] uppercase font-bold tracking-widest">Todos os Rankings</span>
            </router-link>
            
            <div class="text-right">
                <h1 v-if="contest" class="text-xl font-display font-bold text-white uppercase tracking-tight">{{ contest.name }}</h1>
                <p v-if="contest" class="text-[10px] text-white/40 uppercase font-bold tracking-[0.3em]">{{ contest.event?.name }}</p>
            </div>
        </header>

        <main class="flex-1 max-w-4xl mx-auto w-full">
            <div v-if="loading" class="flex justify-center py-20">
                <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-secondary"></div>
            </div>

            <div v-else-if="rankingData.length > 0" class="space-y-8 animate-fade-in">
                <div class="text-center mb-16">
                    <h2 class="text-5xl font-display font-bold text-primary mb-4 tracking-tighter">RESULTADO FINAL</h2>
                    <div class="h-1 w-24 bg-primary mx-auto rounded-full"></div>
                </div>

                <div class="space-y-4">
                    <div v-for="(item, index) in rankingData" :key="item.presentation.id" 
                        class="flex items-center justify-between p-6 rounded-3xl border transition-all duration-500 bg-white/5 border-white/10"
                        :class="index === 0 ? 'border-primary/30 bg-primary/5 scale-105 shadow-[0_0_50px_-10px_rgba(237,134,255,0.2)]' : ''"
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

            <div v-else class="text-center py-20">
                <p class="text-white/30">Nenhum dado disponível para este ranking.</p>
            </div>
        </main>

        <footer class="text-center py-12 border-t border-white/5 mt-auto">
            <p class="text-white/10 text-[8px] font-bold tracking-[0.5em] uppercase">Resultados Oficiais • Gemini Stage System</p>
        </footer>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const contestId = route.params.contestId;

const contest = ref(null);
const rankingData = ref([]);
const loading = ref(true);

onMounted(async () => {
    try {
        const response = await axios.get(`/api/public/contests/${contestId}/ranking`);
        contest.value = response.data.contest;
        rankingData.value = response.data.ranking;
    } catch (error) {
        console.error('Failed to fetch ranking details', error);
        if (error.response?.status === 403) {
            alert('Este ranking ainda não foi liberado para o público.');
            router.push('/rankings');
        }
    } finally {
        loading.value = false;
    }
});
</script>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.8s ease-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
