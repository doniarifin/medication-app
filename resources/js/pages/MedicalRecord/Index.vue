<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <Datatable
                @addNew="addNew"
                @editData="editData"
                @deleteData="deleteData"
                :records="data.records"
                :loading="data.loading"
            >
                <template #filter>
                    <Filter
                        :loading="data.loading"
                        @apply="applyFilter()"
                        @clearFilter="clearFilter"
                    >
                        <template v-slot:input>
                            <div>
                                <FInput
                                    label="Patient Name"
                                    :loading="data.loading"
                                    v-model="data.filter.patientName"
                                    kind="text"
                                    class="rounded-lg"
                                />
                            </div>
                        </template>
                    </Filter>
                </template>

                <template #row_examined_at="{ row }">
                    {{ new Date(row.examined_at).toLocaleString() }}
                </template>
                <template #row_created_at="{ row }">
                    {{ new Date(row.created_at).toLocaleString() }}
                </template>
                <template #row_updated_at="{ row }">
                    {{ new Date(row.updated_at).toLocaleString() }}
                </template>
            </Datatable>
        </div>
        <AModal v-model="showModal" title="Delete Rekam Medis">
            <template #body> Are you sure to delete this data? </template>
            <template #footer>
                <AButton
                    variant="outline"
                    :loading="data.loading"
                    class="cursor-pointer"
                    @click="showModal = false"
                >
                    Cancel
                </AButton>
                <AButton
                    variant="destructive"
                    :loading="data.loading"
                    class="cursor-pointer"
                    @click="onDelete"
                >
                    Yes
                </AButton>
            </template>
        </AModal>
    </AppLayout>
</template>

<script setup lang="ts">
import AButton from '@/components/app/AButton.vue';
import AModal from '@/components/app/AModal.vue';
import Datatable from '@/components/datatable/datatable.vue';
import Filter from '@/components/Filter.vue';
import FInput from '@/components/form/FInput.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { showError, showSuccess } from '@/lib/toast';
import { rekamMedis } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, reactive, ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Rekam Medis',
        href: rekamMedis().url,
    },
];

const data = reactive({
    loading: false,
    selectedId: '',
    records: [],
    filter: {
        patientName: '',
    },
});

const showModal = ref(false);
function addNew() {
    router.visit('rekam-medis/create');
}
function editData(id: string) {
    // console.log(id);
    router.visit(`/rekam-medis/${id}/edit`);
}

function deleteData(record: any) {
    console.log(record);
    data.selectedId = record.id;
    showModal.value = true;
}

async function onDelete() {
    data.loading = true;
    try {
        const res = await axios.delete(`/api/rekam-medis/${data.selectedId}`);
        data.records = res.data;
        showModal.value = false;
        showSuccess('Delete data success');
        data.selectedId = '';
    } catch (error) {
        console.error(error);
        showModal.value = false;
        showError(String(error));
    } finally {
        data.loading = false;
        showModal.value = false;
        data.selectedId = '';
    }
}

async function getData() {
    data.loading = true;
    const res = await axios.get('/api/rekam-medis');

    data.records = res.data;
    // console.log(data.records);
    data.loading = false;
}

function applyFilter() {
    // page.value = 1;
    // state.skip = 0;
    getData();
}

function clearFilter() {
    data.filter = {
        patientName: '',
    };

    getData();
}

onMounted(getData);
</script>
