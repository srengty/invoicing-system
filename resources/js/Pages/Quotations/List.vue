<template>
    <Head title="Quotations" />
    <meta name="_token" content="{{ csrf_token() }}" />
    <GuestLayout>
        <NavbarLayout class="fixed top-0 z-50 w-5/6" />
        <!-- PrimeVue Breadcrumb -->
        <div class="px-4 py-3 mt-20">
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
        <ConfirmDialog
            :draggable="false"
            :resizable="false"
            :position="'center'"
            :closeOnEscape="false"
        />
        <div class="quotations px-4">
            <div class="flex justify-end items-center mb-6">
                <div class="flex gap-2">
                    <Dropdown
                        v-model="searchType"
                        :options="searchOptions"
                        optionLabel="label"
                        optionValue="value"
                        class="w-48 h-9 text-sm"
                        size="small"
                        placeholder="Select field to search"
                    />
                    <InputText
                        v-model="searchTerm"
                        placeholder="Search"
                        class="w-64 h-9 text-sm"
                        size="small"
                    />
                    <Button
                        label="Clear"
                        @click="clearFilters"
                        class="p-button-secondary w-24"
                        icon="pi pi-times"
                        severity="success"
                        variant="outlined"
                        size="small"
                    />
                    <Link :href="route('quotations.create')"
                        ><Button
                            v-if="userPermissions.canIssueQuotation"
                            icon="pi pi-plus"
                            label="Issue Quotation"
                            size="small"
                            raised
                    /></Link>
                    <Link :href="route('invoices.create')"
                        ><Button
                            v-if="userPermissions.canIssueInvoices"
                            icon="pi pi-plus"
                            label="Issue Invoice"
                            size="small"
                            raised
                    /></Link>
                    <Link :href="route('agreements.create')"
                        ><Button
                            v-if="userPermissions.canIssueAgreement"
                            icon="pi pi-plus"
                            label="Record Agreement"
                            size="small"
                            raised
                    /></Link>
                </div>
            </div>
            <div>
                <DataTable
                    :value="filteredQuotations"
                    paginator
                    :rows="5"
                    :rowsPerPageOptions="[5, 10, 20, 50]"
                    :showGridlines="true"
                    tableStyle="min-width: 50rem"
                >
                    <Column
                        field="customer.name"
                        header="Customer/Organization Name"
                        style="width: 15%; font-size: 12px"
                    />
                    <Column
                        field="total"
                        header="Total"
                        style="width: 10%; font-size: 12px"
                    >
                        <template #body="slotProps">
                            {{ formatCurrency(slotProps.data.total) }}
                            <span class="text-xs text-gray-500 ml-1">
                                (KHR)
                            </span>
                        </template>
                    </Column>
                    <Column
                        field="status"
                        header="Status"
                        style="width: 10%; font-size: 12px"
                    >
                        <template #body="slotProps">
                            <div class="flex items-center">
                                <span
                                    class="p-2 border rounded w-28 h-8 flex items-center justify-center gap-2"
                                    :class="{
                                        'bg-yellow-100 text-yellow-800 border-yellow-400':
                                            slotProps.data.status === 'Pending',
                                        'bg-red-100 text-red-800 border-red-400':
                                            slotProps.data.status === 'Revise',
                                        'bg-green-100 text-green-800 border-green-400':
                                            slotProps.data.status ===
                                            'Approved',
                                    }"
                                >
                                    <i
                                        :class="{
                                            'pi pi-clock':
                                                slotProps.data.status ===
                                                'Pending',
                                            'pi pi-times':
                                                slotProps.data.status ===
                                                'Revise',
                                            'pi pi-check':
                                                slotProps.data.status ===
                                                'Approved',
                                        }"
                                    ></i>
                                    {{ slotProps.data.status }}
                                </span>
                                <Button
                                    v-if="
                                        slotProps.data.comments &&
                                        slotProps.data.comments.length > 0
                                    "
                                    icon="pi pi-comment"
                                    class="p-button-info ml-2"
                                    @click="
                                        viewComment(slotProps.data.comments)
                                    "
                                    outlined
                                />
                            </div>
                        </template>
                    </Column>
                    <Column
                        v-if="userPermissions.canCustomerStatus"
                        field="customer_status"
                        header="Customer Status"
                        style="width: 10%; font-size: 12px"
                    >
                        <template #body="slotProps">
                            <div class="flex items-center">
                                <span
                                    @click="handleStatusClick(slotProps.data)"
                                    class="p-2 rounded w-24 h-8 flex items-center justify-center"
                                    :class="{
                                        'bg-blue-100 text-blue-800 border-2 border-blue-200':
                                            slotProps.data.customer_status ===
                                            'Sent',
                                        'bg-yellow-100 text-yellow-800 border-yellow-200':
                                            slotProps.data.customer_status ===
                                            'Pending',
                                        'bg-green-100 text-green-800 border-green-200':
                                            slotProps.data.customer_status ===
                                            'Accept',
                                        'bg-red-100 text-red-800 border-red-200':
                                            slotProps.data.customer_status ===
                                            'Reject',
                                    }"
                                >
                                    <i
                                        :class="{
                                            'pi pi-send':
                                                slotProps.data
                                                    .customer_status === 'Sent',
                                            'pi pi-clock':
                                                slotProps.data
                                                    .customer_status ===
                                                'Pending',
                                            'pi pi-check':
                                                slotProps.data
                                                    .customer_status ===
                                                'Accept',
                                            'pi pi-times':
                                                slotProps.data
                                                    .customer_status ===
                                                'Reject',
                                        }"
                                        style="margin-right: 8px"
                                    ></i>
                                    {{ slotProps.data.customer_status }}
                                </span>
                                <Button
                                    v-if="
                                        slotProps.data.comments &&
                                        slotProps.data.comments.length > 0
                                    "
                                    icon="pi pi-comment"
                                    class="p-button-info ml-2"
                                    @click="viewCommentCustomer(slotProps.data)"
                                    outlined
                                />
                            </div>
                        </template>
                    </Column>
                    <Column
                        header="Actions"
                        style="width: 10%; font-size: 12px"
                    >
                        <template #body="slotProps">
                            <div class="flex gap-2">
                                <Button
                                    icon="pi pi-eye"
                                    aria-label="View"
                                    severity=""
                                    class="custom-button"
                                    @click="viewQuotation(slotProps.data)"
                                    size="small"
                                    outlined
                                    :disabled="!slotProps.data.active"
                                />
                                <Button
                                    v-if="userPermissions.canEditQuotation"
                                    icon="pi pi-pencil"
                                    size="small"
                                    outlined
                                    severity="info"
                                    class="custom-button"
                                    :disabled="
                                        slotProps.data.status === 'Approved' ||
                                        !slotProps.data.active
                                    "
                                    @click="editQuotation(slotProps.data)"
                                />
                                <div>
                                    <Button
                                        v-if="userPermissions.canEditQuotation"
                                        icon="pi pi-print"
                                        :label="
                                            slotProps.data.printed_at
                                                ? formatDate(
                                                      slotProps.data.printed_at
                                                  )
                                                : 'Print-out'
                                        "
                                        class="custom-button"
                                        size="small"
                                        @click="
                                            printQuotation(slotProps.data.id)
                                        "
                                        :disabled="
                                            !slotProps.data.quotation_no ||
                                            !slotProps.data.active
                                        "
                                        :severity="
                                            slotProps.data.printed_at ? '' : ''
                                        "
                                    />
                                </div>
                            </div>
                        </template>
                    </Column>
                    <Column
                        header="Active"
                        style="width: 10%; font-size: 12px"
                        v-if="userPermissions.canDeactivateQuotation"
                    >
                        <template #body="slotProps">
                            <Button
                                :icon="
                                    slotProps.data.active
                                        ? 'pi pi-unlock'
                                        : 'pi pi-lock'
                                "
                                :label="
                                    slotProps.data.active
                                        ? 'Activate'
                                        : 'Deactivate'
                                "
                                :severity="
                                    slotProps.data.active ? 'success' : 'danger'
                                "
                                :class="{
                                    'p-button-success': slotProps.data.active,
                                    'p-button-danger': !slotProps.data.active,
                                    ' w-28 h-8 flex items-center justify-center': true,
                                }"
                                @click="toggleActive(slotProps.data)"
                                size="small"
                                outlined
                            />
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
                    :draggable="false"
                    :resizable="false"
                    :position="'center'"
                    :closeOnEscape="false"
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
                            v-if="userPermissions.canApprove"
                            label="Approve"
                            severity="success"
                            @click="approveQuotation"
                            :disabled="selectedQuotation.status === 'Approved'"
                        />
                        <Button
                            v-if="userPermissions.canRevise"
                            label="Revise"
                            severity="danger"
                            @click="reviseQuotation"
                            :disabled="
                                selectedQuotation.status === 'Approved' ||
                                selectedQuotation.status === 'Revise'
                            "
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
                    :draggable="false"
                    :resizable="false"
                    :position="'center'"
                    :closeOnEscape="false"
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
                                label="Accept"
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

                <!-- Confirm Dialog for comment -->
                <Dialog
                    v-model:visible="isCommentDialogVisible"
                    header="Customer's Comment"
                    modal
                    :style="{ width: '20rem' }"
                    class="text-sm"
                    :draggable="false"
                    :resizable="false"
                    :position="'center'"
                    :closeOnEscape="false"
                >
                    <div class="">
                        <p class="text-gray-700 rounded border p-2">
                            {{ selectedComment }}
                        </p>
                        <div class="flex justify-end mt-4">
                            <Button
                                label="Close"
                                class="p-button-secondary"
                                @click="isCommentDialogVisible = false"
                            />
                        </div>
                    </div>
                </Dialog>
            </div>
        </div>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import NavbarLayout from "@/Layouts/NavbarLayout.vue";
import { route } from "ziggy-js";
import { ref, computed, onMounted, reactive, nextTick } from "vue";
import { Head, Link, usePage, router, useForm } from "@inertiajs/vue3";
import { Inertia } from "@inertiajs/inertia";
import { useToast } from "primevue/usetoast";
import { useConfirm } from "primevue/useconfirm";
import { PDFDocument } from "pdf-lib";
import html2pdf from "html2pdf.js";
import {
    InputText,
    DataTable,
    Column,
    Button,
    Dialog,
    ConfirmDialog,
    Dropdown,
    Toast,
    Breadcrumb,
    Checkbox,
} from "primevue";

const toast = useToast();
const confirm = useConfirm();

const isViewDialogVisible = ref(false);
const isFeedbackDialogVisible = ref(false);
const selectedQuotation = ref(null);
const userRole = ref("manager");
const comment = ref("");
const feedbackComment = ref("");

const searchType = ref("");
const searchTerm = ref("");
const searchOptions = ref([
    { label: "Customer Name", value: "customer.name" },
    { label: "Total", value: "total" },
    { label: "Status", value: "status" },
    { label: "Customer Status", value: "customer_status" },
]);
const clearFilters = () => {
    searchType.value = "";
    searchTerm.value = "";
};

// The Breadcrumb Quotations
const page = usePage();
// console.log(page.props.userRoles);
const userPermissions = computed(() => {
    const roles = page.props.userRoles || [];
    return {
        canApprove: roles.some(
            (role) =>
                role.toLowerCase().includes("chef department") ||
                role.toLowerCase().includes("director")
        ),
        canRevise: roles.some(
            (role) =>
                role.toLowerCase().includes("chef department") ||
                role.toLowerCase().includes("director")
        ),
        canCustomerStatus: roles.some((role) =>
            role.toLowerCase().includes("division staff")
        ),
        canIssueQuotation: roles.some((role) =>
            role.toLowerCase().includes("division staff")
        ),
        canIssueInvoices: roles.some((role) =>
            role.toLowerCase().includes("division staff")
        ),
        canIssueAgreement: roles.some((role) =>
            role.toLowerCase().includes("chef department")
        ),
        canDeactivateQuotation: roles.some(
            (role) =>
                role.toLowerCase().includes("division staff") ||
                role.toLowerCase().includes("chef department")
        ),
        canEditQuotation: roles.some(
            (role) =>
                role.toLowerCase().includes("division staff") ||
                role.toLowerCase().includes("chef department")
        ),
    };
});

onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search);
    const isDownload = urlParams.get("download") === "1";

    if (isDownload) {
        downloading.value = true;
        setTimeout(() => {
            generateAndDownloadPDF().finally(() => {
                downloading.value = false;
            });
        }, 1000);
    }
});

onMounted(() => {
    console.log("User Roles:", page.props.userRoles);
    console.log("Permissions:", userPermissions.value);
});
const items = computed(() => [
    {
        label: "",
        to: "/dashboard",
        icon: "pi pi-home",
    },
    { label: page.props.title || "Quotations", to: route("quotations.list") },
]);
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
const downloading = ref(false);

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

const editQuotation = (quotation) => {
    if (quotation.status === "Approved") {
        showToast(
            "error",
            "Edit Disabled",
            "You cannot edit an approved quotation!",
            3000
        );
        return;
    }

    if (!quotation.active) {
        showToast(
            "error",
            "Edit Disabled",
            "You cannot edit an inactive quotation!",
            3000
        );
        return;
    }

    router.visit(route("quotations.edit", { quotation: quotation.id }), {
        preserveState: true,
        preserveScroll: true,
    });
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

const formatDate = (dateString) => {
    if (!dateString) return "Print";
    const date = new Date(dateString);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const day = String(date.getDate()).padStart(2, "0");
    return `${year}-${month}-${day}`;
};

// Mark as printed
const printQuotation = async (quotationId) => {
    try {
        window.open(`/quotations/${quotationId}?include_catelog=0`, "_self");
        const response = await fetch(
            `/quotations/${quotationId}/mark-printed`,
            {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                },
            }
        );
        if (response.ok) {
            router.reload({ only: ["quotations"] });
        } else {
            console.error("Failed to mark as printed");
        }
    } catch (error) {
        console.error("Error printing quotation:", error);
    }
};

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

const formatCurrency = (value) => {
    return new Intl.NumberFormat("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(value || 0);
};

const toggleActive = (quotation) => {
    confirm.require({
        message: `Are you sure you want to ${
            quotation.active ? "deactivate" : "activate"
        } this quotation?`,
        header: "Confirmation",
        icon: "pi pi-exclamation-triangle",
        rejectProps: {
            label: "Cancel",
            icon: "pi pi-times",
            outlined: true,
            size: "small",
        },
        acceptProps: {
            label: "Save",
            icon: "pi pi-check",
            size: "small",
        },
        accept: async () => {
            try {
                await router.put(`/quotations/${quotation.id}/toggle-active`, {
                    active: !quotation.active,
                });

                showToast(
                    "success",
                    "Status Updated",
                    `Quotation ${
                        quotation.active ? "deactivated" : "activated"
                    } successfully!`
                );

                router.get(
                    route("quotations.list"),
                    {},
                    { preserveScroll: true }
                );
            } catch (error) {
                showToast("error", "Error", "Failed to update status.");
            }
        },
    });
};
const selectedComment = ref("");
const isCommentDialogVisible = ref(false);
const selectedIds = ref([]);
const selectedQuotations = ref([]);
const isDownloading = ref(false);

const viewComment = (comments) => {
    const latestComment = comments[comments.length - 1].comment;
    selectedComment.value = latestComment;
    isCommentDialogVisible.value = true;
};

const viewCommentCustomer = (customerStatusData) => {
    const latestCustomerStatusComment =
        customerStatusData.comments?.[customerStatusData.comments.length - 1]
            ?.comment;
    if (latestCustomerStatusComment) {
        selectedComment.value = latestCustomerStatusComment;
        isCommentDialogVisible.value = true;
    } else {
        selectedComment.value = "No comment available for this status.";
        isCommentDialogVisible.value = true;
    }
};

const downloadPrintWithCatalog = async () => {
    if (selectedIds.value.length < 2) {
        showToast(
            "warn",
            "Selection Required",
            "Please select at least 2 quotations to download.",
            3000
        );
        return;
    }

    const isDownloading = ref(true);
    try {
        // Get the selected quotations
        selectedQuotations.value = props.quotations.filter((quotation) =>
            selectedIds.value.includes(quotation.id)
        );

        // Generate PDF for each selected quotation
        const pdfBlobs = await Promise.all(
            selectedQuotations.value.map(async (quotation) => {
                try {
                    // Use the correct endpoint with Ziggy
                    const url = route("quotations.print", {
                        quotation: quotation.id,
                        include_catalog: 0,
                    });

                    const response = await fetch(url);
                    if (!response.ok) {
                        throw new Error(
                            `Failed to fetch quotation ${quotation.id}`
                        );
                    }
                    const html = await response.text();
                    const tempDiv = document.createElement("div");
                    tempDiv.innerHTML = html;
                    document.body.appendChild(tempDiv);

                    // Generate PDF from the HTML
                    const pdfBlob = await html2pdf()
                        .from(tempDiv)
                        .set({
                            margin: 10,
                            filename: `quotation_${quotation.quotation_no}.pdf`,
                            image: { type: "jpeg", quality: 0.98 },
                            html2canvas: { scale: 2 },
                            jsPDF: {
                                unit: "mm",
                                format: "a4",
                                orientation: "portrait",
                            },
                        })
                        .outputPdf("blob");

                    // Clean up
                    document.body.removeChild(tempDiv);
                    return pdfBlob;
                } catch (error) {
                    console.error(
                        `Error generating PDF for quotation ${quotation.id}:`,
                        error
                    );
                    return null;
                }
            })
        );
        // Filter out any failed PDF generations
        const validPdfBlobs = pdfBlobs.filter((blob) => blob !== null);

        if (validPdfBlobs.length === 0) {
            throw new Error("No valid PDFs were generated");
        }
        // Merge all PDFs
        const mergedPdf = await mergePDFs(validPdfBlobs);
        // Download the merged PDF
        const blob = new Blob([mergedPdf], { type: "application/pdf" });
        const link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = `quotations_${new Date()
            .toISOString()
            .slice(0, 10)}.pdf`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        showToast(
            "success",
            "Download Complete",
            `Successfully downloaded ${validPdfBlobs.length} of ${selectedIds.value.length} quotations.`,
            3000
        );
    } catch (error) {
        console.error("Error in downloadPrintWithCatalog:", error);
        showToast(
            "error",
            "Download Failed",
            error.message || "Failed to download selected quotations.",
            3000
        );
    } finally {
        isDownloading.value = false;
    }
};

const generateCatalogPDFs = async (products) => {
    const pdfs = await Promise.all(
        products
            .filter((p) => p.pivot?.include_catalog)
            .map(async (p) => {
                try {
                    const res = await fetch(p.pdf_url);
                    return await res.blob();
                } catch {
                    return null;
                }
            })
    );
    return pdfs.filter((b) => b);
};

const mergePDFs = async (pdfBlobs) => {
    const mergedDoc = await PDFDocument.create();
    for (const blob of pdfBlobs) {
        const src = await PDFDocument.load(
            blob instanceof Blob ? await blob.arrayBuffer() : blob
        );
        const [...pages] = await mergedDoc.copyPages(src, src.getPageIndices());
        pages.forEach((page) => mergedDoc.addPage(page));
    }
    return mergedDoc.save();
};
</script>

<style scoped></style>
