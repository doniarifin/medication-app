<script setup lang="ts">
defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <section class="space-y-2">
        <div v-if="title">
            <h3 class="text-md font-medium text-gray-700">
                {{ title }}
            </h3>
            <p v-if="description" class="text-sm text-gray-500">
                {{ description }}
            </p>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <slot />
        </div>
    </section>
</template>
