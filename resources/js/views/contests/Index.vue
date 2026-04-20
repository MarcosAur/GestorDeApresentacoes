<template>
    <AuthenticatedLayout>
        <div class="space-y-6">
            <header class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-display font-bold text-primary tracking-tight">Concursos</h1>
                    <p class="text-white/50 mt-1">Gerencie os concursos e baremas.</p>
                </div>
                <BaseButton v-if="auth.isAdmin" @click="openModal()">
                    Novo Concurso
                </BaseButton>
            </header>

            <BaseCard :padding="false">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-outline-variant/10 text-white/50 text-sm font-admin uppercase tracking-wider">
                                <th class="px-6 py-4">Concurso</th>
                                <th class="px-6 py-4">Evento</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/5">
                            <tr v-if="contestStore.loading && contestStore.contests.length === 0">
                                <td colspan="4" class="px-6 py-8 text-center text-white/30">Carregando concursos...</td>
                            </tr>
                            <tr v-else v-for="contest in contestStore.contests" :key="contest.id" class="hover:bg-white/5 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-white group-hover:text-primary transition-colors">{{ contest.name }}</div>
                                    <div class="text-xs text-white/40">{{ contest.jurors?.length || 0 }} jurados atribuídos</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-white/70">{{ contest.event?.name }}</td>
                                <td class="px-6 py-4">
                                    <span 
                                        class="px-2 py-1 rounded-full text-[10px] font-bold uppercase border"
                                        :class="{
                                            'bg-secondary/10 text-secondary border-secondary/20': contest.status === 'AGENDADO',
                                            'bg-primary/10 text-primary border-primary/20 animate-pulse': contest.status === 'EM_ANDAMENTO',
                                            'bg-white/10 text-white/50 border-white/20': contest.status === 'FINALIZADO',
                                        }"
                                    >
                                        {{ contest.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end space-x-2">
                                        <template v-if="auth.isAdmin">
                                            <router-link v-if="contest.status !== 'FINALIZADO'" :to="`/contests/${contest.id}/stage`" class="p-2 hover:bg-secondary/10 rounded-lg text-white/50 hover:text-secondary transition-all" title="Controle de Palco">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </router-link>
                                            <button @click="openModal(contest)" class="p-2 hover:bg-primary/10 rounded-lg text-white/50 hover:text-primary transition-all">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button @click="confirmDelete(contest)" class="p-2 hover:bg-error/10 rounded-lg text-white/50 hover:text-error transition-all">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </template>
                                        <template v-if="auth.isJurado && contest.status === 'EM_ANDAMENTO'">
                                            <router-link :to="`/evaluate/${contest.id}`" class="px-3 py-1 bg-secondary text-surface text-xs font-bold rounded-full hover:shadow-lg hover:shadow-secondary/20 transition-all">
                                                AVALIAR
                                            </router-link>
                                        </template>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!contestStore.loading && contestStore.contests.length === 0">
                                <td colspan="4" class="px-6 py-8 text-center text-white/30">Nenhum concurso encontrado.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </BaseCard>
        </div>

        <BaseModal 
            :show="showModal" 
            :title="editingContest ? 'Editar Concurso' : 'Novo Concurso'"
            @close="closeModal"
            class="max-w-3xl"
        >
            <form @submit.prevent="saveContest" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-white/70 ml-1">Evento</label>
                        <select 
                            v-model="form.event_id"
                            class="w-full bg-surface-container-highest border border-outline-variant/20 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary/50 transition-all"
                        >
                            <option value="">Selecione um evento</option>
                            <option v-for="event in contestStore.options.events" :key="event.id" :value="event.id">
                                {{ event.name }}
                            </option>
                        </select>
                        <p v-if="contestStore.errors?.event_id" class="text-xs text-error">{{ contestStore.errors.event_id[0] }}</p>
                    </div>

                    <BaseInput
                        v-model="form.name"
                        label="Nome do Concurso"
                        placeholder="Ex: Cosplay Desfile"
                        :error="contestStore.errors?.name?.[0]"
                    />
                </div>

                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-white/70 ml-1">Status</label>
                    <div class="flex space-x-4">
                        <label v-for="status in ['AGENDADO', 'EM_ANDAMENTO', 'FINALIZADO']" :key="status" class="flex items-center space-x-2 cursor-pointer group">
                            <input type="radio" v-model="form.status" :value="status" class="hidden" />
                            <div 
                                class="w-4 h-4 rounded-full border border-outline-variant/40 flex items-center justify-center transition-all"
                                :class="form.status === status ? 'border-primary bg-primary/20' : 'group-hover:border-primary/50'"
                            >
                                <div v-if="form.status === status" class="w-2 h-2 rounded-full bg-primary"></div>
                            </div>
                            <span class="text-sm" :class="form.status === status ? 'text-primary font-bold' : 'text-white/50'">{{ status }}</span>
                        </label>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-display font-bold text-secondary">Critérios de Avaliação</h3>
                        <BaseButton type="button" variant="outline" @click="addCriterion" class="py-1 px-3 text-xs">
                            + Critério
                        </BaseButton>
                    </div>
                    
                    <div class="space-y-3 max-h-60 overflow-y-auto pr-2">
                        <div v-for="(criterion, index) in form.criteria" :key="index" class="p-4 bg-white/5 rounded-lg border border-outline-variant/10 space-y-3 relative group">
                            <button type="button" @click="removeCriterion(index)" class="absolute top-2 right-2 text-white/20 hover:text-error transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                                <div class="md:col-span-6">
                                    <BaseInput v-model="criterion.name" placeholder="Nome do Critério" class="text-sm" />
                                </div>
                                <div class="md:col-span-2">
                                    <BaseInput v-model.number="criterion.max_score" type="number" placeholder="Máx" class="text-sm" />
                                </div>
                                <div class="md:col-span-2">
                                    <BaseInput v-model.number="criterion.weight" type="number" step="0.1" placeholder="Peso" class="text-sm" />
                                </div>
                                <div class="md:col-span-2">
                                    <BaseInput v-model.number="criterion.tiebreak_priority" type="number" placeholder="Desemp" class="text-sm" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <p v-if="contestStore.errors?.criteria" class="text-xs text-error">{{ contestStore.errors.criteria[0] }}</p>
                </div>

                <div class="space-y-3">
                    <h3 class="text-lg font-display font-bold text-tertiary">Jurados</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                        <label v-for="juror in contestStore.options.jurors" :key="juror.id" class="flex items-center space-x-2 p-2 bg-white/5 rounded-lg border border-outline-variant/5 cursor-pointer hover:bg-white/10 transition-colors">
                            <input type="checkbox" v-model="form.selectedJurors" :value="juror.id" class="rounded border-outline-variant/40 bg-transparent text-primary focus:ring-primary/20" />
                            <span class="text-xs truncate">{{ juror.name }}</span>
                        </label>
                    </div>
                </div>

                <div class="pt-4 flex justify-end space-x-3">
                    <BaseButton variant="ghost" @click="closeModal">Cancelar</BaseButton>
                    <BaseButton type="submit" :loading="contestStore.loading">
                        {{ editingContest ? 'Salvar Alterações' : 'Criar Concurso' }}
                    </BaseButton>
                </div>
            </form>
        </BaseModal>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import AuthenticatedLayout from '../../components/AuthenticatedLayout.vue';
import BaseCard from '../../components/BaseCard.vue';
import BaseButton from '../../components/BaseButton.vue';
import BaseInput from '../../components/BaseInput.vue';
import BaseModal from '../../components/BaseModal.vue';
import { useContestStore } from '../../stores/contest';
import { useAuthStore } from '../../stores/auth';

const contestStore = useContestStore();
const auth = useAuthStore();

const showModal = ref(false);
const editingContest = ref(null);

const form = reactive({
    id: null,
    event_id: '',
    name: '',
    status: 'AGENDADO',
    criteria: [],
    selectedJurors: []
});

onMounted(() => {
    contestStore.fetchContests();
    if (auth.isAdmin) {
        contestStore.fetchOptions();
    }
});

function openModal(contest = null) {
    if (contest) {
        editingContest.value = contest;
        form.id = contest.id;
        form.event_id = contest.event_id;
        form.name = contest.name;
        form.status = contest.status;
        form.criteria = JSON.parse(JSON.stringify(contest.evaluation_criteria || []));
        form.selectedJurors = (contest.jurors || []).map(j => j.id);
    } else {
        editingContest.value = null;
        form.id = null;
        form.event_id = '';
        form.name = '';
        form.status = 'AGENDADO';
        form.criteria = [
            { name: 'Apresentação', max_score: 10, weight: 1, tiebreak_priority: 1 },
            { name: 'Fidelidade', max_score: 10, weight: 1, tiebreak_priority: 2 }
        ];
        form.selectedJurors = [];
    }
    showModal.value = true;
}

function closeModal() {
    showModal.value = false;
    contestStore.errors = null;
}

function addCriterion() {
    form.criteria.push({
        name: '',
        max_score: 10,
        weight: 1,
        tiebreak_priority: form.criteria.length + 1
    });
}

function removeCriterion(index) {
    form.criteria.splice(index, 1);
}

async function saveContest() {
    try {
        await contestStore.saveContest({ ...form });
        closeModal();
    } catch (error) {
        // Errors handled in store
    }
}

async function confirmDelete(contest) {
    if (confirm(`Tem certeza que deseja deletar o concurso "${contest.name}"?`)) {
        await contestStore.deleteContest(contest.id);
    }
}
</script>
