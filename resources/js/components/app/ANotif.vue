<script setup lang="ts">
import { ref } from 'vue';
import Icon from '../Icon.vue';

export type AlertVariant = 'info' | 'success' | 'error';

export interface ToastItem {
    id: number;
    type: AlertVariant;
    title: string;
    message: string;
    autoHide: number;
}

const toasts = ref<ToastItem[]>([]);

function addToast(toast: ToastItem) {
    toasts.value.push(toast);

    setTimeout(() => {
        toasts.value = toasts.value.filter((t) => t.id !== toast.id);
    }, toast.autoHide);
}

function iconByType(type: 'info' | 'success' | 'error') {
    switch (type) {
        case 'success':
            return 'CircleCheck';
        case 'error':
            return 'CircleX';
        default:
            return 'Info';
    }
}

defineExpose({ addToast });
</script>

<template>
    <div class="anotif fixed right-4 bottom-4 z-50 space-y-2">
        <transition-group name="slide-fade" tag="div">
            <div
                v-for="toast in toasts"
                :key="toast.id"
                class="w-[400px] overflow-hidden rounded-lg border shadow-lg"
            >
                <!-- header -->
                <div
                    class="px-4 py-2 font-semibold text-white"
                    :class="{
                        'bg-blue-500': toast.type === 'info',
                        'bg-green-500': toast.type === 'success',
                        'bg-red-500': toast.type === 'error',
                    }"
                >
                    <div class="flex items-center gap-2">
                        <Icon :name="iconByType(toast.type)"></Icon>
                        {{ toast.title }}
                    </div>
                </div>
                <!-- body -->
                <div class="bg-white px-4 py-3 text-sm text-gray-700">
                    {{ toast.message }}
                </div>
            </div>
        </transition-group>
    </div>
</template>

<style scoped>
.slide-fade-enter-from {
    transform: translateX(100%);
    opacity: 0;
}
.slide-fade-enter-active {
    transition:
        transform 0.4s ease,
        opacity 0.4s ease;
}
.slide-fade-enter-to {
    transform: translateX(0);
    opacity: 1;
}

.slide-fade-leave-from {
    transform: translateX(0);
    opacity: 1;
}
.slide-fade-leave-active {
    transition:
        transform 0.4s ease,
        opacity 0.4s ease;
}
.slide-fade-leave-to {
    transform: translateX(100%);
    opacity: 0;
}
</style>
