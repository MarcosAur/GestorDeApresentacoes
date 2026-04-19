<template>
    <div 
        :class="[
            'bg-surface-container-low border border-outline-variant/10 rounded-xl overflow-hidden backdrop-blur-md transition-all duration-300',
            hoverable ? 'hover:border-primary/30 hover:shadow-2xl hover:shadow-primary/5' : '',
            padding ? 'p-6' : ''
        ]"
    >
        <div v-if="$slots.header" class="border-b border-outline-variant/10 p-4 mb-4">
            <slot name="header"></slot>
        </div>
        <slot></slot>
        <div v-if="$slots.footer" class="border-t border-outline-variant/10 p-4 mt-4">
            <slot name="footer"></slot>
        </div>
    </div>
</template>

<script setup>
defineProps({
    padding: {
        type: Boolean,
        default: true
    },
    hoverable: {
        type: Boolean,
        default: false
    }
});
</script>
