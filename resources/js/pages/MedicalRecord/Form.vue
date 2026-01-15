<template>
    <AppContent />
    <BaseForm
        :hide-header="props.hideHeader"
        :title="(props.medicalRecord ? 'Edit' : 'Add') + ' Rekam Medis'"
        :loading="data.loading"
        @submit="submit"
    >
        <FormSection title="Informasi Pasien">
            <FInput
                label="Nama"
                :disabled="props.readOnly"
                :loading="data.loading"
                v-model="form.patient_name"
                kind="text"
            />
            <FInput
                label="Tanggal Pemeriksaan"
                :loading="data.loading"
                :disabled="props.readOnly"
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
                :disabled="props.readOnly"
                v-model="form.height"
                kind="number"
            />
            <FInput
                label="Berat Badan"
                :loading="data.loading"
                :disabled="props.readOnly"
                v-model="form.weight"
                kind="number"
            />
            <FInput
                label="Systole"
                v-model="form.systole"
                :disabled="props.readOnly"
                kind="number"
                :loading="data.loading"
            />
            <FInput
                label="Diastole"
                v-model="form.diastole"
                :disabled="props.readOnly"
                kind="number"
                :loading="data.loading"
            />
            <FInput
                label="Heart Rate"
                v-model="form.heart_rate"
                :disabled="props.readOnly"
                kind="number"
                :loading="data.loading"
            />
            <FInput
                label="Respiration Rate"
                v-model="form.respiration_rate"
                :disabled="props.readOnly"
                kind="number"
                :loading="data.loading"
            />
            <FInput
                label="Body Temperature"
                v-model="form.body_temperature"
                :disabled="props.readOnly"
                kind="number"
                :loading="data.loading"
            />
        </FormSection>

        <FormSection title="Resep Dokter">
            <template #form-section>
                <div class="gap-4 border p-2 shadow">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="flex items-center">
                            <div class="w-full text-sm text-gray-500">
                                Nama Obat
                            </div>
                        </div>
                        <div
                            class="flex w-full items-center justify-between gap-4"
                        >
                            <div class="w-full text-sm text-gray-500">
                                Keterangan
                            </div>
                            <div class="p-2">
                                <AButton
                                    v-if="!props.readOnly"
                                    class="cursor-pointer"
                                    variant="secondary"
                                    @click="addMedicine"
                                >
                                    <Icon name="plus"></Icon>
                                </AButton>
                            </div>
                        </div>
                    </div>

                    <div
                        class="shadow-b grid gap-4 border-b p-2 md:grid-cols-2"
                        v-for="(med, index) in medicines"
                        :key="index"
                    >
                        <input v-model="med.id" hidden />
                        <div v-if="!props.readOnly">
                            <ASelectItem
                                :items="listMedicines"
                                v-model="data.resep_dokter[index]"
                                :searchable="true"
                                button-label="Pilih Obat"
                                label-name="name"
                                :loading="data.loading"
                                @select="onSelectMedicine"
                            >
                                <template #item="{ item }">
                                    {{ item.name }}
                                </template>
                            </ASelectItem>
                        </div>
                        <div v-else>
                            {{ data.resep_dokter[index].name }}
                        </div>

                        <div
                            class="flex w-full items-center justify-between gap-4"
                        >
                            <div class="w-full">
                                <FInput
                                    v-model="
                                        data.resep_dokter[index].description
                                    "
                                    :loading="data.loading"
                                    kind="text-area"
                                    :disabled="props.readOnly"
                                ></FInput>
                            </div>
                            <div>
                                <AButton
                                    v-if="!props.readOnly"
                                    class="cursor-pointer"
                                    variant="destructive"
                                    @click="removeMedicine"
                                >
                                    <Icon name="trash"></Icon>
                                </AButton>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </FormSection>

        <FormSection title="Catatan Dokter">
            <FInput
                label="Notes"
                :disabled="props.readOnly"
                v-model="form.notes"
                :loading="data.loading"
                kind="text-area"
            ></FInput>
            <FUpload
                v-if="!props.readOnly"
                @clear-file="clearFile"
                @onChange="onChangefile"
                @downloadFile="downloadFile"
                :placeholder="'Choose File'"
                :file-name="data.fileName"
                v-model="form.file"
                label="Upload File"
                :loading="data.loading"
            />
            <div v-if="props.readOnly">
                <div class="text-sm text-gray-500">
                    {{ 'File' }}
                </div>
                <div class="border p-2">
                    <AButton
                        @click="downloadFile"
                        variant="ghost"
                        class="w-full cursor-pointer break-words whitespace-normal"
                        as="button"
                        :label="form.file?.path ? data.fileName : 'No File'"
                        :disabled="!form.file?.path"
                    >
                    </AButton>
                </div>
            </div>
        </FormSection>
    </BaseForm>
</template>

<script setup lang="ts">
import AButton from '@/components/app/AButton.vue';
import ASelectItem, { SelectItem } from '@/components/app/ASelectItem.vue';
import AppContent from '@/components/AppContent.vue';
import BaseForm from '@/components/form/BaseForm.vue';
import FInput from '@/components/form/FInput.vue';
import FormSection from '@/components/form/FormSection.vue';
import FUpload from '@/components/form/FUpload.vue';
import Icon from '@/components/Icon.vue';
import { showError, showInfo, showSuccess } from '@/lib/toast';
import { router, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, reactive, ref, watch } from 'vue';

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
    resepDokter: {
        type: Object,
        default: null,
    },
    medNotes: {
        type: Object,
        default: null,
    },
    form: {
        type: String,
        default: null,
    },
    hideHeader: {
        type: Boolean,
        default: false,
    },
    readOnly: {
        type: Boolean,
        default: false,
    },
    loading: {
        type: Boolean,
        default: false,
    },
    selectedId: {
        type: String,
        default: '',
    },
});

const form = useForm({
    patient_name: null,
    examined_at: props.medicalRecord?.examined_at
        ? convertStrDate(props.medicalRecord?.examined_at)
        : null,
    medicine_price: props.medicalRecord?.medicine_price ?? 0,
    //
    height: null as any,
    weight: null as any,
    blood_pressure: null,
    systole: null,
    diastole: null,
    heart_rate: null,
    respiration_rate: null,
    body_temperature: null,
    //
    resep_dokter: [] as any,
    notes: props.medNotes?.notes ?? null,
    file: null as any,
});

function onSelectMedicine(item: any) {
    console.log('Selected:', item);
}

const medicines = ref([
    {
        id: null,
        name: '',
        description: '',
    },
]);

const data = reactive({
    loading: false,
    records: null as any,
    filter: {} as any,
    fileName: '',
    attachment: {} as any,
    resep_dokter: medicines,
});

const listMedicines = ref<SelectItem[]>([
    { id: '1', name: 'test', description: 'desc1' },
    { id: '2', name: 'test2', description: 'desc2' },
]);

// const selectedItem = ref();

function addMedicine() {
    data.resep_dokter.push({ id: null, name: '', description: '' });
}

function removeMedicine(index: number) {
    data.resep_dokter.splice(index, 1);
}

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
    if (data.loading) return;
    data.loading = true;

    // form.body_temperature = Number(form.body_temperature);
    console.log('before', form);

    form.resep_dokter = mapSpecificKey(data.resep_dokter, [
        'id',
        'name',
        'description',
    ]);

    console.log(form);
    // data.loading = false;

    // return;

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

        console.log('response', response.data?.data);

        await uploadData(response.data?.data);

        // reset
        form.patient_name = null;
        form.examined_at = null;
        form.height = null;
        form.weight = null;
        form.blood_pressure = null;
        form.notes = null;

        await redirect();
        showSuccess(response.data.message ?? 'Data has been saved');
    } catch (error: any) {
        if (error.response?.data?.errors) {
            const messages = Object.values(error.response.data.errors)
                .flat()
                .join('\n');
            showError(messages);
            data.loading = false;
        } else {
            showError(error);
            data.loading = false;
        }
    } finally {
        // data.loading = false;
    }
}

function mapSpecificKey(arr: any[], keys: string[]) {
    return arr.map((item) => {
        const mapped: any = {};
        keys.forEach((key) => {
            if (key === 'id') {
                mapped[key] = item[key];
            } else {
                mapped[key] = item[key] !== undefined ? item[key] : '';
            }
        });
        return mapped;
    });
}

async function redirect() {
    router.visit('/rekam-medis', {
        onFinish: () => {
            data.loading = false;
        },
    });
}

async function uploadData(record: any) {
    data.loading = true;
    const payload = {
        id: data.attachment?.id,
        file: form.file,
        medical_record_id: record?.medical_record?.id,
        path: data.attachment?.path,
        original_name: data.attachment?.original_name,
        mime_type: data.attachment?.mime_type,
        size: data.attachment?.size,
        is_deleted: data.attachment?.is_deleted,
    };

    if (!(payload.file instanceof File)) {
        payload.file = null;
    }

    if (props.form !== 'edit') {
        payload.medical_record_id = record.id;
    }
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
    console.log('props', props);

    // console.log(idMedical);
    if (!idMedical) {
        return;
    }
    data.loading = true;

    try {
        const res = await axios.post('/api/rekam-medis/getdata', {
            id: idMedical,
        });

        data.records = res.data;
        data.loading = false;
    } catch (error: any) {
        if (error.response?.data?.errors) {
            const messages = Object.values(error.response.data.errors)
                .flat()
                .join('\n');
            showError(messages);
            data.loading = false;
        } else {
            showError(error);
            data.loading = false;
        }
    } finally {
        data.loading = false;
    }
}

async function getDataById() {
    if (!props.readOnly) {
        return;
    }
    data.loading = true;

    try {
        const res = await axios.get(
            '/api/rekam-medis/getbyid/' + props.selectedId,
        );
        data.records = res.data;
        data.loading = false;
        return res.data;
    } catch (error) {
        showError(String(error));
    } finally {
        data.loading = false;
    }
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

async function getMedicineData() {
    data.loading = true;

    // console.log('resepDokter', props.resepDokter);

    const res = await axios.post('/api/medicines/getdata', {});

    listMedicines.value = res.data;
    // console.log('yuhu', form.resep_dokter);
    console.log(props.form);
    if (props.form == 'edit') {
        data.resep_dokter = form.resep_dokter;
        // console.log('resep', data.resep_dokter);
        if (!data.resep_dokter.length) {
            addMedicine();
        }
    }
    if (props.form == 'view') {
        data.resep_dokter = form.resep_dokter;
    }
    data.loading = false;
}

async function downloadFile(id: any) {
    id = form.file?.id;
    if (!id) return;
    if (data.loading) return;
    data.loading = true;

    try {
        const res = await axios.get(`/api/download/${id}`, {
            responseType: 'blob',
        });

        const disposition = res.headers['content-disposition'];

        let filename = 'file';

        if (disposition) {
            const match = disposition.match(
                /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/,
            );
            if (match && match[1]) {
                filename = match[1].replace(/['"]/g, '');
            }
        }

        const blob = new Blob([res.data]);
        const url = window.URL.createObjectURL(blob);

        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();

        a.remove();
        window.URL.revokeObjectURL(url);
        showInfo('file downloading..');
    } catch (error: any) {
        if (error.response?.data?.errors) {
            const messages = Object.values(error.response.data.errors)
                .flat()
                .join('\n');
            showError(messages);
            data.loading = false;
        } else {
            showError(error);
            data.loading = false;
        }
    } finally {
        data.loading = false;
    }
}

watch(
    [() => props.medicalRecord, () => data.records],
    ([medicalRecord, records]) => {
        console.log('recod', records);
        const source = medicalRecord ?? records?.medical_record;
        if (!source) return;

        form.patient_name = source.patient_name ?? null;
        form.examined_at = source.examined_at
            ? convertStrDate(source.examined_at)
            : null;
        form.medicine_price = source.medicine_price ?? 0;
    },
    { immediate: true },
);

watch(
    [() => props.vitalSign, () => data.records],
    ([vitalSign, records]) => {
        const source = vitalSign ?? records?.vital_sign;
        if (!source) return;

        form.height = source.height ? convertInt(source.height) : null;
        form.weight = source.weight ? convertInt(source.weight) : null;
        form.blood_pressure = source.blood_pressure ?? null;
        form.systole = source.systole ?? null;
        form.diastole = source.diastole ?? null;
        form.heart_rate = source.heart_rate ?? null;
        form.respiration_rate = source.respiration_rate ?? null;
        form.body_temperature = source.body_temperature ?? null;
    },
    { immediate: true },
);

watch(
    [() => props.resepDokter, () => data.records],
    ([resepDokter, records]) => {
        // console.log('recod', records);
        const source = resepDokter ?? records?.resep_dokter;
        if (!source) return;

        form.resep_dokter = source.resep_dokter ?? null;
    },
    { immediate: true },
);

watch(
    [() => props.medAttachment, () => data.records],
    ([attachment, records]) => {
        // let source = null;
        // if (Object.keys(attachment).length > 0) {
        //     source = attachment;
        // } else {
        //     source = records?.medical_attachment;
        // }
        const source = attachment ?? records?.medical_attachment;
        if (!source) return;

        form.file = source ?? null;
        console.log('formfile', form.file);
        data.fileName = source.original_name;
    },
    { immediate: true },
);

watch(
    [() => props.medNotes, () => data.records],
    ([medNotes, records]) => {
        const source = medNotes ?? records?.medical_notes;
        if (!source) return;
        form.notes = source.notes ?? null;
    },
    { immediate: true },
);

onMounted(async () => {
    await getData();
    await getDataById();
    await getAttachment();
    await getMedicineData();
});
</script>
