<template>
    <BaseFormSection :data="formSections">
        <template #field_patient_name="{ field }">
            <FInput
                :label="field.label"
                :loading="data.loading"
                :disabled="true"
                v-model="data.records.patient_name"
            >
            </FInput>
        </template>
        <template #field_examined_at="{ field }">
            <FInput
                :label="field.label"
                :loading="data.loading"
                :kind="field.type"
                :kind-date="'only-date'"
                :disabled="true"
                v-model="data.records.examined_at"
            >
            </FInput>
        </template>
        <template #field_resep_dokter>
            <div class="rounded bg-gray-100 p-2">
                <div
                    v-for="(value, idx) in data.records.resep_dokter
                        ?.resep_dokter"
                    :key="idx"
                    class="mb-2"
                >
                    <div class="flex items-start gap-2">
                        <span class="w-5 text-right font-medium">
                            {{ Number(idx) + 1 }}.
                        </span>

                        <div>
                            <div>{{ value.name }}</div>
                            <div class="pl-4 text-sm text-gray-600">
                                ket: {{ value.description }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <template #field_total_harga="{}">
            <FInput
                :loading="data.loading"
                :disabled="true"
                v-model="data.rp_total_price"
            >
            </FInput>
        </template>

        <template #field_is_paid="{ field }">
            {{ field.label }}
            <div v-if="!data.records.is_paid">
                <ASelectItem
                    :items="[
                        { id: 1, label: 'Yes' },
                        { id: 0, label: 'No' },
                    ]"
                    v-model="data.is_paid"
                >
                </ASelectItem>
            </div>
            <div v-else>
                <FInput
                    :loading="data.loading"
                    :disabled="true"
                    v-model="data.is_paid"
                >
                </FInput>
            </div>
            <div class="mb-50"></div>
        </template>
    </BaseFormSection>
</template>

<script setup lang="ts">
import ASelectItem from '@/components/app/ASelectItem.vue';
import BaseFormSection from '@/components/form/BaseFormSection.vue';
import FInput from '@/components/form/FInput.vue';

import { showError, showInfo, showSuccess } from '@/lib/toast';
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
    selectedId: {
        type: String,
        default: '',
    },
});

const formSections = [
    {
        title: 'Data Pasien',
        grid: 'grid-cols-1 md:grid-cols-2',
        fields: [
            {
                label: 'Name',
                model: 'patient_name',
                type: 'text',
            },
            {
                label: 'Examined At',
                model: 'examined_at',
                type: 'datetime',
            },
        ],
    },
    {
        title: 'Catatan Dokter',
        grid: 'grid-cols-1 md:grid-cols-1',
        fields: [
            {
                label: 'Notes',
                model: 'note',
                type: 'text-area',
            },
            {
                label: 'File',
                model: 'attachment',
                type: 'text',
            },
        ],
    },
    {
        title: 'Resep',
        grid: 'grid-cols-1',
        fields: [
            {
                label: 'Resep Dokter',
                model: 'resep_dokter',
            },
        ],
    },
    {
        title: 'Total Harga',
        grid: 'grid-cols-1',
        fields: [
            {
                label: 'Total Harga',
                model: 'total_harga',
            },
        ],
    },

    {
        title: 'Pembayaran',
        grid: 'grid-cols-1 md:grid-cols-1',
        fields: [
            {
                label: 'Apakah sudah dibayar?',
                model: 'is_paid',
                type: 'text-text',
            },
        ],
    },
];

const emit = defineEmits(['closeModal']);

const medicines = ref([
    {
        id: null,
        name: '',
        description: '',
    },
]);

const data = reactive({
    loading: false,
    records: [] as any,
    filter: {} as any,
    fileName: '',
    attachment: {} as any,
    resep_dokter: medicines,
    is_paid: [] as any,
    medicines: [] as any,
    list_medId: [] as any,
    rp_total_price: null as any,
    total_price: null as any,
});

async function submit() {
    if (data.loading) return;
    data.loading = true;
    const is_paid = data.is_paid.id;
    const total_price = data.total_price;

    try {
        let response = {} as any;
        response = await axios.post('/api/resep/update', {
            id: props.selectedId,
            is_paid: is_paid,
            total_price: total_price,
        });

        showSuccess(response.data.message ?? 'Data has been saved');
        emit('closeModal');
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

async function getData() {
    if (data.loading) {
        return;
    }
    data.loading = true;

    try {
        const res = await axios.post('/api/resep/getdata', {
            id: props.selectedId,
        });

        data.records = res.data[0];

        data.is_paid = {
            id: data.records?.is_paid ? 1 : 0,
            label: data.records?.is_paid ? 'Yes' : 'No',
        };

        if (data.records?.is_paid) {
            data.is_paid = data.records?.is_paid ? 'Yes' : 'No';
        }

        if (data.records?.resep_dokter) {
            getListMedId(data.records.resep_dokter?.resep_dokter);
        }
        data.loading = false;
    } catch (error: any) {
        console.log(error);
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

function getListMedId(resepDokter: any[]) {
    console.log('resep', resepDokter);
    data.list_medId = resepDokter.map((el) => el.id);
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

    // console.log(res);
    data.attachment = res?.data;
    data.fileName = data.attachment?.original_name;
    // console.log(data.attachment);
    data.loading = false;
}

async function getMedicineData() {
    data.loading = true;

    const listmedId = data.list_medId;

    try {
        const today = new Date().toISOString().slice(0, 10);
        const res = await axios.post('/api/medicines/getprice', {
            // params: {
            medicine_id: listmedId,
            start_date: today,
            end_date: today,
            // },
        });

        data.medicines = res.data;
        data.total_price = getTotalPriceInt(data.medicines);
        data.rp_total_price = getTotalPrice(data.medicines);
    } catch (error) {
        showError(error);
    } finally {
        data.loading = false;
    }
}

function getTotalPriceInt(medicines: any[]): string {
    const total = medicines.reduce(
        (sum, item) => sum + Number(item.unit_price || 0),
        0,
    );
    return total;
}

function getTotalPrice(medicines: any[]): string {
    const total = medicines.reduce(
        (sum, item) => sum + Number(item.unit_price || 0),
        0,
    );

    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(total);
}

async function exportPdf(id: any) {
    if (data.loading) return;
    data.loading = true;

    try {
        const res = await axios.get(`/api/resep/${id}/pdf`, {
            responseType: 'blob',
        });

        const url = window.URL.createObjectURL(new Blob([res.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `invoice-${id}.pdf`);
        document.body.appendChild(link);
        link.click();
        link.remove();
        showInfo('export pdf success');
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

defineExpose({
    submit,
    getData,
    exportPdf,
});

onMounted(async () => {
    await getData();
    await getAttachment();
    await getMedicineData();
});
</script>
