<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { Skeleton } from '../ui/skeleton';

export interface SelectItem {
    id: string | number;
    [props.labelName]: string;
}

const props = defineProps({
    items: { type: Object, default: () => {} },
    modelValue: { type: Object, default: () => {} },
    placeholder: { type: String, default: 'Select item' },
    labelName: { type: String, default: 'label' },
    searchable: { type: Boolean, default: false },
    loading: { type: Boolean, default: false },
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: SelectItem | null): void;
}>();

const open = ref(false);
const search = ref('');
const wrapper = ref<HTMLElement | null>(null);

const filteredItems = computed(() => {
    if (!props.searchable || !search.value) return props.items;

    const key = labelKey.value;
    const keyword = search.value.toLowerCase();

    return props.items.filter((item: any) => {
        const value = String(item[key] ?? '').toLowerCase();
        return value.includes(keyword);
    });
});

const labelKey = computed(() => props.labelName ?? 'label');

const selectedLabel = computed(() => {
    if (!props.modelValue) {
        return props.placeholder ?? 'Select item';
    }

    const key = labelKey.value;

    return String(props.modelValue[key] ?? '');
});

function toggle() {
    open.value = !open.value;
}

function select(item: SelectItem) {
    emit('update:modelValue', item);
    open.value = false;
    search.value = '';
}

function handleClickOutside(e: MouseEvent) {
    if (wrapper.value && !wrapper.value.contains(e.target as Node)) {
        open.value = false;
    }
}

function clear() {
    emit('update:modelValue', null);
    search.value = '';
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
    <div v-if="!loading" ref="wrapper" class="relative w-full">
        <!-- trigger -->
        <button
            type="button"
            @click="toggle"
            class="custom-input flex w-full cursor-pointer items-center justify-between rounded border px-3 py-2 text-left text-sm"
        >
            <span :class="!modelValue ? 'text-gray-500' : ''">
                {{ selectedLabel }}
            </span>

            <div class="flex items-center gap-2 text-gray-500">
                <button
                    v-if="modelValue"
                    type="button"
                    @click.stop="clear"
                    class="cursor-pointer text-gray-500 hover:text-red-400"
                >
                    ✕
                </button>
                <!-- arrow -->
                <svg
                    class="h-4 w-4 transition-transform"
                    :class="{ 'rotate-180': open }"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M19 9l-7 7-7-7"
                    />
                </svg>
            </div>
        </button>

        <!-- dropwodn -->
        <div
            v-if="open"
            class="absolute z-50 mt-1 w-full cursor-pointer rounded border bg-white text-gray-500 shadow"
        >
            <!-- search -->
            <div v-if="searchable" class="p-2">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search..."
                    class="w-full rounded border px-2 py-1 text-sm"
                />
            </div>

            <!-- items -->
            <ul class="max-h-60 overflow-auto">
                <li
                    v-for="item in filteredItems"
                    :key="item.id"
                    @click="select(item)"
                    class="cursor-pointer px-3 py-2 text-sm hover:bg-gray-100"
                    :class="{
                        'bg-gray-100 font-medium': modelValue?.id === item.id,
                    }"
                >
                    <slot name="item" :item="item">
                        {{ item.label }}
                    </slot>
                </li>

                <li
                    v-if="filteredItems.length === 0"
                    class="px-3 py-2 text-sm text-gray-400"
                >
                    Not found
                </li>
            </ul>
        </div>
    </div>
    <Skeleton v-else class="custom-input h-10" />
</template>

<style scoped>
.custom-input {
    padding: 0.5rem;
    border: 1px solid #ccc;
    border-radius: 0.25rem;
}
</style>
