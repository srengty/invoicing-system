<template>
    <Toast position="top-center" />
    <Dialog
        v-model:visible="dialogVisible"
        :header="isEditMode ? 'Edit Receipt' : 'Create New Receipt'"
        :modal="true"
        :style="{ width: '50vw' }"
        @hide="closeDialog"
        :draggable="false"
        :resizable="false"
        :position="'center'"
        :closeOnEscape="false"
    >
        <form @submit.prevent="isEditMode ? updateReceipt() : createReceipt()">
            <div class="grid">
                <div class="col-12 md:col-6">
                    <div class="field">
                        <label for="invoice_no">Invoice No</label>
                        <div class="flex gap-2">
                            <Dropdown
                                v-if="!isEditMode || !formData.invoice_no"
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
                            <!-- Show read-only input when editing with invoice -->
                            <InputText
                                v-else
                                v-model="formData.invoice_no"
                                class="w-full"
                                readonly
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
                <div
                    class="col-12 md:col-6"
                    v-if="
                        formData.invoice_no &&
                        selectedInvoice?.payment_schedules?.length
                    "
                >
                    <div class="field">
                        <label>Payment Schedules</label>
                        <div class="p-inputgroup">
                            <InputText
                                :value="formattedScheduleId"
                                class="w-full"
                                readonly
                                size="small"
                            />
                        </div>
                        <!-- Optional: Show detailed list -->
                        <!-- <ul class="pl-2 mt-2">
                            <li
                                v-for="ps in selectedInvoice.payment_schedules"
                                :key="ps.id"
                                class="text-sm"
                            >
                                PS-{{ String(ps.id).padStart(6, "0") }} -
                                {{ ps.status }} - Paid: {{ ps.paid_amount }} /
                                {{ ps.amount }}
                            </li>
                        </ul> -->
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
                            :readonly="!!formData.invoice_no"
                            mode="decimal"
                            :minFractionDigits="2"
                            :maxFractionDigits="2"
                            thousandSeparator=","
                            decimalSeparator="."
                            suffix=" ( KHR )"
                            size="small"
                            @input="updateAmountInWords"
                        />
                        <!-- Else allow manual input -->
                        <InputNumber
                            v-else
                            v-model="formData.paid_amount"
                            class="w-full"
                            placeholder="Enter amount paid "
                            mode="decimal"
                            :minFractionDigits="2"
                            :maxFractionDigits="2"
                            thousandSeparator=","
                            decimalSeparator="."
                            suffix=" ( KHR )"
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
                    :label="isEditMode ? 'Update Receipt' : 'Save Receipt'"
                    icon="pi pi-check"
                    @click="isEditMode ? updateReceipt() : createReceipt()"
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
        :draggable="false"
        :resizable="false"
        :position="'center'"
        :closeOnEscape="false"
    >
        <template #header>
            <div class="flex items-center gap-2">
                <img src="/User.png" alt="Item Customer" class="h-8 w-8 ml-2" />
                <span class="text-xl font-semibold bor">Create Customers</span>
            </div>
        </template>

        <Customers
            :customerCategories="customerCategories"
            redirect_route="receitps.create"
            :mode="'create'"
        />
    </Dialog>
</template>

<script setup>
import Customers from "@/Components/CustomersReceipts.vue";
import { convertCurrencyToWords } from "@/utils/currencyWords";
import { ref, computed, onMounted, defineExpose, watch } from "vue";
import { router } from "@inertiajs/vue3";
import { Inertia } from "@inertiajs/inertia";
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
const isEditMode = ref(false);

const props = defineProps({
    customerCategories: {
        type: Array,
        default: () => [],
    },
    receipt: {
        type: Object,
        default: null,
    },
    visible: { type: Boolean, required: true },
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
    paid_amount: null,
    payment_method: null,
    payment_reference_no: null,
    payment_schedule_ids: [],
});

const customers = ref([]);
const invoices = ref([]);
const paymentMethods = ref([
    { label: "Cash", value: "Cash" },
    { label: "Bank Transfer", value: "Bank Transfer" },
    { label: "Credit Card", value: "Credit Card" },
]);
const showSuccessToast = () => {
    toast.add({
        severity: "success",
        summary: "Test Toast",
        detail: "This is a test",
        life: 3000,
    });
};

showSuccessToast();
const resetForm = () => {
    formData.value = {
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
        payment_schedule_ids: [],
    };
    amountInWords.value = "";
};

const loadCustomers = async () => {
    try {
        const response = await axios.get("/settings/customer-categories");
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
            .filter(
                (invoice) =>
                    Number(invoice.grand_total) > Number(invoice.paid_amount)
            )
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

const customerCategories = ref([]);
const loadCustomerCategories = async () => {
    try {
        const response = await axios.get("/settings/customer-categories");
        customerCategories.value = response.data.categories || [];
    } catch (error) {
        toast.add({
            severity: "error",
            summary: "Error",
            detail: "Failed to load customer categories",
        });
    }
};

watch(
    () => props.receipt,
    (newVal) => {
        if (newVal) {
            isEditMode.value = true;
            formData.value = {
                invoice_no: newVal.invoice_no,
                receipt_no: newVal.receipt_no,
                receipt_date: new Date(newVal.receipt_date),
                customer_id: newVal.customer_id,
                customer_code: newVal.customer_code,
                customer_name: newVal.customer?.name || "",
                purpose: newVal.purpose || "",
                paid_amount: Number(newVal.paid_amount),
                payment_method: newVal.payment_method,
                payment_reference_no: newVal.payment_reference_no || null,
            };

            // Update amount in words
            updateAmountInWords();

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
                            customer_name: newVal.customer?.name || "",
                            paid_amount: newVal.paid_amount,
                            has_receipt: true,
                        },
                    ];
                }
            }
        } else {
            isEditMode.value = false;
            resetForm();
        }
    },
    { immediate: true, deep: true }
);

watch(
    () => props.receipt,
    (newReceipt) => {
        if (newReceipt) {
            isEditMode.value = true;
        }
    }
);

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

    if (!isEditMode.value) {
        try {
            const receiptNoResponse = await axios.get(route("receipts.create"));
            formData.value.receipt_no = String(
                receiptNoResponse.data.nextReceiptNo
            ).padStart(8, "0");
        } catch (error) {
            console.error("Error getting receipt number", error);
        }
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
        payment_schedule_ids: [],
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
    const selectedNo = formData.value.invoice_no;

    if (!selectedNo) {
        resetInvoiceFields();
        return;
    }

    const invoice = invoices.value.find((inv) => inv.invoice_no === selectedNo);

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

    const remaining = Number(invoice.grand_total) - Number(invoice.paid_amount);
    if (remaining <= 0) {
        toast.add({
            severity: "warn",
            summary: "Invoice Fully Paid",
            detail: "This invoice is already fully paid.",
            life: 4000,
        });
    }

    selectedInvoice.value = invoice;
    formData.value.paid_amount = remaining;
    formData.value.customer_id = invoice.customer_id;
    formData.value.customer_code = invoice.customer_code || "";
    formData.value.customer_name = invoice.customer_name || "";
    formData.value.payment_schedule_ids = (invoice.payment_schedules || []).map(
        (ps) => ps.id
    );

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
    let label = `${invoice.invoice_no} - ${invoice.customer_code} (${invoice.customer_name})`;
    if (invoice.payment_schedule_ids) {
        label += ` [Schedule: ${invoice.payment_schedule_ids}]`;
    }
    return label;
};
const customerLabel = (customer) => {
    if (!customer) return "";
    return ` ${customer.name}`;
};

function handleCustomerCreated(newCustomer) {
    customers.value.push({
        ...newCustomer,
        name_with_code: `${newCustomer.name} (${newCustomer.customer_code})`,
    });
    formData.value.customer_id = newCustomer.id;
    formData.value.customer_code = newCustomer.customer_code;
    isCreateCustomerVisible.value = false;
    toast.add({
        severity: "success",
        summary: "Added",
        detail: `${newCustomer.name} selected`,
    });
}

const isLoading = ref(false);

const updateReceipt = async () => {
    isLoading.value = true;

    try {
        const payload = {
            ...formData.value,
            receipt_no: String(formData.value.receipt_no),
            invoice_no: formData.value.invoice_no
                ? String(formData.value.invoice_no)
                : null,
            receipt_date: formData.value.receipt_date
                .toISOString()
                .split("T")[0],
            amount_in_words: amountInWords.value,
            payment_schedule_ids: formData.value.payment_schedule_ids || [], // Include payment schedule IDs
        };

        const response = await axios.put(
            route("receipts.update", { receipt_no: formData.value.receipt_no }),
            payload
        );

        toast.add({
            severity: "success",
            summary: "Success",
            detail: `Receipt ${formData.value.receipt_no} updated successfully`,
            life: 3000,
        });

        Inertia.visit(route("receipts.index"), {
            method: "get",
            preserveState: true,
        });
        closeDialog();
    } catch (error) {
        console.error("Update error:", error.response);
        let errorDetail = "Failed to update receipt";
        if (error.response?.status === 422) {
            errorDetail = Object.values(error.response.data.errors).join(" ");
        } else if (error.response?.data?.message) {
            errorDetail = error.response.data.message;
        } else if (error.message) {
            errorDetail = error.message;
        }

        toast.add({
            severity: "error",
            summary: "Error",
            detail: errorDetail,
            life: 5000,
        });
    } finally {
        isLoading.value = false;
    }
};

const createReceipt = async () => {
    isLoading.value = true;

    try {
        const payload = {
            invoice_no: formData.value.invoice_no,
            receipt_no: formData.value.receipt_no,
            receipt_date: new Date(formData.value.receipt_date)
                .toISOString()
                .split("T")[0],
            customer_id: formData.value.customer_id,
            paid_amount: Number(formData.value.paid_amount),
            amount_in_words: amountInWords.value,
            payment_method: formData.value.payment_method,
            payment_reference_no: formData.value.payment_reference_no || null,
            payment_schedule_ids: formData.value.payment_schedule_ids || [], // Include payment schedule IDs
        };

        const response = await axios.post(route("receipts.store"), payload);

        toast.add({
            severity: "success",
            summary: "Success",
            detail: `Receipt ${response.data.receipt_no} created successfully`,
            life: 3000,
        });

        dialogVisible.value = false;
        emit("receipt-created", {
            receipt: response.data.receipt,
            shouldReload: true,
        });
    } catch (error) {
        console.error("Error:", error);
        let errorDetail = "Failed to create receipt";
        if (error.response?.status === 422) {
            errorDetail = Object.values(error.response.data.errors).join(" ");
        } else if (error.response?.data?.message) {
            errorDetail = error.response.data.message;
        } else if (error.message) {
            errorDetail = error.message;
        }

        toast.add({
            severity: "error",
            summary: "Error",
            detail: errorDetail,
            life: 5000,
        });
    } finally {
        isLoading.value = false;
    }
};

const formattedScheduleId = computed(() => {
    if (!formData.value.payment_schedule_ids?.length) return "No schedules";
    return formData.value.payment_schedule_ids
        .map((id) => `PS-${String(id).padStart(6, "0")}`)
        .join(", ");
});
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
