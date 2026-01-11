<template>
    <div class="w-full py-2">
        <div class="grid grid-cols-4 gap-4 px-2 py-1 pt-2">
            <slot name="input"> Input </slot>
        </div>
        <div class="mt-2 flex w-full justify-end gap-3 px-2 pt-4 pb-2">
            <AButton
                v-if="withExport"
                variant="secondary"
                :loading="props.loading"
                class="cursor-pointer"
                @click="clickExport"
                :disabled="props.disabled"
            >
                Export
            </AButton>
            <AButton
                variant="destructive"
                :loading="props.loading"
                class="cursor-pointer"
                @click="clearFilter"
            >
                Clear
            </AButton>
            <AButton
                variant="outline"
                :loading="props.loading"
                class="cursor-pointer"
                @click="apply"
                >Apply</AButton
            >
        </div>
    </div>
</template>
<script lang="ts" setup>
import AButton from './app/AButton.vue';

const props = defineProps({
    withExport: { type: Boolean, default: false },
    user: { type: Object, default: () => {} },
    test3: { type: Array, default: () => [] },
    width: { type: String, default: 'w-[300px]' }, //used
    search: { type: String, default: '' },
    value: { type: Boolean, default: false },
    reduce: { type: String, default: '' },
    disabled: { type: Boolean, default: false },
    loading: { type: Boolean, default: false },
});
// const state = reactive({
//     loading: false,
//     name: '',
// });
defineExpose({
    refresh,
});
const emit = defineEmits({
    openFilter: null,
    close: null,
    clearFilter: null,
    apply: null,
    onExport: null,
});

function clearFilter(value: any) {
    emit('clearFilter', value);
}

function apply(value: any) {
    emit('apply', value);
}

function clickExport() {
    emit('onExport');
}

function refresh() {}
</script>
