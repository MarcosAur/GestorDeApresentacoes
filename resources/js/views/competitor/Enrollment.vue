<template>
    <AuthenticatedLayout>
        <div class="space-y-8">
            <header>
                <h1 class="text-3xl font-display font-bold text-primary tracking-tight">Minhas Inscrições</h1>
                <p class="text-white/50 mt-1">Gerencie suas apresentações e documentos.</p>
            </header>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column: Enrollment Form and Documents -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- New Enrollment -->
                    <BaseCard>
                        <template #header>
                            <h2 class="text-lg font-display font-bold text-secondary">Nova Inscrição</h2>
                        </template>
                        <form @submit.prevent="handleEnroll" class="space-y-4">
                            <div class="space-y-1.5">
                                <label class="block text-sm font-medium text-white/70 ml-1">Concurso Disponível</label>
                                <select 
                                    v-model="enrollForm.contest_id"
                                    class="w-full bg-surface-container-highest border border-outline-variant/20 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary/50 transition-all"
                                >
                                    <option value="">Selecione um concurso</option>
                                    <option v-for="contest in competitorStore.availableContests" :key="contest.id" :value="contest.id">
                                        {{ contest.name }} ({{ contest.event?.name }})
                                    </option>
                                </select>
                                <p v-if="competitorStore.errors?.contest_id" class="text-xs text-error">{{ competitorStore.errors.contest_id[0] }}</p>
                            </div>

                            <BaseInput
                                v-model="enrollForm.work_title"
                                label="Título da Obra / Personagem"
                                placeholder="Ex: Cloud Strife - FFVII"
                                :error="competitorStore.errors?.work_title?.[0]"
                            />

                            <BaseButton type="submit" full-width :loading="competitorStore.loading" :disabled="!enrollForm.contest_id">
                                Confirmar Inscrição
                            </BaseButton>
                        </form>
                    </BaseCard>

                    <!-- Document Upload -->
                    <BaseCard>
                        <template #header>
                            <h2 class="text-lg font-display font-bold text-tertiary">Enviar Documento</h2>
                        </template>
                        <form @submit.prevent="handleUpload" class="space-y-4">
                            <BaseInput
                                v-model="uploadForm.document_type"
                                label="Tipo de Documento"
                                placeholder="Ex: Áudio, Referência, PDF"
                                :error="competitorStore.errors?.document_type?.[0]"
                            />

                            <div class="space-y-1.5">
                                <label class="block text-sm font-medium text-white/70 ml-1">Arquivo</label>
                                <input 
                                    type="file" 
                                    @change="onFileChange"
                                    class="w-full text-xs text-white/50 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer"
                                />
                                <p v-if="competitorStore.errors?.document_file" class="text-xs text-error">{{ competitorStore.errors.document_file[0] }}</p>
                            </div>

                            <BaseButton type="submit" variant="secondary" full-width :loading="competitorStore.loading" :disabled="!uploadForm.document_file">
                                Enviar Arquivo
                            </BaseButton>
                        </form>
                    </BaseCard>
                </div>

                <!-- Right Column: List of Presentations and Documents -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- My Presentations -->
                    <BaseCard :padding="false">
                        <template #header>
                            <h2 class="text-lg font-display font-bold text-white p-4">Minhas Apresentações</h2>
                        </template>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-outline-variant/10 text-white/50 text-xs font-admin uppercase">
                                        <th class="px-6 py-3">Concurso</th>
                                        <th class="px-6 py-3">Obra</th>
                                        <th class="px-6 py-3">Status</th>
                                        <th class="px-6 py-3 text-right">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-outline-variant/5">
                                    <tr v-for="pres in competitorStore.presentations" :key="pres.id" class="text-sm">
                                        <td class="px-6 py-4">
                                            <div class="font-bold">{{ pres.contest?.name }}</div>
                                            <div class="text-[10px] text-white/40 uppercase">{{ pres.contest?.event?.name }}</div>
                                        </td>
                                        <td class="px-6 py-4">{{ pres.work_title }}</td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/5 border border-white/10 uppercase">
                                                {{ pres.status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <button @click="confirmDeletePresentation(pres)" class="text-white/20 hover:text-error transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="!competitorStore.presentations.length">
                                        <td colspan="4" class="px-6 py-8 text-center text-white/20 italic">Nenhuma inscrição realizada.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </BaseCard>

                    <!-- My Documents -->
                    <BaseCard :padding="false">
                        <template #header>
                            <h2 class="text-lg font-display font-bold text-white p-4">Documentos Enviados</h2>
                        </template>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4">
                            <div v-for="doc in competitorStore.documents" :key="doc.id" class="p-4 bg-white/5 rounded-xl border border-outline-variant/10 flex justify-between items-center group">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-white/10 rounded-lg text-white/50 group-hover:text-secondary transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-bold text-sm">{{ doc.document_type }}</div>
                                        <div class="text-[10px] text-white/30 truncate max-w-[150px]">{{ doc.original_name }}</div>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <a :href="`/documents/${doc.id}/download`" target="_blank" class="p-2 hover:bg-white/10 rounded-lg text-white/20 hover:text-primary transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                    </a>
                                    <button @click="confirmDeleteDocument(doc)" class="p-2 hover:bg-error/10 rounded-lg text-white/20 hover:text-error transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div v-if="!competitorStore.documents.length" class="col-span-2 py-8 text-center text-white/20 italic">
                                Nenhum documento enviado.
                            </div>
                        </div>
                    </BaseCard>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { reactive, onMounted } from 'vue';
import AuthenticatedLayout from '../../components/AuthenticatedLayout.vue';
import BaseCard from '../../components/BaseCard.vue';
import BaseButton from '../../components/BaseButton.vue';
import BaseInput from '../../components/BaseInput.vue';
import { useCompetitorStore } from '../../stores/competitor';

const competitorStore = useCompetitorStore();

const enrollForm = reactive({
    contest_id: '',
    work_title: ''
});

const uploadForm = reactive({
    document_type: '',
    document_file: null
});

onMounted(() => {
    competitorStore.fetchDashboardData();
});

function onFileChange(e) {
    uploadForm.document_file = e.target.files[0];
}

async function handleEnroll() {
    try {
        await competitorStore.enroll({ ...enrollForm });
        enrollForm.contest_id = '';
        enrollForm.work_title = '';
        alert('Inscrição realizada com sucesso!');
    } catch (error) {
        // Handled in store
    }
}

async function handleUpload() {
    const formData = new FormData();
    formData.append('document_type', uploadForm.document_type);
    formData.append('document_file', uploadForm.document_file);

    try {
        await competitorStore.uploadDocument(formData);
        uploadForm.document_type = '';
        uploadForm.document_file = null;
        alert('Documento enviado com sucesso!');
    } catch (error) {
        // Handled in store
    }
}

async function confirmDeletePresentation(pres) {
    if (confirm(`Deseja cancelar sua inscrição em "${pres.contest?.name}"?`)) {
        await competitorStore.deletePresentation(pres.id);
    }
}

async function confirmDeleteDocument(doc) {
    if (confirm(`Deseja remover o documento "${doc.document_type}"?`)) {
        await competitorStore.deleteDocument(doc.id);
    }
}
</script>
