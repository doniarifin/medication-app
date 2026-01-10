<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <Datatable
                @addNew="addNew"
                :records="data.records"
                :loading="data.loading"
            >
                <template #filter>
                    <Filter @apply="applyFilter()" @clearFilter="clearFilter">
                        <template v-slot:input>
                            <div>
                                <div class="text-slate-400">Candidates</div>
                                <div>
                                    <Input v-model="data.filter.patientName">
                                    </Input>
                                </div>
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
    </AppLayout>
</template>

<script setup lang="ts">
import Datatable from '@/components/datatable/datatable.vue';
import Filter from '@/components/Filter.vue';
import Input from '@/components/ui/input/Input.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { rekamMedis } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, reactive } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Rekam Medis',
        href: rekamMedis().url,
    },
];

const data = reactive({
    loading: false,
    records: [],
    filter: {
        patientName: '',
    },
});

function addNew() {
    router.visit('rekam-medis/create');
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
