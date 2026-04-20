<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-surface/80 backdrop-blur-sm overflow-y-auto">
        <BaseCard class="w-full max-w-lg shadow-2xl border-primary/20 my-auto">
            <template #header>
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-display font-bold text-primary">{{ title }}</h2>
                    <button @click="$emit('close')" class="text-white/50 hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </template>

            <slot></slot>

            <template #footer v-if="$slots.footer">
                <slot name="footer"></slot>
            </template>
        </BaseCard>
    </div>
</template>

<script setup>
import BaseCard from './BaseCard.vue';

defineProps({
    show: Boolean,
    title: String
});

defineEmits(['close']);
</script>
