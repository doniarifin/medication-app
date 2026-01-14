<template>
    <Head title="Rekam Medis" />

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
        <AModal v-model="showModal" title="Delete Rekam Medis">
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
                    @click="showModal = false"
                >
                    Cancel
                </AButton>
            </template>
        </AModal>

        <!-- modal view -->
        <AModal
            type="form"
            v-model="showEditModal"
            :title="`${breadcrumbs[0]?.title}`"
            width="xl"
            :removeSaveBtn="true"
        >
            <template #body>
                <Form
                    :hideHeader="true"
                    :readOnly="true"
                    :loading="data.loading"
                    ref="formRef"
                    :selectedId="data.selectedId"
                    :medical-record="data.props?.medicalRecord"
                    :vital-sign="data.props?.vitalSign"
                    :med-attachment="data.props?.medAttachment"
                    :med-notes="data.props?.medicalNotes"
                    :form="'view'"
                />
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
import moment from 'moment';
import { onMounted, reactive, ref, watch } from 'vue';
import Form from './Form.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Rekam Medis',
        href: rekamMedis().url,
    },
];

const props = defineProps({
    records: Object,
    filters: Object,
    medicalRecord: Object,
    medAttachment: Object,
    vitalSign: Object,
    medicalNotes: Object,
});

const data = reactive({
    loading: false,
    selectedId: '',
    records: [] as any,
    filter: {} as any,
    props: [] as any,
});

const showModal = ref(false);
const showEditModal = ref(false);

function addNew() {
    router.visit('rekam-medis/create');
}
async function editData(records: any) {
    if (records.is_paid) {
        // const data = await getDataById(records.id);
        data.selectedId = records.id;
        // console.log(data);
        showEditModal.value = true;
        return;
    }
    // console.log(id);
    router.visit(`/rekam-medis/${records.id}/edit`);
}

function deleteData(record: any) {
    console.log(record);
    data.selectedId = record.id;
    showModal.value = true;
}

async function getData() {
    data.loading = true;

    try {
        const res = await axios.post('/api/rekam-medis/getdata', data.filter);
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

// async function getDataById(id: string) {
//     data.loading = true;

//     try {
//         const res = await axios.get('/api/rekam-medis/getbyid/' + id);
//         data.props = res.data;
//         data.loading = false;
//         return res.data;
//     } catch (error) {
//         showError(String(error));
//     } finally {
//         data.loading = false;
//     }
// }

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
