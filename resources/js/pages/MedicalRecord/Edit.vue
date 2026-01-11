<script lang="ts" setup>
import Form from './Form.vue';

defineProps({
    record: Object,
});
</script>

<template>
    <Form :record="record" />
</template>
