<template>
    <Head title="Receipts" />
    <GuestLayout>
        <NavbarLayout class="fixed top-0 z-50 w-5/6" />
        <Toast position="top-center" group="tc" />
        <Toast position="top-right" group="tr" />
        <div class="py-3 mt-16">
            <Breadcrumb
                :model="breadcrumbItems"
                class="border-none bg-transparent p-0"
            >
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
        <div class="receipts px-4">
            <!-- Page Header -->
            <div class="flex justify-end items-center mb-3">
                <div class="flex gap-2">
                    <Dropdown
                        v-model="searchType"
                        :options="searchOptions"
                        optionLabel="label"
                        optionValue="value"
                        class="w-48 text-sm"
                        placeholder="Select field to search"
                    />
                    <InputText
                        v-model="searchTerm"
                        placeholder="Search"
                        class="w-64"
                        size="small"
                    />
                    <Button
                        label="Clear"
                        @click="clearFilters"
                        class="p-button-secondary w-24"
                        icon="pi pi-times"
                        size="small"
                        severity="success"
                        variant="outlined"
                    />
                    <Button
                        v-if="userPermissions.canCreateReceipt"
                        icon="pi pi-plus"
                        label="New"
                        raised
                        size="small"
                        @click="openCreate"
                    />
                </div>
            </div>
            <DataTable
                :value="filteredReceipts"
                paginator
                :rows="5"
                :rowsPerPageOptions="[5, 10, 20, 50]"
                :stripedRows="true"
                :showGridlines="true"
                resizableColumns
                columnResizeMode="fit"
                class="mt-4"
            >
                <Column
                    field="receipt_no"
                    header="Receipt No"
                    style="width: 5%; font-size: 12px"
                ></Column>
                <Column
                    field="receipt_date"
                    header="Date"
                    style="width: 5%; font-size: 12px"
                >
                    <template #body="slotProps">
                        {{ formatDate(slotProps.data.receipt_date) }}
                    </template>
                </Column>
                <Column header="Invoice No" style="width: 10%; font-size: 14px">
                    <template #body="slotProps">
                        <ul>
                            <li
                                v-for="invoice in slotProps.data.invoices"
                                :key="invoice.id"
                            >
                                {{ invoice.invoice_no }}
                            </li>
                        </ul>
                    </template>
                </Column>
                <Column
                    field="customer.name"
                    header="Customer"
                    style="width: 5%; font-size: 12px"
                ></Column>
                <Column
                    field="paid_amount"
                    header="Amount Paid"
                    style="width: 5%; font-size: 12px"
                >
                    <template #body="slotProps">
                        {{ formatCurrency(slotProps.data.paid_amount) }}
                        <span class="text-xs text-gray-500 ml-1">
                            ( KHR )
                        </span>
                    </template>
                </Column>
                <Column
                    field="payment_method"
                    header="Payment Method"
                    style="width: 5%; font-size: 12px"
                ></Column>
                <Column
                    field="payment_reference_no"
                    header="Payment Reference No"
                    style="width: 5%; font-size: 12px"
                ></Column>
                <Column header="Actions" style="width: 5%; font-size: 12px">
                    <template #body="slotProps">
                        <Button
                            severity="info"
                            aria-label="View"
                            icon="pi pi-eye"
                            size="small"
                            @click="viewReceipt(slotProps.data)"
                            outlined
                            class="mr-2"
                            style="width: 30px; height: 30px"
                        />
                        <Button
                            v-if="userPermissions.canEditReceipt"
                            severity=""
                            aria-label="Print"
                            icon="pi pi-print"
                            size="small"
                            style="width: 30px; height: 30px"
                            @click="
                                router.get(
                                    route('receipts.print', {
                                        id: slotProps.data.receipt_no,
                                    })
                                )
                            "
                            outlined
                        />
                    </template>
                </Column>
            </DataTable>
        </div>
        <!-- Receipt Detail Dialog -->
        <Dialog
            v-model:visible="dialogVisible"
            header="Receipt Details"
            :modal="true"
            :style="{ width: '50vw' }"
            @hide="closeDialog"
            :draggable="false"
            :resizable="false"
            :position="'center'"
            :closeOnEscape="false"
        >
            <div
                class="p-3 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 m-4 mt-0"
            >
                <div class="">
                    <div class="grid">
                        <label for="invoice_no">Invoice No</label>
                        <input
                            v-model="selectedReceipt.invoice_no"
                            class="p-inputtext p-component"
                            disabled
                        />
                    </div>
                </div>
                <div class="">
                    <div class="grid">
                        <label for="receipt_no">Receipt No</label>
                        <input
                            v-model="selectedReceipt.receipt_no"
                            class="p-inputtext p-component"
                            disabled
                        />
                    </div>
                </div>
                <div class="">
                    <div class="grid">
                        <label for="receipt_date">Date</label>
                        <!-- Format date using a computed property or method, but bind to the raw value -->
                        <input
                            :value="formatDate(selectedReceipt.receipt_date)"
                            class="p-inputtext p-component"
                            disabled
                        />
                    </div>
                </div>
                <div class="">
                    <div class="grid">
                        <label for="customer_code">Customer Code</label>
                        <input
                            v-model="selectedReceipt.customer.code"
                            class="p-inputtext p-component"
                            disabled
                        />
                    </div>
                </div>
                <div class="">
                    <div class="grid">
                        <label for="customer_name">Customer Name</label>
                        <input
                            v-model="selectedReceipt.customer.name"
                            class="p-inputtext p-component"
                            disabled
                        />
                    </div>
                </div>
                <div class="">
                    <div class="grid">
                        <label for="purpose">Purpose</label>
                        <input
                            v-model="selectedReceipt.purpose"
                            class="p-inputtext p-component"
                            disabled
                        />
                    </div>
                </div>
                <div class="">
                    <div class="grid">
                        <label for="paid_amount">Amount Paid</label>
                        <input
                            :value="formatCurrency(selectedReceipt.paid_amount)"
                            class="p-inputtext p-component"
                            disabled
                        />
                    </div>
                </div>
                <div class="">
                    <div class="grid">
                        <label for="amount_in_words">Amount in Words</label>
                        <input
                            v-model="selectedReceipt.amount_in_words"
                            class="p-inputtext p-component"
                            disabled
                        />
                    </div>
                </div>
                <div class="">
                    <div class="grid">
                        <label for="payment_method">Payment Method</label>
                        <input
                            v-model="selectedReceipt.payment_method"
                            class="p-inputtext p-component"
                            disabled
                        />
                    </div>
                </div>
                <div class="">
                    <div class="grid">
                        <label for="payment_reference_no"
                            >Payment Reference No</label
                        >
                        <input
                            v-model="selectedReceipt.payment_reference_no"
                            class="p-inputtext p-component"
                            disabled
                        />
                    </div>
                </div>
            </div>
        </Dialog>
        <!-- Dialog for Create Receipts -->
        <CreateReceiptDialog
            ref="receiptDialog"
            :customerCategories="customerCategories"
            @receipt-created="handleReceiptCreated"
            :draggable="false"
        />
        <!-- Dialog for Edit  -->
        <CreateReceiptDialog
            ref="receiptDialog"
            :customer-categories="customerCategories"
            :receipt="editingReceipt"
            @receipt-created="handleReceiptCreated"
            @receipt-updated="handleReceiptUpdated"
            :draggable="false"
        />
    </GuestLayout>
</template>

<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import NavbarLayout from "@/Layouts/NavbarLayout.vue";
import CreateReceiptDialog from "@/Pages/Receipts/Create.vue";
import { useToast } from "primevue/usetoast";
import { ref, computed } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import { usePage } from "@inertiajs/vue3";
import moment from "moment";
import {
    Dropdown,
    InputText,
    Button,
    DataTable,
    Column,
    Toast,
    Breadcrumb,
    Dialog,
} from "primevue";

const toast = useToast();
const receiptDialog = ref(null);
const dialogVisible = ref(false);
const selectedReceipt = ref(null);
const customerCategories = ref([]);

const props = defineProps({
    receipts: {
        type: Array,
        default: () => [],
    },
});
const page = usePage();
const userPermissions = computed(() => {
    const roles = page.props.userRoles || [];
    return {
        canCreateReceipt: roles.some((role) =>
            role.toLowerCase().includes("revenue manager")
        ),
        canEditReceipt: roles.some((role) =>
            role.toLowerCase().includes("revenue manager")
        ),
    };
});
// Breadcrumb items for navigation
const breadcrumbItems = computed(() => [
    {
        label: "",
        to: "/dashboard",
        icon: "pi pi-home",
    },
    { label: "Receipts", to: route("receipts.index") },
]);

const openCreate = () => {
    if (receiptDialog.value) {
        receiptDialog.value.show();
    }
};

// Initialize search filters
const searchTerm = ref("");
const searchType = ref("");
const searchOptions = ref([
    { label: "Receipt No", value: "receipt_no" },
    { label: "Invoice No", value: "invoice_no" },
    { label: "Customer", value: "customer.name" },
]);
const clearFilters = () => {
    searchType.value = "";
    searchTerm.value = "";
};

// Filter logic for the receipt list
const filteredReceipts = computed(() => {
    return props.receipts.filter((receipt) => {
        if (!searchTerm.value) return true; // If no search term, return all receipts

        let fieldValue = receipt[searchType.value] || ""; // Get the field value
        // Handle nested fields (like 'customer.name')
        if (searchType.value === "customer.name") {
            fieldValue = receipt.customer ? receipt.customer.name : ""; // Access the customer name
        }

        const matchesSearch =
            fieldValue &&
            fieldValue
                .toString()
                .toLowerCase()
                .includes(searchTerm.value.toLowerCase());

        return matchesSearch;
    });
});

const viewReceipt = (receipt) => {
    selectedReceipt.value = receipt;
    dialogVisible.value = true;
};

const editReceipt = (receipt) => {
    editingReceipt.value = {
        ...receipt,
        customer_id: receipt.customer.id,
        receipt_date: new Date(receipt.receipt_date),
    };

    if (receiptDialog.value) {
        receiptDialog.value.show();
    }
};

const closeDialog = () => {
    dialogVisible.value = false;
};

const formatDate = (date, format = "YYYY-MM-DD") => {
    if (!date) return "N/A";
    const parsedDate = moment(date, ["YYYY-MM-DD", moment.ISO_8601], true);
    return parsedDate.isValid() ? parsedDate.format(format) : "Invalid Date";
};

const formatCurrency = (value) => {
    if (value === null || value === undefined || value === "") return "0.00";
    const numValue =
        typeof value === "string"
            ? parseFloat(value.replace(/,/g, ""))
            : Number(value);

    return numValue.toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
};
const editingReceipt = ref(null);

const handleReceiptCreated = async ({ shouldReload }) => {
    if (shouldReload) {
        await router.reload();
        // toast.add({
        //     severity: "success",
        //     summary: "Success",
        //     detail: "Receipt created successfully",
        //     life: 3000,
        // });
    }
};

// const handleReceiptUpdated = async ({ shouldReload }) => {
//     editingReceipt.value = null;
//     if (shouldReload) {
//         await router.reload({ only: ["receipts"] });
//         // toast.add({
//         //     severity: "success",
//         //     summary: "Success",
//         //     detail: "Receipt updated successfully",
//         //     life: 3000,
//         // });
//     }
// };
const handleReceiptUpdated = (updatedReceipt) => {
    // Find the index of the updated receipt in the receipts array
    const index = props.receipts.findIndex(
        (receipt) => receipt.receipt_no === updatedReceipt.receipt_no
    );

    // If the receipt exists, update it in the array
    if (index !== -1) {
        props.receipts[index] = updatedReceipt;
    }
};
</script>

<style scoped></style>
