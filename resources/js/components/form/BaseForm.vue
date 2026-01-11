<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import AButton from '../app/AButton.vue';
import Icon from '../Icon.vue';

const props = defineProps<{
    title: string;
    loading?: boolean;
    backUrl?: string;
}>();

const emit = defineEmits<{
    (e: 'submit'): void;
}>();

function back() {
    if (props.backUrl) {
        router.visit(props.backUrl);
    } else {
        window.history.back();
    }
}
</script>

<template>
    <!-- Header -->
    <div
        class="sticky top-0 z-30 flex items-center justify-between border-b bg-white px-6 py-4 shadow-sm"
    >
        <div class="flex items-center gap-3">
            <slot name="title">
                <AButton
                    type="button"
                    variant="ghost"
                    @click="back"
                    :disabled="loading"
                    class="cursor-pointer text-gray-600 hover:text-gray-900"
                >
                    <Icon name="ArrowLeft" stroke-width="4"></Icon>
                </AButton>

                <h1 class="text-lg font-semibold text-gray-800">
                    {{ title }}
                </h1>
            </slot>
        </div>

        <slot name="save-button">
            <AButton
                :disabled="loading"
                @click="emit('submit')"
                type="submit"
                class="cursor-pointer"
                :loading="props.loading"
            >
                <Icon name="save"></Icon>
                Save
            </AButton>
        </slot>
    </div>
    <form
        @submit.prevent="emit('submit')"
        class="h-screen space-y-6 bg-white p-6 shadow"
    >
        <!-- Content -->
        <div class="space-y-4">
            <slot />
        </div>

        <!-- Footer -->
        <div v-if="$slots.footer" class="border-t pt-4">
            <slot name="footer" />
        </div>
    </form>
</template>
