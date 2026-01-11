<script setup lang="ts">
import Button from '../ui/button/Button.vue';
import { Spinner } from '../ui/spinner';

const props = defineProps({
    label: { type: String, default: '' },
    size: {
        type: String as () =>
            | 'sm'
            | 'default'
            | 'lg'
            | 'icon'
            | 'icon-sm'
            | 'icon-lg',
        default: 'default',
    },
    variant: {
        type: String as () =>
            | 'default'
            | 'secondary'
            | 'destructive'
            | 'outline'
            | 'ghost'
            | 'link',
        default: 'default',
    },
    class: { type: String, default: '' },
    disabled: { type: Boolean, default: false },
    loading: { type: Boolean, default: false },
});
</script>

<template>
    <Button
        :size="props.size"
        :variant="props.variant"
        :class="[
            props.disabled
                ? props.class + ' cursor-not-allowed opacity-50'
                : props.class,
            $attrs.class,
        ]"
        :disabled="props.disabled"
        v-bind="$attrs"
    >
        <template #default>
            <span class="flex items-center gap-2">
                <Spinner v-if="props.loading" />
                <slot />
            </span>
        </template>
    </Button>
</template>
