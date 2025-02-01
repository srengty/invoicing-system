<template>
    <DataTable :value="items" class="p-datatable-striped" responsiveLayout="scroll" :show-gridlines="true">
        <Column field="index" header="No.">
            <template #body="slotProps">
                {{ slotProps.index + 1 }}
            </template>
        </Column>
        <Column field="due_date" header="Due Date"></Column>
        <Column field="short_description" header="Short Description"></Column>
        <Column field="amount" header="Amount">
            <template #body="slotProps">
                {{ priceTemplate(slotProps.data) }}
            </template>
        </Column>
        <Column header="Action" :exportable="false" >
            <template #body="slotProps">
                <Button label="Edit" icon="pi pi-pencil" class="p-button-text p-button-sm p-button-info mr-2" />
            </template>
        </Column>
    </DataTable>
</template>

<script setup>
import { ref } from "vue";
import { DataTable, Column, Button } from "primevue";
const items = ref([
    {
        id: 1,
        due_date: "28/01/2025",
        short_description: "Item description",
        remark: "Additional remark",
        amount: 2000,
    },
    {
        id: 2,
        due_date: "04/01/2025",
        short_description: "Item description",
        remark: "Additional remark",
        amount: 5000,
    },
    {
        id: 3,
        due_date: "11/01/2025",
        short_description: "Item description",
        remark: "Additional remark",
        amount: 3000,
    },
]);
const priceTemplate = (data) => `$ ${data.amount.toLocaleString()}`;
</script>

<style lang="scss" scoped></style>