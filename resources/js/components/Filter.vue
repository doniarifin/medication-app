<template>
    <div class="mt-2 w-full rounded-md border py-2">
        <div class="grid grid-cols-4 gap-4 px-2 py-1 pt-2">
            <slot name="input"> Input </slot>
        </div>
        <div class="mt-2 flex w-full justify-end gap-3 px-2 pt-4 pb-2">
            <Button
                v-if="withExport"
                label="Export"
                color="yellow"
                @click="clickExport"
                :disabled="props.disabled"
            >
                <img class="h-[14px]" src="/img/export.svg" /> Export
            </Button>
            <Button
                variant="destructive"
                class="cursor-pointer"
                @click="clearFilter"
            >
                Clear
            </Button>
            <Button variant="outline" class="cursor-pointer" @click="apply"
                >Apply</Button
            >
        </div>
    </div>
</template>
<script lang="ts" setup>
import Button from './ui/button/Button.vue';

const props = defineProps({
    withExport: { type: Boolean, default: false },
    user: { type: Object, default: () => {} },
    test3: { type: Array, default: () => [] },
    width: { type: String, default: 'w-[300px]' }, //used
    search: { type: String, default: '' },
    value: { type: Boolean, default: false },
    reduce: { type: String, default: '' },
    disabled: { type: Boolean, default: false },
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
