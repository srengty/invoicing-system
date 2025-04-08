<template>
    <Head title="Quotations" />
    <meta name="_token" content="{{ csrf_token() }}" />

    <GuestLayout>
        <NavbarLayout />
        <!-- PrimeVue Breadcrumb -->
        <div class="px-4 py-3 mt-4">
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
        <Toast position="top-center" group="tc" />
        <Toast position="top-right" group="tr" />

        <div class="quotations text-sm p-4">
            <!-- <div class="flex justify-between items-center p-4">
                <h1 class="text-2xl">Quotations list</h1>
            </div> -->
            <div class="flex justify-end p-4 gap-4">
                <div>
                    <Dropdown
                        v-model="searchType"
                        :options="searchOptions"
                        optionLabel="label"
                        optionValue="value"
                        class="w-48 text-sm"
                        placeholder="Select field to search"
                    />
                </div>
                <div>
                    <InputText
                        v-model="searchTerm"
                        placeholder="Search"
                        class="w-64"
                        size="small"
                    />
                </div>
                <div>
                    <Link :href="route('quotations.create')"
                        ><Button
                            icon="pi pi-plus"
                            label="Issue Quotation"
                            size="small"
                            raised
                    /></Link>
                </div>
                <Link :href="route('invoices.create')"
                    ><Button
                        icon="pi pi-plus"
                        label="Issue Invoice"
                        size="small"
                        raised
                /></Link>
                <Link :href="route('agreements.create')"
                    ><Button
                        icon="pi pi-plus"
                        label="Record Agreement"
                        size="small"
                        raised
                /></Link>
            </div>

            <div>
                <DataTable
                    :value="filteredQuotations"
                    paginator
                    :rows="5"
                    :rowsPerPageOptions="[5, 10, 20, 50]"
                    tableStyle="min-width: 50rem"
                >
                    <!-- <Column header="No." style="width: 5%">
                        <template #body="slotProps">
                            {{ slotProps.index + 1 }}
                        </template>
                    </Column> -->
                    <Column
                        field="customer.name"
                        header="Customer/Organization Name"
                        style="width: 15%"
                    />
                    <Column field="total" header="Total" style="width: 10%">
                        <template #body="slotProps">
                            {{ formatCurrency(slotProps.data.total) }}
                            <span class="text-xs text-gray-500 ml-1">
                                (KHR)
                            </span>
                        </template>
                    </Column>

                    <!-- Correctly Map the Status Column -->
                    <Column field="status" header="Status" style="width: 10%">
                        <template #body="slotProps">
                            <span
                                class="p-2 border rounded w-28 h-8 flex items-center justify-center gap-2"
                                :class="{
                                    'bg-yellow-100 text-yellow-800 border-yellow-400':
                                        slotProps.data.status === 'Pending',
                                    'bg-red-100 text-red-800 border-red-400':
                                        slotProps.data.status === 'Revise',
                                    'bg-green-100 text-green-800 border-green-400':
                                        slotProps.data.status === 'Approved',
                                }"
                            >
                                <i
                                    :class="{
                                        'pi pi-clock':
                                            slotProps.data.status === 'Pending',
                                        'pi pi-times':
                                            slotProps.data.status === 'Revise',
                                        'pi pi-check':
                                            slotProps.data.status ===
                                            'Approved',
                                    }"
                                ></i>
                                {{ slotProps.data.status }}
                            </span>
                        </template>
                    </Column>

                    <!-- Correctly Map the Customer Status Column -->
                    <Column
                        field="customer_status"
                        header="Customer Status"
                        style="width: 10%"
                    >
                        <template #body="slotProps">
                            <span
                                @click="handleStatusClick(slotProps.data)"
                                v-tooltip.top="
                                    'Current customer status: ' +
                                    slotProps.data.customer_status
                                "
                                class="p-2 border rounded w-24 h-8 flex items-center justify-center cursor-pointer"
                                :class="{
                                    'bg-blue-100 text-blue-800 border-blue-400':
                                        slotProps.data.customer_status ===
                                        'Sent',
                                    'bg-yellow-100 text-yellow-800 border-yellow-400':
                                        slotProps.data.customer_status ===
                                        'Pending',
                                    'bg-green-100 text-green-800 border-green-400':
                                        slotProps.data.customer_status ===
                                        'Accept',
                                    'bg-red-100 text-red-800 border-red-400':
                                        slotProps.data.customer_status ===
                                        'Reject',
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

                    <!-- Other columns -->
                    <Column
                        header="Customer's Comment"
                        style="width: 15%; min-width: 200px"
                        bodyStyle="white-space: normal"
                    >
                        <template #body="{ data }">
                            <div class="comment-container">
                                <template v-if="data.comments?.length">
                                    <div class="latest-comment">
                                        <p class="comment-text">
                                            {{
                                                data.comments[
                                                    data.comments.length - 1
                                                ].comment
                                            }}
                                        </p>
                                    </div>
                                </template>
                                <template v-else>
                                    <span class="no-comment">No comment</span>
                                </template>
                            </div>
                        </template>
                    </Column>
                    <Column header="View / Print-out" style="width: 8%">
                        <template #body="slotProps">
                            <div class="flex gap-4">
                                <Button
                                    icon="pi pi-eye"
                                    aria-label="View"
                                    severity="info"
                                    class="custom-button"
                                    @click="viewQuotation(slotProps.data)"
                                    size="small"
                                    outlined
                                />
                                <div>
                                    <Button
                                        icon="pi pi-print"
                                        aria-label="Print out"
                                        class="custom-button"
                                        @click="
                                            printQuotation(slotProps.data.id, 1)
                                        "
                                        size="small"
                                        outlined

                                    />
                                </div>
                            </div>
                        </template>
                    </Column>
                </DataTable>

                <!-- View Dialog display -->
                <Dialog
                    v-model:visible="isViewDialogVisible"
                    header="Quotation Details"
                    modal
                    :style="{ width: '40rem' }"
                    class="text-sm"
                >
                    <div
                        v-if="selectedQuotation"
                        class="flex flex-col gap-2 text-sm pl-6"
                    >
                        <div class="flex flex-row justify-between mb-6">
                            <!-- <p>
                            <strong>Customer ID:</strong>
                            {{ selectedQuotation.customer_id }}
                        </p> -->
                            <div class="flex flex-col w-1/2 gap-4">
                                <p>
                                    <strong>Customer Name:</strong>
                                    {{
                                        selectedQuotation.customer?.name ||
                                        "N/A"
                                    }}
                                </p>
                                <p>
                                    <strong>Address:</strong>
                                    {{ selectedQuotation.address }}
                                </p>
                                <p>
                                    <strong>Phone Number:</strong>
                                    {{ selectedQuotation.phone_number }}
                                </p>
                                <p>
                                    <strong>Email:</strong>
                                    <a
                                        v-if="
                                            selectedQuotation.email ||
                                            selectedQuotation.customer?.email
                                        "
                                        :href="`mailto:${
                                            selectedQuotation.email ||
                                            selectedQuotation.customer?.email
                                        }`"
                                        class="text-blue-600 hover:underline"
                                    >
                                        {{
                                            selectedQuotation.email ||
                                            selectedQuotation.customer?.email
                                        }}
                                    </a>
                                    <span v-else>N/A</span>
                                </p>
                            </div>
                            <div class="flex flex-col w-1/2 items-end gap-4">
                                <div class="grid gap-4">
                                    <p>
                                        <strong>Quotation No.:</strong>
                                        {{ selectedQuotation.quotation_no }}
                                    </p>
                                    <p>
                                        <strong>Quotation Date:</strong>
                                        {{ selectedQuotation.quotation_date }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Items Section -->
                        <span class="font-bold block mb-2 text-center"
                            >Items</span
                        >
                        <div v-if="selectedQuotation.products?.length">
                            <DataTable
                                :value="selectedQuotation.products"
                                responsiveLayout="scroll"
                            >
                                <Column field="name" header="Item"></Column>
                                <Column
                                    field="pivot.quantity"
                                    header="QTY"
                                ></Column>
                                <Column field="pivot.price" header="Unit Price">
                                    <template #body="slotProps">
                                        <span>
                                            {{
                                                formatCurrency(
                                                    slotProps.data.price
                                                )
                                            }}
                                        </span>
                                    </template>
                                </Column>
                            </DataTable>
                        </div>

                        <br />
                        <p>
                            <strong>Total:</strong>
                            {{ formatCurrency(selectedQuotation.total) }}
                            <span class="text-xs text-gray-500 ml-1"
                                >(KHR)</span
                            >
                        </p>
                        <p v-if="selectedQuotation.comment">
                            <strong>Comment:</strong>
                            {{ selectedQuotation.comment }}
                        </p>
                        <p v-if="selectedQuotation.role">
                            <strong>Role:</strong> {{ selectedQuotation.role }}
                        </p>
                    </div>

                    <!-- Comment input area for approving/revising -->
                    <div class="mt-4 p-6">
                        <label for="comment" class="block font-bold mb-2"
                            >Comment:</label
                        >
                        <textarea
                            id="comment"
                            v-model="comment"
                            rows="3"
                            class="w-full border rounded p-2"
                            placeholder="Enter your comment here..."
                        ></textarea>
                    </div>

                    <template #footer>
                        <Button
                            label="Approve"
                            :class="{
                                'custom-approved':
                                    selectedQuotation.status === 'Approved',
                            }"
                            severity="success"
                            @click="approveQuotation"
                            :disabled="selectedQuotation.status === 'Approved'"
                        />
                        <Button
                            label="Revise"
                            severity="danger"
                            @click="reviseQuotation"
                            :disabled="
                                selectedQuotation.status === 'Approved' ||
                                selectedQuotation.status === 'Revise'
                            "
                        />
                        <Button
                            label="Edit"
                            severity="info"
                            @click="editQuotation"
                            :disabled="selectedQuotation.status === 'Approved'"
                        />

                        <Button
                            label="Close"
                            severity="secondary"
                            @click="showCancel"
                        />
                    </template>
                </Dialog>

                <!-- Feedback Dialog display -->
                <Dialog
                    v-model:visible="isFeedbackDialogVisible"
                    header="Customer Feedback"
                    modal
                    :style="{ width: '30rem' }"
                    class="text-sm"
                >
                    <div v-if="selectedQuotation" class="flex flex-col gap-4">
                        <p>
                            <strong>Quotation No.:</strong>
                            {{ selectedQuotation.quotation_no }}
                        </p>
                        <p>
                            <strong>Customer Name:</strong>
                            {{ selectedQuotation.customer?.name || "N/A" }}
                        </p>
                        <p>
                            <strong>Total:</strong>
                            {{ selectedQuotation.total }}
                        </p>

                        <!-- Comment Input -->
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
            </div>
        </div>
    </GuestLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { usePage } from "@inertiajs/vue3";
import { router } from "@inertiajs/vue3";
import { useForm } from "@inertiajs/vue3";
import { Inertia } from "@inertiajs/inertia";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Button from "primevue/button";
import Dialog from "primevue/dialog";
import Dropdown from "primevue/dropdown";
import Toast from "primevue/toast";
import { useToast } from "primevue/usetoast";
import { InputText } from "primevue";
import Breadcrumb from "primevue/breadcrumb";
import html2pdf from "html2pdf.js";
import NavbarLayout from "@/Layouts/NavbarLayout.vue";
import { route } from 'ziggy-js';

const toast = useToast();

// Reactive properties
const isViewDialogVisible = ref(false);
const isFeedbackDialogVisible = ref(false);
const selectedQuotation = ref(null);
const userRole = ref("manager");
const comment = ref("");
const feedbackComment = ref("");
const isSending = ref(false);

const searchType = ref("");
const searchTerm = ref("");
const searchOptions = ref([
    { label: "Customer Name", value: "customer.name" },
    { label: "Total", value: "total" },
    { label: "Status", value: "status" },
    { label: "Customer Status", value: "customer_status" },
]);

// The Breadcrumb Quotations
const page = usePage();
const items = computed(() => [
    {
        label: "",
        to: "/",
        icon: "pi pi-home",
    },
    { label: page.props.title || "Quotations", to: route("quotations.list") },
]);

const filteredQuotations = computed(() => {
    if (!searchTerm.value || !searchType.value) {
        return props.quotations;
    }

    return props.quotations.filter((quotation) => {
        const fieldValue = getFieldValue(quotation, searchType.value);
        if (typeof fieldValue === "number") {
            return fieldValue
                .toString()
                .includes(searchTerm.value.toLowerCase());
        }
        return fieldValue
            .toLowerCase()
            .includes(searchTerm.value.toLowerCase());
    });
});

const getFieldValue = (obj, path) => {
    return path.split(".").reduce((acc, part) => acc && acc[part], obj) || "";
};

const openSendDialog = (quotation) => {
    selectedQuotation.value = quotation;
    sendForm.value = { emailChecked: false, telegramChecked: false }; // Reset form
    isSendDialogVisible.value = true;
};

const openFeedbackDialog = (quotation) => {
    if (quotation.customer_status === "Sent") {
        selectedQuotation.value = quotation;
        isFeedbackDialogVisible.value = true;
    }
};

const showToast = (
    type = "success",
    title = "Success",
    message = "Operation completed",
    duration = 3000
) => {
    toast.add({
        severity: type,
        summary: title,
        detail: message,
        life: duration,
        group: "tr",
    });
};

const props = defineProps({
    customers: Array,
    products: Array,
    quotations: Array,
});

const form = useForm({
    id: "",
    quotation_no: "",
    quotation_date: "",
    address: "",
    phone_number: "",
    email: "",
    customer_id: "",
    total: 0,
    tax: 0,
    products: [],
});

const openForm = (quotations = null) => {
    if (quotations) {
        form.quotation_no = quotations.id;
        form.quotation_date = quotations.quotation_date;
        form.address = quotations.address;
        form.phone_number = quotations.phone_number;
        form.email = quotations.email;
        form.customer_id = quotations.customer_id;
    } else {
        form.reset();
    }
    isFormVisible.value = true;
};

const isFormVisible = ref(false);
const editQuotation = () => {
    if (selectedQuotation.value.status !== "Approved") {
        console.log(selectedQuotation.value);
        router.visit(route("quotations.create"), {
            method: "get",
            data: {
                quotation: JSON.stringify(selectedQuotation.value),
            },
            preserveState: true,
            preserveScroll: true,
        });

        isViewDialogVisible.value = false;
    } else {
        showToast(
            "error",
            "Edit Disabled",
            "You cannot edit an approved quotation!",
            3000
        );
    }
};

const closeForm = () => {
    isFormVisible.value = false;
    form.reset();
};

const viewQuotation = (quotation) => {
    selectedQuotation.value = quotation;
    if (quotation.comments && quotation.comments.length) {
        const lastComment = quotation.comments[quotation.comments.length - 1];
        comment.value = lastComment.comment;
        userRole.value = lastComment.role;
    } else {
        comment.value = "";
        userRole.value = "manager";
    }
    isViewDialogVisible.value = true;
};

const columns = [
    { field: "quotation_no", header: "Quotation No." },
    { field: "quotation_date", header: "Quotation Date" },
    { field: "customer.name", header: "Customer/Organization Name" },
    { field: "address", header: "Address" },
    { field: "phone_number", header: "Phone Number" },
    { field: "email", header: "email" },
    { field: "terms", header: "Terms" },
    { field: "total", header: "Total" },
    { field: "tax", header: "Tax" },
    { field: "status", header: "Status" },
    { field: "customer_status", header: "Customer Status" },
];
const printQuotation = (quotation_no, include_catelog = 0) => {
    const quotUrl = `/quotations/${quotation_no}?include_catelog=${include_catelog}`;
    // Inertia.visit(quotUrl);
    const printWindow = window.open(quotUrl, "_self");
};

const selectedColumns = ref(columns);
const showColumns = ref(columns);
const updateColumns = (columns) => {
    showColumns.value = selectedColumns.value;
};

const selectedStatus = ref();
const StatusOptions = ref([
    { name: "Pending", code: "Pending" },
    { name: "Approved", code: "Approved" },
    { name: "Revise", code: "Revise" },
]);

const updateQuotationStatus = (quotation, message) => {
    router.put(
        `/quotations/${quotation.id}/update-status`,
        {
            status: quotation.status,
            customer_status: quotation.customer_status,
            comment: quotation.comment,
            role: quotation.role,
        },
        {
            preserveScroll: true,
            onSuccess: (response) => {
                showToast("success", "Success", message, 3000);
                router.get(route("quotations.list"), {}, { replace: true });
            },
            onError: (err) => {
                showToast(
                    "error",
                    "Error",
                    "Failed to update quotation status!",
                    3000
                );
            },
        }
    );
};

const approveQuotation = () => {
    if (!comment.value.trim()) {
        showToast(
            "error",
            "Error",
            "Please enter a comment before approving!",
            3000
        );
        return;
    }
    selectedQuotation.value.status = "Approved";
    selectedQuotation.value.customer_status = "Sent";
    selectedQuotation.value.comment = comment.value;
    selectedQuotation.value.role = userRole.value;

    updateQuotationStatus(
        selectedQuotation.value,
        "Quotation approved and sent to customer!"
    );
    isViewDialogVisible.value = false;
    comment.value = "";
};

const reviseQuotation = () => {
    if (!comment.value.trim()) {
        showToast(
            "error",
            "Error",
            "Please enter a comment before revising!",
            3000
        );
        return;
    }
    selectedQuotation.value.status = "Revise";
    selectedQuotation.value.comment = comment.value;
    selectedQuotation.value.role = userRole.value;

    updateQuotationStatus(
        selectedQuotation.value,
        "Quotation revised and sent to customer!"
    );
    isViewDialogVisible.value = false;
    comment.value = "";
};

const showCancel = () => {
    toast.add({
        severity: "secondary",
        summary: "Cancelled",
        detail: "Operation was cancelled.",
        life: 3000,
        group: "tr",
    });
    isViewDialogVisible.value = false;
};

const sendForm = ref({
    emailChecked: false,
    telegramChecked: false,
});

const handleApprove = async () => {
    if (!feedbackComment.value.trim()) {
        showToast(
            "error",
            "Error",
            "Please enter a comment before approving!",
            3000
        );
        return;
    }
    try {
        await router.put(
            `/quotations/${selectedQuotation.value.id}/update-status`,
            {
                customer_status: "Accept",
                status: "Approved",
                comment: feedbackComment.value,
                role: "customer",
            }
        );
        showToast(
            "success",
            "Success",
            "Quotation approved successfully!",
            3000
        );
        isFeedbackDialogVisible.value = false;
        feedbackComment.value = "";
        router.get(route("quotations.list"), {}, { preserveScroll: true });
    } catch (error) {
        showToast("error", "Error", "Failed to approve quotation.", 3000);
    }
};

const handleReject = async () => {
    if (!feedbackComment.value.trim()) {
        showToast(
            "error",
            "Error",
            "Please enter a comment before rejecting!",
            3000
        );
        return;
    }

    try {
        await router.put(
            `/quotations/${selectedQuotation.value.id}/update-status`,
            {
                customer_status: "Reject",
                status: "Revise",
                comment: feedbackComment.value,
                role: "customer",
            }
        );

        showToast(
            "success",
            "Success",
            "Quotation rejected successfully!",
            3000
        );
        isFeedbackDialogVisible.value = false;
        feedbackComment.value = "";
        router.get(route("quotations.list"), {}, { preserveScroll: true });
    } catch (error) {
        showToast("error", "Error", "Failed to reject quotation.", 3000);
    }
};

const handleStatusClick = (quotation) => {
    selectedQuotation.value = quotation;

    if (quotation.customer_status === "Sent") {
        isSendDialogVisible.value = true;
    } else if (quotation.customer_status === "Pending") {
        isFeedbackDialogVisible.value = true;
    }
};

const sendQuotationToCustomer = async () => {
    // Ensure the email option is selected
    if (!sendForm.value.emailChecked && !sendForm.value.telegramChecked) {
        toast.add({
            severity: "error",
            summary: "Error",
            detail: "Please select at least one option (Email or Telegram).",
            life: 3000,
        });
        return;
    }

    isSending.value = true;

    try {
        // Generate the PDF for the quotation
        generatePDF(selectedQuotation.value);

        // Prepare the form data for sending
        const formData = new FormData();
        formData.append("quotation_id", selectedQuotation.value.id);
        formData.append("send_email", sendForm.value.emailChecked);
        formData.append("send_telegram", sendForm.value.telegramChecked);

        // Add the generated PDF to the form data
        const pdfBlob = await html2pdf()
            .from(document.createElement("div"))
            .outputPdf("blob");
        formData.append(
            "pdf_file",
            pdfBlob,
            `quotation_${selectedQuotation.value.quotation_no}.pdf`
        );

        // Get CSRF token from the meta tag
        const csrfToken = document
            .querySelector('meta[name="csrf_token"]')
            .getAttribute("content");

        // Send the request to the backend to save/send the quotation
        const response = await fetch("/quotations/send", {
            method: "POST",
            body: formData, // Send the FormData directly
            headers: {
                "X-CSRF-TOKEN": csrfToken, // CSRF token for security
            },
        });

        // Parse the JSON response from the server
        const responseData = await response.json();

        if (responseData.success) {
            // If the quotation was approved, change the status to "Pending"
            if (selectedQuotation.value.status === "Approved") {
                await router.put(
                    `/quotations/${selectedQuotation.value.id}/update-status`,
                    {
                        status: "Approved", // Change the status to Pending
                    }
                );

                showToast(
                    "success",
                    "Success",
                    "Quotation sent and status updated to 'Pending'!",
                    3000
                );
            } else {
                showToast(
                    "success",
                    "Success",
                    "Quotation sent successfully!",
                    3000
                );
            }

            // Close the send dialog
            isSendDialogVisible.value = false;
        } else {
            // Handle case where sending the quotation fails
            showToast(
                "error",
                "Error",
                "Failed to send the quotation. Please try again.",
                3000
            );
        }
    } catch (error) {
        // Catch any errors and show the error message
        toast.add({
            severity: "error",
            summary: "Error",
            detail:
                error.message || "Failed to send quotation. Please try again.",
            life: 3000,
        });
    } finally {
        // Reset the sending state
        isSending.value = false;
    }
};
const formatCurrency = (value) => {
    return new Intl.NumberFormat("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(value || 0);
};
</script>

<style scoped>
.custom-button {
    padding: 7px 7px;
    font-size: 12px;
    min-width: 30px;
}
.custom-approved {
    border: 1px solid green;
    background-color: #d4edda;
    color: green;
}
.p-dialog-header {
    pointer-events: none;
}
:deep(.p-breadcrumb) {
    background: transparent;
    border: none;
    padding-left: 0;
    padding-right: 0;
}

:deep(.p-menuitem-text) {
    font-size: 0.875rem;
}
.comment-container {
    padding: 0.5rem;
}

.latest-comment {
    background-color: #f8f9fa;
    border-left: 3px solid #007ad9;
    padding: 0.5rem;
    border-radius: 0 4px 4px 0;
}

.comment-text {
    margin: 0;
    word-break: break-word;
}

.no-comment {
    color: #6c757d;
    font-style: italic;
}
.breadcrumb-container {
    background-color: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
    padding: 0.75rem 1rem;
}

/* For PrimeVue Breadcrumb customization */
:deep(.p-breadcrumb) {
    background: transparent;
    border: none;
    padding: 0;
}

:deep(.p-menuitem-text) {
    font-size: 0.875rem;
}
.custom-button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>
