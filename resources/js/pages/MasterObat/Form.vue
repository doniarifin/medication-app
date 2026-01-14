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
                label="Tanggal Pemeriksaan"
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
                kind="number"
            />
            <FInput
                label="Berat Badan"
                :loading="data.loading"
                v-model="form.weight"
                kind="number"
            />
            <FInput
                label="Systole"
                v-model="form.systole"
                kind="number"
                :loading="data.loading"
            />
            <FInput
                label="Diastole"
                v-model="form.diastole"
                kind="number"
                :loading="data.loading"
            />
            <FInput
                label="Heart Rate"
                v-model="form.heart_rate"
                kind="number"
                :loading="data.loading"
            />
            <FInput
                label="Respiration Rate"
                v-model="form.respiration_rate"
                kind="number"
                :loading="data.loading"
            />
            <FInput
                label="Body Temperature"
                v-model="form.body_temperature"
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
                        <!-- <FInput
                            v-model="med.name"
                            :loading="data.loading"
                            kind="text"
                        ></FInput> -->
                        <!-- {{ form.resep_dokter }} -->
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
                        <!-- {{ form.resep_dokter[index] }} -->
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
                                ></FInput>
                            </div>
                            <div>
                                <AButton
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
                v-model="form.notes"
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
                :loading="data.loading"
            />
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
import { showError, showSuccess } from '@/lib/toast';
import { router, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, reactive, ref } from 'vue';

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
});

const form = useForm({
    patient_name: props.medicalRecord?.patient_name ?? null,
    examined_at: props.medicalRecord?.examined_at
        ? convertStrDate(props.medicalRecord?.examined_at)
        : null,
    medicine_price: props.medicalRecord?.medicine_price ?? 0,
    height: props.vitalSign?.height
        ? convertInt(props.vitalSign?.height)
        : null,
    weight: props.vitalSign?.weight
        ? convertInt(props.vitalSign?.weight)
        : null,
    blood_pressure: props.vitalSign?.blood_pressure ?? null,
    systole: props.vitalSign?.systole ?? null,
    diastole: props.vitalSign?.diastole ?? null,
    heart_rate: props.vitalSign?.heart_rate ?? null,
    respiration_rate: props.vitalSign?.respiration_rate ?? null,
    body_temperature: props.vitalSign?.body_temperature ?? null,
    resep_dokter: props.resepDokter?.resep_dokter ?? ([] as any),
    notes: props.medNotes?.notes ?? null,
    file: null,
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
    records: [],
    filter: {} as any,
    fileName: '',
    attachment: {} as any,
    resep_dokter: medicines,
});

function mapSpecificKey<T extends { id: any } & Record<string, any>>(
    data: T[],
    keys: (keyof T)[],
): Partial<T>[] {
    return data
        .filter((item) => item.id !== null)
        .map((item) => {
            const result: Partial<T> = {};

            keys.forEach((key) => {
                result[key] = item[key];
            });

            return result;
        });
}

const listMedicines = ref<SelectItem[]>([
    { id: '1', name: 'test' },
    { id: '2', name: 'test2' },
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
    console.log(form.file);

    // form.body_temperature = Number(form.body_temperature);

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

        console.log(response);

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
    if (props.form == 'edit') {
        data.resep_dokter = form.resep_dokter;
        if (!data.resep_dokter.length) {
            addMedicine();
        }
    }
    data.loading = false;
}

onMounted(async () => {
    await getData();
    await getAttachment();
    await getMedicineData();
});
</script>
