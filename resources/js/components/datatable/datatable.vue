<template>
    <div class="overflow-hidden rounded-lg border">
        <div class="flex items-center justify-between p-2">
            <!-- <h1 class="text-lg font-semibold">Rekam Medis</h1> -->
            <Button
                @click="openFilter = !openFilter"
                class="cursor-pointer bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
            >
                Filter
            </Button>

            <!-- add Button -->
            <Button @click="addNew" class="cursor-pointer">
                + Create New
            </Button>
        </div>

        <div class="mb-2 p-2" v-if="openFilter">
            <div class="overflow-hidden rounded-lg border p-2">
                <slot name="filter"></slot>
            </div>
        </div>

        <table class="mt-4 w-full border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th
                        v-for="header in headers"
                        :key="header"
                        class="border px-3 py-2 text-left capitalize"
                    >
                        {{ header.replace('_', ' ') }}
                    </th>
                    <slot name="head" />
                    <th class="p-2 text-center">Action</th>
                </tr>
            </thead>

            <tbody>
                <tr v-if="props.loading">
                    <td colspan="100" class="p-4 text-center">Loading...</td>
                </tr>
                <!-- Data -->
                <tr
                    v-else
                    v-for="(row, rowIndex) in records"
                    :key="rowIndex"
                    class="hover:bg-gray-50"
                >
                    <td
                        v-for="header in headers"
                        :key="header"
                        class="border px-3 py-2"
                    >
                        <slot
                            :name="`row_${header}`"
                            :value="row[header]"
                            :row="row"
                        >
                            {{ row[header] ?? '-' }}
                        </slot>
                    </td>
                    <td class="border p-2 px-3 py-2 text-center">
                        <button class="text-blue-600 hover:underline">
                            Detail
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import Button from '../ui/button/Button.vue';

const props = defineProps<{
    // url: string;
    records: Array<Record<string, any>>;
    loading: boolean;
}>();

const emit = defineEmits({
    addNew: null,
    close: null,
    clearFilter: null,
    apply: null,
    onExport: null,
});

const openFilter = ref(false);

const hiddenColumns = ['id'];

const headers = computed(() => {
    if (!props.records.length) return [];
    return Object.keys(props.records[0]).filter(
        (key) => !hiddenColumns.includes(key),
    );
});

function addNew(value: any) {
    emit('addNew', value);
}
</script>
