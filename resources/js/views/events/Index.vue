<template>
    <AuthenticatedLayout>
        <div class="space-y-6">
            <header class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-display font-bold text-primary tracking-tight">Eventos</h1>
                    <p class="text-white/50 mt-1">Gerencie os eventos do sistema.</p>
                </div>
                <BaseButton @click="openModal()">
                    Novo Evento
                </BaseButton>
            </header>

            <BaseCard :padding="false">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-outline-variant/10 text-white/50 text-sm font-admin uppercase tracking-wider">
                                <th class="px-6 py-4">Evento</th>
                                <th class="px-6 py-4">Data</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/5">
                            <tr v-if="eventStore.loading && eventStore.events.length === 0">
                                <td colspan="4" class="px-6 py-8 text-center text-white/30">Carregando eventos...</td>
                            </tr>
                            <tr v-else v-for="event in eventStore.events" :key="event.id" class="hover:bg-white/5 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-white group-hover:text-primary transition-colors">{{ event.name }}</div>
                                    <div class="text-xs text-white/40 truncate max-w-xs">{{ event.description }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm">{{ formatDate(event.event_date) }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase bg-secondary/10 text-secondary border border-secondary/20">
                                        Ativo
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end space-x-2">
                                        <button @click="openModal(event)" class="p-2 hover:bg-primary/10 rounded-lg text-white/50 hover:text-primary transition-all">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button @click="confirmDelete(event)" class="p-2 hover:bg-error/10 rounded-lg text-white/50 hover:text-error transition-all">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!eventStore.loading && eventStore.events.length === 0">
                                <td colspan="4" class="px-6 py-8 text-center text-white/30">Nenhum evento encontrado.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </BaseCard>
        </div>

        <BaseModal 
            :show="showModal" 
            :title="editingEvent ? 'Editar Evento' : 'Novo Evento'"
            @close="closeModal"
        >
            <form @submit.prevent="saveEvent" class="space-y-4">
                <BaseInput
                    v-model="form.name"
                    label="Nome do Evento"
                    id="name"
                    placeholder="Ex: Anime Fest 2026"
                    :error="eventStore.errors?.name?.[0]"
                />

                <BaseInput
                    v-model="form.event_date"
                    label="Data do Evento"
                    type="date"
                    id="event_date"
                    :error="eventStore.errors?.event_date?.[0]"
                />

                <div class="space-y-1.5">
                    <label for="description" class="block text-sm font-medium text-white/70 ml-1">Descrição</label>
                    <textarea
                        v-model="form.description"
                        id="description"
                        rows="3"
                        class="w-full bg-surface-container-highest border border-outline-variant/20 rounded-lg px-4 py-2 text-white placeholder:text-white/30 focus:outline-none focus:border-primary/50 focus:ring-1 focus:ring-primary/20 transition-all duration-200"
                        placeholder="Detalhes sobre o evento..."
                    ></textarea>
                </div>

                <div class="pt-4 flex justify-end space-x-3">
                    <BaseButton variant="ghost" @click="closeModal">Cancelar</BaseButton>
                    <BaseButton type="submit" :loading="eventStore.loading">
                        {{ editingEvent ? 'Salvar Alterações' : 'Criar Evento' }}
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
import { useEventStore } from '../../stores/event';

const eventStore = useEventStore();

const showModal = ref(false);
const editingEvent = ref(null);

const form = reactive({
    id: null,
    name: '',
    event_date: '',
    description: ''
});

onMounted(() => {
    eventStore.fetchEvents();
});

function openModal(event = null) {
    if (event) {
        editingEvent.value = event;
        form.id = event.id;
        form.name = event.name;
        form.event_date = event.event_date.split(' ')[0]; // Handle datetime string
        form.description = event.description;
    } else {
        editingEvent.value = null;
        form.id = null;
        form.name = '';
        form.event_date = '';
        form.description = '';
    }
    showModal.value = true;
}

function closeModal() {
    showModal.value = false;
    eventStore.errors = null;
}

async function saveEvent() {
    try {
        await eventStore.saveEvent({ ...form });
        closeModal();
    } catch (error) {
        // Errors handled in store
    }
}

async function confirmDelete(event) {
    if (confirm(`Tem certeza que deseja deletar o evento "${event.name}"?`)) {
        try {
            await eventStore.deleteEvent(event.id);
        } catch (error) {
            // Error alert handled in store
        }
    }
}

function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('pt-BR').format(date);
}
</script>
