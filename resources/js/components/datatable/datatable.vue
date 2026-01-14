<template>
    <div class="overflow-hidden rounded-lg border">
        <div class="flex items-center justify-between p-2">
            <!-- <h1 class="text-lg font-semibold">Rekam Medis</h1> -->
            <AButton
                @click="openFilter = !openFilter"
                class="cursor-pointer bg-blue-400 hover:bg-blue-300"
            >
                <Icon name="filter" />
                Filter
            </AButton>

            <!-- add Button -->
            <div v-if="props.withAddBtn">
                <AButton @click.once="addNew" class="cursor-pointer">
                    <Icon name="plus" />
                    Add New
                </AButton>
            </div>
        </div>

        <div class="mb-2 p-2" v-if="openFilter">
            <div class="overflow-hidden rounded-lg border p-2 shadow">
                <slot name="filter"></slot>
            </div>
        </div>

        <div class="overflow-hidden">
            <table ref="headTable" class="mt-2 w-full border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th
                            v-for="header in headers"
                            :key="header"
                            class="border px-3 py-2 capitalize"
                        >
                            <slot
                                :name="`header_${header}`"
                                :value="header"
                                :header="header"
                            >
                                <!-- {{ row[header] ?? '-' }} -->
                                {{ header.replace('_', ' ') }}
                            </slot>
                        </th>
                        <slot name="head" />
                        <th
                            v-if="paginatedRecords.length && props.withAction"
                            class="w-[140px] border px-3 py-2"
                        >
                            Action
                        </th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="max-h-screen overflow-y-auto">
            <table ref="bodyTable" class="w-full">
                <tbody>
                    <tr v-if="props.loading">
                        <td colspan="100" class="justify-items-center p-4">
                            <div class="items-center">
                                <Spinner class="h-8 w-8" />
                            </div>
                        </td>
                    </tr>
                    <!-- Data -->
                    <tr
                        v-else
                        v-for="(row, rowIndex) in paginatedRecords"
                        :key="rowIndex"
                        class="hover:bg-gray-50"
                    >
                        <td
                            v-for="header in headers"
                            :key="header"
                            class="max-w-xs truncate border px-3 py-2"
                        >
                            <slot
                                :name="`row_${header}`"
                                :value="row[header]"
                                :row="row"
                            >
                                {{ row[header] ?? '-' }}
                            </slot>
                        </td>
                        <slot name="body" />
                        <td class="border px-3 py-2">
                            <slot name="action_body" />
                            <div class="flex justify-center">
                                <AButton
                                    v-if="props.allwedRole && props.withEditBtn"
                                    @click.prevent="editData(row)"
                                    variant="ghost"
                                    class="cursor-pointer hover:opacity-50"
                                >
                                    <Icon
                                        class="text-blue-400"
                                        stroke-width="3"
                                        name="SquarePen"
                                    ></Icon>
                                </AButton>
                                <AButton
                                    v-if="
                                        props.allwedRole && props.withDeleteBtn
                                    "
                                    @click.prevent="deleteData(row)"
                                    variant="ghost"
                                    class="cursor-pointer hover:opacity-50"
                                >
                                    <Icon
                                        class="text-red-400"
                                        stroke-width="3"
                                        name="trash"
                                    ></Icon>
                                </AButton>
                            </div>
                        </td>
                    </tr>
                    <tr
                        v-if="!paginatedRecords.length && !props.loading"
                        class="hover:bg-gray-50"
                    >
                        <td
                            colspan="100"
                            class="justify-items-center p-4 py-20"
                        >
                            <div class="items-center">No Data</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-3 flex items-center justify-end gap-2 p-2 text-sm">
            <div class="flex items-center gap-2">
                <span>Rows per page:</span>
                <select
                    v-model.number="perPage"
                    @change="changePerPage(perPage)"
                    class="rounded border px-2 py-1"
                >
                    <option
                        v-for="opt in perPageOptions"
                        :key="opt"
                        :value="opt"
                    >
                        {{ opt }}
                    </option>
                </select>
            </div>

            <AButton
                type="button"
                @click="prevPage"
                :disabled="currentPage === 1"
                class="cursor-pointer rounded-full px-3 py-2"
            >
                <Icon name="ChevronLeft" stroke-width="4"></Icon>
            </AButton>

            <template v-for="page in pages" :key="page">
                <button
                    v-if="page !== '…'"
                    @click="goToPage(Number(page))"
                    :class="[
                        'cursor-pointer rounded border px-3 py-1 hover:bg-blue-200',
                        currentPage === page
                            ? 'bg-blue-500 text-white'
                            : 'bg-white',
                    ]"
                >
                    {{ page }}
                </button>
                <span v-else class="px-2 py-1">…</span>
            </template>

            <AButton
                @click="nextPage"
                :disabled="currentPage === totalPages"
                class="cursor-pointer rounded-full px-3 py-2"
            >
                <Icon name="ChevronRight" stroke-width="4"></Icon>
            </AButton>
        </div>
    </div>
</template>

<script setup lang="ts">
import {
    computed,
    nextTick,
    onBeforeUnmount,
    onMounted,
    ref,
    watch,
} from 'vue';
import AButton from '../app/AButton.vue';
import Icon from '../Icon.vue';
import { Spinner } from '../ui/spinner';

type RecordRow = Record<string, any>;

const props = withDefaults(
    defineProps<{
        records?: RecordRow[];
        loading?: boolean;
        withAddBtn?: boolean;
        allwedRole?: boolean;
        withAction?: boolean;
        withEditBtn?: boolean;
        withDeleteBtn?: boolean;
        listHeader?: Record<string, string>;
        hiddenColumns?: string[];
    }>(),
    {
        records: () => [],
        loading: false,
        withAddBtn: true,
        allwedRole: true,
        withAction: true,
        withEditBtn: true,
        withDeleteBtn: true,
        listHeader: () => ({}),
        hiddenColumns: () => ['id'],
    },
);

const emit = defineEmits({
    addNew: null,
    editData: null,
    deleteData: null,
    close: null,
    clearFilter: null,
    apply: null,
    onExport: null,
});

const openFilter = ref(false);
// const headTable = ref(null);
const safeRecords = computed<any[]>(() => props.records ?? []);

const hiddenColumns = computed<any[]>(() => props.hiddenColumns ?? []);

const headers = computed(() => {
    if (Object.keys(props.listHeader).length > 0) {
        return Object.keys(props.listHeader).filter(
            (key) => !hiddenColumns.value.includes(key),
        );
    }
    if (!safeRecords.value.length) return [];
    return Object.keys(safeRecords.value[0]).filter(
        (key) => !hiddenColumns.value.includes(key),
    );
});

const currentPage = ref(1);
const perPage = ref(5);

const totalPages = computed(() =>
    Math.ceil(safeRecords.value.length / perPage.value),
);

const perPageOptions = [3, 5, 10, 20, 50];

const paginatedRecords = computed(() => {
    const start = (currentPage.value - 1) * perPage.value;
    const end = start + perPage.value;
    return safeRecords.value.slice(start, end);
});

function changePerPage(value: number) {
    perPage.value = value;
    currentPage.value = 1;
}

function nextPage() {
    if (currentPage.value < totalPages.value) currentPage.value++;
}

function prevPage() {
    if (currentPage.value > 1) currentPage.value--;
}

function addNew(value: any) {
    emit('addNew', value);
}

function editData(value: any) {
    emit('editData', value);
}
function deleteData(value: any) {
    emit('deleteData', value);
}

const maxVisible = 4;

const pageWindow = computed(() => {
    const visible = Math.min(maxVisible, totalPages.value);
    let start = 1;
    let end = visible;

    if (currentPage.value > totalPages.value - visible + 1) {
        start = totalPages.value - visible + 1;
        end = totalPages.value;
    } else if (currentPage.value > 2) {
        start = currentPage.value - 1;
        end = start + visible - 1;
    }

    if (start < 1) start = 1;
    if (end > totalPages.value) end = totalPages.value;

    return { start, end };
});

const pages = computed(() => {
    const { start, end } = pageWindow.value;
    const arr: (number | string)[] = [];

    if (start > 1) arr.push('…');
    for (let i = start; i <= end; i++) arr.push(i);
    if (end < totalPages.value) arr.push('…');

    return arr;
});

function goToPage(page: number | string) {
    if (page === '…') {
        if (currentPage.value < totalPages.value - maxVisible + 1) {
            currentPage.value += maxVisible;
            if (currentPage.value > totalPages.value)
                currentPage.value = totalPages.value;
        } else {
            currentPage.value -= maxVisible;
            if (currentPage.value < 1) currentPage.value = 1;
        }
    } else if (typeof page === 'number') {
        currentPage.value = page;
    }
}

const headTable = ref<HTMLTableElement | null>(null);
const bodyTable = ref<HTMLTableElement | null>(null);

const syncColumnWidth = () => {
    if (!headTable.value || !bodyTable.value) return;

    const ths = headTable.value.querySelectorAll('th');
    const bodyRows = bodyTable.value.querySelectorAll('tr');

    ths.forEach((th, index) => {
        const width = th.offsetWidth;

        bodyRows.forEach((row) => {
            const td = row.children[index] as HTMLElement;
            if (td) {
                td.style.width = `${width}px`;
                td.style.minWidth = `${width}px`;
                td.style.maxWidth = `${width}px`;
            }
        });
    });
};

onMounted(async () => {
    await nextTick();
    syncColumnWidth();
    window.addEventListener('resize', syncColumnWidth);
});

onBeforeUnmount(() => {
    window.removeEventListener('resize', syncColumnWidth);
});

watch(
    () => paginatedRecords,
    async () => {
        await nextTick();
        syncColumnWidth();
    },
    { deep: true },
);
</script>
