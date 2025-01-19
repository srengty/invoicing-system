<template>
    <Head title="Quotations" />
    <GuestLayout>
        <div class="quotations">
            <div class="flex justify-between items-center p-4">
                <h1 class="text-2xl">Quotations list (Staff)</h1>
            </div>
            <div class="flex justify-between items-center p-4">
                Quotations are proposed to customer to bid for a project.
            </div>
            <div class="flex justify-end p-4 gap-4">
                <Button label="Issue Quotation" rounded/>
                <Button label="Issue Invoice" rounded/>
                <Button label="Record Agreement" rounded/>
            </div>
            <TableData>
            </TableData>
            <!-- <DataTable :value="agreements" paginator :rows="5" :rowsPerPageOptions="[5, 10, 20, 50]">
            <Column v-for="col of showColumns" :key="col.field" :field="col.field" :header="col.header" sortable></Column>
            </DataTable> -->
        </div>
    </GuestLayout>
</template>

<script setup>
import ChooseColumns from '@/Components/ChooseColumns.vue';
import TableData from '@/Components/TableData.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head } from '@inertiajs/vue3';
import { DataTable, Column, Button, Popover } from 'primevue';
import { ref } from "vue";

defineProps({
    quotations: {
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