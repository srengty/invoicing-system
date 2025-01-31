<template>
    <Head title="Products" />
    <GuestLayout>
        <div class="products">
            <div class="flex justify-between items-center p-3">
                <h1 class="text-2xl">Manage Products</h1>
                <Button icon="pi pi-plus" label="New Product" class="p-button-success" @click="openForm()" rounded />
            </div>
            <!-- DataTable to display products -->
            <DataTable :value="products" paginator :rows="5" :rowsPerPageOptions="[5, 10, 20, 50]" striped>
                <Column v-for="col of columns" :key="col.field" :field="col.field" :header="col.header" sortable />
                <Column header="Actions">
                    <template #body="slotProps">
                        <div class="flex gap-2">
                            <Button
                                icon="pi pi-pencil"
                                class="p-button-warning"
                                label="Edit"
                                @click="openForm(slotProps.data)"
                                rounded
                            />
                            <Button
                                icon="pi pi-trash"
                                class="p-button-danger"
                                label="Delete"
                                @click="deleteProduct(slotProps.data.id)"
                                rounded
                            />
                        </div>
                    </template>
                </Column>
            </DataTable>

            <!-- Product Form Dialog -->
            <Dialog :visible="isFormVisible" @hide="closeForm" header="Product Form" :modal="true">
                <form @submit.prevent="submitForm">
                    <div class="field">
                        <label for="code">Code</label>
                        <InputText id="code" v-model="form.code" class="w-full" required />
                    </div>
                    <div class="field">
                        <label for="name">Name</label>
                        <InputText id="name" v-model="form.name" class="w-full" required />
                    </div>
                    <div class="field">
                        <label for="unit">Unit</label>
                        <InputText id="unit" v-model="form.unit" class="w-full" required />
                    </div>
                    <div class="field">
                        <label for="price">Price</label>
                        <InputNumber id="price" v-model="form.price" class="w-full" required />
                    </div>
                    <div class="field">
                        <label for="quantity">Quantity</label>
                        <InputNumber id="quantity" v-model="form.quantity" class="w-full" required />
                    </div>
                    <div class="field">
                        <label for="category">Category</label>
                        <InputText id="category" v-model="form.category" class="w-full" />
                    </div>
                    <div class="flex justify-end gap-2 mt-4">
                        <Button label="Cancel" class="p-button-secondary" @click="closeForm" />
                        <Button label="Save" class="p-button-primary" type="submit" :loading="form.processing" />
                    </div>
                </form>
            </Dialog>
        </div>
    </GuestLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { DataTable, Column, Button, Dialog, InputText, InputNumber } from 'primevue';
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia';

defineProps({
    products: {
        type: Array,
        required: true,
    },
});

// Define columns for DataTable
const columns = [
    { field: 'id', header: 'ID' },
    { field: 'code', header: 'Code' },
    { field: 'name', header: 'Name' },
    { field: 'unit', header: 'Unit' },
    { field: 'price', header: 'Price' },
    { field: 'quantity', header: 'Quantity' },
    { field: 'category', header: 'Category' },
];

// State for dialog visibility
const isFormVisible = ref(false);

// Create form using Inertia's `useForm`
const form = useForm({
    id: null,
    code: '',
    name: '',
    unit: '',
    price: null,
    quantity: null,
    category: '',
});

// Methods for handling form operations
const openForm = (product = null) => {
    if (product) {
        // Populate form with product data for editing
        form.id = product.id;
        form.code = product.code;
        form.name = product.name;
        form.unit = product.unit;
        form.price = product.price;
        form.quantity = product.quantity;
        form.category = product.category;
    } else {
        // Reset form for creating a new product
        form.reset();
    }
    isFormVisible.value = true;
};

const closeForm = () => {
    isFormVisible.value = false;
    form.reset();
};

const submitForm = () => {
    if (form.id) {
        // Update existing product
        form.put(route('products.update', form.id), {
            onSuccess: () => closeForm(),
        });
    } else {
        // Create new product
        form.post(route('products.store'), {
            onSuccess: () => closeForm(),
        });
    }
};

// Emit event to notify parent about product deletion
const deleteProduct = (id) => {
    if (confirm('Are you sure you want to delete this product?')) {
        // Send delete request to the server
        Inertia.delete(route('products.destroy', id), {
            onSuccess: () => {
                // Emit event to notify parent component
                emit('productDeleted', id);
                console.log('Product deleted!');
            },
            onError: (error) => {
                // Handle any errors that occur during the delete request
                console.error('Error deleting product:', error);
            }
        });
    }
};
</script>
