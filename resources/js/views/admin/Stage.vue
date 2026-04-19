<template>
    <AuthenticatedLayout>
        <div v-if="loading" class="flex items-center justify-center h-64">
            <p class="text-primary animate-pulse font-display text-xl">SINCRONIZANDO PALCO...</p>
        </div>
        
        <div v-else-if="contest" class="space-y-8">
            <header class="flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-display font-bold text-primary tracking-tight uppercase">{{ contest.name }}</h1>
                    <div class="flex items-center space-x-2 mt-1">
                        <span class="px-2 py-0.5 rounded bg-white/10 text-[10px] text-white/50 border border-white/10 uppercase font-bold">
                            {{ contest.status }}
                        </span>
                        <span class="text-white/30 text-xs">•</span>
                        <span class="text-white/50 text-xs">{{ contest.event?.name }}</span>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <div class="flex p-1 bg-white/5 rounded-lg border border-white/10 mr-4">
                        <button 
                            @click="activeTab = 'stage'" 
                            class="px-4 py-1.5 rounded-md text-xs font-bold uppercase transition-all"
                            :class="activeTab === 'stage' ? 'bg-primary text-surface' : 'text-white/40 hover:text-white'"
                        >
                            Controle
                        </button>
                        <button 
                            @click="activeTab = 'ranking'" 
                            class="px-4 py-1.5 rounded-md text-xs font-bold uppercase transition-all"
                            :class="activeTab === 'ranking' ? 'bg-primary text-surface' : 'text-white/40 hover:text-white'"
                        >
                            Ranking
                        </button>
                    </div>
                    <BaseButton variant="error" @click="handleFinishContest">
                        Finalizar Concurso
                    </BaseButton>
                </div>
            </header>

            <div v-if="activeTab === 'stage'" class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Current Presentation -->
                <div class="lg:col-span-8 space-y-6">
                    <BaseCard class="relative overflow-hidden group border-primary/20 bg-primary/5">
                        <div class="absolute top-0 right-0 p-4">
                            <span class="flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-primary"></span>
                            </span>
                        </div>
                        
                        <div v-if="contest.current_presentation" class="space-y-4">
                            <p class="text-primary font-admin text-sm font-bold tracking-widest uppercase">No Palco Agora</p>
                            <div>
                                <h2 class="text-4xl font-display font-bold text-white">{{ contest.current_presentation.competitor?.name }}</h2>
                                <p class="text-xl text-white/70 italic mt-1">"{{ contest.current_presentation.work_title }}"</p>
                            </div>
                            
                            <div class="pt-6 border-t border-white/10">
                                <h3 class="text-sm font-bold text-white/50 uppercase mb-4">Status da Votação</h3>
                                <div class="flex flex-wrap gap-4">
                                    <div v-for="juror in contest.jurors" :key="juror.id" class="flex flex-col items-center">
                                        <div 
                                            class="w-12 h-12 rounded-full flex items-center justify-center border-2 transition-all duration-500"
                                            :class="votedJurorIds.includes(juror.id) ? 'border-secondary bg-secondary/20 text-secondary' : 'border-white/10 bg-white/5 text-white/20'"
                                        >
                                            <svg v-if="votedJurorIds.includes(juror.id)" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span v-else class="font-bold text-xs">?</span>
                                        </div>
                                        <span class="mt-2 text-[10px] font-bold text-white/40 uppercase text-center max-w-[80px] truncate">{{ juror.name }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="py-12 text-center">
                            <p class="text-white/30 italic">Nenhuma apresentação no palco.</p>
                            <p class="text-sm text-white/50 mt-2">Selecione uma apresentação na lista ao lado para começar.</p>
                        </div>
                    </BaseCard>

                    <!-- All Presentations -->
                    <BaseCard :padding="false">
                        <template #header>
                            <h3 class="text-lg font-display font-bold text-white p-4">Apresentações Aptas</h3>
                        </template>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="border-b border-outline-variant/10 text-white/30 text-[10px] uppercase font-bold tracking-wider">
                                        <th class="px-6 py-3">Ordem</th>
                                        <th class="px-6 py-3">Competidor</th>
                                        <th class="px-6 py-3">Obra</th>
                                        <th class="px-6 py-3 text-right">Ação</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-outline-variant/5">
                                    <tr v-for="(pres, index) in presentations" :key="pres.id" :class="contest.current_presentation_id === pres.id ? 'bg-primary/5' : ''">
                                        <td class="px-6 py-4 font-mono text-xs text-white/30">#{{ String(index + 1).padStart(2, '0') }}</td>
                                        <td class="px-6 py-4 font-bold text-white">{{ pres.competitor?.name }}</td>
                                        <td class="px-6 py-4 text-sm text-white/50 italic">"{{ pres.work_title }}"</td>
                                        <td class="px-6 py-4 text-right">
                                            <BaseButton 
                                                v-if="contest.current_presentation_id !== pres.id" 
                                                size="sm" 
                                                class="py-1 px-3 text-xs"
                                                @click="handleSetOnStage(pres.id)"
                                            >
                                                CHAMAR
                                            </BaseButton>
                                            <span v-else class="text-primary font-bold text-[10px] uppercase tracking-widest">EM PALCO</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </BaseCard>
                </div>

                <!-- Right Column: Info & Logs -->
                <div class="lg:col-span-4 space-y-6">
                    <BaseCard>
                        <template #header>
                            <h3 class="text-lg font-display font-bold text-secondary">Barema do Concurso</h3>
                        </template>
                        <div class="space-y-3">
                            <div v-for="criterion in contest.evaluation_criteria" :key="criterion.id" class="flex justify-between items-center p-3 bg-white/5 rounded-lg border border-outline-variant/5">
                                <div>
                                    <div class="text-sm font-bold">{{ criterion.name }}</div>
                                    <div class="text-[10px] text-white/40 uppercase">Peso: {{ criterion.weight }}x</div>
                                </div>
                                <div class="text-secondary font-display font-bold">
                                    {{ criterion.max_score }} pts
                                </div>
                            </div>
                        </div>
                    </BaseCard>

                    <BaseCard class="bg-surface-container-highest/50">
                        <div class="flex items-center space-x-2 text-white/30 text-[10px] uppercase font-bold mb-4">
                            <span class="w-2 h-2 rounded-full bg-secondary animate-pulse"></span>
                            <span>Live Activity Feed</span>
                        </div>
                        <div class="space-y-4 max-h-64 overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-white/10">
                            <div v-for="(log, i) in activityLogs" :key="i" class="text-xs border-l-2 border-white/10 pl-3 py-1">
                                <span class="text-white/30 block mb-1">{{ log.time }}</span>
                                <span class="text-white/70">{{ log.message }}</span>
                            </div>
                        </div>
                    </BaseCard>
                </div>
            </div>

            <!-- Ranking Tab -->
            <div v-else class="space-y-6">
                <BaseCard :padding="false">
                    <template #header>
                        <div class="p-4 flex justify-between items-center">
                            <h3 class="text-lg font-display font-bold text-white">Ranking em Tempo Real</h3>
                            <button @click="fetchRanking" class="text-[10px] uppercase font-bold text-primary hover:underline">Atualizar</button>
                        </div>
                    </template>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="border-b border-outline-variant/10 text-white/30 text-[10px] uppercase font-bold tracking-wider">
                                    <th class="px-6 py-3">Posição</th>
                                    <th class="px-6 py-3">Competidor</th>
                                    <th class="px-6 py-3">Obra</th>
                                    <th class="px-6 py-3 text-right">Pontuação Final</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-outline-variant/5">
                                <tr v-for="(item, index) in rankingData" :key="item.presentation.id" :class="index < 3 ? 'bg-primary/5' : ''">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-2">
                                            <span v-if="index === 0" class="text-2xl">🥇</span>
                                            <span v-else-if="index === 1" class="text-2xl">🥈</span>
                                            <span v-else-if="index === 2" class="text-2xl">🥉</span>
                                            <span v-else class="font-mono text-white/30">#{{ index + 1 }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-white">{{ item.presentation.competitor?.name }}</div>
                                        <div v-if="item.presentation.id === contest.current_presentation_id" class="text-[10px] text-primary uppercase font-bold">Em Palco</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-white/50 italic">"{{ item.presentation.work_title }}"</td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-xl font-display font-bold text-secondary">{{ item.total_score.toFixed(2) }}</span>
                                    </td>
                                </tr>
                                <tr v-if="rankingData.length === 0">
                                    <td colspan="4" class="px-6 py-12 text-center text-white/30 italic">Aguardando primeiras avaliações...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </BaseCard>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import AuthenticatedLayout from '../../components/AuthenticatedLayout.vue';
import BaseCard from '../../components/BaseCard.vue';
import BaseButton from '../../components/BaseButton.vue';

const route = useRoute();
const router = useRouter();
const contestId = route.params.contestId;

const contest = ref(null);
const presentations = ref([]);
const votedJurorIds = ref([]);
const loading = ref(true);
const activityLogs = ref([]);
const activeTab = ref('stage');
const rankingData = ref([]);

onMounted(async () => {
    await fetchData();
    setupWebSocket();
});

onUnmounted(() => {
    if (window.Echo) {
        window.Echo.leave(`concurso.${contestId}`);
    }
});

watch(activeTab, (newTab) => {
    if (newTab === 'ranking') {
        fetchRanking();
    }
});

async function fetchData() {
    try {
        const response = await axios.get(`/api/contests/${contestId}/stage`);
        contest.value = response.data.contest;
        presentations.value = response.data.presentations;
        votedJurorIds.value = response.data.votedJurorIds;
        loading.value = false;
    } catch (error) {
        console.error('Failed to fetch stage data', error);
        router.push('/contests');
    }
}

async function fetchRanking() {
    try {
        const response = await axios.get(`/api/contests/${contestId}/ranking/admin`);
        rankingData.value = response.data.ranking;
    } catch (error) {
        console.error('Failed to fetch ranking', error);
    }
}

function setupWebSocket() {
    if (window.Echo) {
        window.Echo.channel(`concurso.${contestId}`)
            .listen('.ApresentacaoAlterada', (e) => {
                addLog(`Apresentação alterada: ID ${e.presentationId || 'Fim'}`);
                fetchData();
                if (activeTab.value === 'ranking') fetchRanking();
            })
            .listen('.NotaAtribuida', (e) => {
                const juror = contest.value.jurors.find(j => j.id === e.jurorId);
                addLog(`Jurado ${juror?.name || e.jurorId} enviou as notas.`);
                if (!votedJurorIds.value.includes(e.jurorId)) {
                    votedJurorIds.value.push(e.jurorId);
                }
                if (activeTab.value === 'ranking') fetchRanking();
            });
    }
}

async function handleSetOnStage(presentationId) {
    try {
        const response = await axios.post(`/api/contests/${contestId}/stage`, { presentation_id: presentationId });
        contest.value = response.data.contest;
        presentations.value = response.data.presentations;
        votedJurorIds.value = response.data.votedJurorIds;
        addLog('Novo competidor chamado para o palco.');
    } catch (error) {
        if (error.response?.data?.message) {
            alert(error.response.data.message);
        }
    }
}

async function handleFinishContest() {
    if (!confirm('Deseja realmente finalizar o concurso? Esta ação é irreversível.')) return;
    
    try {
        await axios.post(`/api/contests/${contestId}/finish`);
        router.push('/contests');
    } catch (error) {
        if (error.response?.data?.message) {
            alert(error.response.data.message);
        }
    }
}

function addLog(message) {
    activityLogs.value.unshift({
        time: new Date().toLocaleTimeString(),
        message
    });
}
</script>
