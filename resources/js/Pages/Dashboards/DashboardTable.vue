<template>
    <div class="dashboard">
        <!-- Error message -->
        <div v-if="error" class="p-4 mb-4 text-red-600 bg-red-100 rounded">
            {{ error }}
        </div>

        <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Customers Table -->
            <div class="card-style shadow-md border-2">
                <DataTable
                    :value="customers"
                    scrollable
                    scrollHeight="350px"
                    class="p-datatable-sm"
                    :loading="loading"
                >
                    <template #header>
                        <div class="flex items-center gap-2">
                            <i class="pi pi-users text-[#10B981]"></i>
                            <Link
                                href="/settings/customers"
                                class="text-lg font-bold text-blue-600 hover:underline"
                            >
                                Recent Customers ({{ customers.length }})
                            </Link>
                        </div>
                    </template>
                    <Column field="code" header="Code" sortable></Column>
                    <Column field="name" header="Name" sortable></Column>
                    <Column
                        field="credit_period"
                        header="Credit Period"
                        sortable
                    ></Column>
                    <Column
                        field="customer_category_id"
                        header="Category"
                        sortable
                    ></Column>
                </DataTable>
            </div>
            <div class="card-style shadow-md border-2">
                <DataTable
                    :value="items"
                    scrollable
                    scrollHeight="350px"
                    class="p-datatable-sm"
                    :loading="loading"
                >
                    <template #header>
                        <div class="flex items-center gap-2">
                            <i class="pi pi-box text-[#10B981]"></i>
                            <span class="text-lg font-bold"
                                >Recent Items ({{ items.length }})</span
                            >
                        </div>
                    </template>
                    <Column field="code" header="Code" sortable></Column>
                    <Column field="name" header="Name" sortable></Column>
                    <Column field="unit" header="Unit" sortable></Column>
                    <Column field="price" header="Price" sortable>
                        <template #body="{ data }">
                            {{ formatCurrency(data.price) }}
                        </template>
                    </Column>
                </DataTable>
            </div>

            <!-- Quotations Table -->
            <div class="card-style shadow-md border-2">
                <DataTable
                    :value="quotations"
                    scrollable
                    scrollHeight="350px"
                    class="p-datatable-sm"
                    :loading="loading"
                >
                    <template #header>
                        <div class="flex items-center gap-2">
                            <i class="pi pi-file text-[#10B981]"></i>
                            <span class="text-lg font-bold"
                                >Recent Quotations ({{
                                    quotations.length
                                }})</span
                            >
                        </div>
                    </template>
                    <Column
                        field="quotation_no"
                        header="Quotation No"
                        sortable
                    ></Column>
                    <Column field="customer.name" header="Customer" sortable>
                        <template #body="{ data }">
                            {{ data.customer?.name || "N/A" }}
                        </template>
                    </Column>
                    <Column field="amount" header="Amount" sortable>
                        <template #body="{ data }">
                            {{ formatCurrency(data.amount) }}
                        </template>
                    </Column>
                    <Column field="status" header="Status" sortable>
                        <template #body="{ data }">
                            <Tag
                                :value="data.status"
                                :severity="getStatusSeverity(data.status)"
                            />
                        </template>
                    </Column>
                </DataTable>
            </div>

            <!-- Agreements Table -->
            <div class="card-style shadow-md border-2">
                <DataTable
                    :value="agreements"
                    scrollable
                    scrollHeight="350px"
                    class="p-datatable-sm"
                    :loading="loading"
                >
                    <template #header>
                        <div class="flex items-center gap-2">
                            <i class="pi pi-file-edit text-[#10B981]"></i>
                            <span class="text-lg font-bold"
                                >Recent Agreements ({{
                                    agreements.length
                                }})</span
                            >
                        </div>
                    </template>
                    <Column
                        field="agreement_no"
                        header="Agreement No"
                        sortable
                    ></Column>
                    <Column field="customer.name" header="Customer" sortable>
                        <template #body="{ data }">
                            {{ data.customer?.name || "N/A" }}
                        </template>
                    </Column>
                    <Column field="amount" header="Total Amount" sortable>
                        <template #body="{ data }">
                            {{ formatCurrency(data.amount) }}
                        </template>
                    </Column>
                    <Column field="status" header="Status" sortable>
                        <template #body="{ data }">
                            <Tag
                                :value="data.status"
                                :severity="getStatusSeverity(data.status)"
                            />
                        </template>
                    </Column>
                </DataTable>
            </div>

            <!-- Invoices Table -->
            <div class="card-style shadow-md border-2">
                <DataTable
                    :value="invoices"
                    scrollable
                    scrollHeight="350px"
                    class="p-datatable-sm"
                    :loading="loading"
                >
                    <template #header>
                        <div class="flex items-center gap-2">
                            <i class="pi pi-file text-[#10B981]"></i>
                            <span class="text-lg font-bold"
                                >Recent Invoices ({{ invoices.length }})</span
                            >
                        </div>
                    </template>
                    <Column
                        field="invoice_no"
                        header="Invoice No"
                        sortable
                    ></Column>
                    <Column field="customer.name" header="Customer" sortable>
                        <template #body="{ data }">
                            {{ data.customer?.name || "N/A" }}
                        </template>
                    </Column>
                    <Column field="amount" header="Amount" sortable>
                        <template #body="{ data }">
                            {{ formatCurrency(data.amount) }}
                        </template>
                    </Column>
                    <Column field="status" header="Status" sortable>
                        <template #body="{ data }">
                            <Tag
                                :value="data.status"
                                :severity="getStatusSeverity(data.status)"
                            />
                        </template>
                    </Column>
                </DataTable>
            </div>

            <!-- Receipts Table -->
            <div class="card-style shadow-md border-2">
                <DataTable
                    :value="receipts"
                    scrollable
                    scrollHeight="350px"
                    class="p-datatable-sm"
                    :loading="loading"
                >
                    <template #header>
                        <div class="flex items-center gap-2">
                            <i class="pi pi-receipt text-[#10B981]"></i>
                            <span class="text-lg font-bold"
                                >Recent Receipts ({{ receipts.length }})</span
                            >
                        </div>
                    </template>
                    <Column
                        field="receipt_no"
                        header="Receipt No"
                        sortable
                    ></Column>
                    <Column field="customer.name" header="Customer" sortable>
                        <template #body="{ data }">
                            {{ data.customer?.name || "N/A" }}
                        </template>
                    </Column>
                    <Column field="paid_amount" header="Amount Paid" sortable>
                        <template #body="{ data }">
                            {{ formatCurrency(data.paid_amount) }}
                        </template>
                    </Column>
                    <Column
                        field="payment_method"
                        header="Payment Method"
                        sortable
                    ></Column>
                </DataTable>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Tag from "primevue/tag";
import axios from "axios";

// Reactive data
const customers = ref([]);
const items = ref([]);
const quotations = ref([]);
const agreements = ref([]);
const invoices = ref([]);
const receipts = ref([]);
const loading = ref(true);
const error = ref(null);

// Fetch data from API
const fetchDashboardData = async () => {
    try {
        loading.value = true;
        error.value = null;

        // Use the correct endpoint based on where you defined the route
        const response = await axios.get("/dashboard"); // If in web.php
        // OR if in api.php:
        // const response = await axios.get("/api/dashboard");

        customers.value = response.data.customers || [];
        items.value = response.data.items || [];
        quotations.value = response.data.quotations || [];
        agreements.value = response.data.agreements || [];
        invoices.value = response.data.invoices || [];
        receipts.value = response.data.receipts || [];
    } catch (err) {
        console.error("Dashboard error:", err);
        error.value = "Failed to load dashboard data. Please try again later.";

        // Initialize empty arrays to prevent template errors
        customers.value = [];
        items.value = [];
        quotations.value = [];
        agreements.value = [];
        invoices.value = [];
        receipts.value = [];
    } finally {
        loading.value = false;
    }
};

// Helper functions
const formatCurrency = (value) => {
    if (!value) return "$0.00";
    return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
    }).format(value);
};

const getStatusSeverity = (status) => {
    if (!status) return "info";

    switch (status.toLowerCase()) {
        case "approved":
        case "paid":
        case "closed":
            return "success";
        case "pending":
            return "warning";
        case "draft":
            return "info";
        case "rejected":
        case "overdue":
        case "abnormal closed":
            return "danger";
        default:
            return null;
    }
};

// Fetch data when component mounts
onMounted(() => {
    fetchDashboardData();
});
</script>
