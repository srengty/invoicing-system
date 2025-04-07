<template>
    <div>
        <DataTable
            v-model:editingRows="editingRows"
            editMode="row"
            dataKey="id"
            @row-edit-save="onRowEditSave"
            :value="items"
            class="p-datatable-striped"
            responsiveLayout="scroll"
            :show-gridlines="true"
        >
            <Column field="index" header="No." sortable class="text-sm">
                <template #body="slotProps">
                    {{ slotProps.index + 1 }}
                </template>
            </Column>
            <Column field="due_date" header="Due Date" sortable class="text-sm">
                <template #body="slotProps">
                    {{
                        moment(slotProps.data["due_date"]).format("YYYY/MM/DD")
                    }}
                </template>
            </Column>
            <Column
                field="short_description"
                header="Short Description"
                sortable
                class="text-sm"
            >
                <template #editor="{ data, field }">
                    <DatePicker
                        v-model="data[field]"
                        fluid
                        date-format="yy/mm/dd"
                    />
                </template>
            </Column>
            <Column
                field="percentage"
                header="Percentage"
                sortable
                class="text-sm"
            >
                <template #body="slotProps">
                    {{ slotProps.data["percentage"] }} %
                </template>
                <template #editor="{ data, field }">
                    <InputGroup>
                        <InputNumber v-model="data[field]" fluid />
                        <InputGroupAddon append>%</InputGroupAddon>
                    </InputGroup>
                </template>
            </Column>
            <Column
                field="amount"
                header="Amount"
                style="text-align: right"
                sortable
                class="text-sm"
            >
                <template #body="slotProps">
                    {{ priceTemplate(slotProps.data) }}
                </template>
                <template #editor="{ data, field }">
                    <InputGroup>
                        <InputGroupAddon append>{{
                            props.currency ?? "$"
                        }}</InputGroupAddon>
                        <InputNumber v-model="data[field]" fluid />
                    </InputGroup>
                </template>
            </Column>
            <Column
                header="Action"
                :exportable="false"
                v-if="!readonly"
                sortable
                class="text-sm"
            >
                <template #body="slotProps">
                    <div class="flex items-center gap-2">
                        <Button
                            icon="pi pi-pencil"
                            class="p-button-success p-button-outlined"
                            label="Edit"
                            @click="
                                doEditPaymentSchedule({ ...slotProps.data })
                            "
                        />
                        <Button
                            icon="pi pi-trash"
                            class="p-button-danger p-button-outlined"
                            label="Remove"
                            @click="
                                doDeletePaymentSchedule({ ...slotProps.data })
                            "
                        />
                        <Button
                            icon="pi pi-file-pdf"
                            class="p-button-success p-button-outlined"
                            label="Generate invoice"
                            :loading="generatingInvoice"
                            @click="generateInvoice(slotProps.data)"
                        />
                    </div>
                </template>
            </Column>
            <ColumnGroup type="footer">
                <Row>
                    <Column
                        footer="Total:"
                        footerStyle="text-align:right"
                        :colspan="4"
                        style="text-align: right"
                    ></Column>
                    <Column
                        :footer="`${currencySign} ${totalAmount.toLocaleString()}`"
                        style="text-align: right"
                    ></Column>
                    <Column
                        footer=""
                        style="text-align: right"
                        v-if="!readonly"
                    ></Column>
                </Row>
            </ColumnGroup>
        </DataTable>
        <Dialog
            v-model="isShowing"
            class="w-1/4"
            header="Update payment schedule"
            :visible="isShowing"
            @update:visible="isShowing = $event"
            modal
        >
            <AddPayment
                v-model="editingSchedule"
                @cancel="doCancel"
                @save="doSave"
            />
        </Dialog>
    </div>
</template>

<script setup>
import { ref, defineModel, computed, onMounted } from "vue";
import { currencies } from "@/constants";
import moment from "moment";
import AddPayment from "@/Components/Agreements/AddPayment.vue";
import { useToast } from "primevue/usetoast";
import {
    DataTable,
    Column,
    Button,
    InputNumber,
    InputGroup,
    InputGroupAddon,
    DatePicker,
    Row,
    ColumnGroup,
    Dialog,
    Toast,
} from "primevue";

const toast = useToast();
const items = defineModel({
    default: [
        /* {
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
    }, */
    ],
});
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
    toast.add({
        severity: "success",
        summary: "Success",
        detail: "Payment schedule updated successfully",
        life: 3000,
    });
};
const currencySign = computed(
    () => currencies.filter((v) => v.value == props.currency)[0]?.sign ?? "$"
);
const priceTemplate = (data) => {
    return `${
        currencies.filter((v) => v.value == data.currency)[0]?.sign ?? "$"
    } ${data.amount.toLocaleString()}`;
};
const calculateRate = (propsCurrency, itemCurrency) => {
    if (propsCurrency == "USD" && itemCurrency == "KHR")
        return 1 / exchange_rate.value;
    else if (propsCurrency == "KHR" && itemCurrency == "USD")
        return exchange_rate.value;
    return 1;
};
const totalAmount = computed(() =>
    items.value.reduce(
        (acc, item) =>
            acc + calculateRate(props.currency, item.currency) * item.amount,
        0
    )
);
const isShowing = ref(false);
const editingSchedule = ref({
    agreement_amount: 200,
    due_date: new Date(),
    short_description: "Item description",
    percentage: 100,
    remark: "Additional remark",
    amount: 2000,
    currency: "KHR",
    agreement_currency: "KHR",
    exchange_rate: 4200,
    status: "Pending",
});
const generatingInvoice = ref(false);
const generateInvoice = async (paymentItem) => {
    generatingInvoice.value = true;

    try {
        // Simulate API call
        await new Promise((resolve) => setTimeout(resolve, 1000));

        toast.add({
            severity: "success",
            summary: "Invoice Generated",
            detail: `Invoice for ${paymentItem.short_description} has been downloaded`,
            life: 3000,
        });

        // In a real app, this would be the actual API call:
        // const response = await axios.post('/api/generate-invoice', {
        //     payment: paymentItem,
        //     agreement: {
        //         amount: props.agreement_amount,
        //         currency: props.currency
        //     }
        // });

        // Simulate PDF download
        console.log("Would generate invoice for:", paymentItem);
    } catch (error) {
        toast.add({
            severity: "error",
            summary: "Generation Failed",
            detail: "Failed to generate invoice. Please try again.",
            life: 3000,
        });
    } finally {
        generatingInvoice.value = false;
    }
};
const doEditPaymentSchedule = (data) => {
    Object.assign(editingSchedule.value, data);
    editingSchedule.value.agreement_currency = data.currency;
    editingSchedule.value.agreement_amount = props.agreement_amount ?? 2000;
    console.log(props.agreement_amount);
    isShowing.value = true;
};
const doDeletePaymentSchedule = (data) => {
    const index = items.value.findIndex((v) => v.id == data.id);
    if (index >= 0) {
        items.value.splice(index, 1);
        toast.add({
            severity: "warn",
            summary: "Deleted",
            detail: "Payment schedule removed successfully",
            life: 3000,
        });
    }
};
const doCancel = () => {
    isShowing.value = false;
    toast.add({
        severity: "info",
        summary: "Cancelled",
        detail: "Update was cancelled",
        life: 3000,
    });
};
const doSave = () => {
    isShowing.value = false;
    const schedule = items.value.find((v) => v.id == editingSchedule.value.id);
    if (schedule) {
        Object.assign(schedule, editingSchedule.value);
        toast.add({
            severity: "success",
            summary: "Success",
            detail: "Payment schedule updated successfully",
            life: 3000,
        });
    } else {
        toast.add({
            severity: "error",
            summary: "Error",
            detail: "Failed to update payment schedule",
            life: 3000,
        });
    }
};
</script>
<style lang="scss" scoped></style>
