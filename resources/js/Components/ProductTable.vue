
<template>
    <div class="card">
        <DataTable :value="products" tableStyle="min-width: 50rem">
            <Column v-for="col of columns" :key="col.field" :field="col.field" :header="col.header"></Column>
            <Column field="action" header="Action">
                <template #body="slotProps">
                    <img :src="`https://picsum.photos/seed/${slotProps.data.image}/300/150`" :alt="slotProps.data.image" class="w-24 rounded" />
                </template>
            </Column>
        </DataTable>
        <br/><Button>New</Button>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
//import { ProductService } from '@/service/ProductService';

onMounted(() => {
    products.value = [
        { code: 'P001', name: 'Product 1', category: 'Category 1', quantity: 10, image: 'product1' },
        { code: 'P002', name: 'Product 2', category: 'Category 2', quantity: 20, image: 'product2' },
        { code: 'P003', name: 'Product 3', category: 'Category 3', quantity: 30, image: 'product3' },
        { code: 'P004', name: 'Product 4', category: 'Category 4', quantity: 40, image: 'product5' },
        { code: 'P005', name: 'Product 5', category: 'Category 5', quantity: 50, image: 'product6' },
    ];
    //ProductService.getProductsMini().then((data) => (products.value = data));
});

const products = ref();
const columns = [
    { field: 'code', header: 'Code' },
    { field: 'name', header: 'Name' },
    { field: 'category', header: 'Category' },
    { field: 'quantity', header: 'Quantity' }
];

</script>
