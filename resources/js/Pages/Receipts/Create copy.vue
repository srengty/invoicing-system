<template>
    <Head title="Create Receipt" />
    <GuestLayout>
        <NavbarLayout />

        <div class="py-3">
            <Breadcrumb :model="breadcrumbItems" class="border-none bg-transparent p-0">
                <template #item="{ item }">
                    <Link
                        :href="item.to"
                        class="text-sm hover:text-primary flex items-center gap-1"
                    >
                        <i v-if="item.icon" :class="item.icon"></i>
                        {{ item.label }}
                    </Link>
                </template>
            </Breadcrumb>
        </div>

        <Toast position="top-center" group="tc" />
        <Toast position="top-right" group="tr" />

        <!-- Receipt Creation Form -->
        <Dialog
            v-model:visible="dialogVisible"
            :style="{ width: '50vw' }"
            header="Create New Receipt"
            :modal="true"
            :dismissableMask="true"
            class="text-sm"
        >
            <form @submit.prevent="createReceipt">
                <div class="grid">
                    <!-- Invoice No -->
                    <div class="col-12 md:col-6">
                        <div class="field">
                            <label for="invoice_no">Invoice No *</label>
                            <Dropdown
                                v-model="formData.invoice_no"
                                :options="invoices"
                                optionLabel="invoice_no"
                                optionValue="invoice_no"
                                placeholder="Select Invoice"
                                class="w-full"
                            />
                        </div>
                    </div>

                    <!-- Receipt No -->
                    <div class="col-12 md:col-6">
                        <div class="field">
                            <label for="receipt_no">Receipt No *</label>
                            <InputText
                                v-model="formData.receipt_no"
                                :disabled="true"
                                class="w-full"
                                placeholder="Auto-generate"
                            />
                        </div>
                    </div>

                    <!-- Date -->
                    <div class="col-12 md:col-6">
                        <div class="field">
                            <label for="receipt_date">Date *</label>
                            <Calendar
                                v-model="formData.receipt_date"
                                :dateFormat="'yy-mm-dd'"
                                showIcon
                                class="w-full"
                            />
                        </div>
                    </div>

                    <!-- Customer -->
                    <div class="col-12 md:col-6">
                        <div class="field">
                            <label for="customer_id">Customer / Organization *</label>
                            <AutoComplete
                                v-model="formData.customer_id"
                                :suggestions="filteredCustomers"
                                field="name"
                                :complete-method="filterCustomer"
                                placeholder="Search for Customer"
                                class="w-full"
                            />
                        </div>
                    </div>

                    <!-- Amount Paid -->
                    <div class="col-12 md:col-6">
                        <div class="field">
                            <label for="amount_paid">Amount Paid *</label>
                            <InputNumber
                                v-model="formData.amount_paid"
                                mode="currency"
                                currency="USD"
                                locale="en-US"
                                class="w-full"
                            />
                        </div>
                    </div>

                    <!-- Payment Method -->
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
                            />
                        </div>
                    </div>

                    <!-- Payment Reference No -->
                    <div class="col-12 md:col-6">
                        <div class="field">
                            <label for="payment_reference_no">Payment Reference No (if any)</label>
                            <InputText
                                v-model="formData.payment_reference_no"
                                class="w-full"
                            />
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <template >
                    <Button
                        label="Save Receipt"
                        icon="pi pi-check"
                        @click="createReceipt"
                        severity="success"
                    />
                    <Button
                        label="Cancel"
                        icon="pi pi-times"
                        @click="closeDialog"
                        class="p-button-text"
                    />
                </template>
            </form>
        </Dialog>
    </GuestLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import { route } from "ziggy-js";
import { useToast } from "primevue/usetoast";
import { usePage } from "@inertiajs/vue3";
import { Dialog, Dropdown, InputText, InputNumber, AutoComplete, Calendar, Button } from "primevue";
import axios from "axios";

const toast = useToast();
const page = usePage();

// Form Data
const formData = ref({
    invoice_no: null,
    receipt_no: "", // Auto-generate
    receipt_date: new Date(),
    customer_id: null,
    amount_paid: null,
    payment_method: null,
    payment_reference_no: null,
});

// Dropdown options
const invoices = ref([]);
const customers = ref([]);
const paymentMethods = ref([
    { label: "Cash", value: "cash" },
    { label: "Bank Transfer", value: "bank_transfer" },
    { label: "Credit Card", value: "credit_card" },
]);

// Dialog visibility
const dialogVisible = ref(false);

// Fetch invoices and customers data on page load
axios.get(route("invoices.index")).then((response) => {
    invoices.value = response.data;
});
axios.get(route("customers.index")).then((response) => {
    customers.value = response.data;
});

// Filter customers for autocomplete
const filteredCustomers = computed(() => {
    return customers.value.filter(customer =>
        customer.name.toLowerCase().includes(formData.value.customer_id?.toLowerCase() || "")
    );
});

// Open dialog for creating receipt
const openCreate = () => {
    dialogVisible.value = true;
};

// Close dialog
const closeDialog = () => {
    dialogVisible.value = false;
};

// Auto-generate receipt number (incremental)
const generateReceiptNo = () => {
    const currentYear = new Date().getFullYear();
    const count = formData.value.receipt_no.length + 1;
    formData.value.receipt_no = `R${currentYear}${String(count).padStart(5, "0")}`;
};

// Create new receipt
const createReceipt = () => {
    // Validation
    if (!formData.value.invoice_no || !formData.value.customer_id || !formData.value.amount_paid) {
        toast.add({ severity: "error", summary: "Error", detail: "Please fill all required fields", life: 3000 });
        return;
    }

    axios.post(route("receipts.store"), formData.value)
        .then(response => {
            toast.add({ severity: "success", summary: "Success", detail: "Receipt created successfully", life: 3000 });
            dialogVisible.value = false;
        })
        .catch(error => {
            toast.add({ severity: "error", summary: "Error", detail: "Failed to create receipt", life: 3000 });
        });
};
</script>

<style scoped>
/* Add your custom styles */
</style>
