<script lang="ts" setup>
import Form from './Form.vue';

const props = defineProps({
    medicalRecord: Object,
    vitalSign: Object,
    medAttachment: Object,
    resepDokter: Object,
    medicalNotes: Object,
    form: String,
});
</script>

<template>
    <Form
        :medical-record="props.medicalRecord"
        :vital-sign="props.vitalSign"
        :med-attachment="props.medAttachment"
        :med-notes="props.medicalNotes"
        :form="'edit'"
    />
</template>
