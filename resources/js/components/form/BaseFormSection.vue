<script setup lang="ts">
import { reactive, watch } from 'vue';
import FInput from './FInput.vue';

// interface FormField {
//     label: string;
//     model: string;
//     type?: string;
//     placeholder?: string;
// }

const props = defineProps({
    title: { type: String, default: '' },
    description: { type: String, default: '' },
    data: { type: Object, default: () => {} },
    modelValue: { type: String, default: '' },
    placeholder: { type: String, default: '' },
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: Record<string, any>): void;
}>();

const localForm = reactive<Record<string, any>>({});

watch(
    localForm,
    (newVal) => {
        emit('update:modelValue', { ...newVal });
    },
    { deep: true },
);
</script>

<template>
    <div v-for="(section, sIndex) in props.data" :key="sIndex">
        <section class="space-y-3">
            <!-- Header -->
            <div v-if="section.title">
                <h3 class="text-md font-medium text-gray-700">
                    {{ section.title }}
                </h3>
                <p v-if="section.description" class="text-sm text-gray-500">
                    {{ section.description }}
                </p>
            </div>

            <!-- Grid -->
            <div class="grid gap-4" :class="section.grid">
                <div
                    v-for="(field, fIndex) in section.fields"
                    :key="fIndex"
                    class="flex flex-col"
                >
                    <slot
                        :name="`field_${field.model}`"
                        :section="section"
                        :field="field"
                        :model="localForm"
                    >
                        <f-input
                            :kind="field.type"
                            :label="field.label"
                            v-model="localForm[field.model]"
                            :placeholder="field.placeholder"
                        />
                    </slot>
                </div>
            </div>
        </section>
    </div>
</template>
