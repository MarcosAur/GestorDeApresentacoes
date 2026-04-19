<template>
    <AuthenticatedLayout>
        <div class="space-y-6">
            <header class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-display font-bold text-primary tracking-tight">Jurados</h1>
                    <p class="text-white/50 mt-1">Gerencie os avaliadores do sistema.</p>
                </div>
                <BaseButton @click="openModal()">
                    Novo Jurado
                </BaseButton>
            </header>

            <BaseCard :padding="false">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-outline-variant/10 text-white/50 text-sm font-admin uppercase tracking-wider">
                                <th class="px-6 py-4">Jurado</th>
                                <th class="px-6 py-4">E-mail</th>
                                <th class="px-6 py-4">Concursos</th>
                                <th class="px-6 py-4 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/5">
                            <tr v-if="jurorStore.loading && jurorStore.jurors.length === 0">
                                <td colspan="4" class="px-6 py-8 text-center text-white/30">Carregando jurados...</td>
                            </tr>
                            <tr v-else v-for="juror in jurorStore.jurors" :key="juror.id" class="hover:bg-white/5 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-white group-hover:text-primary transition-colors">{{ juror.name }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-white/70">{{ juror.email }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        <span v-for="contest in juror.contests" :key="contest.id" class="px-2 py-0.5 rounded bg-white/5 border border-white/10 text-[10px] text-white/50">
                                            {{ contest.name }}
                                        </span>
                                        <span v-if="!juror.contests?.length" class="text-xs text-white/20 italic">Nenhum vínculo</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end space-x-2">
                                        <button @click="openModal(juror)" class="p-2 hover:bg-primary/10 rounded-lg text-white/50 hover:text-primary transition-all">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button @click="confirmDelete(juror)" class="p-2 hover:bg-error/10 rounded-lg text-white/50 hover:text-error transition-all">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!jurorStore.loading && jurorStore.jurors.length === 0">
                                <td colspan="4" class="px-6 py-8 text-center text-white/30">Nenhum jurado encontrado.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </BaseCard>
        </div>

        <BaseModal 
            :show="showModal" 
            :title="editingJuror ? 'Editar Jurado' : 'Novo Jurado'"
            @close="closeModal"
        >
            <form @submit.prevent="saveJuror" class="space-y-4">
                <BaseInput
                    v-model="form.name"
                    label="Nome do Jurado"
                    id="name"
                    placeholder="Nome completo"
                    :error="jurorStore.errors?.name?.[0]"
                />

                <BaseInput
                    v-model="form.email"
                    label="E-mail"
                    type="email"
                    id="email"
                    placeholder="email@exemplo.com"
                    :error="jurorStore.errors?.email?.[0]"
                />

                <BaseInput
                    v-model="form.password"
                    label="Senha"
                    type="password"
                    id="password"
                    placeholder="••••••••"
                    :error="jurorStore.errors?.password?.[0]"
                />

                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-white/70 ml-1">Concursos Vinculados</label>
                    <div class="grid grid-cols-2 gap-2 max-h-40 overflow-y-auto p-2 bg-white/5 rounded-lg border border-outline-variant/10">
                        <label v-for="contest in jurorStore.options.contests" :key="contest.id" class="flex items-center space-x-2 p-1 cursor-pointer hover:bg-white/5 rounded transition-colors">
                            <input type="checkbox" v-model="form.selectedContests" :value="contest.id" class="rounded border-outline-variant/40 bg-transparent text-primary focus:ring-primary/20" />
                            <span class="text-xs truncate">{{ contest.name }}</span>
                        </label>
                    </div>
                </div>

                <div class="pt-4 flex justify-end space-x-3">
                    <BaseButton variant="ghost" @click="closeModal">Cancelar</BaseButton>
                    <BaseButton type="submit" :loading="jurorStore.loading">
                        {{ editingJuror ? 'Salvar Alterações' : 'Cadastrar Jurado' }}
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
import { useJurorStore } from '../../stores/juror';

const jurorStore = useJurorStore();

const showModal = ref(false);
const editingJuror = ref(null);

const form = reactive({
    id: null,
    name: '',
    email: '',
    password: '',
    selectedContests: []
});

onMounted(() => {
    jurorStore.fetchJurors();
    jurorStore.fetchOptions();
});

function openModal(juror = null) {
    if (juror) {
        editingJuror.value = juror;
        form.id = juror.id;
        form.name = juror.name;
        form.email = juror.email;
        form.password = '';
        form.selectedContests = (juror.contests || []).map(c => c.id);
    } else {
        editingJuror.value = null;
        form.id = null;
        form.name = '';
        form.email = '';
        form.password = '';
        form.selectedContests = [];
    }
    showModal.value = true;
}

function closeModal() {
    showModal.value = false;
    jurorStore.errors = null;
}

async function saveJuror() {
    try {
        await jurorStore.saveJuror({ ...form });
        closeModal();
    } catch (error) {
        // Errors handled in store
    }
}

async function confirmDelete(juror) {
    if (confirm(`Tem certeza que deseja deletar o jurado "${juror.name}"?`)) {
        await jurorStore.deleteJuror(juror.id);
    }
}
</script>
