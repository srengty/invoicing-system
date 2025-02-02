<template>
    <DataTable v-model:editingRows="editingRows" editMode="row" dataKey="id" @row-edit-save="onRowEditSave" :value="items" class="p-datatable-striped" responsiveLayout="scroll" :show-gridlines="true">
        <Column field="index" header="No.">
            <template #body="slotProps">
                {{ slotProps.index + 1 }}
            </template>
        </Column>
        <Column field="due_date" header="Due Date"></Column>
        <Column field="short_description" header="Short Description">
            <template #editor="{ data, field }">
                <InputText v-model="data[field]" fluid />
            </template>
        </Column>
        <Column field="amount" header="Amount">
            <template #body="slotProps">
                {{ priceTemplate(slotProps.data) }}
            </template>
            <template #editor="{ data, field }">
                <InputNumber v-model="data[field]" fluid />
            </template>
        </Column>
        <Column header="Action" :exportable="false" :rowEditor="true"></Column>
    </DataTable>
</template>

<script setup>
import { ref, defineModel } from "vue";
import { DataTable, Column, Button, InputNumber, InputText } from "primevue";
const items = defineModel({default:[
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
]});
const editingRows = ref([]);
const onRowEditSave = (event) => {
    let { newData, index } = event;

    items.value[index] = newData;
};
const priceTemplate = (data) => `$ ${data.amount.toLocaleString()}`;
</script>

<style lang="scss" scoped></style>