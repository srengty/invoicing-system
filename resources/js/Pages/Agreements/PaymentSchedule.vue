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
                <DatePicker v-model="data[field]" fluid date-format="dd/mm/yy" />
            </template>
        </Column>
        <Column field="percentage" header="Percentage">
            <template #body="slotProps">
                {{ slotProps.data['percentage'] }} %
            </template>
            <template #editor="{ data, field }">
                <InputGroup>
                    <InputNumber v-model="data[field]" fluid />
                    <InputGroupAddon append>%</InputGroupAddon>
                </InputGroup>
            </template>
        </Column>
        <Column field="amount" header="Amount" style="text-align: right;">
            <template #body="slotProps">
                {{ priceTemplate(slotProps.data) }}
            </template>
            <template #editor="{ data, field }">
                <InputGroup>
                    <InputGroupAddon append>{{ props.currency??'$' }}</InputGroupAddon>
                    <InputNumber v-model="data[field]" fluid />
                </InputGroup>
            </template>
        </Column>
        <Column header="Action" :exportable="false" :rowEditor="true" v-if="!readonly">
            <template #body="slotProps">
                <Button icon="pi pi-file-pdf" class="p-button-rounded p-button-success p-button-outlined" label="Generate invoice" />
            </template>
        </Column>
        <ColumnGroup type="footer">
            <Row>
                <Column footer="Total:" footerStyle="text-align:right" :colspan="4" style="text-align:right"></Column>
                <Column :footer="`${props.currency??'$'} ${totalAmount.toLocaleString()}`" style="text-align: right;"></Column>
                <Column footer=""></Column>
            </Row>
        </ColumnGroup>
    </DataTable>
</template>

<script setup>
import { ref, defineModel, computed } from "vue";
import { DataTable, Column, Button, InputNumber, InputGroup, InputGroupAddon, DatePicker, Row, ColumnGroup } from "primevue";

const items = defineModel({default:[
    {
        id: 1,
        due_date: "28/01/2025",
        short_description: "Item description",
        percentage: 50,
        remark: "Additional remark",
        amount: 2000,
    },
    {
        id: 2,
        due_date: "04/01/2025",
        short_description: "Item description",
        percentage: 50,
        remark: "Additional remark",
        amount: 5000,
    },
    {
        id: 3,
        due_date: "11/01/2025",
        short_description: "Item description",
        percentage: 50,
        remark: "Additional remark",
        amount: 3000,
    },
]});
const props = defineProps({
    currency: String,
    readonly: Boolean,
});
const editingRows = ref([]);
const onRowEditSave = (event) => {
    let { newData, index } = event;

    items.value[index] = newData;
};
const priceTemplate = (data) => `${props.currency??'$'} ${data.amount.toLocaleString()}`;
const totalAmount = computed(()=>items.value.reduce((acc, item) => acc + item.amount, 0));
</script>

<style lang="scss" scoped></style>