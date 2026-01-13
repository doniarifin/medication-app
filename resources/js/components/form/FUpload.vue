<template>
    <div class="text-gray-500">
        <div class="mb-1 text-sm text-gray-500">
            {{ props.label }}
        </div>

        <input
            ref="fileInput"
            type="file"
            class="hidden"
            :accept="props.accept"
            :disabled="props.disabled"
            @change="onChange"
        />

        <div class="flex items-center gap-2">
            <div
                v-if="!props.loading"
                class="flex cursor-pointer items-center gap-2"
                variant="outline"
                @click="openFile"
            >
                <div class="flex text-gray-500">
                    <div
                        class="custom-input flex w-full items-center gap-2 hover:bg-gray-100 focus:outline-none"
                    >
                        <Icon v-if="!fileName" name="Paperclip"></Icon>
                        {{ fileName || props.placeholder }}
                        <Icon v-if="fileName" name="file"></Icon>
                    </div>
                </div>
            </div>
            <div v-if="fileName && !props.loading">
                <AButton
                    variant="ghost"
                    class="cursor-pointer"
                    @click="clearFile"
                >
                    <Icon name="x" strokeWidth="4"></Icon>
                </AButton>
            </div>
            <Skeleton v-if="props.loading" class="custom-input h-10 w-30" />
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import Icon from '../Icon.vue';
import AButton from '../app/AButton.vue';
import { Skeleton } from '../ui/skeleton';

const props = defineProps({
    label: { type: String, default: '' },
    class: { type: String, default: '' },
    accept: { type: String, default: '' },
    fileName: { type: String, default: '' },
    modelValue: { type: null, default: null },
    placeholder: { type: String, default: 'Choose File' },
    disabled: { type: Boolean, default: false },
    loading: { type: Boolean, default: false },
    isDeleted: { type: Boolean, default: false },
});

// const emit = defineEmits<{
//     (e: 'update:modelValue', file: File | null): void;
// }>();

const emit = defineEmits({
    'update:modelValue': null,
    clearFile: null,
    onChange: null,
});

const fileInput = ref<HTMLInputElement | null>(null);
const fileName = ref(props.fileName);
const selectedFile = ref<File | null>(null);
const isDeleted = ref(props.isDeleted);

function openFile() {
    if (!fileInput.value) return;
    selectedFile.value = null;
    console.log('filename open', fileName.value);
    fileInput.value.click();
    emit('onChange', fileName.value == '');
}

function onChange(e: Event) {
    const input = e.target as HTMLInputElement;
    const file = input.files?.[0] ?? null;

    fileName.value = file?.name ?? '';
    isDeleted.value = fileName.value == '';
    console.log('filename', fileName.value);
    selectedFile.value = file;
    console.log(file);

    emit('update:modelValue', file);
    emit('onChange', fileName.value == '');
}

function clearFile() {
    fileName.value = '';
    isDeleted.value = fileName.value == '';
    emit('update:modelValue', null);
    emit('clearFile', isDeleted.value);
}

watch(
    () => props.fileName,
    (val) => {
        fileName.value = val;
    },
);
</script>

<style scoped>
.custom-input {
    padding: 0.5rem;
    border: 1px solid #ccc;
    border-radius: 0.25rem;
}
</style>
