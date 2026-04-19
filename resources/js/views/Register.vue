<template>
    <div class="min-h-screen flex items-center justify-center bg-surface p-4">
        <BaseCard class="w-full max-w-md">
            <template #header>
                <div class="text-center">
                    <h1 class="text-3xl font-display font-bold text-primary tracking-tight">GEMINI</h1>
                    <p class="text-white/50 mt-1">Crie sua conta de Competidor</p>
                </div>
            </template>

            <form @submit.prevent="handleRegister" class="space-y-4">
                <BaseInput
                    v-model="form.name"
                    label="Nome Completo"
                    type="text"
                    placeholder="Seu nome"
                    id="name"
                    :error="auth.errors?.name?.[0]"
                />

                <BaseInput
                    v-model="form.email"
                    label="E-mail"
                    type="email"
                    placeholder="seu@email.com"
                    id="email"
                    :error="auth.errors?.email?.[0]"
                />

                <BaseInput
                    v-model="form.password"
                    label="Senha"
                    type="password"
                    placeholder="••••••••"
                    id="password"
                    :error="auth.errors?.password?.[0]"
                />

                <BaseInput
                    v-model="form.password_confirmation"
                    label="Confirmar Senha"
                    type="password"
                    placeholder="••••••••"
                    id="password_confirmation"
                />

                <div class="pt-2">
                    <BaseButton
                        type="submit"
                        full-width
                        :loading="auth.loading"
                    >
                        Cadastrar
                    </BaseButton>
                </div>
            </form>

            <template #footer>
                <p class="text-center text-sm text-white/50">
                    Já tem uma conta? 
                    <router-link to="/login" class="text-primary hover:underline font-medium">Entrar</router-link>
                </p>
            </template>
        </BaseCard>
    </div>
</template>

<script setup>
import { reactive } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import BaseButton from '../components/BaseButton.vue';
import BaseInput from '../components/BaseInput.vue';
import BaseCard from '../components/BaseCard.vue';

const auth = useAuthStore();
const router = useRouter();

const form = reactive({
    name: '',
    email: '',
    password: '',
    password_confirmation: ''
});

async function handleRegister() {
    try {
        await auth.register(form);
        router.push('/dashboard');
    } catch (error) {
        // Errors are handled in the store
    }
}
</script>
