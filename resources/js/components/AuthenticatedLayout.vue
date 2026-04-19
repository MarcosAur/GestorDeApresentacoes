<template>
    <div class="flex h-screen overflow-hidden bg-surface">
        <!-- Sidebar -->
        <aside 
            :class="sidebarOpen ? 'w-64' : 'w-20'" 
            class="bg-surface-container-low transition-all duration-300 ease-in-out flex flex-col border-r border-outline-variant/20 z-20"
        >
            <div class="p-6 flex items-center justify-between">
                <span v-if="sidebarOpen" class="font-display text-xl font-bold tracking-tight text-primary">GEMINI</span>
                <button @click="sidebarOpen = !sidebarOpen" class="text-white hover:text-primary transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                    </svg>
                </button>
            </div>

            <nav class="flex-1 px-4 space-y-2 mt-4 overflow-y-auto">
                <router-link 
                    to="/dashboard" 
                    class="flex items-center space-x-3 p-3 rounded-lg transition-colors group"
                    active-class="bg-surface-container-high text-primary"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" :class="$route.name === 'dashboard' ? 'text-primary' : 'text-secondary'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span v-if="sidebarOpen" :class="$route.name !== 'dashboard' ? 'group-hover:text-primary' : ''">Dashboard</span>
                </router-link>

                <!-- Admin and Juror: Contests -->
                <template v-if="auth.isAdmin || auth.isJurado">
                    <router-link 
                        to="/contests" 
                        class="flex items-center space-x-3 p-3 rounded-lg transition-colors group"
                        active-class="bg-surface-container-high text-primary"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" :class="$route.path.startsWith('/contests') ? 'text-primary' : 'text-secondary'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        <span v-if="sidebarOpen" :class="!$route.path.startsWith('/contests') ? 'group-hover:text-primary' : ''">Concursos</span>
                    </router-link>
                </template>

                <!-- Competitor: Enrollment -->
                <template v-if="auth.isCompetidor">
                    <router-link 
                        to="/enrollment" 
                        class="flex items-center space-x-3 p-3 rounded-lg transition-colors group"
                        active-class="bg-surface-container-high text-primary"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" :class="$route.path.startsWith('/enrollment') ? 'text-primary' : 'text-secondary'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span v-if="sidebarOpen" :class="!$route.path.startsWith('/enrollment') ? 'group-hover:text-primary' : ''">Inscrições</span>
                    </router-link>
                </template>

                <!-- Admin Only -->
                <template v-if="auth.isAdmin">
                    <router-link 
                        to="/events" 
                        class="flex items-center space-x-3 p-3 rounded-lg transition-colors group"
                        active-class="bg-surface-container-high text-primary"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" :class="$route.path.startsWith('/events') ? 'text-primary' : 'text-secondary'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span v-if="sidebarOpen" :class="!$route.path.startsWith('/events') ? 'group-hover:text-primary' : ''">Eventos</span>
                    </router-link>

                    <router-link 
                        to="/analyzer" 
                        class="flex items-center space-x-3 p-3 rounded-lg transition-colors group"
                        active-class="bg-surface-container-high text-primary"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" :class="$route.path.startsWith('/analyzer') ? 'text-primary' : 'text-secondary'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        <span v-if="sidebarOpen" :class="!$route.path.startsWith('/analyzer') ? 'group-hover:text-primary' : ''">Análise</span>
                    </router-link>

                    <router-link 
                        to="/jurors" 
                        class="flex items-center space-x-3 p-3 rounded-lg transition-colors group"
                        active-class="bg-surface-container-high text-primary"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" :class="$route.path.startsWith('/jurors') ? 'text-primary' : 'text-secondary'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span v-if="sidebarOpen" :class="!$route.path.startsWith('/jurors') ? 'group-hover:text-primary' : ''">Jurados</span>
                    </router-link>

                    <router-link 
                        to="/checkin" 
                        class="flex items-center space-x-3 p-3 rounded-lg transition-colors group"
                        active-class="bg-surface-container-high text-primary"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" :class="$route.path.startsWith('/checkin') ? 'text-primary' : 'text-secondary'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                        <span v-if="sidebarOpen" :class="!$route.path.startsWith('/checkin') ? 'group-hover:text-primary' : ''">Check-in</span>
                    </router-link>
                </template>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="h-16 bg-surface-container-low border-b border-outline-variant/20 flex items-center justify-between px-8 z-10">
                <div></div>
                <div class="flex items-center space-x-4 relative">
                    <button 
                        @click="userMenuOpen = !userMenuOpen" 
                        class="flex items-center space-x-2 font-admin text-sm font-medium hover:text-primary transition-colors"
                    >
                        <span>{{ auth.user?.name }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div 
                        v-if="userMenuOpen" 
                        @click.away="userMenuOpen = false" 
                        class="absolute right-0 top-10 w-48 bg-surface-container-high border border-outline-variant/20 rounded-lg shadow-xl py-2 z-50"
                    >
                        <button 
                            @click="handleLogout" 
                            class="w-full text-left px-4 py-2 text-sm hover:bg-surface-container-highest transition-colors text-error"
                        >
                            Sair
                        </button>
                    </div>
                </div>
            </header>

            <!-- Main area -->
            <main class="flex-1 overflow-y-auto p-8 relative">
                <slot></slot>
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';

const auth = useAuthStore();
const router = useRouter();
const sidebarOpen = ref(true);
const userMenuOpen = ref(false);

async function handleLogout() {
    await auth.logout();
    router.push('/login');
}
</script>
