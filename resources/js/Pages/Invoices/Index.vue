<template>
    <Head title="Invoices" />
    <GuestLayout>
        <NavbarLayout />
        <!-- PrimeVue Breadcrumb -->
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
        <div class="invoices">
            <div class="flex justify-end items-center">
                <div class="flex gap-2">
                    <Button
                        icon="pi pi-plus"
                        label="Create Invoice"
                        size="small"
                        @click="navigateToCreate"
                    />
                    <ChooseColumns
                        :columns="columns"
                        v-model="selectedColumns"
                        @apply="updateColumns"
                        size="small"
                    />
                    <Button
                            label="Search"
                            icon="pi pi-search"
                            @click="searchInvoices"
                            size="small"
                            class="w-full md:w-32"
                        />
                    <Button
                            label="Clear"
                            icon="pi pi-times"
                            severity="secondary"
                            @click="clearFilters"
                            size="small"
                            class="w-full md:w-32"
                        />
                </div>
            </div>

            <!-- Filters -->
            <div class="mb-8 mt-6">
                <div class="flex flex-wrap justify-between text-sm mb-2">
                    <!-- Invoice No. Start - Restrict to Numbers -->
                    <div class="flex flex-row w-full sm:w-1/3 md:w-1/3 items-center">
                        <label for="invoice_no_start" class="mb-1 w-1/2 font-semibold ">Invoice No. Start:</label>
                        <InputText
                            v-model="filters.invoice_no_start"
                            placeholder="Input Invoice No Start"
                            v-keyfilter="['num']"
                            size="small"
                            class="w-full mr-8 items-start"
                        />
                    </div>

                    <!-- Invoice No. End - Restrict to Numbers -->
                    <div class="flex flex-row w-full sm:w-1/3 md:w-1/3 items-center">
                        <label for="invoice_no_end" class="mb-1 w-1/2 font-semibold">Invoice No. End:</label>
                        <InputText
                            v-model="filters.invoice_no_end"
                            placeholder="Input Invoice No End"
                            v-keyfilter="['num']"
                            size="small"
                            class="w-full mr-8 "
                        />
                    </div>

                    <!-- Currency Dropdown -->
                    <div class="flex flex-row w-full sm:w-1/3 md:w-1/3 items-center">
                        <label for="currency" class="mb-1 w-1/2 font-semibold">Currency:</label>
                        <Dropdown
                            v-model="filters.currency"
                            :options="currencyOptions"
                            placeholder="Select Currency"
                            optionLabel="label"
                            optionValue="value"
                            size="small"
                            class="w-full"
                        />
                    </div>
                </div>

                <div class="flex flex-wrap justify-between text-sm mb-2">

                    <div class="flex flex-row w-full sm:w-1/3 md:w-1/3 items-center">
                        <label for="end_date" class="mb-1 w-1/2 font-semibold">Start Date:</label>
                        <DatePicker
                            v-model="filters.start_date"
                            placeholder="Pick Start Date"
                            size="small"
                            class="w-full mr-8"
                        />
                    </div>

                    <!-- End Date -->
                    <div class="flex flex-row w-full sm:w-1/3 md:w-1/3 items-center">
                        <label for="end_date" class="mb-1 w-1/2 font-semibold">End Date:</label>
                        <DatePicker
                            v-model="filters.end_date"
                            placeholder="Pick End Date"
                            size="small"
                            class="w-full mr-8"
                        />
                    </div>

                    <!-- Payment Status -->
                    <div class="flex flex-row w-full sm:w-1/3 md:w-1/3 items-center">
                        <label for="status" class="mb-1 w-1/2 font-semibold">Payment Status:</label>
                        <Dropdown
                            v-model="filters.status"
                            :options="statusOptions"
                            placeholder="Select Status"
                            optionLabel="label"
                            optionValue="value"
                            size="small"
                            class="w-full"
                        />
                    </div>
                </div>

                <div class="flex flex-wrap justify-between text-sm">
                    <!-- Customer Name - Restrict to Letters Only -->
                    <div class="flex flex-row w-full sm:w-1/3 md:w-1/3 items-center">
                        <label for="customer" class="mb-1 w-1/2 font-semibold">Customer:</label>
                        <InputText
                            v-model="filters.customer"
                            placeholder="Input Customer Name"
                            v-keyfilter="['alpha']"
                            size="small"
                            class="w-full mr-8"
                        />
                    </div>

                    <!-- Customer Type -->
                    <div class="flex flex-row w-full sm:w-1/3 md:w-1/3 items-center">
                        <label for="category_name_english" class="mb-1 w-1/2 font-semibold">Customer Type:</label>
                        <Dropdown
                            v-model="filters.category_name_english"
                            :options="customerTypeOptions"
                            placeholder="Select Customer Type"
                            optionLabel="label"
                            optionValue="value"
                            size="small"
                            class="w-full mr-8"
                        />
                    </div>

                    <!-- Income Type -->
                    <div class="flex flex-row w-full sm:w-1/3 md:w-1/3 items-center">
                        <label for="income_type" class="w-1/2 font-semibold">Income Type:</label>
                        <Dropdown
                            placeholder="Select Income Type"
                            optionLabel="label"
                            optionValue="value"
                            size="small"
                            class="w-full"
                        />
                    </div>
                </div>
            </div>


            <!-- Data Table -->
            <DataTable
                :value="invoices.data"
                paginator
                :rows="5"
                :rowsPerPageOptions="[5, 10, 20, 50]"
                size="small"
                class="text-sm"
            >
                <Column
                    class=""
                    v-for="col in showColumns"
                    :key="col.field"
                    :field="col.field"
                    :header="col.header"
                    sortable
                />

                <Column header="Amount Due">
                    <template #body="{ data }">
                        <template v-if="data.due_date && moment(data.due_date).isBefore(moment(), 'day')">
                            <!-- Invoice is overdue, compute the amount due in red color -->
                            <span class="text-red-500"> {{ computeAmountDue(data) }}</span>
                        </template>
                        <template v-else>
                            <!-- Invoice is not overdue, compute the amount due with regular styling -->
                            {{ computeAmountDue(data) }}
                        </template>
                    </template>
                </Column>

                <Column header="Overdue">
                    <template #body="{ data }">
                        <span :class="{'text-red-500': over_due(data).includes('days ago') && over_due(data) !== 'Not Past Due'}">
                            {{ over_due(data) }}
                        </span>
                    </template>
                </Column>

                <Column field="payment_status" header="Payment Status">
                    <template #body="{ data }">
                        <div class="flex">
                            <Button
                                :icon="paymentStatus(data) === 'Fully Paid' ? 'pi pi-check' :
                                    paymentStatus(data) === 'Partially Paid' ? 'pi pi-pencil' :
                                    paymentStatus(data) === 'Overdue' ? 'pi pi-times' : 'pi pi-clock'"
                                :label="paymentStatus(data)"
                                :class="{
                                    'p-button-success': paymentStatus(data) === 'Fully Paid',
                                    'p-button-info': paymentStatus(data) === 'Partially Paid',
                                    'p-button-danger': paymentStatus(data) === 'Overdue',
                                    'p-button-secondary': paymentStatus(data) === 'Pending'
                                }"
                                size="small"
                                class="text-sm flex-grow w-auto"
                                outlined
                            />
                        </div>
                    </template>
                </Column>

                <!-- Actions -->
                <!-- <Column
                    header="Actions"
                    headerStyle="text-align: center"
                    bodyStyle="text-align: center"
                >
                    <template #body="{ data }">
                        <div class="flex gap-2 justify-center">
                            <Button
                                icon="pi pi-print"
                                class="p-button-info"
                                aria-label="Print"
                                @click="printInvoice(data.invoice_no)"
                                outlined
                            />
                        </div>
                    </template>
                </Column> -->
            </DataTable>

            <Dialog
                v-model:visible="isStatusDialogVisible"
                header="Change Invoice Status"
                :modal="true"
                class="w-96"
            >
                <div class="p-4">
                    <p class="text-center text-base mb-3">
                        Do you want to approve or reject this invoice?
                    </p>

                    <textarea
                        v-model="statusForm.comment"
                        placeholder="Enter your comment..."
                        class="w-full p-2 border rounded"
                        :class="{ 'border-red-500': statusForm.errors.comment }"
                    ></textarea>
                    <p v-if="statusForm.errors.comment" class="text-red-500 text-xs mt-1">
                        {{ statusForm.errors.comment }}
                    </p>

                    <div class="flex justify-center gap-4 mt-4">
                        <Button
                            label="Revise"
                            icon="pi pi-times"
                            class="p-button-danger"
                            size="small"
                            @click="changeStatus('revise')"
                            :disabled="statusForm.processing"
                        />
                        <Button
                            label="Approve"
                            icon="pi pi-check"
                            class="p-button-success"
                            size="small"
                            @click="changeStatus('approved')"
                            :disabled="statusForm.processing"
                        />
                    </div>
                </div>
            </Dialog>

            <Dialog v-model:visible="isCommentDialogVisible" header="Comments" class="w-80">
                <div v-if="selectedComment.length > 0">
                    <div v-for="(item, index) in selectedComment" :key="index" class="text-border">
                        <p>{{ item.comment || "No comment text" }}</p>
                        <small>{{ formatDate(item.created_at) }}</small>
                    </div>
                </div>
                <div v-else>
                    <p>No comments available</p>
                </div>
                <div class="flex justify-end mt-4">
                    <Button label="Close" class="p-button-secondary" @click="isCommentDialogVisible = false" />
                </div>
            </Dialog>

        </div>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { useForm } from '@inertiajs/vue3';
import { Head, Link } from "@inertiajs/vue3";
import {
    Button,
    DataTable,
    Column,
    DatePicker,
    InputText,
    Dropdown,
    Dialog,
} from "primevue";
import KeyFilter from "primevue/keyfilter";
import ChooseColumns from "@/Components/ChooseColumns.vue";
import { ref, computed } from "vue";
import { Inertia } from "@inertiajs/inertia";
import moment from "moment";
import Customers from "@/Components/Customers.vue";
import NavbarLayout from "@/Layouts/NavbarLayout.vue";
import Breadcrumb from "primevue/breadcrumb";
import { usePage } from "@inertiajs/vue3";

// Props
defineProps({
    invoices: {
        type: Object,
        required: true,
    },
});

// The Breadcrumb Quotations
const page = usePage();
const items = computed(() => [
    {
        label: "",
        to: "/",
        icon: "pi pi-home",
    },
    { label: page.props.title || "Invoices", to: route("invoices.index") },
]);

const storedFilters = localStorage.getItem("invoiceFilters");
const filters = ref(
    storedFilters
        ? JSON.parse(storedFilters)
        : {
              invoice_no_start: null,
              invoice_no_end: null,
              category_name_english: null,
              currency: null,
              start_date: null,
              end_date: null,
              customer: null,
              payment_status: null,
              income_type: null,
          }
);

const customerTypeOptions = ref([
    { label: "Individual", value: "Individual" },
    { label: "Public Organization", value: "Public Organization" },
    { label: "NGO", value: "NGO" },
    { label: "Private Company", value: "Private Company" },
]);

const currencyOptions = ref([
    { label: "KHR", value: "USD" },
    { label: "USD", value: "KHR" },
]);

const statusOptions = ref([
  { label: "Pending", value: "Pending" },
  { label: "Fully Paid", value: "Fully Paid" },
  { label: "Partially Paid", value: "Partially Paid" },
  { label: "Overdue", value: "Overdue" }
]);



const columns = [
    { field: "invoice_no", header: "Invoice No" },
    { field: "invoice_date", header: "Date" },
    { field: "invoice_end_date", header: "Due Date" },
    { field: "customer.name", header: "Customer" },
    { field: "grand_total", header: "Amount" },
    { field: "paid_amount", header: "Amount Paid" },
];

const isCommentDialogVisible = ref(false);
const selectedInvoice = ref(null);
const isStatusDialogVisible = ref(false);

const statusForm = useForm({
    status: '',
    comment: '',
});


// Open the status dialog for a selected invoice
const toggleStatus = (invoice) => {
    selectedInvoice.value = invoice;
    isStatusDialogVisible.value = true;

    // Reset form fields before showing the dialog
    statusForm.reset();
};

const formatDate = (dateString) => {
    return moment(dateString).format('MMMM D, YYYY [at] h:mm A');
};

const selectedColumns = ref(columns.slice());
const showColumns = ref(columns);

const updateColumns = () => {
    showColumns.value = selectedColumns.value;
};

const navigateToCreate = () => {
    Inertia.visit("/invoices/create");
};

const editInvoice = (id) => {
    Inertia.visit(`/invoices/${id}/edit`);
};

const deleteInvoice = (id) => {
    if (confirm("Are you sure you want to delete this invoice?")) {
        Inertia.delete(`/invoices/${id}`);
    }
};

const over_due = (rowData) => {
    if (!rowData.end_date) return "-"; // If there's no end date, return "-"

    const dueDate = moment(rowData.end_date);
    const currentDate = moment();
    const overdue = currentDate.diff(dueDate, "days");

    // If overdue is 0, return "Not Past Due", otherwise return the number of overdue days
    return overdue > 0 ? `${overdue} days ago` : "Not Past Due";
};


const printInvoice = (id) => {
    const invoiceUrl = `/invoices/${id}`;
    const printWindow = window.open(invoiceUrl, "_blank");
    setTimeout(() => {
        printWindow.print();
    }, 1000);
};

const searchInvoices = () => {
    const formattedStartDate = filters.value.start_date
        ? moment(filters.value.start_date).format("YYYY-MM-DD")
        : null;
    const formattedEndDate = filters.value.end_date
        ? moment(filters.value.end_date).format("YYYY-MM-DD")
        : null;

    // Create an object with all filter values
    const invoiceFilters = {
        invoice_no_start: filters.value.invoice_no_start,
        invoice_no_end: filters.value.invoice_no_end,
        category_name_english: filters.value.category_name_english,
        currency: filters.value.currency,
        start_date: formattedStartDate,
        end_date: formattedEndDate,
        customer: filters.value.customer,
        payment_status: filters.value.payment_status,
        income_type: filters.value.income_type,
    };

    // Save all filter values to localStorage as a JSON string
    localStorage.setItem("invoiceFilters", JSON.stringify(invoiceFilters));

    Inertia.get("/invoices", invoiceFilters);
};

const clearFilters = () => {
    filters.value = {
        invoice_no_start: null,
        invoice_no_end: null,
        category_name_english: null, // Changed from customer_type to category_name_english
        currency: null,
        start_date: null,
        end_date: null,
        customer: null,
        status: null,
    };
    searchInvoices();
};

const paymentStatus = (invoice) => {
    const amountDue = computeAmountDue(invoice);
    
    if (amountDue <= 0) {
        return "Fully Paid";
    } else if (amountDue < invoice.grand_total) {
        return "Partially Paid";
    } else if (over_due(invoice) !== "Not Past Due") {
        return "Overdue";
    } else {
        return "Pending";
    }
};

const computeAmountDue = (invoice) => {
    const grandTotal = invoice.grand_total || 0;
    const amountPaid = invoice.paid_amount || 0;  // Ensure you are using the correct field
    return (grandTotal - amountPaid).toFixed(2);  // Return as a fixed decimal number
};

</script>

<style scoped>
.invoices {
    padding: 1rem;
}
</style>
