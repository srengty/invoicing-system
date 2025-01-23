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
                <div><Link :href="route('quotations.create')">
                    <Button label="Issue Quotation" rounded/>
                </Link></div>
                <Button label="Issue Invoice" rounded/>
                <Button label="Record Agreement" rounded/>
            </div>

            <div>
                <DataTable :value="quotations" paginator :rows="5" :rowsPerPageOptions="[5, 10, 20, 50]" tableStyle="min-width: 50rem">
                    <Column header="View Print-out" style="width: 20%">
                        <template #body="" >
                            <!-- Add button inside the column body template -->
                            <div class="flex gap-4">
                                <Button icon="pi pi-plus" label="View" rounded style="padding-left: 12px;padding-right: 18px;" />
                                <Button icon="pi pi-plus" label="Print out" rounded style="padding-left: 12px;padding-right: 18px;" />
                            </div>
                        </template>
                    </Column>
                    <Column field="id" header="No." style="width: 10%" />
                    <Column field="customer.name" header="Customer/Organization Name" style="width: 25%" />
                    <Column field="grand_total" header="Total" style="width: 10%" />
                    <Column field="status" header="Status" style="width: 15%" />
                    <Column field="customer_status" header="Customer Status" style="width: 20%" />
                </DataTable>
            </div>
        </div>
    </GuestLayout>
</template>

<script setup>
import ChooseColumns from '@/Components/ChooseColumns.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from "vue";
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
defineProps({
    quotations: {
        type: Array,
    },
});
const columns = [
    { field: 'id', header: 'No.' },
    { field: 'quotation_no', header: 'Quotation No.' },
    { field: 'quotation_date', header: 'Quotation Date' },
    { field: 'customer.name', header: 'Customer/Organization Name' },
    { field: 'address', header: 'Address' },
    { field: 'phone_number', header: 'Phone Number' },
    { field: 'terms', header: 'Terms' },
    { field: 'total', header: 'Total' },
    { field: 'tax', header: 'Tax' },
    { field: 'grand_total', header: 'Grand Total' },
    { field: 'status', header: 'Status' },
    { field: 'customer_status', header: 'Customer Status' },
];

const selectedColumns = ref(columns);
const showColumns = ref(columns);
const updateColumns = (columns) => {
    showColumns.value = selectedColumns.value;
}
</script>

