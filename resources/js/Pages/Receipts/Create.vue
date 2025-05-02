<template>
    <Dialog
        v-model:visible="dialogVisible"
        :header="isEditMode ? 'Edit Receipt' : 'Create New Receipt'"
        :modal="true"
        :style="{ width: '50vw' }"
        @hide="closeDialog"
    >
        <form @submit.prevent="isEditMode ? updateReceipt() : createReceipt()">
            <div class="grid">
                <div class="col-12 md:col-6">
                    <div class="field">
                        <label for="invoice_no">Invoice No</label>
                        <div class="flex gap-2">
                            <Dropdown
                                v-model="formData.invoice_no"
                                :options="invoices"
                                :optionLabel="invoiceLabel"
                                optionValue="invoice_no"
                                placeholder="Select Invoice"
                                class="w-full"
                                filterPlaceholder="Search invoices"
                                showClear
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
                                v-model="formData.customer_id"
                                :options="customers"
                                :optionLabel="customerLabel"
                                optionValue="id"
                                placeholder="Search customer..."
                                class="w-full"
                                :disabled="!!formData.invoice_no"
                                filter
                                filterPlaceholder="Search customers"
                                showClear
                                @change="updateCustomerDetails"
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
                        <label>Paid Amount</label>
                        <!-- If invoice is selected, show readonly amount -->
                        <InputNumber
                            v-if="formData.invoice_no"
                            v-model="formData.paid_amount"
                            class="w-full"
                            mode="currency"
                            :currency="selectedInvoice?.currency || 'USD'"
                            locale="en-US"
                            readonly
                            size="small"
                            @input="updateAmountInWords"
                        />
                        <!-- Else allow manual input -->
                        <InputNumber
                            v-else
                            v-model="formData.paid_amount"
                            class="w-full"
                            mode="currency"
                            currency="USD"
                            locale="en-US"
                            placeholder="Enter amount paid  "
                            size="small"
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
const dialogVisible = ref(false);
const emit = defineEmits(["receipt-created", "receipt-updated"]);
const amountInWords = ref("");
const isCreateCustomerVisible = ref(false);

const props = defineProps({
    customerCategories: {
        type: Array,
        default: () => [],
    },
    receipt: {
        type: Object,
        default: null,
    },
});
onMounted(async () => {
    await loadCustomers();
    await loadInvoices();
    await loadCustomerCategories();
});
const isEditMode = ref(false);
const formData = ref({
    invoice_no: null,
    receipt_no: "",
    receipt_date: new Date(),
    customer_id: null,
    customer_code: "",
    customer_name: "",
    purpose: "",
    paid_amount: null,
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
        const response = await axios.get(route("receipts.create"));
        invoices.value = (response.data.invoices || [])
            .filter((invoice) => !invoice.has_receipt) // Exclude invoices with receipts
            .map((invoice) => ({
                ...invoice,
                label: `${invoice.invoice_no} - ${invoice.customer_name}`,
            }));
    } catch (error) {
        console.error("Error loading invoices", error);
        toast.add({
            severity: "error",
            summary: "Error",
            detail: "Failed to load invoices",
            life: 3000,
        });
    }
};

const loadCustomerCategories = async () => {
    try {
        const response = await axios.get("/settings/customer-categories");
        customerCategories.value = response.data.categories || [];
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

watch(
    () => props.receipt,
    (newVal) => {
        if (newVal) {
            isEditMode.value = true;
            formData.value = {
                ...newVal,
                customer_id: newVal.customer?.id,
            };

            // If editing, ensure the current invoice is in the list
            if (newVal.invoice_no) {
                const existingInvoice = invoices.value.find(
                    (inv) => inv.invoice_no === newVal.invoice_no
                );

                if (!existingInvoice) {
                    invoices.value = [
                        ...invoices.value,
                        {
                            invoice_no: newVal.invoice_no,
                            customer_code: newVal.customer_code,
                            customer_name: newVal.customer_name,
                            paid_amount: newVal.paid_amount,
                            has_receipt: true, // Mark as having receipt
                        },
                    ];
                }
            }
        } else {
            isEditMode.value = false;
        }
    },
    { immediate: true }
);
const updateReceipt = async () => {
    emit("receipt-updated", {});
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
    if (formData.value.paid_amount) {
        amountInWords.value = convertCurrencyToWords(
            formData.value.paid_amount
        );
    } else {
        amountInWords.value = "";
    }
};
watch(
    () => formData.value.paid_amount,
    (newValue) => {
        updateAmountInWords(); // This will be triggered automatically whenever paid_amount changes
    }
);

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
        paid_amount: null,
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

const selectedInvoice = ref(null);
const updateInvoiceDetails = () => {
    if (!formData.value.invoice_no) {
        resetInvoiceFields();
        return;
    }

    const invoice = invoices.value.find(
        (inv) => inv.invoice_no === formData.value.invoice_no
    );

    if (!invoice) {
        toast.add({
            severity: "error",
            summary: "Error",
            detail: "Selected invoice not found",
            life: 3000,
        });
        resetInvoiceFields();
        return;
    }

    // Check if invoice already has a receipt
    if (invoice.has_receipt) {
        toast.add({
            severity: "error",
            summary: "Error",
            detail: "This invoice already has a receipt",
            life: 3000,
        });
        resetInvoiceFields();
        return;
    }

    selectedInvoice.value = invoice;
    formData.value.paid_amount = invoice.paid_amount;
    formData.value.customer_id = invoice.customer_id;
    formData.value.customer_code = invoice.customer_code;
    formData.value.customer_name = invoice.customer_name;
    updateAmountInWords();
};

const resetInvoiceFields = () => {
    formData.value.invoice_no = null;
    selectedInvoice.value = null;
    formData.value.customer_id = null;
    formData.value.customer_code = "";
    formData.value.customer_name = "";
    formData.value.paid_amount = null;
    amountInWords.value = "";
};

const invoiceLabel = (invoice) => {
    if (!invoice) return "";
    return `${invoice.invoice_no} - ${invoice.customer_code} (${invoice.customer_name})`;
    // return `${invoice.invoice_no}`;
};
const customerLabel = (customer) => {
    if (!customer) return "";
    return ` ${customer.name}`;
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
        if (!formData.value.paid_amount || formData.value.paid_amount <= 0) {
            throw new Error("Please enter a valid amount greater than zero.");
        }
        if (!formData.value.payment_method) {
            throw new Error("Please select a payment method.");
        }

        // Check if the selected invoice already has a receipt
        if (formData.value.invoice_no) {
            const selectedInv = invoices.value.find(
                (inv) => inv.invoice_no === formData.value.invoice_no
            );

            if (selectedInv?.has_receipt) {
                throw new Error(
                    "This invoice already has a receipt. Please select another invoice."
                );
            }
        }

        // Proceed with the rest of the receipt creation logic...
        const payload = {
            invoice_no: formData.value.invoice_no
                ? String(formData.value.invoice_no)
                : null,
            receipt_no: String(formData.value.receipt_no),
            receipt_date: new Date(formData.value.receipt_date)
                .toISOString()
                .split("T")[0],
            customer_id: Number(formData.value.customer_id),
            customer_code: String(formData.value.customer_code),
            purpose: formData.value.purpose || null,
            paid_amount: Number(
                String(formData.value.paid_amount).replace(/,/g, "")
            ),
            amount_in_words: String(amountInWords.value),
            payment_method: String(formData.value.payment_method),
            payment_reference_no: formData.value.payment_reference_no || null,
        };

        const response = await axios.post(route("receipts.store"), payload);

        // Handle success
        dialogVisible.value = false;
        emit("receipt-created", {
            receipt: response.data.receipt,
            shouldReload: true,
        });

        // Reset invoice_no and selectedInvoice
        formData.value.invoice_no = null; // Reset the invoice_no field
        selectedInvoice.value = null; // Reset the selected invoice reference

        // Reset formData to initial values
        formData.value = {
            invoice_no: null,
            receipt_no: String(parseInt(response.data.nextReceiptNo)).padStart(
                8,
                "0"
            ),
            receipt_date: new Date(),
            customer_id: null,
            customer_code: "",
            customer_name: "",
            paid_amount: null,
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
        console.error("Error:", error);

        let errorMessage =
            error.response?.data?.message ||
            error.response?.data?.error ||
            error.message ||
            "Failed to create receipt";

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
.p-dropdown-item.p-disabled {
    opacity: 0.6;
    background-color: #f8f9fa;
    color: #f8f9fa;
    cursor: not-allowed;
}

.p-dropdown-item.p-disabled .pi-ban {
    color: #dc3545;
    margin-right: 5px;
}

.p-dropdown .p-dropdown-item.p-disabled {
    color: #f8f9fa;
    background-color: #f8f9fa;
}

.p-dropdown .p-dropdown-item.p-disabled:hover {
    background-color: #f8f9fa;
    cursor: not-allowed;
}

.required:after {
    content: " *";
    color: red;
}
</style>
