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
                        <div class="flex gap-2">
                            <Dropdown
                                v-model="formData.invoice_no"
                                :options="invoices"
                                optionLabel="invoice_no"
                                optionValue="invoice_no"
                                placeholder="Select
                            Invoice"
                                class="w-full"
                                @change="updateInvoiceDetails"
                            />
                            <Button
                                icon="pi pi-plus"
                                label="Add Invoice"
                                raised
                                size="small"
                                class="w-40"
                                @click="openInvoiceDialog"
                            />
                        </div>
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
                        <label for="customer_id" class="required"
                            >Customer</label
                        >
                        <Dropdown
                            v-model="formData.customer_id"
                            :options="customers"
                            optionLabel="name"
                            optionValue="id"
                            placeholder="Select a customer"
                            class="w-full"
                            @change="updateCustomerDetails"
                            :filter="true"
                            filterPlaceholder="Search customers"
                            :showClear="true"
                        >
                        </Dropdown>
                    </div>
                </div>

                <div class="col-12 md:col-6">
                    <div class="field">
                        <label>Customer Code</label>
                        <InputText
                            v-model="formData.customer_code"
                            class="w-full"
                            readonly
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
import { ref, computed, onMounted, defineExpose } from "vue";
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
const dialogVisible = ref(false);
const emit = defineEmits(["receipt-created"]);

const formData = ref({
    invoice_no: null,
    receipt_no: "",
    receipt_date: new Date().toISOString().split("T")[0],
    customer_id: null,
    customer_code: "",
    customer_name: "",
    amount_paid: null,
    payment_method: null,
    payment_reference_no: null,
});

const customers = ref([]);
const invoices = ref([]);
const getCustomerName = (customerId) => {
    const customer = customers.value.find((c) => c.id === customerId);
    return customer ? customer.name : "";
};
const paymentMethods = ref([
    { label: "Cash", value: "cash" },
    { label: "Bank Transfer", value: "bank_transfer" },
    { label: "Credit Card", value: "credit_card" },
]);

const updateCustomerDetails = () => {
    const selectedCustomer = customers.value.find(
        (customer) => customer.id === formData.value.customer_id
    );

    if (selectedCustomer) {
        formData.value.customer_code = selectedCustomer.customer_code || "";
        formData.value.customer_name = selectedCustomer.name || "";
    } else {
        formData.value.customer_code = "";
        formData.value.customer_name = "";
    }
};

const loadCustomers = async () => {
    try {
        const response = await axios.get("/api/customers");
        customers.value = response.data.customers || [];
    } catch (error) {
        console.error("Error loading customers", error);
        toast.add({
            severity: "error",
            summary: "Error",
            detail: "Failed to load customers",
        });
    }
};
const loadInvoices = async () => {
    try {
        const response = await axios.get("/api/invoices");
        invoices.value = response.data.invoices || [];
    } catch (error) {
        console.error("Error loading invoices", error);
        toast.add({
            severity: "error",
            summary: "Error",
            detail: "Failed to load invoices",
        });
    }
};

const show = async () => {
    await loadCustomers();
    await loadInvoices();
    try {
        const receiptNoResponse = await axios.get(route("receipts.create"));
        // Ensure the response contains 'nextReceiptNo' and is a number
        formData.value.receipt_no = String(
            receiptNoResponse.data.nextReceiptNo
        ).padStart(8, "0");
    } catch (error) {
        console.error("Error getting receipt number", error);
    }

    dialogVisible.value = true;
};

defineExpose({ show });

const closeDialog = () => {
    dialogVisible.value = false;
    formData.value = {
        invoice_no: null,
        receipt_no: "",
        receipt_date: new Date().toISOString().split("T")[0],
        customer_id: null,
        customer_code: "",
        customer_name: "",
        amount_paid: null,
        payment_method: null,
        payment_reference_no: null,
    };
};

const openInvoiceDialog = () => {
    toast.add({
        severity: "info",
        summary: "Info",
        detail: "Open invoice creation dialog here",
    });
};
const updateInvoiceDetails = () => {
    const selectedInvoice = invoices.value.find(
        (invoice) => invoice.invoice_no === formData.value.invoice_no
    );

    if (selectedInvoice) {
        formData.value.invoice_amount = selectedInvoice.amount || 0; // Example logic
    }
};

const createReceipt = async () => {
    try {
        console.log("Form Data: ", formData.value); // Check if customer_code is present
        if (!formData.value.customer_id) {
            throw new Error("Please select a customer");
        }

        const response = await axios.post(
            route("receipts.store"),
            formData.value
        );

        toast.add({
            severity: "success",
            summary: "Success",
            detail: "Receipt created successfully",
        });

        dialogVisible.value = false;
        emit("receipt-created", response.data.receipt);
    } catch (error) {
        toast.add({
            severity: "error",
            summary: "Error",
            detail:
                error.response?.data?.message ||
                error.message ||
                "Failed to create receipt",
        });
    }
};
</script>

<style scoped></style>
