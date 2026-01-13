<template>
    <div class="">
        <div class="text-sm text-gray-500">
            {{ props.label }}
        </div>
        <div v-if="props.kind == 'text' || props.kind == 'number'">
            <input
                v-if="!props.loading"
                v-model="internalValue"
                :type="props.kind"
                :placeholder="placeholder"
                :disabled="disabled"
                :loading="props.loading"
                :class="props.class"
                class="custom-input w-full focus:outline-none"
            />
            <Skeleton v-else class="custom-input h-10" />
        </div>
        <div v-if="props.kind == 'datetime'">
            <div v-if="!props.loading">
                <VueDatePicker
                    v-if="props.kindDate == 'only-date'"
                    :formats="{ input: 'dd MMM yyyy' }"
                    :time-config="{ enableTimePicker: false }"
                    v-model="internalValue"
                    class="datepicker w-full focus:outline-none"
                ></VueDatePicker>
                <VueDatePicker
                    v-else-if="props.kindDate == 'only-year'"
                    :time-config="{ enableTimePicker: false }"
                    year-picker
                    v-model="internalValue"
                    class="datepicker w-full focus:outline-none"
                ></VueDatePicker>
                <VueDatePicker
                    v-else
                    year-picker
                    v-model="internalValue"
                    class="datepicker w-full focus:outline-none"
                ></VueDatePicker>
            </div>
            <Skeleton v-else class="custom-input h-10" />
        </div>
        <div v-if="kind == 'text-area'">
            <textarea
                v-if="!props.loading"
                v-model="internalValue"
                class="custom-input col-span-2 w-full focus:outline-none"
                :placeholder="placeholder"
            />
            <Skeleton v-else class="custom-input h-15" />
        </div>
    </div>
</template>

<script lang="ts" setup>
import { VueDatePicker } from '@vuepic/vue-datepicker';
import { ref, watch } from 'vue';
import { Skeleton } from '../ui/skeleton';

const props = defineProps({
    label: { type: String, default: '' },
    class: { type: String, default: '' },
    kind: { type: String, default: 'text' },
    kindDate: { type: String, default: 'datetime-local' },
    modelValue: { type: [String, Number, null], default: '' },
    placeholder: { type: String, default: 'type here..' },
    disabled: { type: Boolean, default: false },
    loading: { type: Boolean, default: false },
});

const emit = defineEmits(['update:modelValue']);

const internalValue = ref(props.modelValue);

watch(
    () => props.modelValue,
    (val) => {
        internalValue.value = val;
    },
);

watch(internalValue, (val) => {
    emit('update:modelValue', val);
});
</script>

<style scoped>
.custom-input {
    padding: 0.5rem;
    border: 1px solid #ccc;
    border-radius: 0.25rem;
}
.datepicker {
    border-radius: 0.25rem;
}
</style>
