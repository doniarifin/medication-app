<template>
    <Head :title="breadcrumbs[0]?.title" />

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
                :withAddBtn="false"
                :withDeleteBtn="false"
                :listHeader="data.listHeader"
                :hiddenColumns="['id', 'created_at']"
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
                                    v-model="data.filter.patient_name"
                                    kind="text"
                                    class="rounded-lg"
                                />
                            </div>
                        </template>
                    </Filter>
                </template>
                <template #header_is_paid>
                    {{ 'Paid' }}
                </template>
                <template #row_resep_dokter="{ row }">
                    <div v-if="row?.resep_dokter?.resep_dokter?.length">
                        <div
                            v-for="(
                                res, index
                            ) in row.resep_dokter.resep_dokter.slice(0, 3)"
                            :key="index"
                            class="truncate"
                        >
                            <span class="pr-2">-</span> {{ res.name }}
                        </div>
                        <div
                            v-if="row.resep_dokter?.resep_dokter.length > 3"
                            class="cursor-pointer truncate text-center text-sm text-blue-500 hover:underline"
                            @click="editData(row)"
                        >
                            more..
                        </div>
                    </div>
                    <span v-else> - </span>
                </template>
                <template #row_is_paid="{ row }">
                    {{ row.is_paid ? 'Yes' : 'Not yet' }}
                </template>
                <template #row_examined_at="{ row }">
                    {{ convertStrDate(row.examined_at) }}
                </template>
                <template #row_created_at="{ row }">
                    {{ convertStrDate(row.created_at) }}
                </template>
                <template #row_updated_at="{ row }">
                    {{ convertStrDate(row.updated_at) }}
                </template>
            </Datatable>
        </div>
        <AModal v-model="showDeleteModal" title="Delete Rekam Medis">
            <template #body> Are you sure to delete this data? </template>
            <template #footer>
                <AButton
                    variant="destructive"
                    :loading="data.loading"
                    class="cursor-pointer"
                    @click.once="onDelete"
                >
                    Yes
                </AButton>
                <AButton
                    variant="outline"
                    :loading="data.loading"
                    class="cursor-pointer"
                    @click="showDeleteModal = false"
                >
                    Cancel
                </AButton>
            </template>
        </AModal>
        <AModal
            type="form"
            v-model="showEditModal"
            :title="`${breadcrumbs[0]?.title}`"
            width="xl"
            @submit="submit"
            :disabled-save-btn="disableSaveBtn"
        >
            <template #body>
                <Form
                    :selectedId="data.selectedId"
                    ref="formRef"
                    :medical-record="props.medicalRecord"
                    :vital-sign="props.vitalSign"
                    :med-attachment="props.medAttachment"
                    :med-notes="props.medicalNotes"
                    :form="'edit'"
                    @close-modal="showEditModal = false"
                />
            </template>
            <template #footer>
                <AButton
                    as="button"
                    :disabled="disableSaveBtn"
                    type="submit"
                    class="cursor-pointer"
                    @click.prevent="submit"
                    >Save</AButton
                >
                <AButton
                    as="button"
                    :disabled="disableExport"
                    type="submit"
                    class="cursor-pointer"
                    @click.prevent="exportPdf"
                    >Export</AButton
                >
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
import { resep } from '@/routes';
import { BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import moment from 'moment';
import { onMounted, reactive, ref, watch } from 'vue';
import Form from './Form.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Resep Dokter',
        href: resep().url,
    },
];

const props = defineProps({
    records: Object,
    filters: Object,
    medicalRecord: Object,
    vitalSign: Object,
    medAttachment: Object,
    resepDokter: Object,
    medicalNotes: Object,
    form: String,
});

const data = reactive({
    loading: false,
    selectedId: '',
    records: [] as any,
    filter: {} as any,
    listHeader: {
        patient_name: 'Nama Pasien',
        resep_dokter: 'Resep Dokter',
        examined_at: 'Nama Pasien',
        is_paid: 'Nama Pasien',
        updated_at: 'Nama Pasien',
    },
});

const showDeleteModal = ref(false);
const showEditModal = ref(false);
const formRef = ref(null as any);
const disableSaveBtn = ref(false);
const disableExport = ref(false);

function addNew() {
    router.visit('rekam-medis/create');
}
function editData(record: any) {
    console.log(record);
    // router.visit(`/rekam-medis/${dt}/edit`);
    console.log(showEditModal.value);
    console.log(formRef.value);
    showEditModal.value = true;
    disableSaveBtn.value = record.is_paid;
    disableExport.value = !record.is_paid;
    data.selectedId = record.id;
}

function deleteData(record: any) {
    console.log(record);
    data.selectedId = record.id;
    showDeleteModal.value = true;
}

async function getData() {
    data.loading = true;

    try {
        const res = await axios.post('/api/resep/getdata', data.filter);
        data.records = res.data;
        data.loading = false;
    } catch (error) {
        showError(String(error));
    } finally {
        data.loading = false;
    }
}

async function onDelete() {
    data.loading = true;
    try {
        await axios.delete(`/api/rekam-medis/${data.selectedId}`);
        await getData();
        showDeleteModal.value = false;
        showSuccess('Delete data success');
        data.selectedId = '';
    } catch (error) {
        console.error(error);
        showDeleteModal.value = false;
        showError(String(error));
    } finally {
        data.loading = false;
        showDeleteModal.value = false;
        data.selectedId = '';
    }
}

function applyFilter() {
    getData();
}

function clearFilter() {
    data.filter = {};
    getData();
}

function convertStrDate(datetime: string): string {
    return moment(datetime).format('DD MMM YYYY');
}

async function submit() {
    await formRef.value?.submit();
    getData();
}

async function exportPdf() {
    await formRef.value?.exportPdf(data.selectedId);
    // getData();
}

watch(
    () => props.records,
    (val) => {
        data.records = val?.data ?? [];
        data.loading = false;
    },
    { immediate: true },
);

onMounted(getData);
</script>
