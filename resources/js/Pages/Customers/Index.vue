<template>
    <Head title="Customers/Organization Name" />
    <GuestLayout>
        <BodyLayout>
            <div class="customers">
                <div class="flex justify-between items-center pb-4">
                    <div class="flex items-center gap-2">
                        <img src="/User.png" alt="Item Icon" class="h-8 w-8" />
                        <h1 class="text-xl text-green-600">Customers/Organization Name</h1>
                    </div>
                    <div>
                        <Button icon="pi pi-plus" label="New" outlined @click="isCreateCustomerVisible = true" size="small" />
                        <ChooseColumns :columns="columns" v-model="selectedColumns" @apply="updateColumns" outlined size="small" />
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

            <!-- Create Customer Dialog -->
            <Dialog v-model:visible="isCreateCustomerVisible" modal header="Add Customer" class="w-2/3">
                <template #header>
                    <div class="flex items-center gap-2">
                        <img src="/User.png" alt="Item Customer" class="h-8 w-8 ml-2" />
                        <span class="text-xl font-semibold bor">Add Customer</span>
                    </div>
                </template>
                <Customers
                    :customerCategories="customerCategories"
                    redirect_route="customers.index"
                    :mode="'create'"
                    @close="isCreateCustomerVisible = false"
                />
            </Dialog>

            <!-- Edit Customer Dialog -->
            <Dialog v-model:visible="isEditCustomerVisible" modal header="Edit Customer" class="w-2/3">
                <template #header>
                    <div class="flex items-center gap-2">
                        <img src="/Item.png" alt="Item Customer" class="h-8 w-8 ml-2" />
                        <span class="text-xl font-semibold bor">Edit Customer</span>
                    </div>
                </template>
                <Customers
                    :customerCategories="customerCategories"
                    redirect_route="customers.index"
                    :mode="'edit'"
                    :customer="selectedCustomer"
                    @close="isEditCustomerVisible = false"
                />
            </Dialog>

            <!-- View Customer Dialog -->
            <Dialog v-model:visible="isViewCustomerVisible" modal header="View Customer" class="w-2/3">
                <template #header>
                    <div class="flex items-center gap-2">
                        <img src="/Item.png" alt="Item Customer" class="h-8 w-8 ml-2" />
                        <span class="text-xl font-semibold bor">Customer Details</span>
                    </div>
                </template>
                <Customers
                    :customerCategories="customerCategories"
                    redirect_route="customers.index"
                    :mode="'view'"
                    :customer="selectedCustomer"
                    @close="isViewCustomerVisible = false"
                />
            </Dialog>
        </BodyLayout>
    </GuestLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import { DataTable, Column, Button, Dialog } from 'primevue';
import { Inertia } from '@inertiajs/inertia';
import ChooseColumns from '@/Components/ChooseColumns.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import BodyLayout from '@/Layouts/BodyLayout.vue';
import Customers from '@/Components/Customers.vue';

const props = defineProps({
    customers: Array,
    customerCategories: Array,
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

const selectedColumns = ref(columns);
const showColumns = ref(columns);

const isCreateCustomerVisible = ref(false);
const isEditCustomerVisible = ref(false);
const isViewCustomerVisible = ref(false);
const selectedCustomer = ref(null);

const updateColumns = () => {
    showColumns.value = selectedColumns.value;
};

const editCustomer = (id) => {
    const customer = props.customers.find(cust => cust.id === id);
    selectedCustomer.value = customer;
    isEditCustomerVisible.value = true;
};

const deleteCustomer = (id) => {
    if (confirm('Are you sure you want to delete this customer?')) {
        Inertia.delete(route('customers.destroy', id), {
            onSuccess: () => {
                props.customers = props.customers.filter(customer => customer.id !== id);
                console.log('Customer deleted!');
            },
            onError: (error) => {
                console.error('Error deleting customer:', error);
            },
        });
    }
};

const viewCustomer = (id) => {
    const customer = props.customers.find(cust => cust.id === id);
    selectedCustomer.value = customer;
    isViewCustomerVisible.value = true;
};

const indexedCustomers = computed(() => {
    return props.customers.map((customer, index) => ({
        ...customer,
        index: index + 1,
    }));
});
</script>