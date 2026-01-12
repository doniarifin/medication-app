<script lang="ts" setup>
import Form from './Form.vue';

const props = defineProps({
    medicalRecord: Object,
    vitalSign: Object,
    medAttachment: Object,
});
</script>

<template>
    <Form
        :medical-record="props.medicalRecord"
        :vital-sign="props.vitalSign"
        :med-attachment="props.medAttachment"
    />
</template>
