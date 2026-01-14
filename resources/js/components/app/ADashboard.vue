<template>
    <div
        v-if="!props.loading"
        class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4"
    >
        <div class="rounded-lg bg-blue-500 p-4 text-white shadow">
            <div class="text-sm">Pasien Hari Ini</div>
            <div class="text-2xl font-bold">{{ props.todayPatients }}</div>
        </div>

        <div class="rounded-lg bg-green-500 p-4 text-white shadow">
            <div class="text-sm">Total Pasien Bulan Ini</div>
            <div class="text-2xl font-bold">{{ props.monthPatients }}</div>
        </div>

        <div class="rounded-lg bg-yellow-500 p-4 text-white shadow">
            <div class="text-sm">Sudah Bayar Resep</div>
            <div class="text-2xl font-bold">{{ props.paidPatients }}</div>
        </div>

        <div class="rounded-lg bg-red-500 p-4 text-white shadow">
            <div class="text-sm">Belum Bayar Resep</div>
            <div class="text-2xl font-bold">{{ props.unpaidPatients }}</div>
        </div>
        <slot name="card"> </slot>
    </div>
    <div v-else>
        <Spinner class="h-10 w-10" />
    </div>
</template>

<script lang="ts" setup>
import Spinner from '../ui/spinner/Spinner.vue';

const props = defineProps({
    todayPatients: { type: Number, default: 0 },
    monthPatients: { type: Number, default: 0 },
    paidPatients: { type: Number, default: 0 },
    unpaidPatients: { type: Number, default: 0 },
    loading: { type: Boolean, default: false },
});
</script>

<style scoped></style>
