<template>
    <transition name="fade">
        <div
            v-if="modelValue"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
            @click.self="close"
        >
            <transition name="scale">
                <div
                    v-show="modelValue"
                    :class="`max-w-${props.width}`"
                    class="flex max-h-[90vh] w-full flex-col rounded-lg bg-white shadow-lg dark:bg-gray-800"
                >
                    <!-- Header -->
                    <div class="flex items-center justify-between border-b p-4">
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
                    <div class="flex-1 space-y-4 overflow-y-auto p-4">
                        <slot name="body"></slot>
                    </div>

                    <!-- Footer -->
                    <div class="flex justify-end gap-2 border-t p-4">
                        <slot name="footer">
                            <AButton
                                as="button"
                                v-if="!props.removeSaveBtn"
                                :disabled="props.disabledSaveBtn"
                                type="submit"
                                class="cursor-pointer"
                                @click.prevent="submit"
                                >Save</AButton
                            >
                        </slot>
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
    disabledSaveBtn: { type: Boolean, default: false },
    removeSaveBtn: { type: Boolean, default: false },
    width: {
        type: String as () => 'sm' | 'lg' | 'xl' | '2xl' | '3xl' | 'full',
        default: 'lg',
    },
});

const emit = defineEmits(['update:modelValue', 'submit']);

function close() {
    emit('update:modelValue', false);
}

function submit(e: Event) {
    emit('submit', e);
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
