<template>
    <Head title="Invoices" />
    <GuestLayout>
        <div class="invoices">
            <div class="flex justify-between items-center p-3">
                <h1 class="text-2xl">Invoices</h1>
                <div>
                    <Button icon="pi pi-plus" label="New" rounded/>
                    <ChooseColumns :columns="columns" v-model="selectedColumns" @apply="updateColumns" rounded/>
                </div>
            </div>
            <DataTable :value="invoices" paginator :rows="5" :rowsPerPageOptions="[5, 10, 20, 50]">
            <Column v-for="col of showColumns" :key="col.field" :field="col.field" :header="col.header" sortable></Column>
            </DataTable>
        </div>
    </GuestLayout>
</template>
<script setup>
import ChooseColumns from '@/Components/ChooseColumns.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head } from '@inertiajs/vue3';
import { DataTable, Column, Button, Popover } from 'primevue';
import { ref } from "vue";

defineProps({
    invoices: {
        type: Array,
    },
});
const columns = [
    { field: 'id', header: 'ID' },
    { field: 'invoice_no', header: 'Invoice No' },
    { field: 'agreement_no', header: 'Agreement No' },
    { field: 'quotation_no', header: 'Quotation No.' },
    { field: 'customer_id', header: 'Customer ID' },
    { field: 'address', header: 'Address' },
    { field: 'phone', header: 'Phone' },
    { field: 'start_date', header: 'Start Date' },
    { field: 'end_date', header: 'End Date' },
    { field: 'sub_total', header: 'Sub total' },
    { field: 'status', header: 'Status' },
];
const selectedColumns = ref(columns);
const showColumns = ref(columns);
const updateColumns = (columns) => {
    showColumns.value = selectedColumns.value;
}
</script>