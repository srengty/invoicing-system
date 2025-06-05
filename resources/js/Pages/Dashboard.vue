<template>
    <Head title="Dashboard" />
    <GuestLayout>
        <NavbarLayout />
        <div class="surface-ground min-h-screen p-5">
            <div class="mb-6">
                <h1 class="text-xl font-semibold text-gray-900">Welcome to your Dashboard</h1>
                <p class="text-sm text-gray-500 mt-2">Overview of your key metrics.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- New Quotations Card -->
                <div class="p-6 bg-white border rounded-lg shadow-lg flex flex-col justify-between">
                    <h2 class="text-sm font-medium text-gray-500">New Quotations</h2>
                    <div class="text-2xl font-semibold text-gray-900 mt-2">
                        {{ quotations?.length || 0 }} Quotations
                    </div>
                </div>

                <!-- New Agreements Card -->
                <div class="p-6 bg-white border rounded-lg shadow-lg flex flex-col justify-between">
                    <h2 class="text-sm font-medium text-gray-500">New Agreements</h2>
                    <div class="text-2xl font-semibold text-gray-900 mt-2">
                        {{ agreements?.length || 0 }} Agreements
                    </div>
                </div>

                <!-- Customers Card -->
                <div class="p-6 bg-white border rounded-lg shadow-lg flex flex-col justify-between">
                    <h2 class="text-sm font-medium text-gray-500">Customers</h2>
                    <div class="text-2xl font-semibold text-gray-900 mt-2">
                        {{ customers?.length || 0 }} Customers
                    </div>
                </div>

                <!-- Items Card -->
                <div class="p-6 bg-white border rounded-lg shadow-lg flex flex-col justify-between">
                    <h2 class="text-sm font-medium text-gray-500">Items</h2>
                    <div class="text-2xl font-semibold text-gray-900 mt-2">
                        {{ products?.length || 0 }} Items
                    </div>
                </div>

                <!-- Invoices Card -->
                <div class="p-6 bg-white border rounded-lg shadow-lg flex flex-col justify-between col-span-2 lg:col-span-1">
                    <h2 class="text-sm font-medium text-gray-500">Invoices</h2>
                    <div class="text-2xl font-semibold text-gray-900 mt-2">
                        {{ invoices?.length || 0 }} Invoices
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <div class="surface-ground">
                    <div>
                        <!-- Header Section with Filters -->
                        <div class="flex justify-between items-center mb-5">
                            <div>
                                <h1 class="text-md font-medium text-gray-400">Dashboard</h1>
                            </div>
                            <div class="flex gap-4">
                                <!-- Currency Selector -->
                                <Select
                                    v-model="selectedLanguage"
                                    :options="languageOptions"
                                    optionLabel="name"
                                    placeholder="KHR/USD"
                                    class="w-full md:w-30 h-9"
                                    size="small"
                                />

                                <!-- Period Selector -->
                                <SelectButton
                                    v-model="selectedPeriod"
                                    :options="periodOptions"
                                    optionLabel="name"
                                    aria-labelledby="period-label"
                                    class="w-full md:w-30 h-9"
                                    size="small"
                                />

                                <!-- Time Range Selector -->
                                <Select
                                    v-model="selectedTimeRange"
                                    :options="timeRanges"
                                    optionLabel="label"
                                    placeholder="Select time range"
                                    class="w-full md:w-44 h-9"
                                    size="small"
                                >
                                    <template #value="slotProps">
                                        <div v-if="slotProps.value" class="flex items-center text-sm">
                                            <i class="pi pi-calendar mr-2"></i>
                                            <span>{{ slotProps.value.label }}</span>
                                        </div>
                                        <span v-else>{{ slotProps.placeholder }}</span>
                                    </template>
                                </Select>
                            </div>
                        </div>

                        <!-- Main Content Layout -->
                        <div class="flex gap-6 w-full mt-6">
                            <!-- Recent Transactions Section -->
                            <div class="col-12 md:w-1/3 border rounded-lg shadow-lg">
                                <div class="text-lg font-semibold border-b-4 p-4">
                                    Recent Transactions
                                </div>
                                <div class="p-4">
                                    <div class="grid gap-6 mb-4">
                                        <div v-for="transaction in transactions" :key="transaction.label" class="flex justify-between text-gray-500 items-center p-2 border-b-2 border-b-gray-200">
                                            <span class="text-600 font-medium">{{ transaction.label }}</span>
                                            <span class="text-900 font-semibold">៛ {{ formatNumber(transaction.value) }}</span>
                                        </div>
                                    </div>
                                    <!-- Total Outstanding Section -->
                                    <div class="flex justify-between items-center p-3 bg-gray-50 border-round">
                                        <span class="text-600 font-medium">Total Invoices Outstanding</span>
                                        <span class="text-900 font-bold">៛ {{ totalOutstanding.toFixed(2) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Chart Section -->
                            <div class="md:w-2/3">
                                <div class="border rounded-lg shadow-lg p-6">
                                   <Chart
                                        type="bar"
                                        :data="chartData"
                                        :options="chartOptions"
                                        class="h-[30rem] p-2"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <div class="flex mb-6 gap-4">
                    <div class="card-style border rounded-lg shadow-lg w-1/2 p-4">
                        <DataTable
                            :value="customers.slice(0, 5)"
                            scrollable
                            stripedRows 
                            scrollHeight="350px"
                            class="p-datatable-sm"
                            :loading="loading"
                        >
                            <template #header>
                                <div class="flex items-center gap-2 mb-1">
                                    <i class="pi pi-users text-[#10B981]"></i>
                                    <span class="text-lg font-bold">Recent Customers ({{ customers.length }})</span>
                                </div>
                            </template>
                            <Column field="code" header="Code" sortable class="m-2"></Column>
                            <Column field="name" header="Name" sortable></Column>
                            <Column field="credit_period" header="Credit Period" sortable></Column>
                            <Column field="customer_category_id" header="Category" sortable></Column>
                        </DataTable>
                    </div>

                    <!-- Items Table -->
                    <div class="card-style border rounded-lg shadow-lg w-1/2 p-4">
                        <DataTable
                            :value="products.slice(0, 5)"
                            scrollable
                            scrollHeight="350px"
                            class="p-datatable-sm"
                            :loading="loading"
                            stripedRows 
                        >
                            <template #header>
                                <div class="flex items-center gap-2 mb-1">
                                    <i class="pi pi-box text-[#10B981]"></i>
                                    <span class="text-lg font-bold">Recent Items ({{ products.length }})</span>
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
                </div>
                
                <div class="flex mb-6 gap-4">
                    <div class="card-style border rounded-lg shadow-lg w-1/2 p-4">
                        <DataTable
                            :value="quotations.slice(0, 5)"
                            scrollable
                            scrollHeight="350px"
                            class="p-datatable-sm"
                            :loading="loading"
                            stripedRows 
                        >
                            <template #header>
                                <div class="flex items-center gap-2 mb-1">
                                    <i class="pi pi-file text-[#10B981]"></i>
                                    <span class="text-lg font-bold">Recent Quotations ({{ quotations.length }})</span>
                                </div>
                            </template>
                            <Column field="quotation_no" header="Quotation No" sortable></Column>
                            <Column field="customer.name" header="Customer" sortable>
                                <template #body="{ data }">
                                    {{ data.customer?.name || "N/A" }}
                                </template>
                            </Column>
                            <Column field="amount" header="Amount" sortable>
                                <template #body="{ data }">
                                    <span :class="{
                                        'text-blue-600': (data.total) > 0,
                                    }">
                                        {{ formatCurrency(data.total) }}
                                    </span>
                                </template>
                            </Column>
                            <Column field="status" header="Status" sortable>
                                <template #body="{ data }">
                                    <Tag :value="data.status" :severity="getStatusSeverity(data.status)" />
                                </template>
                            </Column>
                        </DataTable>
                    </div>

                    <div class="card-style border rounded-lg shadow-lg w-1/2 p-4">
                        <DataTable
                            :value="receipts.slice(0, 5)"
                            scrollable
                            scrollHeight="350px"
                            class="p-datatable-sm"
                            :loading="loading"
                            stripedRows 
                        >
                            <template #header>
                                <div class="flex items-center gap-2 mb-1">
                                    <i class="pi pi-receipt text-[#10B981]"></i>
                                    <span class="text-lg font-bold">Recent Receipts ({{ receipts.length }})</span>
                                </div>
                            </template>
                            <Column field="receipt_no" header="Receipt No" sortable></Column>
                            <Column field="customer.name" header="Customer" sortable>
                                <template #body="{ data }">
                                    {{ data.customer?.name || "N/A" }}
                                </template>
                            </Column>
                            <Column field="paid_amount" header="Amount Paid" sortable>
                                <template #body="{ data }">
                                    <span :class="{
                                        'text-green-600': (data.paid_amount) > 0,
                                    }">
                                        {{ formatCurrency(data.paid_amount) }}
                                    </span>
                                </template>
                            </Column>
                            <Column field="payment_method" header="Payment Method" sortable>
                                <template #body="{ data }">
                                    <Tag :value="data.payment_method" :severity="getStatusSeverity(data.payment_method)" />
                                </template></Column>
                        </DataTable>
                    </div>
                </div>

                <div class="flex mb-6 gap-4">
                    <!-- Agreements Table -->
                    <div class="card-style border rounded-lg shadow-lg w-full p-4">
                        <DataTable
                            :value="agreements.slice(0, 5)"
                            scrollable
                            scrollHeight="350px"
                            class="p-datatable-sm"
                            :loading="loading"
                            stripedRows 
                        >
                            <template #header>
                                <div class="flex items-center gap-2 mb-1">
                                    <i class="pi pi-file-edit text-[#10B981]"></i>
                                    <span class="text-lg font-bold">Recent Agreements ({{ agreements.length }})</span>
                                </div>
                            </template>
                            <Column field="agreement_no" header="Agreement No" sortable></Column>
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
                                    <Tag :value="data.status" :severity="getStatusSeverity(data.status)" />
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                </div>

                <div class="flex mb-6 gap-4">
                    <div class="card-style border rounded-lg shadow-lg w-full p-4">
                        <DataTable
                            :value="sortedInvoices"
                            scrollable
                            scrollHeight="350px"
                            class="p-datatable-sm"
                            :loading="loading"
                            stripedRows
                        >
                            <template #header>
                                <div class="flex items-center gap-2 mb-1">
                                    <i class="pi pi-file text-[#10B981]"></i>
                                    <span class="text-lg font-bold">Recent Invoices ({{ invoices.length }})</span>
                                </div>
                            </template>
                            <Column field="invoice_no" header="Invoice No" sortable></Column>
                            <Column field="customer.name" header="Customer" sortable>
                                <template #body="{ data }">
                                    {{ data.customer?.name || "N/A" }}
                                </template>
                            </Column>
                            <Column field="amount" header="Amount" sortable>
                                <template #body="{ data }">
                                    <span :class="{
                                        'text-blue-600': (data.grand_total) > 0,
                                    }">
                                        {{ formatCurrency(data.grand_total) }}
                                    </span>
                                </template>
                            </Column>
                            <Column header="Paid Amount" sortable>
                                <template #body="{ data }">
                                    <span :class="{
                                        'text-green-600': (data.paid_amount) > 0,
                                        'text-red-500': (data.paid_amount) <= 0
                                    }">
                                        {{ formatCurrency(data.paid_amount) }}
                                    </span>
                                </template>
                            </Column>
                            <Column header="Due Amount" sortable>
                                <template #body="{ data }">
                                    <span :class="{
                                        'text-red-500': (data.grand_total - data.paid_amount) > 0,
                                        'text': (data.grand_total - data.paid_amount) <= 0
                                    }">
                                        {{ formatCurrency(data.grand_total - data.paid_amount) }}
                                    </span>
                                </template>
                            </Column>
                            <Column header="Payment Status" sortable>
                                <template #body="{ data }">
                                    <Tag :class="{
                                        'text-green-600': getStatusSeverity(getPaymentStatus(data)) === 'success',
                                        'text-yellow-600': getStatusSeverity(getPaymentStatus(data)) === 'warn',
                                        'text-red-600': getStatusSeverity(getPaymentStatus(data)) === 'danger',
                                        'text-blue-600': getStatusSeverity(getPaymentStatus(data)) === 'info'
                                    }" :severity="getStatusSeverity(getPaymentStatus(data))">
                                        {{ getPaymentStatus(data) }}
                                    </Tag>
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<script setup>
import { ref, computed , onMounted, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import GuestLayout from "@/Layouts/GuestLayout.vue";
import NavbarLayout from "@/Layouts/NavbarLayout.vue";
import { Head } from "@inertiajs/vue3";
import { SelectButton, Select } from "primevue";
import Chart from "primevue/chart";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import 'primeicons/primeicons.css';
import Tag from "primevue/tag";

// Functions declared before usage to avoid the "before initialization" error
const formatNumber = (value) => {
    const number = Number(value);
    return isNaN(number) ? '0.00' : number.toFixed(2);
};

const formatCurrency = (value) => {
    return `៛ ${formatNumber(value)}`;
};

// Fetch data from Inertia page props
const page = usePage();
const { customers, products, quotations, agreements, invoices, receipts, transactions, totalOutstanding, quotationDates, agreementDates, invoiceDates, receiptDates } = page.props;

// Selected Period Options
const selectedPeriod = ref({ name: "Day", value: "Day" });
const periodOptions = ref([
    { name: "Day", value: "Day" },
    { name: "Week", value: "Week" },
    { name: "Month", value: "Month" },
]);

// Selected Language Options
const selectedLanguage = ref({ name: "KHR", code: "KHR" });
const languageOptions = ref([
    { name: "USD", code: "USD" },
    { name: "KHR", code: "KHR" },
]);

// Time Range Selector Options
const selectedTimeRange = ref();
const timeRanges = ref([
    { label: "Last 7 Days", value: "7days" },
    { label: "Last 30 Days", value: "30days" },
    { label: "This Month", value: "month" },
    { label: "Last Month", value: "lastmonth" },
    { label: "This Year", value: "year" },
    { label: "Last Year", value: "lastyear" },
]);

const getPaymentStatus = (invoice) => {
    const status = 
        invoice.grand_total === invoice.paid_amount ? "Fully Paid" :
        invoice.paid_amount > 0 && invoice.paid_amount < invoice.grand_total ? "Partially Paid" :
        invoice.grand_total - invoice.paid_amount > 0 ? "Not Paid" :
        invoice.due_date && new Date(invoice.due_date) < new Date() && invoice.paid_amount === 0 ? "Overdue" :
        "Pending";

    return status;
};

const getStatusSeverity = (status) => {
    switch (status.toLowerCase()) {
        case 'approved': return 'success';
        case 'fully paid': return 'success';
        case 'partially paid': return 'info';
        case 'not paid': return 'warn';
        case 'Overdue': return 'danger';
        case 'Pending': return 'warn';
        default: return 'info'; // Default in case no status is matched
    }
};

const sortedInvoices = computed(() => {
    // Filter invoices that have an invoice_no and sort by status
    return invoices
        .filter(invoice => invoice.invoice_no) // Ensure the invoice has an invoice_no
        .sort((a, b) => {
            const statusOrder = {
                'Overdue': 1,
                'Not Paid': 2,
                'Partially Paid': 3,
                'Fully Paid': 5,
                'Pending': 4
            };

            // Get the status of each invoice
            const statusA = getPaymentStatus(a);
            const statusB = getPaymentStatus(b);

            // First, compare by status order
            if (statusOrder[statusA] < statusOrder[statusB]) return -1;
            if (statusOrder[statusA] > statusOrder[statusB]) return 1;

            // If statuses are the same, compare by invoice_no (ascending order)
            // Handle cases where invoice_no might be null/undefined or not a string
            const invoiceNoA = a.invoice_no?.toString() || '';
            const invoiceNoB = b.invoice_no?.toString() || '';
            
            return invoiceNoA.localeCompare(invoiceNoB);
        });
});

// Chart Data and Options
const chartData = ref([]);
const chartOptions = ref({
    maintainAspectRatio: false,
    aspectRatio: 0.8,
    plugins: {
        legend: {
            display: true,
            position: 'top',
            labels: {
                color: '#000'
            }
        },
        tooltip: {
            mode: 'index',
            intersect: false
        }
    },
    scales: {
        x: {
            stacked: false,
            ticks: { color: "#000" },
            grid: { color: "#ddd" },
        },
        y: {
            stacked: false,
            ticks: { color: "#000" },
            grid: { color: "#ddd" },
        },
    },
    responsive: true,
});

const loading = ref(false);

// Check if data is loaded
const isDataLoaded = ref(false);

const setChartData = () => {
    // Get the current date
    const currentDate = new Date();
    
    // Get the last 6 months (from current date)
    const last6Months = [];
    const months = new Set();  // To store unique months
    const agreementsData = {};
    const quotationsData = {};
    const invoicesData = {};
    const receiptDate = {};

    // Helper function to format date as "Month Year" (e.g., "Jan 2025")
    const formatMonthYear = (date) => {
        return date.toLocaleString('default', { month: 'short', year: 'numeric' });
    };

    // Generate the last 6 months dynamically
    for (let i = 0; i < 12; i++) {
        const monthDate = new Date();
        monthDate.setMonth(currentDate.getMonth() - i);  // Move back one month at a time
        last6Months.push(formatMonthYear(monthDate)); // Store month in "Month Year" format
    }

    // Loop through agreements to generate dynamic labels and datasets
    agreements.forEach((agreement) => {
        const date = new Date(agreement.created_at);
        const monthYear = formatMonthYear(date);

        // Only include the last 6 months
        if (last6Months.includes(monthYear)) {
            months.add(monthYear);  // Add the month to the set to ensure it's unique
            if (!agreementsData[monthYear]) agreementsData[monthYear] = 0;
            agreementsData[monthYear] += 1;  // Increment the count for agreements
        }
    });

    // Loop through quotations to generate dynamic labels and datasets
    quotations.forEach((quotation) => {
        const date = new Date(quotation.created_at);
        const monthYear = formatMonthYear(date);

        if (last6Months.includes(monthYear)) {  // Within last 6 months
            months.add(monthYear);  // Add the month to the set to ensure it's unique
            if (!quotationsData[monthYear]) quotationsData[monthYear] = 0;
            quotationsData[monthYear] += 1;  // Increment the count for quotations
        }
    });

    receipts.forEach((receipt) => {
        const date = new Date(receipt.created_at);
        const monthYear = formatMonthYear(date);

        if (last6Months.includes(monthYear)) {  // Within last 6 months
            months.add(monthYear);  // Add the month to the set to ensure it's unique
            if (!receiptDate[monthYear]) receiptDate[monthYear] = 0;
            receiptDate[monthYear] += 1;  // Increment the count for quotations
        }
    });

    // Loop through invoices to generate dynamic labels and datasets
    invoices.forEach((invoice) => {
        const date = new Date(invoice.created_at);
        const monthYear = formatMonthYear(date);

        if (last6Months.includes(monthYear)) {  // Within last 6 months
            months.add(monthYear);  // Add the month to the set to ensure it's unique
            if (!invoicesData[monthYear]) invoicesData[monthYear] = 0;
            invoicesData[monthYear] += 1;  // Increment the count for invoices
        }
    });

    // Sort the months correctly
    const sortedMonths = last6Months.sort((a, b) => {
        const dateA = new Date(a);
        const dateB = new Date(b);
        return dateA - dateB;  // Sort by date
    });

    // Ensure each dataset has data for each of the last 6 months, even if it's missing some
    const monthLabels = sortedMonths;

    // Fill missing months with 0 if no data for that month
    const fillMissingData = (data, months) => {
        return months.map(month => {
            return data[month] || 0;  // If the month is missing, return 0
        });
    };

    return {
        labels: monthLabels,  // Dynamic labels (months)
        datasets: [
            {
                type: "bar",
                label: "Agreements",
                backgroundColor: "#10b981",
                data: fillMissingData(agreementsData, monthLabels),  // Fill missing months with 0
            },
            {
                type: "bar",
                label: "Quotations",
                backgroundColor: "#00a38c",
                data: fillMissingData(quotationsData, monthLabels),
            },
            {
                type: "bar",
                label: "Invoices",
                backgroundColor: "#008c8d",
                data: fillMissingData(invoicesData, monthLabels),
            },
            {
                type: "bar",
                label: "Receipts",
                backgroundColor: "#66bb6a",
                data: fillMissingData(receiptDate, monthLabels),
            },
        ],
    };
};

onMounted(() => {
    if (transactions && transactions.length > 0) {
        chartData.value = setChartData();
        isDataLoaded.value = true;
    } else {
        console.warn("No transactions data found");
    }
});

watch(() => [...transactions], (newVal) => {
    chartData.value = setChartData();
}, { deep: true });
</script>


<style scoped>
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 16px;
}
.card-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #4A4A4A;
}
.card-value {
    font-size: 2rem;
    font-weight: 700;
    color: #2D3748;
}
::v-deep(.p-dropdown-item) {
    position: relative;
}
::v-deep(.p-dropdown-item) .count-badge {
    position: absolute;
    right: 1rem;
    background-color: var(--primary-color);
    color: white;
    border-radius: 50%;
    width: 1.5rem;
    height: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
}
</style>
