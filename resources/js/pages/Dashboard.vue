<script setup lang="ts">
import ADashboard from '@/components/app/ADashboard.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, reactive } from 'vue';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const data = reactive({
    loading: false,
    records: [] as any,
});

async function getData() {
    data.loading = true;

    try {
        const res = await axios.post('/api/rekam-medis/getdata');
        data.records = res.data;
    } catch (error) {
        console.log(error);
    } finally {
        data.loading = false;
    }
}

onMounted(async () => {
    await getData();
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <ADashboard
                        :loading="data.loading"
                        :month-patients="data.records?.monthpatiens"
                        :paid-patients="data.records?.paid_patiens"
                        :unpaid-patients="data.records?.unpaid_patients"
                        :today-patients="data.records?.monthpatiens"
                    >
                    </ADashboard>
                </div>
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <PlaceholderPattern />
                </div>
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <PlaceholderPattern />
                </div>
            </div>
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border"
            >
                <PlaceholderPattern />
            </div>
        </div>
    </AppLayout>
</template>
