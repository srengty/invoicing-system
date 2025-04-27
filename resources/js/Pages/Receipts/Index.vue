<template>
    <Head title="Receipts" />
    <GuestLayout>
        <NavbarLayout />

        <div class="py-3">
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

        <Toast position="top-center" group="tc" />
        <Toast position="top-right" group="tr" />

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
                        icon="pi pi-plus"
                        label="New"
                        @click="openCreate"
                        raised
                        size="small"
                    />
                </div>
            </div>
            <DataTable
                :value="receipts"
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
                    sortable
                    style="width: 5%; font-size: 14px"
                ></Column>
                <Column
                    field="receipt_date"
                    header="Date"
                    sortable
                    style="width: 5%; font-size: 14px"
                >
                    <template #body="slotProps">
                        {{ formatDate(slotProps.data.receipt_date) }}
                    </template>
                </Column>
                <Column
                    field="invoice_no"
                    header="Invoice No"
                    sortable
                    style="width: 5%; font-size: 14px"
                ></Column>
                <Column
                    field="customer.name"
                    header="Customer"
                    sortable
                    style="width: 5%; font-size: 14px"
                ></Column>
                <Column
                    field="amount_paid"
                    header="Amount Paid"
                    sortable
                    style="width: 5%; font-size: 14px"
                >
                    <template #body="slotProps">
                        {{ formatCurrency(slotProps.data.amount_paid) }}
                    </template>
                </Column>
                <Column
                    field="payment_method"
                    header="Payment Method"
                    sortable
                    style="width: 5%; font-size: 14px"
                ></Column>
                <Column
                    field="payment_reference_no"
                    header="Payment Reference No (if any)"
                    sortable
                    style="width: 5%; font-size: 14px"
                ></Column>
                <Column header="View" style="width: 5%; font-size: 14px">
                    <template #body="slotProps">
                        <Button
                            icon="pi pi-eye"
                            class="p-button-text p-button-info"
                            size="small"
                            @click="viewReceipt(slotProps.data)"
                        />
                    </template>
                </Column>
            </DataTable>
        </div>
        <CreateReceiptDialog ref="receiptDialog" />
    </GuestLayout>
</template>

<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import NavbarLayout from "@/Layouts/NavbarLayout.vue";
import CreateReceiptDialog from "@/Pages/Receipts/Create.vue";
import { ref, computed } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import moment from "moment";
import {
    Dropdown,
    InputText,
    Button,
    DataTable,
    Column,
    Toast,
    Breadcrumb,
} from "primevue";

const props = defineProps({
    receipts: {
        type: Array,
        default: () => [],
    },
});

// Breadcrumb items for navigation
const breadcrumbItems = computed(() => [
    { label: "Home", to: "/" },
    { label: "Receipts", to: route("receipts.index") },
]);

const receiptDialog = ref(null);
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

// Filter logic for the receipt list
const filteredReceipts = computed(() => {
    return props.receipts.filter((receipt) => {
        const fieldValue = receipt[searchType.value] || "";
        const matchesSearch =
            !searchTerm.value ||
            fieldValue.toLowerCase().includes(searchTerm.value.toLowerCase());
        return matchesSearch;
    });
});

const viewReceipt = (receipt) => {
    router.get(route("receipts.show", { id: receipt.id }));
};

// Formatting functions for date and currency
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
</script>

<style scoped></style>
