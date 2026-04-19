<template>
    <AuthenticatedLayout>
        <div class="space-y-6">
            <header>
                <h1 class="text-3xl font-display font-bold text-primary tracking-tight">Dashboard</h1>
                <p class="text-white/50 mt-1">Bem-vindo ao centro de comando, {{ auth.user?.name }}.</p>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <BaseCard hoverable>
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-primary/10 rounded-lg text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-white/50 text-sm">Próximos Eventos</p>
                            <p class="text-2xl font-bold">--</p>
                        </div>
                    </div>
                </BaseCard>

                <BaseCard hoverable>
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-secondary/10 rounded-lg text-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-white/50 text-sm">Competidores</p>
                            <p class="text-2xl font-bold">--</p>
                        </div>
                    </div>
                </BaseCard>

                <BaseCard hoverable>
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-tertiary/10 rounded-lg text-tertiary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-white/50 text-sm">Resultados</p>
                            <p class="text-2xl font-bold">--</p>
                        </div>
                    </div>
                </BaseCard>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { useAuthStore } from '../stores/auth';
import AuthenticatedLayout from '../components/AuthenticatedLayout.vue';
import BaseCard from '../components/BaseCard.vue';

const auth = useAuthStore();
</script>
