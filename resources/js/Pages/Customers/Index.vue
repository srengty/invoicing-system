<template>
    <Head title="Customers/Organization Name" />
    <GuestLayout>
        <div class="customers">
            <div class="flex justify-between items-center p-3">
                <h1 class="text-2xl">Customers/Organization Name</h1>
                <div>
                    <!-- Correcting Link for routing -->
                    <Link :href="route('customers.create')">
                        <Button icon="pi pi-plus" label="New" rounded />
                    </Link>
                    <ChooseColumns :columns="columns" v-model="selectedColumns" @apply="updateColumns" rounded />
                </div>
            </div>
            <DataTable :value="customers" paginator :rows="5" :rowsPerPageOptions="[5, 10, 20, 50]">
                <Column v-for="col of showColumns" :key="col.field" :field="col.field" :header="col.header" sortable></Column>
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
    customers: {
        type: Array,
    },
});
const columns = [
    { field: 'id', header: 'ID' },
    { field: 'name', header: 'Name' },
    { field: 'email', header: 'Email' },
    { field: 'address', header: 'Address' },
    { field: 'phone', header: 'Phone' },
    { field: 'telegram', header: 'Telegram' },
    { field: 'website', header: 'Website' },
    { field: 'bank_name', header: 'Bank Name' },
    { field: 'bank_address', header: 'Bank Address' },
    { field: 'bank_account_name', header: 'Bank Account Name' },
    { field: 'bank_accout_number', header: 'Bank Account Number' },
    { field: 'bank_swif', header: 'Bank Swif' },
];
const selectedColumns = ref(columns);
const showColumns = ref(columns);
const updateColumns = () => {
    showColumns.value = selectedColumns.value;
};
</script>
