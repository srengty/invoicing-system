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
                    <Dropdown
                    v-model="filters.status"
                    :options="statusOptions"
                    optionLabel="label"
                    optionValue="value"
                    placeholder="Filter by Status"
                    class="w-48"
                    size="small"
                    />
                    <Button
                    label="Clear"
                    icon="pi pi-times"
                    class="p-button-secondary"
                    size="small"
                    @click="filters.status = null"
                    />
                </div>
            </div>

            <!-- Data Table -->
            <DataTable
                :value="filteredInvoices"
                paginator
                :rows="5"
                :rowsPerPageOptions="[5, 10, 20, 50]"
                size="small"
                class="text-sm mt-8"
            >
                <Column
                    class=""
                    v-for="col in showColumns"
                    :key="col.field"
                    :field="col.field"
                    :header="col.header"
                    sortable
                />

                <Column field="grand_total" header="Amount">
                    <template #body="{ data }">
                        <span :class="{ 'text-blue-500': (data.grand_total) >= 0 }">
                        {{ formatCurrency(data.grand_total) }} (KHR)
                        </span>
                    </template>
                </Column>

                <Column field="status" header="Status">
                    <template #body="{ data }">
                        <div class="flex">
                            <Button
                                :icon="
                                    data.status === 'approved'
                                        ? 'pi pi-check'
                                        : data.status === 'rejected'
                                        ? 'pi pi-times'
                                        : data.status === 'revise'
                                        ? 'pi pi-pencil'
                                        : 'pi pi-clock'
                                "
                                :label="
                                    data.status === 'approved'
                                        ? 'Approved'
                                        : data.status === 'rejected'
                                        ? 'Rejected'
                                        : data.status === 'revise'
                                        ? 'Revise'
                                        : 'Pending'
                                "
                                :class="
                                    data.status === 'pending'
                                        ? 'p-button-warning'
                                        : data.status === 'approved'
                                        ? 'p-button-success'
                                        : data.status === 'revise'
                                        ? 'p-button-danger'
                                        : 'p-button-warning'
                                "
                                size="small"
                                @click="toggleStatus(data)"
                                class="text-sm flex-grow w-auto"
                                :disabled="data.status === 'approved'"
                                outlined
                            />
                            <Button
                                v-if="
                                    data.invoice_comments &&
                                    data.invoice_comments.length > 0
                                "
                                icon="pi pi-comment"
                                class="p-button-info ml-2"
                                @click="viewComment(data.invoice_comments)"
                                outlined
                            />
                        </div>
                    </template>
                </Column>

                <Column field="customer_status" header="Customer Status">
                    <template #body="slotProps">
                        <span
                            @click="handleStatusClick(slotProps.data)"
                            v-tooltip.top="
                                'Current customer status: ' +
                                (slotProps.data.customer_status || 'Unknown')
                            "
                            class="p-2 border rounded w-auto h-8 flex items-center justify-center cursor-pointer"
                            :class="{
                                'bg-blue-100 text-blue-800 border-blue-400':
                                    slotProps.data.customer_status === 'Sent',
                                'bg-yellow-100 text-yellow-800 border-yellow-400':
                                    slotProps.data.customer_status ===
                                    'Pending',
                                'bg-green-100 text-green-800 border-green-400':
                                    slotProps.data.customer_status === 'Accept',
                                'bg-red-100 text-red-800 border-red-400':
                                    slotProps.data.customer_status === 'Reject',
                            }"
                        >
                            <i
                                :class="{
                                    'pi pi-send':
                                        slotProps.data.customer_status ===
                                        'Sent',
                                    'pi pi-clock':
                                        slotProps.data.customer_status ===
                                        'Pending',
                                    'pi pi-check':
                                        slotProps.data.customer_status ===
                                        'Accept',
                                    'pi pi-times':
                                        slotProps.data.customer_status ===
                                        'Reject',
                                }"
                                style="margin-right: 8px"
                            ></i>
                            {{ slotProps.data.customer_status }}
                        </span>
                    </template>
                </Column>

                <!-- Actions -->
                <Column
                    header="View / Print"
                    headerStyle="text-align: center"
                    bodyStyle="text-align: center"
                >
                    <template #body="{ data }">
                        <div class="flex gap-2 justify-center">
                            <Button
                                icon="pi pi-eye"
                                aria-label="View"
                                severity="info"
                                class="custom-button"
                                @click="viewInvoice(data)"
                                size="small"
                                outlined
                            />
                            <Button
                                v-if="canEditInvoice(data)"
                                icon="pi pi-pencil"
                                aria-label="Edit"
                                severity="warning"
                                class="custom-button"
                                @click="editInvoice(data)"
                                size="small"
                                outlined
                            />
                            <Button
                                icon="pi pi-print"
                                aria-label="Print"
                                @click="printInvoice(data.id)"
                                outlined
                                size="small"
                            />
                        </div>
                    </template>
                </Column>
            </DataTable>

            <Dialog
                v-model:visible="isCommentDialogVisible"
                header="Comments"
                class="w-80"
            >
                <div v-if="selectedComment.length > 0">
                    <div
                        v-for="(item, index) in selectedComment"
                        :key="index"
                        class="text-border"
                    >
                        <p>{{ item.comment || "No comment text" }}</p>
                        <small>{{ formatDate(item.created_at) }}</small>
                    </div>
                </div>
                <div v-else>
                    <p>No comments available</p>
                </div>
                <div class="flex justify-end mt-4">
                    <Button
                        label="Close"
                        class="p-button-secondary"
                        @click="isCommentDialogVisible = false"
                    />
                </div>
            </Dialog>

            <Dialog
                v-model:visible="isFeedbackDialogVisible"
                header="Customer Feedback"
                modal
                :style="{ width: '30rem' }"
                class="text-sm"
            >
                <div v-if="selectedInvoice" class="flex flex-col gap-4">
                    <p>
                        <strong>Quotation No.:</strong>
                        {{ selectedInvoice.invoice_no }}
                    </p>
                    <p>
                        <strong>Customer Name:</strong>
                        {{ selectedInvoice.customer?.name || "N/A" }}
                    </p>
                    <p>
                        <strong>Total:</strong>
                        {{ selectedInvoice.total }}
                    </p>
                    <div class="flex flex-col gap-2">
                        <label for="feedbackComment" class="block font-bold"
                            >Comment:</label
                        >
                        <textarea
                            id="feedbackComment"
                            v-model="feedbackComment"
                            rows="3"
                            class="w-full border rounded p-2"
                            placeholder="Enter your feedback here..."
                        ></textarea>
                    </div>

                    <!-- Approve/Reject Buttons -->
                    <div class="flex justify-end gap-2">
                        <Button
                            label="Approve"
                            severity="success"
                            @click="handleApprove"
                        />
                        <Button
                            label="Reject"
                            severity="danger"
                            @click="handleReject"
                        />
                    </div>
                </div>
            </Dialog>

            <!-- View Detail Invoices -->
            <Dialog
                v-model:visible="isViewDialogVisible"
                header="Invoice Details"
                modal
                :style="{ width: '40rem' }"
                class="text-sm"
            >
                <div v-if="selectedInvoice" class="p-4 space-y-4">
                    <!-- Header Info -->
                    <div class="flex justify-between">
                        <div class="flex flex-col w-1/2 gap-4">
                            <p>
                                <strong>Customer Name:</strong>
                                {{ selectedInvoice.customer?.name || "N/A" }}
                            </p>
                            <p>
                                <strong>Address:</strong>
                                {{ selectedInvoice.address }}
                            </p>
                            <p>
                                <strong>Email:</strong>
                                <a
                                    v-if="selectedInvoice.email || selectedInvoice.customer?.email"
                                    :href="`mailto:${
                                        selectedInvoice.email || selectedInvoice.customer?.email
                                    }`"
                                    class="text-blue-600 hover:underline"
                                >
                                    {{ selectedInvoice.email || selectedInvoice.customer?.email }}
                                </a>
                                <span v-else>N/A</span>
                            </p>
                        </div>
                        <div class="flex flex-col w-1/2 items-end gap-4">
                            <div class="grid gap-4">
                                <p>
                                    <strong>Quotation No.:</strong>
                                    {{ selectedInvoice.quotation_no }}
                                </p>
                                <p>
                                    <strong>Invoice No.:</strong>
                                    {{ selectedInvoice.invoice_no }}
                                </p>
                                <p>
                                    <strong>Invoice Date:</strong>
                                    {{ selectedInvoice.invoice_date }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Products Table -->
                    <span class="font-bold block mb-2 text-center">Items</span>
                    <DataTable
                    v-if="selectedInvoice.payment_schedules?.length"
                    :value="selectedInvoice.payment_schedules"
                    responsiveLayout="scroll"
                    class="text-sm mb-4"
                    >
                    <Column field="id" header="Payment Schedule ID" />
                    <Column field="amount" header="Amount" />
                    <Column field="short_description" header="Description" />
                    </DataTable>

                    <DataTable
                    v-else
                    :value="selectedInvoice.products"
                    responsiveLayout="scroll"
                    class="text-sm"
                    >
                    <Column field="name" header="Item" />
                    <Column field="pivot.quantity" header="Qty" />
                    <Column header="Unit Price">
                        <template #body="{ data }">
                        {{ formatCurrency(data.pivot.price) }}
                        </template>
                    </Column>
                    </DataTable>

                    <!-- Totals -->
                    <div class="text-left">
                        <br />
                        <p>
                            <strong>Total:</strong>
                            {{ formatCurrency(selectedInvoice.grand_total) }}
                            <span class="text-xs text-gray-500 ml-1">(KHR)</span>
                        </p>
                    </div>
                </div>

                <!-- Comments -->
                <div class="p-4">
                    <label for="comment" class="block font-bold mb-2">Comment:</label>
                    <textarea
                        id="comment"
                        v-model="statusForm.comment"
                        placeholder="Enter your comment..."
                        class="w-full p-2 border rounded"
                        :class="{ 'border-red-500': statusForm.errors.comment }"
                    />
                    <p
                        v-if="statusForm.errors.comment"
                        class="text-red-500 text-xs mt-1"
                    >
                        {{ statusForm.errors.comment }}
                    </p>
                </div>

                <!-- Footer Buttons -->
                <template #footer>
                    <Button
                        label="Approve"
                        icon="pi pi-check"
                        class="p-button-success"
                        size="small"
                        @click="changeStatus('approved')"
                        :disabled="statusForm.processing || !statusForm.comment.trim()"
                    />
                    <Button
                        label="Revise"
                        icon="pi pi-times"
                        class="p-button-danger"
                        size="small"
                        @click="changeStatus('revise')"
                        :disabled="statusForm.processing || !statusForm.comment.trim()"
                    />
                    <Button
                        label="Close"
                        severity="secondary"
                        @click="isViewDialogVisible = false"
                    />
                </template>
            </Dialog>

        </div>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { useForm } from "@inertiajs/vue3";
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
import { watch } from "vue";

// Props
const props = defineProps({
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

const filters = ref({
  status: null,
});

const selectedInvoice = ref(null);
const isViewDialogVisible = ref(false);
const isFeedbackDialogVisible = ref(false);
const comment = ref("");
const viewInvoice = (invoice) => {
    statusForm.reset();
    selectedInvoice.value = invoice;
    console.log('Selected Invoice:', invoice);
    console.log('Payment Schedules:', invoice.paymentSchedules);
    isViewDialogVisible.value = true;
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(value || 0);
};

const statusOptions = ref([
  { label: "Pending", value: "Pending" },
  { label: "Approved", value: "approved" },
  { label: "Revise", value: "revise" },
  { label: "Rejected", value: "rejected" },
]);

const columns = [
    { field: "start_date", header: "Date" },
    { field: "end_date", header: "Due Date" },
    { field: "customer.name", header: "Customer" },
];

const filteredInvoices = computed(() => {
  if (!filters.value.status) return props.invoices.data;

  return props.invoices.data.filter((invoice) => {
    return invoice.status === filters.value.status;
  });
});


const selectedComment = ref("");
const commentText = ref("");
const isCommentDialogVisible = ref(false);
const isStatusDialogVisible = ref(false);

const statusForm = useForm({
    status: "",
    comment: "",
});

// Open the status dialog for a selected invoice
const toggleStatus = (invoice) => {
    selectedInvoice.value = invoice;
    isStatusDialogVisible.value = true;

    // Reset form fields before showing the dialog
    statusForm.reset();
};

const canEditInvoice = (invoice) => {
  return invoice.status !== 'approved' || invoice.status == 'revise';
};

const editInvoice = (invoice) => {
  Inertia.visit(`/invoices/${invoice.id}/edit`);
};


const changeStatus = (status) => {
    if (!selectedInvoice.value) return;

    // Ensure the status you're sending is valid
    const validStatuses = ["approved", "rejected", "pending", "revise"]; // Include 'revise'
    if (!validStatuses.includes(status)) {
        console.error(`Invalid status: ${status}`);
        return; // Prevent the API call with invalid status
    }

    // Set the status and comment from the form
    statusForm.status = status;
    statusForm.comment = statusForm.comment.trim(); // Ensure no leading/trailing spaces

    // Update the invoice status via an API request
    statusForm.put(route("invoices.updateStatus", selectedInvoice.value.id), {
        onSuccess: () => {
            // close the details dialog
            isViewDialogVisible.value = false;

            // reset for next time
            statusForm.reset();
            selectedInvoice.value = null;
        },
        onError: (errors) => {
            console.error("Status update failed:", errors);
        },
    });
};

const viewComment = (invoiceComments) => {
    console.log("Comments data:", invoiceComments);
    if (invoiceComments && Array.isArray(invoiceComments)) {
        selectedComment.value = invoiceComments;
        isCommentDialogVisible.value = true;
    } else {
        selectedComment.value = [];
        isCommentDialogVisible.value = true;
    }
};
const handleStatusClick = (invoice) => {
    selectedInvoice.value = invoice;

    if (invoice.customer_status === "Pending") {
        isSendDialogVisible.value = true;
    } else if (invoice.customer_status === "Sent") {
        isFeedbackDialogVisible.value = true;
    }
};

const formatDate = (dateString) => {
    return moment(dateString).format("MMMM D, YYYY [at] h:mm A");
};

const selectedColumns = ref(columns.slice());
const showColumns = ref(columns);

const updateColumns = () => {
    showColumns.value = selectedColumns.value;
};

const navigateToCreate = () => {
    Inertia.visit("/invoices/create");
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

const printInvoice = (invoice_no, include_catelog = 0) => {
    const invoiceUrl = `/invoices/${invoice_no}?include_catelog=${include_catelog}`;
    const printWindow = window.open(invoiceUrl, "_self");
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
        status: filters.value.status,
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

const computeAmountDue = (invoice) => {
    const grandTotal = invoice.grand_total || 0;
    const amountPaid = invoice.agreement?.amount || 0;
    return (grandTotal - amountPaid).toFixed(2);
};
</script>

<style scoped>
.invoices {
    padding: 1rem;
}
</style>
