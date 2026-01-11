<template>
    <transition name="fade">
        <div
            v-if="modelValue"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
            @click.self="close"
        >
            <transition name="scale">
                <div
                    v-show="modelValue"
                    class="w-full max-w-lg rounded-lg bg-white p-4 shadow-lg dark:bg-gray-800"
                >
                    <!-- Header -->
                    <div class="mb-2 flex items-center justify-between">
                        <h3 class="text-lg font-semibold">{{ props.title }}</h3>
                        <a-button
                            variant="ghost"
                            class="cursor-pointer"
                            @click="close"
                        >
                            <Icon name="x"></Icon>
                        </a-button>
                    </div>

                    <!-- Content -->
                    <div class="modal-body mb-8">
                        <slot name="body"></slot>
                    </div>

                    <!-- Footer -->
                    <div
                        v-if="$slots.footer"
                        class="mt-4 flex justify-end gap-2"
                    >
                        <slot name="footer"></slot>
                    </div>
                </div>
            </transition>
        </div>
    </transition>
</template>

<script setup lang="ts">
import Icon from '../Icon.vue';
import AButton from './AButton.vue';

const props = defineProps({
    modelValue: { type: Boolean, required: true },
    title: { type: String, default: 'Modal' },
});

const emit = defineEmits(['update:modelValue']);

function close() {
    emit('update:modelValue', false);
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.scale-enter-active,
.scale-leave-active {
    transition: transform 0.2s;
}
.scale-enter-from,
.scale-leave-to {
    transform: scale(0.95);
}
</style>
