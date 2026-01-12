<template>
    <AppContent />
    <BaseForm
        :title="(props.medicalRecord ? 'Edit' : 'Add') + ' Rekam Medis'"
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
                label="Notes"
                v-model="form.note"
                :loading="data.loading"
                kind="text-area"
            ></FInput>
            <FUpload
                @clear-file="clearFile"
                @onChange="onChangefile"
                :placeholder="'Choose File'"
                :file-name="data.fileName"
                v-model="form.file"
                label="Upload File"
            />
        </FormSection>
    </BaseForm>
</template>

<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import BaseForm from '@/components/form/BaseForm.vue';
import FInput from '@/components/form/FInput.vue';
import FormSection from '@/components/form/FormSection.vue';
import FUpload from '@/components/form/FUpload.vue';
import { showError, showSuccess } from '@/lib/toast';
import { router, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, reactive } from 'vue';

const props = defineProps({
    medicalRecord: {
        type: Object,
        default: null,
    },
    vitalSign: {
        type: Object,
        default: null,
    },
    medAttachment: {
        type: Object,
        default: null,
    },
});

const form = useForm({
    patient_name: props.medicalRecord?.patient_name ?? '',
    examined_at: props.medicalRecord?.examined_at
        ? convertStrDate(props.medicalRecord?.examined_at)
        : '',
    medicine_price: props.medicalRecord?.medicine_price ?? 0,
    height: props.vitalSign?.height ? convertInt(props.vitalSign?.height) : '',
    weight: props.vitalSign?.weight ? convertInt(props.vitalSign?.weight) : '',
    blood_pressure: props.vitalSign?.blood_pressure ?? '',
    note: props.vitalSign?.note ?? '',
    systole: props.vitalSign?.systole ?? '',
    diastole: props.vitalSign?.diastole ?? '',
    heart_rate: props.vitalSign?.heart_rate ?? '',
    respiration_rate: props.vitalSign?.respiration_rate ?? '',
    body_temperature: props.vitalSign?.body_temperature ?? '',
    file: null,
});

const data = reactive({
    loading: false,
    records: [],
    filter: {} as any,
    fileName: '',
    attachment: {} as any,
});

function convertInt(value: string) {
    const result = Number.parseInt(value);

    if (Number.isNaN(result)) {
        throw new Error('Invalid number');
    }

    return result;
}

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
    console.log(form.file);

    form.body_temperature = Number(form.body_temperature);

    try {
        let response = {} as any;
        if (props.medicalRecord) {
            response = await axios.put(
                `/api/rekam-medis/${props.medicalRecord.id}`,
                form,
            );
        } else {
            response = await axios.post('/api/rekam-medis/insert', form);
        }

        console.log(response);

        await uploadData(response.data?.data);

        // reset
        form.patient_name = '';
        form.examined_at = '';
        form.height = '';
        form.weight = '';
        form.blood_pressure = '';
        form.note = '';

        router.visit('/rekam-medis');
        showSuccess(response.data.message ?? 'Data has been saved');
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

async function uploadData(record: any) {
    data.loading = true;
    const payload = {
        id: data.attachment?.id,
        file: form.file,
        medical_record_id: record.id,
        path: data.attachment?.path,
        original_name: data.attachment?.original_name,
        mime_type: data.attachment?.mime_type,
        size: data.attachment?.size,
        is_deleted: data.attachment?.is_deleted,
    };

    const formData = new FormData();

    Object.entries(payload).forEach(([key, value]) => {
        if (value !== undefined && value !== null) {
            if (key === 'is_deleted') {
                formData.append(key, value ? '1' : '0');
            } else {
                formData.append(key, value);
            }
        }
    });

    await axios.post('/api/attachment/upload', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
    });
}

async function getData() {
    const idMedical = props?.medicalRecord?.id;

    console.log(idMedical);
    if (!idMedical) {
        return;
    }
    data.loading = true;

    // data.fileName = props?.medAttachment?.original_name;
    console.log(data.fileName);
    const res = await axios.post('/api/rekam-medis/getdata', {
        id: idMedical,
    });

    data.records = res.data;
    data.loading = false;
}

async function getAttachment() {
    const Id = props?.medicalRecord?.id;

    // console.log(Id);
    if (!Id) {
        return;
    }
    data.loading = true;

    // console.log(data.fileName);
    const res = await axios.post('/api/attachment/gets', {
        medical_record_id: Id,
        is_deleted: false,
    });

    console.log(res);
    data.attachment = res?.data;
    data.fileName = data.attachment?.original_name;
    console.log(data.attachment);
    data.loading = false;
}

function clearFile() {
    data.attachment.is_deleted = true;
    console.log('is_deleted? ', data.attachment.is_deleted);
}

function onChangefile(val: boolean) {
    data.attachment.is_deleted = val;
    console.log('is_deleted? ', val);
}

onMounted(async () => {
    getData();
    await getAttachment();
});
</script>
