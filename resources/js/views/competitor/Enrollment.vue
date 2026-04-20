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

                    <BaseCard>
                        <template #header>
                            <h2 class="text-lg font-display font-bold text-tertiary">Enviar Documento</h2>
                        </template>
                        <form @submit.prevent="handleUpload" class="space-y-4">
                            <div class="space-y-1.5">
                                <label class="block text-sm font-medium text-white/70 ml-1">Para qual Inscrição?</label>
                                <select 
                                    v-model="uploadForm.presentation_id"
                                    class="w-full bg-surface-container-highest border border-outline-variant/20 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary/50 transition-all"
                                >
                                    <option value="">Selecione uma apresentação</option>
                                    <option v-for="pres in competitorStore.presentations" :key="pres.id" :value="pres.id">
                                        {{ pres.contest?.name }} - {{ pres.work_title }}
                                    </option>
                                </select>
                                <p v-if="competitorStore.errors?.presentation_id" class="text-xs text-error">{{ competitorStore.errors.presentation_id[0] }}</p>
                            </div>

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

                            <BaseButton type="submit" variant="secondary" full-width :loading="competitorStore.loading" :disabled="!uploadForm.document_file || !uploadForm.presentation_id">
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
                                        <td class="px-6 py-4 text-right flex justify-end space-x-2">
                                            <button 
                                                v-if="pres.status === 'APTO' && pres.qrcode_svg" 
                                                @click="openQrModal(pres.qrcode_svg)"
                                                class="text-primary hover:text-primary-container transition-colors"
                                                title="Ver QR Code de Check-in"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                                </svg>
                                            </button>
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
                                        <div class="font-bold text-sm">{{ doc.type }}</div>
                                        <div class="text-[10px] text-white/30 truncate max-w-[150px]">{{ doc.presentation?.contest?.name + ' - ' + doc.presentation?.work_title || 'Sem Concurso' }}</div>
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

        <!-- QR Code Modal -->
        <BaseModal :show="showQrModal" @close="showQrModal = false">
            <template #title>
                QR Code de Check-in
            </template>
            <div class="flex flex-col items-center justify-center p-6 space-y-4">
                <p class="text-sm text-white/70 text-center">
                    Apresente este código na recepção do evento para confirmar sua presença e liberar sua participação no palco.
                </p>
                <div class="p-4 bg-white rounded-2xl shadow-[0_0_50px_-12px_rgba(237,134,255,0.3)]" v-html="selectedQrCode"></div>
                <div class="text-center">
                    <p class="font-display font-bold text-primary">Check-in Digital</p>
                    <p class="text-[10px] text-white/30 uppercase tracking-widest">Apresentação Apta</p>
                </div>
            </div>
            <template #footer>
                <BaseButton variant="secondary" full-width @click="showQrModal = false">
                    Fechar
                </BaseButton>
            </template>
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
import { useCompetitorStore } from '../../stores/competitor';

const competitorStore = useCompetitorStore();
const showQrModal = ref(false);
const selectedQrCode = ref('');

const enrollForm = reactive({
    contest_id: '',
    work_title: ''
});

const uploadForm = reactive({
    presentation_id: '',
    document_type: '',
    document_file: null
});

onMounted(() => {
    competitorStore.fetchDashboardData();
});

function onFileChange(e) {
    uploadForm.document_file = e.target.files[0];
}

function openQrModal(svg) {
    selectedQrCode.value = svg;
    showQrModal.value = true;
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
    formData.append('presentation_id', uploadForm.presentation_id);
    formData.append('document_type', uploadForm.document_type);
    formData.append('document_file', uploadForm.document_file);

    try {
        await competitorStore.uploadDocument(formData);
        uploadForm.presentation_id = '';
        uploadForm.document_type = '';
        uploadForm.document_file = null;
        alert('Documento enviado com sucesso!');
        window.location.reload();
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
