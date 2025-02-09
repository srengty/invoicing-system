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
        <Column header="Action" :exportable="false" v-if="!readonly">
            <template #body="slotProps">
                <div class="flex items-center gap-2">
                    <Button icon="pi pi-pencil" class="p-button-rounded p-button-success p-button-outlined" label="Edit" @click="doEditPaymenSchedule({...slotProps.data})" />
                    <Button icon="pi pi-file-pdf" class="p-button-rounded p-button-success p-button-outlined" label="Generate invoice" />
                </div>
            </template>
        </Column>
        <ColumnGroup type="footer">
            <Row>
                <Column footer="Total:" footerStyle="text-align:right" :colspan="4" style="text-align:right"></Column>
                <Column :footer="`${currencySign} ${totalAmount.toLocaleString()}`" style="text-align: right;"></Column>
                <Column footer=""></Column>
            </Row>
        </ColumnGroup>
    </DataTable>
    <Dialog v-model="isShowing" header="Update payment schedule" :visible="isShowing" @update:visible="isShowing = $event" modal>
        <AddPayment v-model="editingSchedule" @cancel="doCancel" @save="doSave"/>
    </Dialog>
</template>

<script setup>
import { ref, defineModel, computed, onMounted } from "vue";
import { DataTable, Column, Button, InputNumber, InputGroup, InputGroupAddon, DatePicker, Row, ColumnGroup, Dialog } from "primevue";
import { currencies } from "@/constants";
import AddPayment from "@/Components/Agreements/AddPayment.vue";
const items = defineModel({default:[
    {
        id: 1,
        due_date: "28/01/2025",
        short_description: "Item description",
        percentage: 50,
        remark: "Additional remark",
        currency: "KHR",
        amount: 2000,
    },
    {
        id: 2,
        due_date: "04/01/2025",
        short_description: "Item description",
        percentage: 50,
        currency: "KHR",
        remark: "Additional remark",
        amount: 5000,
    },
    {
        id: 3,
        due_date: "11/01/2025",
        short_description: "Item description",
        percentage: 50,
        currency: "KHR",
        remark: "Additional remark",
        amount: 3000,
    },
]});
const props = defineProps({
    currency: String,
    readonly: Boolean,
    agreement_amount: Number,
});
const editingRows = ref([]);
const exchange_rate = ref(4100);
const onRowEditSave = (event) => {
    let { newData, index } = event;

    items.value[index] = newData;
};
const currencySign = computed(()=>currencies.filter(v=>v.value==props.currency)[0]?.sign??'$');
const priceTemplate = (data) => `${currencies.filter(v=>v.value==data.currency)[0]?.sign??'$'} ${data.amount.toLocaleString()}`;
const calculateRate = (propsCurrency, itemCurrency)=>{
    if(propsCurrency=='USD'&&itemCurrency=='KHR') return 1/exchange_rate.value;
    else if(propsCurrency=='KHR'&&itemCurrency=='USD') return exchange_rate.value;
    return 1;
};
const totalAmount = computed(()=>items.value.reduce((acc, item) => acc + calculateRate(props.currency,item.currency)*item.amount, 0));
const isShowing = ref(false);
const editingSchedule = ref({
    agreement_amount: 200,
    due_date: new Date(),
    short_description: "Item description",
    percentage: 100,
    remark: "Additional remark",
    amount: 2000,
    currency: 'KHR',
    agreement_currency: 'KHR',
    exchange_rate: 4200,
});
const doEditPaymenSchedule = (data) => {
    Object.assign(editingSchedule.value, data );
    editingSchedule.value.agreement_currency = data.currency;
    editingSchedule.value.agreement_amount = props.agreement_amount??2000;
    console.log(props.agreement_amount);
    isShowing.value = true;
};
const doCancel = () => {
    isShowing.value = false;
};
const doSave = () => {
    isShowing.value = false;
    const schedule = items.value.find(v=>v.id==editingSchedule.value.id);
    if(schedule){
        Object.assign(schedule, editingSchedule.value);
    }
    
};
</script>

<style lang="scss" scoped></style>