<template>
    <AppContent />
    <BaseForm
        :title="(props.record ? 'Edit' : 'Add') + ' Rekam Medis'"
        :loading="data.loading"
        @submit="submit"
    >
        <FormSection title="Informasi Pasien">
            <FInput
                label="Nama"
                :loading="data.loading"
                v-model="form.patient_name"
                kind="text"
            />
            <FInput
                label="Tanggal"
                :loading="data.loading"
                kind="datetime"
                kind-date="only-date"
                v-model="form.examined_at"
                @update:model-value="handleDatepicker"
            />
        </FormSection>

        <FormSection title="Vital Sign">
            <FInput
                label="Tinggi Badan"
                :loading="data.loading"
                v-model="form.height"
                kind="text"
            />
            <FInput
                label="Berat Badan"
                :loading="data.loading"
                v-model="form.weight"
                kind="text"
            />
            <FInput
                label="Systole"
                v-model="form.systole"
                kind="text"
                :loading="data.loading"
            />
            <FInput
                label="Diastole"
                v-model="form.diastole"
                kind="text"
                :loading="data.loading"
            />
            <FInput
                label="Heart Rate"
                v-model="form.heart_rate"
                kind="text"
                :loading="data.loading"
            />
            <FInput
                label="Respiration Rate"
                v-model="form.respiration_rate"
                kind="text"
                :loading="data.loading"
            />
            <FInput
                label="Body Temperature"
                v-model="form.body_temperature"
                kind="text"
                :loading="data.loading"
            />
        </FormSection>

        <FormSection title="Catatan Dokter">
            <FInput
                v-model="form.note"
                :loading="data.loading"
                kind="text-area"
            ></FInput>
        </FormSection>
    </BaseForm>
</template>

<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import BaseForm from '@/components/form/BaseForm.vue';
import FInput from '@/components/form/FInput.vue';
import FormSection from '@/components/form/FormSection.vue';
import { showError, showSuccess } from '@/lib/toast';
import { router, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, reactive } from 'vue';

const props = defineProps({
    record: {
        type: Object,
        default: null,
    },
});

const form = useForm({
    patient_name: props.record?.patient_name ?? '',
    examined_at: props.record?.examined_at
        ? convertStrDate(props.record?.examined_at)
        : '',
    medicine_price: props.record?.medicine_price ?? 0,
    height: props.record?.height ?? '',
    weight: props.record?.weight ?? '',
    blood_pressure: props.record?.blood_pressure ?? '',
    note: props.record?.note ?? '',
    systole: props.record?.systole ?? '',
    diastole: props.record?.diastole ?? '',
    heart_rate: props.record?.heart_rate ?? '',
    respiration_rate: props.record?.respiration_rate ?? '',
    body_temperature: props.record?.body_temperature ?? '',
});

const data = reactive({
    loading: false,
    records: [],
});

function convertStrDate(datetime: string) {
    const d = new Date(datetime);
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function handleDatepicker(val: any) {
    if (!val) return '';

    const date = new Date(val);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();

    form.examined_at = `${year}-${month}-${day}`;
}

async function submit() {
    data.loading = true;

    if (props.record) {
        //edit
        form.put('/api/rekam-medis');
    } else {
        form.post('medical-records.store');
    }
    try {
        const response = await axios.post('/api/rekam-medis', form);

        showSuccess(response.data.message ?? 'Data has been saved');

        // reset
        form.patient_name = '';
        form.examined_at = '';
        form.height = '';
        form.weight = '';
        form.blood_pressure = '';
        form.note = '';

        router.visit('/rekam-medis');
    } catch (error: any) {
        if (error.response?.data?.errors) {
            const messages = Object.values(error.response.data.errors)
                .flat()
                .join('\n');
            showError(messages);
        } else {
            showError(error);
        }
    } finally {
        data.loading = false;
    }
}

// function formEditData(record: any) {}

async function getData() {
    data.loading = true;
    const res = await axios.get('/api/rekam-medis/');

    data.records = res.data;
    data.loading = false;
}

onMounted(getData);
</script>
