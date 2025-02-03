<template>
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
                <form @submit.prevent="saveProduct">
                    <div class="field">
                        <label for="code">Code</label>
                        <InputText id="code" v-model="productForm.code" class="w-full" required />
                    </div>
                    <div class="field">
                        <label for="name">Name</label>
                        <InputText id="name" v-model="productForm.name" class="w-full" required />
                    </div>
                    <div class="field">
                        <label for="price">Price</label>
                        <InputNumber id="price" v-model="productForm.price" class="w-full" required />
                    </div>
                    <div class="field">
                        <label for="stock">Stock</label>
                        <InputNumber id="stock" v-model="productForm.stock" class="w-full" required />
                    </div>
                    <div class="field">
                        <label for="category">Category</label>
                        <InputText id="category" v-model="productForm.category" class="w-full" />
                    </div>
                    <div class="flex justify-end gap-2 mt-4">
                        <Button label="Cancel" class="p-button-secondary" @click="closeForm" />
                        <Button label="Save" class="p-button-primary" type="submit" />
                    </div>
                </form>
            </Dialog>
        </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { DataTable, Column, Button, Dialog, InputText, InputNumber } from 'primevue';
import { ref } from 'vue';

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
    { field: 'price', header: 'Price' },
    { field: 'stock', header: 'Stock' },
    { field: 'category', header: 'Category' },
];

// State for form and dialog visibility
const isFormVisible = ref(false);
const productForm = ref({
    id: null,
    code: '',
    name: '',
    price: null,
    stock: null,
    category: '',
});

// Methods for handling form operations
const openForm = (product = null) => {
    if (product) {
        // Populate form with product data for editing
        productForm.value = { ...product };
    } else {
        // Reset form for creating a new product
        productForm.value = { id: null, code: '', name: '', price: null, stock: null, category: '' };
    }
    isFormVisible.value = true;
};

const closeForm = () => {
    isFormVisible.value = false;
};

const saveProduct = () => {
    if (productForm.value.id) {
        // Update existing product
        Inertia.put(route('products.update', productForm.value.id), productForm.value, {
            onSuccess: () => closeForm(),
        });
    } else {
        // Create new product
        Inertia.post(route('products.store'), productForm.value, {
            onSuccess: () => closeForm(),
        });
    }
};

const deleteProduct = (id) => {
    if (confirm('Are you sure you want to delete this product?')) {
        Inertia.delete(route('products.destroy', id));
    }
};
</script>
