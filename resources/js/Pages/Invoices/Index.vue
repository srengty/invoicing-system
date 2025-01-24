<template>
    <Head title="Invoices" />
    <GuestLayout>
        <div class="invoices">
            <div class="flex justify-between items-center p-3">
                <h1 class="text-2xl">Invoices</h1>
                <div>
                    <Link :href="route('invoices.create')">
                        <Button icon="pi pi-plus" label="New" rounded />
                    </Link>
                    <ChooseColumns :columns="columns" v-model="selectedColumns" @apply="updateColumns" rounded />
                </div>
            </div>
            <DataTable :value="invoices" paginator :rows="5" :rowsPerPageOptions="[5, 10, 20, 50]">
                <!-- Dynamic columns -->
                <Column
                    v-for="col of showColumns"
                    :key="col.field"
                    :field="col.field"
                    :header="col.header"
                    sortable>
                </Column>

                <!-- Centered Action column -->
                <Column 
                    header="Action" 
                    headerStyle="text-align: center" 
                    bodyStyle="text-align: center">
                    <template #body="{ data }">
                        <div class="flex gap-2 justify-center">
                            <Button
                                icon="pi pi-print"
                                class="p-button-secondary p-button-text"
                                @click="data.actions.print"
                                label="Print" />
                            <Button
                                icon="pi pi-pencil"
                                class="p-button-warning p-button-text"
                                @click="data.actions.edit"
                                label="Edit" />
                            <Button
                                icon="pi pi-trash"
                                class="p-button-danger p-button-text"
                                @click="data.actions.delete"
                                label="Delete" />
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>
    </GuestLayout>
</template>


<script setup>
import ChooseColumns from '@/Components/ChooseColumns.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { DataTable, Column, Button } from 'primevue';
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
    { field: 'start_date', header: 'Start Date' },
    { field: 'end_date', header: 'End Date' },
    { field: 'sub_total', header: 'Sub total' },
    { field: 'status', header: 'Status' },
];
const selectedColumns = ref(columns);
const showColumns = ref(columns);
const updateColumns = () => {
    showColumns.value = selectedColumns.value;
};

// Static invoice data with actions
const invoices = ref([
    {
        id: 1,
        invoice_no: 'INV001',
        agreement_no: 'AGR001',
        quotation_no: 'QTN001',
        customer_id: 'CUST001',
        start_date: '2025-01-01',
        end_date: '2025-01-10',
        sub_total: 1500,
        status: 'Paid',
        actions: {
            print: () => console.log('Print invoice: INV001'),
            edit: () => console.log('Edit invoice: INV001'),
            delete: () => console.log('Delete invoice: INV001'),
        },
    },
    {
        id: 2,
        invoice_no: 'INV002',
        agreement_no: 'AGR002',
        quotation_no: 'QTN002',
        customer_id: 'CUST002',
        start_date: '2025-01-05',
        end_date: '2025-01-15',
        sub_total: 2000,
        status: 'Unpaid',
        actions: {
            print: () => console.log('Print invoice: INV002'),
            edit: () => console.log('Edit invoice: INV002'),
            delete: () => console.log('Delete invoice: INV002'),
        },
    },
    {
        id: 3,
        invoice_no: 'INV003',
        agreement_no: 'AGR003',
        quotation_no: 'QTN003',
        customer_id: 'CUST003',
        start_date: '2025-01-10',
        end_date: '2025-01-20',
        sub_total: 2500,
        status: 'Paid',
        actions: {
            print: () => console.log('Print invoice: INV003'),
            edit: () => console.log('Edit invoice: INV003'),
            delete: () => console.log('Delete invoice: INV003'),
        },
    },
    {
        id: 4,
        invoice_no: 'INV004',
        agreement_no: 'AGR004',
        quotation_no: 'QTN004',
        customer_id: 'CUST004',
        start_date: '2025-01-15',
        end_date: '2025-01-25',
        sub_total: 3000,
        status: 'Overdue',
        actions: {
            print: () => console.log('Print invoice: INV004'),
            edit: () => console.log('Edit invoice: INV004'),
            delete: () => console.log('Delete invoice: INV004'),
        },
    },
    {
        id: 5,
        invoice_no: 'INV005',
        agreement_no: 'AGR005',
        quotation_no: 'QTN005',
        customer_id: 'CUST005',
        start_date: '2025-01-20',
        end_date: '2025-01-30',
        sub_total: 3500,
        status: 'Unpaid',
        actions: {
            print: () => console.log('Print invoice: INV005'),
            edit: () => console.log('Edit invoice: INV005'),
            delete: () => console.log('Delete invoice: INV005'),
        },
    },
]);
</script>