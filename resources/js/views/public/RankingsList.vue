<template>
    <div class="min-h-screen bg-surface p-8">
        <header class="max-w-6xl mx-auto mb-12 flex justify-between items-end">
            <div>
                <h1 class="text-4xl font-display font-bold text-primary tracking-tight uppercase">Histórico de Rankings</h1>
                <p class="text-white/40 mt-2 font-body">Confira os resultados oficiais dos concursos finalizados.</p>
            </div>
            <router-link to="/" class="text-xs font-bold text-white/20 hover:text-white transition-colors uppercase tracking-widest border-b border-white/5 pb-1">
                Voltar ao Início
            </router-link>
        </header>

        <main class="max-w-6xl mx-auto">
            <div v-if="loading" class="flex justify-center py-20">
                <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary"></div>
            </div>

            <div v-else-if="rankings.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="contest in rankings" :key="contest.id" 
                    @click="$router.push(`/rankings/${contest.id}`)"
                    class="group relative bg-surface-container-low border border-outline-variant/10 rounded-3xl p-6 cursor-pointer hover:border-primary/30 transition-all duration-500 hover:shadow-[0_0_40px_-10px_rgba(237,134,255,0.15)]"
                >
                    <div class="absolute top-6 right-6">
                        <div class="p-2 bg-primary/10 rounded-full text-primary opacity-0 group-hover:opacity-100 transition-all transform translate-x-2 group-hover:translate-x-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="inline-block px-2 py-1 rounded bg-white/5 text-[10px] text-white/40 font-bold uppercase tracking-widest border border-white/10">
                            {{ contest.event?.name }}
                        </div>
                        <h2 class="text-2xl font-display font-bold text-white group-hover:text-primary transition-colors leading-tight">
                            {{ contest.name }}
                        </h2>
                        <div class="pt-4 flex items-center justify-between border-t border-white/5">
                            <span class="text-[10px] text-white/20 uppercase font-bold tracking-widest">Ver Resultados</span>
                            <span class="text-white/10">#{{ contest.id }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="text-center py-32 bg-white/5 border border-dashed border-white/10 rounded-3xl">
                <p class="text-white/30 italic text-xl">Nenhum ranking liberado no momento.</p>
                <p class="text-white/20 text-sm mt-2">Fique atento aos anúncios do evento!</p>
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const rankings = ref([]);
const loading = ref(true);

onMounted(async () => {
    try {
        const response = await axios.get('/api/public/rankings');
        rankings.value = response.data;
    } catch (error) {
        console.error('Failed to fetch rankings list', error);
    } finally {
        loading.value = false;
    }
});
</script>
