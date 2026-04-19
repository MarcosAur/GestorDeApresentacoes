<template>
    <div class="space-y-1.5">
        <label v-if="label" :for="id" class="block text-sm font-medium text-white/70 ml-1">
            {{ label }}
        </label>
        <div class="relative">
            <input
                :id="id"
                :type="type"
                :value="modelValue"
                @input="$emit('update:modelValue', $event.target.value)"
                :placeholder="placeholder"
                :disabled="disabled"
                :class="[
                    'w-full bg-surface-container-highest border border-outline-variant/20 rounded-lg px-4 py-2 text-white placeholder:text-white/30 focus:outline-none focus:border-primary/50 focus:ring-1 focus:ring-primary/20 transition-all duration-200',
                    error ? 'border-error/50 focus:border-error focus:ring-error/20' : '',
                    disabled ? 'opacity-50 cursor-not-allowed' : ''
                ]"
            />
        </div>
        <p v-if="error" class="text-xs text-error mt-1 ml-1">{{ error }}</p>
    </div>
</template>

<script setup>
defineProps({
    modelValue: [String, Number],
    label: String,
    placeholder: String,
    type: {
        type: String,
        default: 'text'
    },
    id: String,
    error: String,
    disabled: Boolean
});

defineEmits(['update:modelValue']);
</script>
