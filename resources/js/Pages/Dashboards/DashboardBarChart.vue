<template>
    <div class="surface-ground">
        <div class="grid">
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
                <div class="col-12 md:w-1/3 shadow-md">
                    <div class="text-lg font-semibold border-b-4 p-4">
                        Recent Transactions
                    </div>
                    <div class="p-4">
                        <div class="grid gap-6 mb-4">
                            <div
                                v-for="transaction in transactions"
                                :key="transaction.label"
                                class="flex justify-between text-gray-500 items-center p-2 border-b-2 border-b-gray-200"
                            >
                                <span class="text-600 font-medium">{{ transaction.label }}</span>
                                <span class="text-900 font-semibold">៛ {{ transaction.value }}</span>
                            </div>
                        </div>
                        <!-- Total Outstanding Section -->
                        <div class="flex justify-between items-center p-3 bg-gray-50 border-round">
                            <span class="text-600 font-medium">Total Invoices Outstanding</span>
                            <span class="text-900 font-bold">៛ {{ totalOutstanding }}</span>
                        </div>
                    </div>
                </div>

                <!-- Chart Section -->
                <div class="md:w-2/3">
                    <!-- Replace with your actual chart component -->
                    <StackedBar />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { usePage } from "@inertiajs/vue3";
import { Select, SelectButton } from "primevue";
import StackedBar from "../../Layouts/Charts/StackedBar.vue";

// Fetch data from Inertia page props
const { dashboardData } = usePage().props;

// Transactions Data
const transactions = ref([]);
const totalOutstanding = ref("0.00");

// Populate data when component is mounted
onMounted(() => {
    transactions.value = dashboardData.transactions;
    totalOutstanding.value = dashboardData.totalOutstanding;
});

// Selected Period Options
const selectedPeriod = ref({ name: "Day", value: "Day" });
const periodOptions = ref([
    { name: "Day", value: "Day" },
    { name: "Week", value: "Week" },
    { name: "Month", value: "Month" },
]);

// Language Options (Currency)
const selectedLanguage = ref({ name: "KHR", code: "KHR" });
const languageOptions = ref([
    { name: "USD", code: "USD" },
    { name: "KHR", code: "KHR" },
]);

// Time Range Selector Options
const selectedTimeRange = ref();
const timeRanges = ref([
    { label: "Last 7 Days", value: "7days", count: 6 },
    { label: "Last 30 Days", value: "30days" },
    { label: "This Month", value: "month" },
    { label: "Last Month", value: "lastmonth" },
    { label: "This Year", value: "year" },
    { label: "Last Year", value: "lastyear" },
]);
</script>

<style scoped>
/* Custom dropdown item styling for badge */
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

/* Additional custom styling for the grid */
.grid {
    display: grid;
    gap: 16px;
}

.flex {
    display: flex;
    gap: 16px;
}

.text-600 {
    color: #4a4a4a;
}

.text-900 {
    color: #1e293b;
}

.bg-gray-50 {
    background-color: #f9fafb;
}
</style>
