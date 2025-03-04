<template>
    <Head title="Customers/Organization Name" />
    <GuestLayout>
        <BodyLayout>
            <div class="customers">
            <div class="flex justify-between items-center p-3">
                <h1 class="text-xl">Customers/Organization Name</h1>
                <div>
                    <Link :href="route('customers.create')">
                        <Button icon="pi pi-plus" label="New" outlined="" size="small"/>
                    </Link>
                    <ChooseColumns :columns="columns" v-model="selectedColumns" @apply="updateColumns" outlined size="small"/>
                </div>
            </div>
            <DataTable :value="indexedCustomers" paginator :rows="5" :rowsPerPageOptions="[5, 10, 20, 50]" class="text-sm">
                <Column v-for="col of showColumns" :key="col.field" :field="col.field" :header="col.header" sortable></Column>
                <Column header="Actions">
                    <template #body="slotProps">
                        <div class="flex gap-2">
                            <Button
                                icon="pi pi-eye"
                                class="p-button-info"
                                aria-label="View"
                                size="small"
                                @click="viewCustomer(slotProps.data.id)"
                                outlined
                            />
                            <Button
                                icon="pi pi-pencil"
                                class="p-button-warning"
                                aria-label="Edit"
                                size="small"
                                @click="editCustomer(slotProps.data.id)"
                                outlined
                            />
                            <Button
                                icon="pi pi-trash"
                                class="p-button-danger"
                                aria-label="Delete"
                                size="small"
                                @click="deleteCustomer(slotProps.data.id)"
                                outlined
                            />
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>
        </BodyLayout>
    </GuestLayout>
</template>

<script setup>
import { computed, ref } from 'vue'; // Import ref here
import { Head, Link } from '@inertiajs/vue3';
import { DataTable, Column, Button } from 'primevue';
import ChooseColumns from '@/Components/ChooseColumns.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import BodyLayout from '@/Layouts/BodyLayout.vue';

const indexedCustomers = computed(() => {
    return props.customers.map((customer, index) => ({
        ...customer,
        index: index + 1, // Assigns index starting from 1
    }));
});

const props = defineProps({
    customers: Array,
});

const columns = [
    { field: 'id', header: 'ID' },
    { field: 'name', header: 'Name' },
    { field: 'code', header: 'Code' },
    { field: 'email', header: 'Email' },
    { field: 'phone_number', header: 'Phone' },
    { field: 'telegram_number', header: 'Telegram' },
    { field: 'bank_swift', header: 'Bank Swift' },
];

const selectedColumns = ref([]);
const showColumns = ref(columns);

const updateColumns = () => {
    showColumns.value = selectedColumns.value;
};

const createCustomer = () => {
    Inertia.visit(route('customers.create'));
};

const editCustomer = (id) => {
    Inertia.visit(route('customers.edit', id));
};

const deleteCustomer = (id) => {
    if (confirm('Are you sure you want to delete this customer?')) {
        Inertia.delete(route('customers.destroy', id), {
            onSuccess: () => {
                console.log('Customer deleted!');
            },
            onError: (error) => {
                console.error('Error deleting customer:', error);
            }
        });
    }
};

const viewCustomer = (id) => {
    Inertia.visit(route('customers.show', id));
};
</script>