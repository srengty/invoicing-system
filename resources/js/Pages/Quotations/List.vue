<template>
    <Head title="Quotations" />
    <meta name="_token" content="{{ csrf_token() }}" />

    <GuestLayout>
        <Toast position="top-center" group="tc" />
        <Toast position="top-right" group="tr" />

        <div class="quotations text-sm">
            <div class="flex justify-between items-center p-4">
                <h1 class="text-2xl">Quotations list</h1>
            </div>
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
                    <Column header="No." style="width: 5%">
                        <template #body="slotProps">
                            {{ slotProps.index + 1 }}
                        </template>
                    </Column>
                    <Column
                        field="customer.name"
                        header="Customer/Organization Name"
                        style="width: 20%"
                    />
                    <Column field="total" header="Total" style="width: 10%" />

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
                        style="width: 15%"
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
                    <Column header="Comment / Role" style="width: 15%">
                        <template #body="slotProps">
                            <div
                                v-if="
                                    slotProps.data.comments &&
                                    slotProps.data.comments.length
                                "
                            >
                                <p>
                                    <strong>Comment:</strong>
                                    {{
                                        slotProps.data.comments[
                                            slotProps.data.comments.length - 1
                                        ].comment
                                    }}
                                </p>
                                <p>
                                    <strong>Role:</strong>
                                    {{
                                        slotProps.data.comments[
                                            slotProps.data.comments.length - 1
                                        ].role
                                    }}
                                </p>
                            </div>
                            <div v-else><em>No comment</em></div>
                        </template>
                    </Column>
                    <Column header="View / Print-out" style="width: 20%">
                        <template #body="slotProps">
                            <div class="flex gap-4">
                                <Button
                                    icon="pi pi-eye"
                                    aria-label="View"
                                    severity="info"
                                    class="custom-button"
                                    @click="viewQuotation(slotProps.data)"
                                    size="small"
                                    raised
                                />
                                <Button
                                    icon="pi pi-print"
                                    aria-label="Print out"
                                    class="custom-button"
                                    @click="
                                        printQuotation(slotProps.data.id, 1)
                                    "
                                    size="small"
                                    raised
                                />
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
                                <Column
                                    field="pivot.price"
                                    header="Unit Price"
                                ></Column>
                            </DataTable>
                        </div>

                        <br />
                        <p>
                            <strong>Total:</strong>
                            {{ selectedQuotation.total }}
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

                <!-- Send Dialog display -->
                <Dialog
                    v-model:visible="isSendDialogVisible"
                    header="Send Quotation"
                    modal
                    class="text-sm w-auto"
                >
                    <div
                        v-if="selectedQuotation"
                        class="flex flex-col gap-4 ml-2 mr-2"
                    >
                        <!-- Display Selected Quotation Info -->
                        <div>
                            <strong>Quotation No:</strong>
                            <p>{{ selectedQuotation.quotation_no }}</p>
                        </div>
                        <div>
                            <strong>Customer Name:</strong>
                            <p>
                                {{ selectedQuotation.customer?.name || "N/A" }}
                            </p>
                        </div>

                        <!-- Email Checkbox -->
                        <div class="flex items-center">
                            <input
                                type="checkbox"
                                id="emailCheckbox"
                                v-model="sendForm.emailChecked"
                                class="mr-2"
                            />
                            <label for="emailCheckbox" class="font-bold"
                                >Email:
                                {{
                                    selectedQuotation.customer?.email || "N/A"
                                }}</label
                            >
                        </div>

                        <!-- Telegram Checkbox -->
                        <div class="flex items-center">
                            <input
                                type="checkbox"
                                id="telegramCheckbox"
                                v-model="sendForm.telegramChecked"
                                class="mr-2"
                            />
                            <label for="telegramCheckbox" class="font-bold"
                                >Telegram:
                                {{
                                    selectedQuotation.customer
                                        ?.telegram_number || "N/A"
                                }}</label
                            >
                        </div>
                    </div>

                    <template #footer>
                        <Button
                            label="Cancel"
                            severity="secondary"
                            @click="isSendDialogVisible = false"
                        />
                        <Button
                            label="Send"
                            severity="success"
                            @click="sendQuotationToCustomer"
                            :loading="isSending"
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
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Button from "primevue/button";
import Dialog from "primevue/dialog";
import { useForm } from "@inertiajs/vue3";
import Dropdown from "primevue/dropdown";
import { router } from "@inertiajs/vue3";
import Toast from "primevue/toast";
import { useToast } from "primevue/usetoast";
import { InputText } from "primevue";
import html2pdf from "html2pdf.js";

const toast = useToast();

// Reactive properties
const isViewDialogVisible = ref(false);
const isSendDialogVisible = ref(false);
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
    { field: "terms", header: "Terms" },
    { field: "total", header: "Total" },
    { field: "tax", header: "Tax" },
    { field: "status", header: "Status" },
    { field: "customer_status", header: "Customer Status" },
];

// const printQuotation = (quotation_no) => {
//     const quotUrl = `/quotations/${quotation_no}`;
//     const printWindow = window.open(quotUrl, "_blank");
//     printWindow.onload = () => {
//         printWindow.print();
//     };
// };
const printQuotation = (quotation_no, include_catelog = 0) => {
    // Construct the URL with include_catelog parameter
    const quotUrl = `/quotations/${quotation_no}?include_catelog=${include_catelog}`;

    // Open the quotation in a new window for printing
    const printWindow = window.open(quotUrl, "_blank");

    // Print the quotation once the window has loaded
    printWindow.onload = () => {
        printWindow.print();
    };
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
                customer_status: "Accept", // Change customer status
                comment: feedbackComment.value,
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
        // Refresh the quotations list after updating
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
                customer_status: "Reject", // Change customer status
                comment: feedbackComment.value,
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

        // Refresh the quotations list after updating
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

const generatePDF = (quotation) => {
    const element = document.createElement("div");
    element.innerHTML = `
        <h1>Quotation Details</h1>
        <p><strong>Quotation No.:</strong> ${quotation.quotation_no}</p>
        <p><strong>Customer Name:</strong> ${
            quotation.customer?.name || "N/A"
        }</p>
        <p><strong>Total:</strong> ${quotation.total}</p>
        <h2>Items</h2>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>QTY</th>
                    <th>Unit Price</th>
                </tr>
            </thead>
            <tbody>
                ${quotation.products
                    .map(
                        (product) => `
                    <tr>
                        <td>${product.name}</td>
                        <td>${product.pivot.quantity}</td>
                        <td>${product.pivot.price}</td>
                    </tr>
                `
                    )
                    .join("")}
            </tbody>
        </table>
    `;

    // Generate PDF and get it as a Blob
    html2pdf()
        .from(element)
        .get("blob") // Get the generated PDF as a Blob
        .then((pdfBlob) => {
            console.log("PDF generated successfully!");
            console.log(pdfBlob);
            const url = URL.createObjectURL(pdfBlob);

            // Open the PDF in a new tab with the filename
            const link = document.createElement("a");
            link.href = url;
            link.download = filename; // Set the filename
            document.body.appendChild(link);
            link.click(); // Trigger the download
            // Create a FormData object to send the PDF to the server
            const formData = new FormData();
            formData.append(
                "pdf_file",
                pdfBlob,
                `quotation_${quotation.quotation_no}.pdf`
            );

            // Send the PDF to the server via an AJAX POST request
            // fetch('/save-pdf', {
            //     method: 'POST',
            //     body: formData,
            // })
            //     .then((response) => response.json())
            //     .then((data) => {
            //         if (data.success) {
            //             console.log("PDF saved successfully!");
            //         } else {
            //             console.error("Error saving PDF on server.");
            //         }
            //     })
            //     .catch((error) => {
            //         console.error("Error sending PDF to server:", error);
            //     });
        })
        .save();
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
</script>

<style>
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
</style>
