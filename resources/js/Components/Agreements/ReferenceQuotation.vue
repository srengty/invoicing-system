<template>
    <div class="border border-gray-200 rounded-lg p-4">
        <div class="grid grid-cols-2 gap-1">
            <span>Quotation No.</span>
            <input />
            <span>Agreement No.</span>
            <input />
            <span>Date</span>
            <input type="date" pattern="dd/mm/yyyy" />
            <span>Customer</span>
            <input />
            <span>Address</span>
            <input />
            <span>Agreement doc</span>
            <input type="file" @input="form.avatar = $event.target.files[0]" />
            <span :class="[{ hidden: !form.progress }]">Uploading</span>
            <progress
                v-if="form.progress"
                :value="form.progress.percentage"
                max="100"
            >
                {{ form.progress.percentage }}%
            </progress>
        </div>
    </div>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";
const toast = useToast();

const form = useForm({
    avatar: null,
    progress: null,
});
</script>

<style lang="scss" scoped></style>
