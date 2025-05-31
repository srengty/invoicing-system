<template>
    <Head title="Invoices" />
    <GuestLayout>
        <NavbarLayout />

        <!-- Breadcrumb -->
        <div class="py-3">
            <Breadcrumb :model="items" class="border-none bg-transparent p-0">
                <template #item="{ item }">
                    <Link
                        :href="item.to"
                        class="text-sm hover:text-primary flex items-start justify-center gap-1"
                    >
                        <i v-if="item.icon" :class="item.icon"></i>
                        {{ item.label }}
                    </Link>
                </template>
            </Breadcrumb>
        </div>

        <div class="invoices space-y-4">
            <!-- Search Filters -->
            <div class="flex justify-end w-full gap-4">
                <Button
                    label="Clear"
                    @click="clearFilters"
                    class="p-button-secondary w-24"
                    icon="pi pi-times"
                    size="small"
                />
            </div>

            <div class="w-full mt-6">
                <div
                    class="grid grid-cols-1 md:grid-cols-3 gap-4 ml-4 mr-4 text-sm pb-3"
                >
                    <!-- Invoice No. Start - Restrict to Numbers -->
                    <div>
                        <label
                            for="invoice_no_start"
                            class="mb-1 w-1/2 font-semibold"
                            >Invoice No. Start:</label
                        >
                        <InputText
                            v-model="filters.invoice_no_start"
                            placeholder="Invoice No From"
                            class="w-full mr-8"
                            showClear
                            size="small"
                        />
                    </div>

                    <!-- Invoice No. End - Restrict to Numbers -->
                    <div>
                        <label
                            for="invoice_no_end"
                            class="mb-1 w-1/2 font-semibold"
                            >Invoice No. End:</label
                        >
                        <InputText
                            v-model="filters.invoice_no_end"
                            placeholder="Invoice No To"
                            class="w-full mr-8"
                            showClear
                            size="small"
                        />
                    </div>

                    <!-- Currency Dropdown -->
                    <div>
                        <label for="currency" class="mb-1 w-1/2 font-semibold"
                            >Currency:</label
                        >
                        <Dropdown
                            v-model="filters.currency"
                            :options="currencyOptions"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Select Currency"
                            class="w-full"
                            showClear
                        />
                    </div>
                </div>

                <div
                    class="grid grid-cols-1 md:grid-cols-3 gap-4 ml-4 mr-4 text-sm pb-3"
                >
                    <div>
                        <label for="end_date" class="mb-1 w-1/2 font-semibold"
                            >Start Date:</label
                        >
                        <Calendar
                            v-model="filters.start_date"
                            placeholder="Start Date"
                            class="w-full mr-8"
                            icon="pi pi-calendar"
                            size="small"
                            :minDate="new Date()"
                            showButtonBar
                            :showIcon="true"
                            :clearable="true"
                        />
                    </div>

                    <!-- End Date -->
                    <div>
                        <label for="end_date" class="mb-1 w-1/2 font-semibold"
                            >End Date:</label
                        >
                        <Calendar
                            v-model="filters.end_date"
                            placeholder="End Date"
                            class="w-full mr-8"
                            icon="pi pi-calendar"
                            size="small"
                            :minDate="new Date()"
                            showButtonBar
                            :showIcon="true"
                            :clearable="true"
                        />
                    </div>

                    <!-- Payment Status -->
                    <div>
                        <label for="status" class="mb-1 w-1/2 font-semibold"
                            >Payment Status:</label
                        >
                        <Dropdown
                            v-model="filters.payment_status"
                            :options="paymentStatusOptions"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Payment Status"
                            class="w-full"
                            showClear
                        />
                    </div>
                </div>

                <div
                    class="grid grid-cols-1 md:grid-cols-3 gap-4 ml-4 mr-4 text-sm"
                >
                    <!-- Customer Name - Restrict to Letters Only -->
                    <div>
                        <label for="customer" class="mb-1 w-1/2 font-semibold"
                            >Customer:</label
                        >
                        <InputText
                            v-model="filters.customer"
                            placeholder="Customer"
                            class="w-full mr-8"
                            size="small"
                        />
                    </div>

                    <!-- Customer Type -->
                    <div>
                        <label
                            for="customer_category_id"
                            class="mb-1 w-1/2 font-semibold"
                            >Customer Type:</label
                        >
                        <Dropdown
                            v-model="filters.customer_category_id"
                            :options="customerTypeOptions"
                            placeholder="Select Customer Type"
                            optionLabel="label"
                            optionValue="value"
                            class="w-full mr-8"
                            showClear
                        />
                    </div>

                    <!-- Income Type -->
                    <div>
                        <label for="income_type" class="w-1/2 font-semibold"
                            >Overdue Status:</label
                        >
                        <Dropdown
                            v-model="filters.overdue_status"
                            :options="[
                                { label: 'All', value: null },
                                { label: 'Overdue', value: 'overdue' },
                                { label: 'Due Today', value: 'today' },
                                { label: 'Upcoming', value: 'upcoming' },
                                { label: 'Fully Paid', value: 'paid' },
                            ]"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Overdue Status"
                            class="w-full"
                            showClear
                        />
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <DataTable
                :value="filteredInvoices"
                paginator
                :rows="5"
                :rowsPerPageOptions="[5, 10, 20, 50]"
                size="small"
                class="text-sm pt-6"
            >
                <Column
                    v-for="col in showColumns"
                    :key="col.field"
                    :field="col.field"
                    :header="col.header"
                    :sortable="col.sortable"
                />
                <Column header="Amount">
                    <template #body="{ data }">
                        <span
                            :class="{
                                'text-blue-500': computeAmountDue(data) >= 0,
                            }"
                        >
                            {{ formatCurrency(data.grand_total) }} (KHR)
                        </span>
                    </template>
                </Column>

                <Column header="Amount Paid">
                    <template #body="{ data }">
                        <span
                            :class="{
                                'text-green-500': computeAmountDue(data) == 0,
                                'text-yellow-500':
                                    computeAmountDue(data) > 0 &&
                                    data.paid_amount > 0,
                                'text-gray-500': data.paid_amount == 0,
                            }"
                        >
                            {{ formatCurrency(data.paid_amount) }} (KHR)
                        </span>
                    </template>
                </Column>

                <Column header="Amount Due">
                    <template #body="{ data }">
                        <span
                            :class="{
                                'text-red-500': computeAmountDue(data) > 0,
                            }"
                        >
                            {{ formatCurrency(computeAmountDue(data)) }} (KHR)
                        </span>
                    </template>
                </Column>
                <Column header="Overdue">
                    <template #body="{ data }">
                        <span :class="over_due(data).class">{{
                            over_due(data).text
                        }}</span>
                    </template>
                </Column>
                <Column field="payment_status" header="Payment Status">
                    <template #body="{ data }">
                        <span
                            class="p-2 border rounded w-36 h-8 flex items-center justify-center gap-2 text-sm"
                            :class="statusClass(paymentStatus(data))"
                        >
                            <i :class="statusIcon(data)" class="mr-2"></i>
                            {{ paymentStatus(data) }}
                        </span>
                    </template>
                </Column>
            </DataTable>
        </div>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import NavbarLayout from "@/Layouts/NavbarLayout.vue";
import ChooseColumns from "@/Components/ChooseColumns.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { Inertia } from "@inertiajs/inertia";
import {
    Button,
    DataTable,
    Column,
    InputText,
    Dropdown,
    Calendar,
} from "primevue";
import Breadcrumb from "primevue/breadcrumb";
import moment from "moment";

// Props
const props = defineProps({
    invoices: {
        type: Object,
        required: true,
    },
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(value || 0);
};

// Breadcrumb
const page = usePage();
const items = computed(() => [
    { label: "", to: "/", icon: "pi pi-home" },
    { label: page.props.title || "Invoices", to: route("invoices.index") },
]);

// Filters
const filters = ref({
    invoice_no_start: null,
    invoice_no_end: null,
    customer: null,
    status: null,
    start_date: null,
    end_date: null,
    customer_category_id: null,
    currency: null,
    overdue_status: null,
});

// Columns
const columns = [
    { field: "invoice_no", header: "Invoice No" },
    { field: "invoice_date", header: "Date", sortable: true },
    { field: "invoice_end_date", header: "Due Date", sortable: true },
    // { field: "agreement_no", header: "Agreement No" },
    { field: "customer.name", header: "Customer", sortable: true },
];

const selectedColumns = ref(columns.slice());
const showColumns = ref(columns);

// Dropdown options
const statusOptions = ref([
    { label: "Pending", value: "Pending" },
    { label: "Fully Paid", value: "Fully Paid" },
    { label: "Partially Paid", value: "Partially Paid" },
    { label: "Overdue", value: "Overdue" },
]);

const paymentStatusOptions = ref([
    { label: "Fully Paid", value: "Fully Paid" },
    { label: "Partially Paid", value: "Partially Paid" },
    { label: "Overdue", value: "Overdue" },
    { label: "Not Paid", value: "Not Paid" },
    { label: "Pending", value: "Pending" },
]);

const customerTypeOptions = ref([
    { label: "Individual", value: 1 }, // Assuming 1 is the ID for Individual
    { label: "Public Organization", value: 2 },
    { label: "NGO", value: 3 },
    { label: "Private Company", value: 4 },
]);

const currencyOptions = ref([
    { label: "KHR", value: "USD" },
    { label: "USD", value: "KHR" },
]);

// Clear filters
const clearFilters = () => {
    filters.value = {
        invoice_no_start: null,
        invoice_no_end: null,
        customer: null,
        status: null,
        start_date: null,
        end_date: null,
        category_name_english: null,
        currency: null,
        overdue_status: null,
    };
};

// Custom filtered invoices logic
const filteredInvoices = computed(() => {
    const f = filters.value;

    return props.invoices.data.filter((invoice) => {
        if (f.invoice_no_start && invoice.invoice_no < f.invoice_no_start)
            return false;
        if (f.invoice_no_end && invoice.invoice_no > f.invoice_no_end)
            return false;
        if (
            f.customer &&
            !invoice.customer?.name
                ?.toLowerCase()
                .includes(f.customer.toLowerCase())
        )
            return false;
        if (
            f.status &&
            invoice.status?.toLowerCase() !== f.status.toLowerCase()
        )
            return false;
        if (f.payment_status && paymentStatus(invoice) !== f.payment_status)
            return false;
        if (
            f.start_date &&
            new Date(invoice.start_date) < new Date(f.start_date)
        )
            return false;
        if (f.end_date && new Date(invoice.end_date) > new Date(f.end_date))
            return false;

        if (f.category_name_english) {
            const category =
                invoice.customer?.customerCategory?.category_name_english?.toLowerCase() ||
                "";
            if (!category.includes(f.category_name_english.toLowerCase()))
                return false;
        }

        if (f.currency && invoice.currency !== f.currency) return false;

        if (f.overdue_status) {
            const amountDue = computeAmountDue(invoice);
            const dueDate = moment(invoice.invoice_end_date);
            const today = moment();
            const daysDiff = today.diff(dueDate, "days");

            if (f.overdue_status === "paid") {
                if (amountDue > 0) return false;
            }

            if (f.overdue_status === "overdue") {
                if (!(amountDue > 0 && daysDiff > 0)) return false;
            }

            if (f.overdue_status === "today") {
                if (!(amountDue > 0 && daysDiff === 0)) return false;
            }

            if (f.overdue_status === "upcoming") {
                if (!(amountDue > 0 && daysDiff < 0)) return false;
            }
        }

        if (f.customer_category_id) {
            const customerCategoryId = invoice.customer?.customer_category_id;
            if (
                !customerCategoryId ||
                customerCategoryId != f.customer_category_id
            ) {
                return false;
            }
        }

        return true;
    });
});

// Helpers
const computeAmountDue = (invoice) => {
    const grandTotal = invoice.grand_total || 0;
    const paid = invoice.paid_amount || 0;
    const due = grandTotal - paid;
    return due <= 0 ? 0 : due.toFixed(2);
};

const over_due = (rowData) => {
    const amountDue = computeAmountDue(rowData);
    if (amountDue <= 0) return { text: "Fully Paid", class: "text-green-500" };
    if (!rowData.invoice_end_date) return { text: "-", class: "" };

    const dueDate = moment(rowData.invoice_end_date);
    const today = moment();
    const daysDiff = today.diff(dueDate, "days");

    if (daysDiff > 0)
        return { text: `${daysDiff} day(s) overdue`, class: "text-red-500" };
    if (daysDiff < 0)
        return {
            text: `${Math.abs(daysDiff)} day(s) left`,
            class: "text-blue-500",
        };
    return { text: "Due Today", class: "text-orange-500" };
};

const paymentStatus = (invoice) => {
    const grandTotal = Number(invoice.grand_total || 0);
    const paid = Number(invoice.paid_amount || 0);
    const due = grandTotal - paid;

    if (due <= 0) return "Fully Paid";
    if (paid === 0) return "Not Paid";
    if (due < grandTotal) return "Partially Paid";
    return "Pending";
};

const statusIcon = (invoice) => {
    const status = paymentStatus(invoice);
    return status === "Fully Paid"
        ? "pi pi-check"
        : status === "Partially Paid"
        ? "pi pi-pencil"
        : status === "Overdue"
        ? "pi pi-times"
        : "pi pi-clock";
};

const statusClass = (status) => {
    const map = {
        "Fully Paid": "border-green-500 text-green-500",
        "Partially Paid": "border-blue-500  text-blue-500",
        Overdue: "border-red-500   text-red-500",
        "Not Paid": "border-yellow-500 text-yellow-500",
        Pending: "border-gray-500  text-gray-500",
    };
    return map[status] || "border-gray-500 text-gray-500";
};
</script>

<style scoped>
.invoices {
    padding: 1rem;
}
</style>
