<template>
    <Head title="Agreements" />
    <GuestLayout>
        <div class="agreements">
            <div class="flex justify-between items-center p-3">
                <h1 class="text-2xl">Agreements</h1>
                <div>
                    <Button icon="pi pi-plus" label="New"/>
                    <ChooseColumns :columns="columns" v-model="selectedColumns" @apply="updateColumns"/>
                </div>
            </div>
            <DataTable :value="agreements" paginator :rows="5" :rowsPerPageOptions="[5, 10, 20, 50]">
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
    agreements: {
        type: Array,
    },
});
const columns = [
    { field: 'id', header: 'ID' },
    { field: 'agreement_no', header: 'Agreement No' },
    { field: 'agreement_ref_no', header: 'Agreement Ref No' },
    { field: 'agreement_date', header: 'Agreement Date' },
    { field: 'address', header: 'Address' },
    { field: 'agreement_doc', header: 'Agreement Doc' },
    { field: 'start_date', header: 'Start Date' },
    { field: 'end_date', header: 'End Date' },
    { field: 'amount_no_tax', header: 'Amount' },
    { field: 'tax', header: 'Tax' },
    { field: 'status', header: 'Status' },
    { field: 'quotation_no', header: 'Quotation No.' },
    { field: 'customer_id', header: 'Customer ID' },
];
const selectedColumns = ref(columns);
const showColumns = ref(columns);
const updateColumns = (columns) => {
    showColumns.value = selectedColumns.value;
}
</script>