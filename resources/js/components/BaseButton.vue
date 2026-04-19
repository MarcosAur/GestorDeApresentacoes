<template>
    <button
        :type="type"
        :class="[
            'px-4 py-2 rounded-lg font-medium transition-all duration-200 active:scale-95 disabled:opacity-50 disabled:active:scale-100',
            variants[variant],
            fullWidth ? 'w-full' : ''
        ]"
        :disabled="loading || disabled"
    >
        <span v-if="loading" class="flex items-center justify-center space-x-2">
            <svg class="animate-spin h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Carregando...</span>
        </span>
        <slot v-else></slot>
    </button>
</template>

<script setup>
const props = defineProps({
    variant: {
        type: String,
        default: 'primary'
    },
    type: {
        type: String,
        default: 'button'
    },
    loading: {
        type: Boolean,
        default: false
    },
    disabled: {
        type: Boolean,
        default: false
    },
    fullWidth: {
        type: Boolean,
        default: false
    }
});

const variants = {
    primary: 'bg-gradient-to-br from-primary to-primary-container text-surface font-bold shadow-lg shadow-primary/20 hover:shadow-primary/40',
    secondary: 'bg-surface-container-highest text-secondary border border-secondary/20 hover:bg-secondary/10 hover:border-secondary/40',
    ghost: 'bg-transparent text-white hover:bg-surface-container-high transition-colors',
    error: 'bg-error/10 text-error border border-error/20 hover:bg-error/20 hover:border-error/40',
    outline: 'bg-transparent text-primary border border-primary/20 hover:bg-primary/10 hover:border-primary/40'
};
</script>
