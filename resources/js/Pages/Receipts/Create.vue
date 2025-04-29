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
                            <Select
                                :filter="true"
                                v-model="formData.invoice_no"
                                :options="invoices"
                                optionLabel="invoice_no"
                                optionValue="invoice_no"
                                placeholder="Select Invoice"
                                class="w-full"
                                @change="updateInvoiceDetails"
                                size="small"
                            />
                            <Button
                                icon="pi pi-plus"
                                label="Add Invoice"
                                raised
                                size="small"
                                class="w-44"
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
                            :showButtonBar="true"
                        />
                    </div>
                </div>
                <div class="col-12 md:col-6">
                    <div class="field">
                        <label class="required">Customer Code</label>
                        <InputText
                            v-model="formData.customer_code"
                            class="w-full"
                            placeholder="Customer Code"
                            size="small"
                            readonly
                        />
                    </div>
                </div>
                <div class="col-12 md:col-6">
                    <div class="field">
                        <label for="customer_id" class="required"
                            >Customer</label
                        >
                        <div class="flex gap-2">
                            <Dropdown
                                :filter="true"
                                v-model="formData.customer_id"
                                :options="customers"
                                optionLabel="name_with_code"
                                optionValue="id"
                                placeholder="Search customer..."
                                class="w-full"
                                @change="updateCustomerDetails"
                                filterPlaceholder="Search customers"
                                :showClear="true"
                                size="small"
                            />
                            <Button
                                icon="pi pi-plus"
                                title="add customer"
                                label="Add Customer"
                                raised
                                @click="isCreateCustomerVisible = true"
                                class="w-44 start"
                                size="small"
                            />
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="field">
                        <label for="purpose">Purpose</label>
                        <InputText
                            v-model="formData.purpose"
                            class="w-full"
                            size="small"
                            placeholder="Enter purpose of payment"
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
                            placeholder="Enter Amount Paid"
                            @update:modelValue="updateAmountInWords"
                            @input="updateAmountInWords"
                        />
                    </div>
                </div>
                <div class="col-12 md:col-6">
                    <div class="field">
                        <label>Amount in Words</label>
                        <InputText
                            v-model="amountInWords"
                            placeholder="Auto-generate"
                            class="w-full"
                            size="small"
                            readonly
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
                            placeholder="Reference number"
                        />
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-4">
                <Button
                    label="Save Receipt"
                    icon="pi pi-check"
                    @click="createReceipt"
                    :loading="isLoading"
                    :disabled="isLoading"
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
    <!-- Add Customer Dialog -->
    <!-- Add Customer Dialog -->
    <Dialog
        v-model:visible="isCreateCustomerVisible"
        modal
        header="Add Customer"
        class="w-2/3"
        @show="loadCustomerCategories"
    >
        <template #header>
            <div class="flex items-center gap-2">
                <img src="/User.png" alt="Item Customer" class="h-8 w-8 ml-2" />
                <span class="text-xl font-semibold bor">Create Customers</span>
            </div>
        </template>
        <Customers
            :customerCategories="customerCategories"
            redirect_route="receipts.create"
            :mode="'dialog'"
            @customer-created="handleCustomerCreated"
        />
    </Dialog>
</template>

<script setup>
import Customers from "@/Components/Customers.vue";
import { convertCurrencyToWords } from "@/utils/currencyWords";
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
const amountInWords = ref("");
const isCreateCustomerVisible = ref(false);

const props = defineProps({
    customerCategories: {
        type: Array,
        required: true,
        default: () => [],
    },
});
onMounted(async () => {
    await loadCustomers();
    await loadInvoices();
    await loadCustomerCategories();
});

const formData = ref({
    invoice_no: null,
    receipt_no: "",
    receipt_date: new Date(),
    customer_id: null,
    customer_code: "",
    customer_name: "",
    purpose: "",
    amount_paid: null,
    payment_method: null,
    payment_reference_no: null,
});

const customers = ref([]);
const invoices = ref([]);
const customerCategories = ref([]);
const paymentMethods = ref([
    { label: "Cash", value: "Cash" },
    { label: "Bank Transfer", value: "Bank Transfer" },
    { label: "Credit Card", value: "Credit Card" },
]);

const loadCustomers = async () => {
    try {
        const response = await axios.get("/api/customers");
        customers.value = (response.data.customers || []).map((customer) => ({
            ...customer,
            name_with_code: `${customer.name} (${customer.customer_code})`,
        }));
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

const loadCustomerCategories = async () => {
    try {
        const response = await axios.get("/settings/customer-categories");
        customerCategories.value = response.data.categories || [];
        console.log("Loaded categories:", customerCategories.value); // Debug log
    } catch (error) {
        console.error("Error loading customer categories", error);
        toast.add({
            severity: "error",
            summary: "Error",
            detail: "Failed to load customer categories",
            life: 3000,
        });
    }
};

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

const updateAmountInWords = () => {
    if (formData.value.amount_paid) {
        amountInWords.value = convertCurrencyToWords(
            formData.value.amount_paid
        );
    } else {
        amountInWords.value = "";
    }
};

const show = async () => {
    await loadCustomers();
    await loadInvoices();
    await loadCustomerCategories();

    try {
        const receiptNoResponse = await axios.get(route("receipts.create"));
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
        formData.value.invoice_amount = selectedInvoice.amount || 0;
    }
};

const handleCustomerCreated = (newCustomer) => {
    customers.value.push({
        ...newCustomer,
        name_with_code: `${newCustomer.name} (${newCustomer.customer_code})`,
    });

    formData.value.customer_id = newCustomer.id;
    formData.value.customer_code = newCustomer.customer_code;
    isCreateCustomerVisible.value = false;

    toast.add({
        severity: "success",
        summary: "Success",
        detail: "Customer created successfully",
        life: 3000,
    });
};

const isLoading = ref(false);
const createReceipt = async () => {
    isLoading.value = true;

    try {
        // Validate required fields
        if (!formData.value.customer_id) {
            throw new Error("Please select a customer.");
        }
        if (!formData.value.amount_paid || formData.value.amount_paid <= 0) {
            throw new Error("Please enter a valid amount greater than zero.");
        }
        if (!formData.value.payment_method) {
            throw new Error("Please select a payment method.");
        }
        if (!formData.value.receipt_date) {
            throw new Error("Please select a valid date.");
        }

        const payload = {
            invoice_no: formData.value.invoice_no || null, // null instead of empty string
            receipt_no: formData.value.receipt_no,
            receipt_date: new Date(formData.value.receipt_date)
                .toISOString()
                .split("T")[0],
            customer_id: formData.value.customer_id,
            customer_code: formData.value.customer_code,
            purpose: formData.value.purpose || null,
            amount_paid: Number(formData.value.amount_paid), // Ensure number
            amount_in_words: amountInWords.value,
            payment_method: formData.value.payment_method,
            payment_reference_no: formData.value.payment_reference_no || null,
        };

        console.log("Payload being sent:", payload);

        const response = await axios.post(route("receipts.store"), payload);

        dialogVisible.value = false;
        emit("receipt-created", {
            receipt: response.data.receipt,
            shouldReload: true,
        });

        formData.value = {
            invoice_no: null,
            receipt_no: String(response.data.nextReceiptNo || 1).padStart(
                8,
                "0"
            ),
            receipt_date: new Date(),
            customer_id: null,
            customer_code: "",
            customer_name: "",
            purpose: "",
            amount_paid: null,
            payment_method: null,
            payment_reference_no: null,
        };

        toast.add({
            severity: "success",
            summary: "Success",
            detail: "Receipt created successfully.",
            life: 3000,
        });
    } catch (error) {
        console.error("Full validation errors:", error.response?.data?.errors);

        let errorMessage = "Failed to create receipt";
        if (error.response?.data?.errors) {
            const errors = Object.values(error.response.data.errors).flat();
            errorMessage = errors.join("\n");
        } else if (error.response?.data?.message) {
            errorMessage = error.response.data.message;
        } else if (error.message) {
            errorMessage = error.message;
        }

        toast.add({
            severity: "error",
            summary: "Error",
            detail: errorMessage,
            life: 5000,
        });
    } finally {
        isLoading.value = false;
    }
};
</script>

<style scoped>
.required:after {
    content: " *";
    color: red;
}
</style>
