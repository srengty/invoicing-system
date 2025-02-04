<template>
    <Head title="Agreements" ></Head>
    <GuestLayout>
        <div class="agreements">
            <div class="flex justify-between items-center p-3">
                <h1 class="text-2xl">Agreements</h1>
                <div>
                    <Button icon="pi pi-plus" label="New" rounded @click="openCreate" ></Button>
                    <ChooseColumns :columns="columns" v-model="selectedColumns" @apply="updateColumns" rounded />
                </div>
            </div>
            <DataTable :value="agreements" paginator :rows="5" :rowsPerPageOptions="[5, 10, 20, 50]" :stripedRows="true" :showGridlines="true">
                <template v-for="col of showColumns" :key="col.field">
                    <Column v-if="!['agreement_doc','actions'].includes(col.field)" :field="col.field" :header="col.header" sortable></Column>
                    <Column v-if="col.field==='agreement_doc'" :field="col.field" :header="col.header" sortable>
                        <template #body="slotProps">
                            <a v-if="slotProps.data[col.field]" :href="slotProps.data[col.field]" target="_blank">View doc</a>
                        </template>
                    </Column>
                    <Column v-if="col.field==='actions'" :field="col.field" :header="col.header" sortable>
                        <template #body="slotProps">
                            <a :href="route('agreements.show',{'id':slotProps.data.id})" target="_blank">View</a>
                        </template>
                    </Column>
                </template>
            </DataTable>
        </div>
    </GuestLayout>
</template>
<script setup>
import ChooseColumns from '@/Components/ChooseColumns.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { DataTable, Column, Button, Popover } from 'primevue';
import { ref } from "vue";

defineProps({
    agreements: {
        type: Array,
    },
});
const columns = [
    { field: 'actions', header: 'Actions' },
    // { field: 'id', header: 'ID' },
    { field: 'quotation_no', header: 'Quotation No.' },
    { field: 'agreement_no', header: 'Agreement No' },
    { field: 'agreement_ref_no', header: 'Agreement Ref No' },
    { field: 'agreement_date', header: 'Agreement Date' },
    { field: 'address', header: 'Address' },
    { field: 'agreement_doc', header: 'Agreement Doc' },
    { field: 'start_date', header: 'Start Date' },
    { field: 'end_date', header: 'End Date' },
    { field: 'due_payment', header: 'Due Payment' },
    { field: 'total_progress_payment', header: 'Total Progress Payment' },//sum of all recipes amount
    { field: 'amount_no_tax', header: 'Amount' },
    { field: 'tax', header: 'Tax' },
    { field: 'status', header: 'Status' },
    { field: 'customer_id', header: 'Customer ID' },
];
const selectedColumns = ref(columns);
const showColumns = ref(columns);
const updateColumns = (columns) => {
    showColumns.value = selectedColumns.value;
}
const openCreate = () => {
    router.get(route("agreements.create"));
}
</script>