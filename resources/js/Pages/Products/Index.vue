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
                                icon="pi pi-eye"
                                class="p-button-info"
                                aria-label="View"
                                @click="viewProduct(slotProps.data)"
                                rounded
                            />
                            <Button
                                icon="pi pi-pencil"
                                class="p-button-warning"
                                aria-label="Edit"
                                @click="openForm(slotProps.data)"
                                rounded
                            />
                            <Button
                                icon="pi pi-trash"
                                class="p-button-danger"
                                aria-label="Delete"
                                @click="deleteProduct(slotProps.data.id)"
                                rounded
                            />
                        </div>
                    </template>
                </Column>
            </DataTable>

            <!-- View Product Dialog -->
            <Dialog v-model:visible="isViewDialogVisible" header="Product Details" :modal="true">
                <div v-if="selectedProduct" class="flex text-lg flex-col gap-2 w-64">
                    <p><strong>ID:</strong> {{ selectedProduct.id }}</p>
                    <p><strong>Code:</strong> {{ selectedProduct.code }}</p>
                    <p><strong>Name:</strong> {{ selectedProduct.name }}</p>
                    <p><strong>Name (KH):</strong> {{ selectedProduct.name_kh }}</p>
                    <p><strong>Unit:</strong> {{ selectedProduct.unit }}</p>
                    <p><strong>Price:</strong> {{ selectedProduct.price }}</p>
                    <p><strong>Quantity:</strong> {{ selectedProduct.quantity }}</p>
                    <p><strong>Category:</strong> {{ selectedProduct.category }}</p>
                </div>
                <div class="flex justify-end">
                    <Button label="Close" class="p-button-secondary" @click="isViewDialogVisible = false" />
                </div>
            </Dialog>

            <!-- Product Form Dialog -->
            <Dialog v-model:visible="isFormVisible" header="Product Form" :modal="true">
                <form @submit.prevent="submitForm">
                    <div class="field">
                        <label for="code">Division</label>
                        <Select id="code" v-model="form.division_id" option-label="name" option-value="id" :options="[{id:1,name:'GIC'},{id:2,name:'GCA'},{id:3,name:'GIC'}]" class="w-full" required ></Select>
                    </div>
                    <div class="field">
                        <label for="category">Category</label>
                        <Select id="category" :options="categoryOptions" optionValue="name" optionLabel="name" v-model="form.category" class="w-full" ></Select>
                    </div>
                    <div class="field">
                        <label for="code">Code</label>
                        <InputText id="code" v-model="form.code" class="w-full" required />
                    </div>
                    <div class="field">
                        <label for="name">Name</label>
                        <InputText id="name" v-model="form.name" class="w-full" required />
                    </div>
                    <div class="field">
                        <label for="name_kh">Name (KH)</label>
                        <InputText id="name_kh" v-model="form.name_kh" class="w-full" required />
                    </div>
                    <div class="field">
                        <label for="desc">Description</label>
                        <InputText v-model="form.desc" class="w-full" required />
                    </div>
                    <div class="field">
                        <label for="desc_kh">Description (KH)</label>
                        <InputText v-model="form.desc_kh" class="w-full" required />
                    </div>
                    <div class="field">
                        <label for="unit">Unit</label>
                        <InputText id="unit" v-model="form.unit" class="w-full" required />
                    </div>
                    <div class="field">
                        <label for="price">Price in KHR</label>
                        <InputNumber id="price" v-model="form.price" class="w-full" required />
                    </div>
                    <div class="field">
                        <label for="price" class="required">Account code</label>
                        <InputText v-model="form.acc_code" class="w-full" required  />
                    </div>
                    <div class="field">
                        <label for="quantity">Quantity</label>
                        <InputNumber id="quantity" v-model="form.quantity" class="w-full" required />
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
import { DataTable, Column, Button, Dialog, InputText, InputNumber, Select } from 'primevue';
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia';

defineProps({
    products: {
        type: Array,
        required: true,
    },
});
const categoryOptions=[
{id:'R1', name:'Rental Service'}, {id:'T1', name:'Training Service'}, {id:'C1', name:'Consultation Service'}
]

// Define columns for DataTable
const columns = [
    { field: 'id', header: 'ID' },
    { field: 'code', header: 'Code' },
    { field: 'name', header: 'Name' },
    { field: 'name_kh', header: 'Name (KH)' },
    { field: 'unit', header: 'Unit' },
    { field: 'price', header: 'Price' },
    { field: 'quantity', header: 'Quantity' },
    { field: 'category', header: 'Category' },
];

// State for form and view dialogs
const isFormVisible = ref(false);
const isViewDialogVisible = ref(false);
const selectedProduct = ref(null);

// Create form using Inertia's `useForm`
const form = useForm({
    id: null,
    division_id: null,
    code: '',
    acc_code: '73048 ផលពីសេវាផ្សេងៗ',
    name: '',
    name_kh: '',
    desc: '',
    desc_kh: '',
    unit: '',
    price: null,
    quantity: null,
    category_id: '',
});

// Open product form
const openForm = (product = null) => {
    if (product) {
        form.id = product.id;
        form.code = product.code;
        form.name = product.name;
        form.name_kh = product.name_kh;
        form.unit = product.unit;
        form.price = product.price;
        form.quantity = product.quantity;HEAD
        form.category = product.category;
        form.desc = product.desc;
        form.desc_kh = product.desc_kh;
        form.category_id = product.category_id;
        form.desc = product.desc;
        form.desc_kh = product.desc_kh;
        form.division_id = product.division_id;
        form.acc_id = product.acc_id;invoices
    } else {
        form.reset();
    }
    isFormVisible.value = true;
};

// Close product form
const closeForm = () => {
    isFormVisible.value = false;
    form.reset();
};

// Open view product dialog
const viewProduct = (product) => {
    selectedProduct.value = product;
    isViewDialogVisible.value = true;
};

// Submit product form (create/update)
const submitForm = () => {
    if (form.id) {
        form.put(route('products.update', form.id), { onSuccess: () => closeForm() });
    } else {
        form.post(route('products.store'), { onSuccess: () => closeForm() });
    }
};

// Delete product
const deleteProduct = (id) => {
    if (confirm('Are you sure you want to delete this product?')) {
        Inertia.delete(route('products.destroy', id), {
            onSuccess: () => console.log('Product deleted!'),
            onError: (error) => console.error('Error deleting product:', error),
        });
    }
};
</script>
