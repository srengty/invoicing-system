<template>
    <Dialog
        v-model:visible="dialogVisible"
        header="Create New Receipt"
        :modal="true"
        :style="{ width: '50vw' }"
        @hide="closeDialog"
    >
        <form @submit.prevent="createReceipt">
            <div class="grid">
                <div class="col-12 md:col-6">
                    <div class="field">
                        <label for="invoice_no">Invoice No</label>
                        <Dropdown
                            v-model="formData.invoice_no"
                            optionLabel="invoice_no"
                            optionValue="invoice_no"
                            placeholder="Select Invoice"
                            class="w-full"
                        />
                    </div>
                </div>
                <div class="col-12 md:col-6">
                    <div class="field">
                        <label for="receipt_no" class="required"
                            >Receipt No</label
                        >
                        <InputText
                            v-model="formData.receipt_no"
                            class="w-full"
                            :readonly="true"
                            size="small"
                        />
                    </div>
                </div>
                <div class="col-12 md:col-6">
                    <div class="field">
                        <label for="receipt_date" class="required">Date</label>
                        <DatePicker
                            v-model="formData.receipt_date"
                            dateFormat="yy-mm-dd"
                            showIcon
                            class="w-full"
                            placeholder="Select Date"
                            size="small"
                        />
                    </div>
                </div>
                <div class="col-12 md:col-6">
                    <div class="field">
                        <label for="amount_paid" class="required"
                            >Customer Code</label
                        >
                        <InputText
                            v-model="selectedCustomerCode"
                            class="w-full"
                            readonly
                        />
                    </div>
                </div>
                <div class="col-12 md:col-6">
                    <div class="field">
                        <label for="customer_id" class="required"
                            >Customer / Organization name</label
                        >
                        <Dropdown
                            v-model="formData.customer_id"
                            :options="customers"
                            optionLabel="displayName"
                            optionValue="id"
                            placeholder="Select a customer"
                            class="w-full"
                            size="small"
                        />
                    </div>
                </div>
                <div class="col-12 md:col-6">
                    <div class="field">
                        <label for="amount_paid">Amount Paid</label>
                        <InputNumber
                            v-model="formData.amount_paid"
                            mode="currency"
                            currency="USD"
                            locale="en-US"
                            class="w-full"
                            size="small"
                        />
                    </div>
                </div>
                <div class="col-12 md:col-6">
                    <div class="field">
                        <label for="payment_method">Payment Method</label>
                        <Dropdown
                            v-model="formData.payment_method"
                            :options="paymentMethods"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Select Payment Method"
                            class="w-full"
                            size="small"
                        />
                    </div>
                </div>
                <div class="col-12 md:col-6">
                    <div class="field">
                        <label for="payment_reference_no"
                            >Payment Reference No (if any)</label
                        >
                        <InputText
                            v-model="formData.payment_reference_no"
                            class="w-full"
                            size="small"
                        />
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-4">
                <Button
                    label="Save Receipt"
                    icon="pi pi-check"
                    @click="createReceipt"
                />
                <Button
                    label="Cancel"
                    icon="pi pi-times"
                    @click="closeDialog"
                    class="p-button-text"
                />
            </div>
        </form>
    </Dialog>
</template>

<script setup>
import { ref, computed, onMounted, defineExpose, watch } from "vue";
import { useToast } from "primevue/usetoast";
import { route } from "ziggy-js";
import axios from "axios";
import {
    Dialog,
    Dropdown,
    InputText,
    InputNumber,
    DatePicker,
    Button,
    Select,
    Toast,
} from "primevue";

const toast = useToast();
const formData = ref({
    invoice_no: null,
    receipt_no: "",
    receipt_date: new Date().toISOString().split("T")[0],
    customer_id: null,
    amount_paid: null,
    payment_method: null,
    payment_reference_no: null,
});

const nextReceiptNo = ref(0);
const dialogVisible = ref(false);
const customers = ref([]);
const selectedCustomerCode = ref("");
const invoices = ref([]);
// Watch for customer_id changes to update the customer code
watch(
    () => formData.value.customer_id,
    (newCustomerId) => {
        if (newCustomerId) {
            const selectedCustomer = customers.value.find(
                (c) => c.id === newCustomerId
            );
            selectedCustomerCode.value = selectedCustomer
                ? selectedCustomer.customer_code
                : "";
        } else {
            selectedCustomerCode.value = "";
        }
    },
    { immediate: true }
);

const paymentMethods = ref([
    { label: "Cash", value: "cash" },
    { label: "Bank Transfer", value: "bank_transfer" },
    { label: "Credit Card", value: "credit_card" },
]);

onMounted(async () => {
    try {
        const [invoicesResponse, customersResponse] = await Promise.all([
            axios.get(route("invoices.index")),
            axios.get(route("customers.index")),
        ]);

        invoices.value = invoicesResponse.data;

        customers.value = (
            Array.isArray(customersResponse.data) ? customersResponse.data : []
        ).map((customer) => ({
            ...customer,
            displayName: `${customer.customer_code} - ${customer.name}`,
        }));
    } catch (error) {
        console.error("Error fetching data:", error);
    }
});

const show = async () => {
    try {
        // Reset form when showing
        formData.value = {
            invoice_no: null,
            receipt_no: "",
            receipt_date: new Date().toISOString().split("T")[0],
            customer_id: null,
            amount_paid: null,
            payment_method: null,
            payment_reference_no: null,
        };
        selectedCustomerCode.value = "";

        // Fetch customers
        const customerRes = await axios.get(route("customers.index"));

        // Process customers data
        customers.value = Array.isArray(customerRes.data?.data)
            ? customerRes.data.data
            : Array.isArray(customerRes.data)
            ? customerRes.data
            : [];

        customers.value = customers.value.map((customer) => ({
            id: customer.id,
            customer_code: customer.customer_code || "-",
            name: customer.name,
            displayName: `${customer.customer_code} - ${customer.name}`,
        }));

        // Fetch next receipt number
        const receiptResponse = await axios.get(route("receipts.create"));
        if (receiptResponse.data?.nextReceiptNo) {
            formData.value.receipt_no = receiptResponse.data.nextReceiptNo;
        }

        dialogVisible.value = true;
    } catch (error) {
        console.error("Error preparing receipt form:", error);
        toast.add({
            severity: "error",
            summary: "Error",
            detail: "Failed to load receipt data",
            life: 3000,
        });
    }
};

defineExpose({ show });

const closeDialog = () => {
    dialogVisible.value = false;
};
const createReceipt = async () => {
    if (
        !formData.value.customer_id ||
        !formData.value.amount_paid ||
        !formData.value.receipt_date
    ) {
        toast.add({
            severity: "error",
            summary: "Error",
            detail: "Please fill all required fields",
            life: 3000,
        });
        return;
    }

    try {
        const payload = {
            ...formData.value,
            customer_code: selectedCustomerCode.value,
        };

        const response = await axios.post(route("receipts.store"), payload);

        toast.add({
            severity: "success",
            summary: "Success",
            detail: "Receipt created successfully",
            life: 3000,
        });

        dialogVisible.value = false;
    } catch (error) {
        console.error("Error Creating Receipt:", error);
        toast.add({
            severity: "error",
            summary: "Error",
            detail: error.response?.data?.message || "Failed to create receipt",
            life: 3000,
        });
    }
};
</script>

<style scoped></style>
