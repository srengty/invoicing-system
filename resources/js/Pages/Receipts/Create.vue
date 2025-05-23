<template>
    <Toast position="top-center" />
    <Dialog
        v-model:visible="dialogVisible"
        :modal="true"
        :style="{ width: '50vw' }"
        @hide="closeDialog"
    >
        <template #header>
            <div style="display: flex; align-items: center; justify-content: flex-start;">
            <i class="pi pi-receipt text-success" style="color: #28a745; margin-right: 8px;" ></i>
            <span>{{ isEditMode ? 'Edit Receipt' : 'Create New Receipt' }}</span>
            </div>
        </template>

        <form @submit.prevent="isEditMode ? updateReceipt() : createReceipt()">
            <div class="grid gap-2 p-4 pt-0 pb-0">
                <div>
                    <div class="flex w-full mb-2">
                        <div class="field w-full">
                            <label for="invoice_no">Invoice(s)</label>
                            <div class="flex flex-col gap-2">
                                <div v-for="(invoice, index) in formData.invoices" :key="index" class="flex gap-2">
                                    <Dropdown
                                        v-model="formData.invoices[index]"
                                        :options="availableInvoices"
                                        :optionLabel="invoiceLabel" 
                                        optionValue="invoice_no"
                                        placeholder="Select Invoice"
                                        class="w-full"
                                        filter
                                        filterPlaceholder="Search invoices"
                                        showClear
                                        @change="updateInvoiceDetailsMulti"
                                        size="small"
                                    />

                                    <!-- Show Add button only on the last invoice row -->
                                    <Button
                                        v-if="index === formData.invoices.length - 1"
                                        icon="pi pi-plus"
                                        @click="addInvoice"
                                        severity="success"
                                        size="small"
                                        outlined
                                    />

                                    <!-- Show Remove button on all rows if more than 1 invoice -->
                                    <Button
                                        v-if="formData.invoices.length > 1"
                                        icon="pi pi-minus"
                                        @click="removeInvoice(index)"
                                        severity="danger"
                                        size="small"
                                        outlined
                                    />
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>

                <div>
                    <div class="flex w-full gap-6">
                        <div class="w-1/2">
                            <div class="field">
                                <label for="receipt_no" class="required">Receipt No</label>
                                <InputText
                                    v-model="formData.receipt_no"
                                    class="w-full"
                                    :readonly="true"
                                    size="small"
                                />
                            </div>
                        </div>
                        <div class="field w-1/2">
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
                    </div>
                </div>

                <div>
                    <div class="flex w-full gap-6">
                        <div class="w-full">
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
                    </div>
                </div>

                <div class="p-2 ">
                    <hr />
                </div>

                <div>
                    <div class="flex w-full gap-6">
                        <div class="field w-1/2">
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
                                :disabled="hasInvoicesSelected"
                                filter
                                filterPlaceholder="Search customers"
                                showClear
                                @change="updateCustomerDetails"
                                size="small"
                            />
                            <Button
                                icon="pi pi-plus"
                                title="add customer"
                                label="Customer"
                                raised
                                @click="isCreateCustomerVisible = true"
                                class="w-44 start"
                                size="small"
                            />
                        </div>
                    </div>
                        </div>
                        <div class="w-1/2">
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
                    </div>
                </div>

                <div class="p-2">
                    <hr />
                </div>

                <div>
                    <div class="flex w-full gap-6">
                        <div class="field w-1/2">
                            <div class="field">
                                <label>Paid Amount</label>
                                <InputNumber
                                    v-model="formData.paid_amount"
                                    class="w-full"
                                    mode="currency"
                                    :currency="selectedCurrency"
                                    locale="en-US"
                                    placeholder="Enter amount paid"
                                    size="small"
                                    @input="updateAmountInWords"
                                />
                            </div>
                        </div>
                        <div class="w-1/2">
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
                    </div>
                </div>

                <div>
                    <div class="flex w-full gap-6">
                        <div class="field w-1/2">
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
                        <div class="w-1/2">
                            <div class="field">
                                <label for="payment_reference_no">Payment Reference No (if any)</label>
                                <InputText
                                    v-model="formData.payment_reference_no"
                                    class="w-full"
                                    size="small"
                                    placeholder="Reference number"
                                />
                            </div>
                        </div>
                    </div>
                </div>
                
                <div
                    class="col-12 md:col-6"
                    v-if="formData.payment_schedule_ids?.length"
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
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <Button
                        label="Cancel"
                        icon="pi pi-times"
                        @click="closeDialog"
                        class="p-button-text"
                    />
                    <Button
                        :label="isEditMode ? 'Update Receipt' : 'Save Receipt'"
                        icon="pi pi-check"
                        @click="isEditMode ? updateReceipt() : createReceipt()"
                        :loading="isLoading"
                        :disabled="isLoading || !canSubmit"
                    />
                </div>
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

const formData = ref({
    invoices: [null], // Array to hold multiple invoice selections
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
const customerCategories = ref([]);
const paymentMethods = ref([
    { label: "Cash", value: "Cash" },
    { label: "Bank Transfer", value: "Bank Transfer" },
    { label: "Credit Card", value: "Credit Card" },
]);

// Computed properties
const availableInvoices = computed(() => {
    return invoices.value.filter(invoice => {
        // Filter out invoices that are already selected (except the current one)
        const isSelected = formData.value.invoices.some(
            selectedInvoiceNo => selectedInvoiceNo === invoice.invoice_no
        );
        return !isSelected;
    });
});

const selectedCurrency = computed(() => {
    if (!formData.value.invoices || formData.value.invoices.length === 0) return 'USD';
    
    // If multiple invoices, check if they all have same currency
    const selectedInvoices = formData.value.invoices
        .map(invoiceNo => invoices.value.find(inv => inv.invoice_no === invoiceNo))
        .filter(Boolean);
    
    if (selectedInvoices.length === 0) return 'USD';
    
    const firstCurrency = selectedInvoices[0].currency;
    const allSameCurrency = selectedInvoices.every(inv => inv.currency === firstCurrency);
    
    return allSameCurrency ? firstCurrency : 'USD';
});

const hasInvoicesSelected = computed(() => {
    return formData.value.invoices.some(inv => inv !== null);
});

const canSubmit = computed(() => {
    return formData.value.receipt_no && 
           formData.value.receipt_date && 
           formData.value.customer_id && 
           formData.value.paid_amount > 0 &&
           formData.value.payment_method;
});

onMounted(async () => {
    await loadCustomers();
    await loadInvoices();
    await loadCustomerCategories();
});

const resetForm = () => {
    formData.value = {
        invoices: [null],
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
            .filter((invoice) => Number(invoice.grand_total) > Number(invoice.paid_amount))
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

const addInvoice = () => {
    formData.value.invoices.push(null);
};

const removeInvoice = (index) => {
    formData.value.invoices.splice(index, 1);
    updateInvoiceDetailsMulti();
};

const updateInvoiceDetailsMulti = () => {
    const selectedInvoiceNos = formData.value.invoices.filter(Boolean);
    
    if (selectedInvoiceNos.length === 0) {
        resetCustomerAndAmounts();
        return;
    }

    const selectedInvoices = selectedInvoiceNos
        .map(no => invoices.value.find(inv => inv.invoice_no === no))
        .filter(Boolean);

    if (selectedInvoices.length === 0) {
        resetCustomerAndAmounts();
        return;
    }

    // Check if all selected invoices have the same customer
    const firstCustomerId = selectedInvoices[0].customer_id;
    const allSameCustomer = selectedInvoices.every(inv => inv.customer_id === firstCustomerId);
    
    if (!allSameCustomer) {
        toast.add({
            severity: 'warn',
            summary: 'Warning',
            detail: 'All invoices must belong to the same customer',
            life: 3000
        });
        return;
    }

    const firstInvoice = selectedInvoices[0];
    formData.value.customer_id = firstInvoice.customer_id;
    formData.value.customer_code = firstInvoice.customer_code || "";
    formData.value.customer_name = firstInvoice.customer_name || "";

    // Sum remaining balances from all selected invoices
    const totalRemaining = selectedInvoices.reduce(
        (sum, inv) => sum + (Number(inv.grand_total) - Number(inv.paid_amount)),
        0
    );
    formData.value.paid_amount = totalRemaining;

    // Combine payment schedule IDs (unique)
    const combinedScheduleIds = selectedInvoices.flatMap(
        inv => inv.payment_schedules?.map(ps => ps.id) || []
    );
    formData.value.payment_schedule_ids = [...new Set(combinedScheduleIds)];

    updateAmountInWords();
};

const resetCustomerAndAmounts = () => {
    formData.value.customer_id = null;
    formData.value.customer_code = "";
    formData.value.customer_name = "";
    formData.value.paid_amount = null;
    formData.value.payment_schedule_ids = [];
    amountInWords.value = "";
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
                invoices: newVal.invoice_no ? [newVal.invoice_no] : [null],
                receipt_no: newVal.receipt_no,
                receipt_date: new Date(newVal.receipt_date),
                customer_id: newVal.customer_id,
                customer_code: newVal.customer_code,
                customer_name: newVal.customer?.name || "",
                purpose: newVal.purpose || "",
                paid_amount: Number(newVal.paid_amount),
                payment_method: newVal.payment_method,
                payment_reference_no: newVal.payment_reference_no || null,
                payment_schedule_ids: newVal.payment_schedule_ids || [],
            };

            updateAmountInWords();

            if (newVal.invoice_no) {
                const existingInvoice = invoices.value.find(
                    inv => inv.invoice_no === newVal.invoice_no
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

const updateCustomerDetails = () => {
    const selectedCustomer = customers.value.find(
        customer => customer.id === formData.value.customer_id
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
            formData.value.paid_amount,
            selectedCurrency.value
        );
    } else {
        amountInWords.value = "";
    }
};

watch(
    () => formData.value.paid_amount,
    (newValue) => {
        updateAmountInWords();
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
    resetForm();
};

const invoiceLabel = (invoice) => {
    if (!invoice) return "";
    let label = `${invoice.invoice_no} - ${invoice.customer_code} (${invoice.customer_name})`;
    const remaining = Number(invoice.grand_total) - Number(invoice.paid_amount);
    label += ` - ${remaining.toFixed(2)} ${invoice.currency || 'USD'} remaining`;
    return label;
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

const updateReceipt = async () => {
    isLoading.value = true;

    try {
        const payload = {
            ...formData.value,
            receipt_no: String(formData.value.receipt_no),
            invoice_no: formData.value.invoices[0] || null, // For edit, we only support single invoice
            receipt_date: formData.value.receipt_date
                .toISOString()
                .split("T")[0],
            amount_in_words: amountInWords.value,
            payment_schedule_ids: formData.value.payment_schedule_ids || [],
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

const paymentSchedules = ref([
  // Your payment schedules objects with id and paid_amount properties
]);

const syncPaidAmountToSchedules = () => {
    const schedules = paymentSchedules.value.filter(schedule =>
        formData.value.payment_schedule_ids.includes(schedule.id)
    );
    const totalSchedules = schedules.length;
    if (totalSchedules === 0) return;

    // Divide paid_amount evenly or based on schedule amount
    const perScheduleAmount = Number(formData.value.paid_amount) / totalSchedules;

    schedules.forEach(schedule => {
        schedule.paid_amount = perScheduleAmount;
    });
};


const createReceipt = async () => {
    isLoading.value = true;

    try {
        syncPaidAmountToSchedules();
        const invoicesToSend = formData.value.invoices.length
            ? formData.value.invoices.map(inv => inv || null)
            : [null];  // ensure at least one item, null means no invoice selected

        const payload = {
            invoices: invoicesToSend,
            receipt_no: formData.value.receipt_no,
            receipt_date: new Date(formData.value.receipt_date)
                .toISOString()
                .split("T")[0],
            customer_id: formData.value.customer_id,
            paid_amount: Number(formData.value.paid_amount),
            amount_in_words: amountInWords.value,
            payment_method: formData.value.payment_method,
            payment_reference_no: formData.value.payment_reference_no || null,
            payment_schedule_ids: formData.value.payment_schedule_ids || [],
            payment_schedules: paymentSchedules.value.filter(schedule =>
                formData.value.payment_schedule_ids.includes(schedule.id)
            )
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
        .map(id => `PS-${String(id).padStart(6, "0")}`)
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