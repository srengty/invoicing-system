<template>
    <Head title="Customers/Organization Name" />
    <GuestLayout>
        <div class="customers">
            <div class="flex justify-between items-center p-3">
                <h1 class="text-2xl">Customers/Organization Name</h1>
                <div>
                    <Link :href="route('customers.create')">
                        <Button icon="pi pi-plus" label="New" rounded />
                    </Link>
                    <ChooseColumns :columns="columns" v-model="selectedColumns" @apply="updateColumns" rounded />
                </div>
            </div>
            <DataTable :value="customers" paginator :rows="5" :rowsPerPageOptions="[5, 10, 20, 50]">
                <Column v-for="col of showColumns" :key="col.field" :field="col.field" :header="col.header" sortable></Column>
                <Column header="Actions">
                    <template #body="slotProps">
                        <div class="flex gap-2">
                            <Button
                                icon="pi pi-eye"
                                class="p-button-info"
                                label="View"
                                @click="viewCustomer(slotProps.data.id)"
                                rounded
                            />
                            <Button
                                icon="pi pi-pencil"
                                class="p-button-warning"
                                label="Edit"
                                @click="editCustomer(slotProps.data.id)"
                                rounded
                            />
                            <Button
                                icon="pi pi-trash"
                                class="p-button-danger"
                                label="Delete"
                                @click="deleteCustomer(slotProps.data.id)"
                                rounded
                            />
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>
    </GuestLayout>
</template>

<script>
import { ref } from "vue";
import { Head, Link } from '@inertiajs/vue3';
import { DataTable, Column, Button } from 'primevue';
import ChooseColumns from '@/Components/ChooseColumns.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';

export default {
    components: {
        Head,
        Link,
        DataTable,
        Column,
        Button,
        ChooseColumns,
        GuestLayout
    },
    props: {
        customers: Array,
    },
    data() {
        return {
            columns: [
                { field: 'id', header: 'ID' },
                { field: 'name', header: 'Name' },
                { field: 'code', header: 'Code' },
                { field: 'email', header: 'Email' },
                { field: 'address', header: 'Address' },
                { field: 'phone_number', header: 'Phone' },
                { field: 'telegram_number', header: 'Telegram' },
                { field: 'website', header: 'Website' },
                { field: 'bank_name', header: 'Bank Name' },
                { field: 'bank_address', header: 'Bank Address' },
                { field: 'bank_account_name', header: 'Bank Account Name' },
                { field: 'bank_account_number', header: 'Bank Account Number' },
                { field: 'bank_swift', header: 'Bank Swif' },
            ],
            selectedColumns: [],
            showColumns: []
        };
    },
    methods: {
        updateColumns() {
            this.showColumns = this.selectedColumns;
        },
        createCustomer() {
            this.$inertia.visit(route('customers.create'));
        },
        editCustomer(id) {
            // Corrected route for the edit form
            this.$inertia.visit(route('customers.edit', id)); 
        },
        deleteCustomer(id) {
            if (confirm('Are you sure you want to delete this customer?')) {
                this.$inertia.delete(route('customers.destroy', id), {
                    onSuccess: () => {
                        // Optionally, handle success (e.g., refresh the customer list)
                        console.log('Customer deleted!');
                    },
                    onError: (error) => {
                        console.error('Error deleting customer:', error);
                    }
                });
            }
        },
        viewCustomer(id) {
            this.$inertia.visit(route('customers.show', id));
        }
    },
    created() {
        // Set the default columns to show
        this.showColumns = this.columns;
    }
};
</script>
