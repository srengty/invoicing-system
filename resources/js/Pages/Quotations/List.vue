<template>
    <Head title="Quotations" />
    <GuestLayout>
        <Toast position="top-center" group="tc" />
        <Toast position="top-right" group="tr" />

        <div class="quotations text-sm">
            <div class="flex justify-between items-center p-4">
                <h1 class="text-2xl">Quotations list</h1>
            </div>
            <div class="flex justify-between items-center p-4">
                Quotations are proposed to customer to bid for a project.
            </div>
            <div class="flex justify-end p-4 gap-4">
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
                    :value="quotations"
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

                    <Column
                        field="customer_status"
                        header="Customer Status"
                        style="width: 15%"
                    >
                        <template #body="slotProps">
                            <span
                                class="p-2 border rounded w-24 h-8 flex items-center justify-center"
                                :class="{
                                    'bg-blue-100 text-blue-800 border-blue-400':
                                        slotProps.data.customer_status ===
                                        'Sent',
                                    'bg-green-100 text-green-800 border-green-400':
                                        slotProps.data.customer_status ===
                                        'Accept',
                                    'bg-red-100 text-red-800 border-red-400':
                                        slotProps.data.customer_status ===
                                        'Reject',
                                }"
                            >
                                <!-- PrimeVue icons in front of the text -->
                                <i
                                    :class="{
                                        'pi pi-send':
                                            slotProps.data.customer_status ===
                                            'Sent',
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

                    <Column header="Comment / Role" style="width: 15%">
                        <template #body="slotProps">
                            <div
                                v-if="
                                    slotProps.data.comments &&
                                    slotProps.data.comments.length
                                "
                            >
                                <!-- We'll show the last comment in the array -->
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
                            <div v-else>
                                <em>No comment</em>
                            </div>
                        </template>
                    </Column>
                    <Column header="View / Print-out" style="width: 20%">
                        <template #body="slotProps">
                            <div class="flex gap-4">
                                <Button
                                    icon="pi pi-eye"
                                    aria-label="View"
                                    severity="info"
                                    class="custom-butto"
                                    @click="viewQuotation(slotProps.data)"
                                    size="small"
                                    raised
                                />
                                <Button
                                    icon="pi pi-print"
                                    aria-label="Print out"
                                    class="custom-button"
                                    @click="printQuotation(slotProps.data.id)"
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
                        <p><strong>ID:</strong> {{ selectedQuotation.id }}</p>
                        <p>
                            <strong>Quotation No.:</strong>
                            {{ selectedQuotation.quotation_no }}
                        </p>
                        <p>
                            <strong>Quotation Date:</strong>
                            {{ selectedQuotation.quotation_date }}
                        </p>
                        <p>
                            <strong>Customer ID:</strong>
                            {{ selectedQuotation.customer_id }}
                        </p>
                        <p>
                            <strong>Customer Name:</strong>
                            {{ selectedQuotation.customer?.name || "N/A" }}
                        </p>
                        <p>
                            <strong>Address:</strong>
                            {{ selectedQuotation.address }}
                        </p>
                        <p>
                            <strong>Phone Number:</strong>
                            {{ selectedQuotation.phone_number }}
                        </p>

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
                        <!-- Display the comment and role if available -->
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
            </div>
        </div>
    </GuestLayout>
</template>

<script setup>
``;
import ChooseColumns from "@/Components/ChooseColumns.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Button from "primevue/button";
import Dialog from "primevue/dialog";
import { useForm } from "@inertiajs/vue3";
import VirtualScroller from "primevue/virtualscroller";
import moment from "moment";
import Dropdown from "primevue/dropdown";
import { router, usePage } from "@inertiajs/vue3"; // for printing
import Toast from "primevue/toast";
import { useToast } from "primevue/usetoast";

const toast = useToast();
const isViewDialogVisible = ref(false);
const selectedQuotation = ref({});
const selectedQuo_customer = ref([]);
const userRole = ref("manager");
const comment = ref("");

const formMode = ref("create");
const quotationData = ref({});

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
    quotation_no: "",
    quotation_date: "",
    address: "",
    phone_number: "",
    customer_id: "",
    total: 0,
    tax: 0,
    // grand_total: 0,
    products: [],
});

const openForm = (quotations = null) => {
    if (quotations) {
        form.quotation_no = quotations.id;
        form.quotation_date = quotations.quotation_date;
        form.address = quotations.address;
        form.phone_number = quotations.phone_number;
        form.customer_id = quotations.customer_id;
        // form.grand_total = quotations.grand_total;
    } else {
        form.reset();
    }
    isFormVisible.value = true;
};
const isFormVisible = ref(false);
// const editQuotation = () => {
//     if (selectedQuotation.value.status !== "Approved") {
//         router.visit(route("quotations.create"), {
//             method: "get",
//             data: {
//                 quotation: selectedQuotation.value,
//             },
//             preserveState: true,
//             preserveScroll: true,
//         });

//         isViewDialogVisible.value = false;
//     } else {
//         showToast(
//             "error",
//             "Edit Disabled",
//             "You cannot edit an approved quotation!",
//             3000
//         );
//     }
// };
const editQuotation = () => {
    if (selectedQuotation.value.status !== "Approved") {
        router.visit(route("quotations.create"), {
            method: "get",
            data: {
                quotation: selectedQuotation.value,
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

// Open view quotation dialog
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

    // When Edit is clicked, navigate to create.vue with quotation data
    const navigateToEditQuotation = () => {
        router.visit(route("quotations.create"), {
            method: "get",
            data: { quotation: selectedQuotation.value }, // Pass selected quotation data
            preserveState: true,
            preserveScroll: true,
        });
        isViewDialogVisible.value = false; // Close the dialog
    };
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
    // { field: 'grand_total', header: 'Grand Total' },
    { field: "status", header: "Status" },
    { field: "customer_status", header: "Customer Status" },
];

// Printing quotations
const printQuotation = (quotation_no) => {
    const quotUrl = `/quotations/${quotation_no}`;
    const printWindow = window.open(quotUrl, "_blank"); //create new tab

    printWindow.onload = () => {
        printWindow.print();
    };
};
const selectedColumns = ref(columns);
const showColumns = ref(columns);
const updateColumns = (columns) => {
    showColumns.value = selectedColumns.value;
};

// quotations status
const selectedStatus = ref();
const StatusOptions = ref([
    { name: "Pending", code: "Pending" },
    { name: "Approved", code: "Approved" },
    { name: "Revise", code: "Revise" },
]);

// Update Quotation Status (called by Approve/Revise)
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

// Approve Quotation
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

// Revise Quotation
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
    // selectedQuotation.value.customer_status = "Sent";
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
