<template>
    <div class="surface-ground">
        <div class="grid">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-md font-medium text-gray-400 mb-5">
                       
                    </h1>
                </div>
                <div class="flex">
                    <Select
                        v-model="selectedLanguage"
                        :options="languageOptions"
                        optionLabel="name"
                        placeholder="KHR/USD"
                        class="w-full md:w-30 h-9"
                        size="small"
                    />
                    <SelectButton
                        v-model="selectedPeriod"
                        :options="periodOptions"
                        optionLabel="name"
                        aria-labelledby="period-label"
                        class="w-full md:w-30 h-9"
                        size="small"
                    />
                    <Select
                        v-model="selectedTimeRange"
                        :options="timeRanges"
                        optionLabel="label"
                        placeholder="Select time range"
                        class="w-full md:w-44 h-9"
                        size="small"
                    >
                        <template #value="slotProps">
                            <div
                                v-if="slotProps.value"
                                class="flex item-center w-full md:w-44 text-sm"
                            >
                                <div>
                                    <i class="pi pi-calendar mr-2 text-sm"></i>
                                </div>
                                <div class="text-sm">
                                    {{ slotProps.value.label }}
                                </div>
                            </div>
                            <span v-else>
                                {{ slotProps.placeholder }}
                            </span>
                        </template>
                    </Select>
                </div>
            </div>
            <div class="flex gap-6 w-full mt-6">
                <div class="col-12 w-1/3 shadow-md">
                    <div class="text-lg font-semibold border-b-4 p-4">
                        Recent Transactions
                    </div>
                    <div class="p-4">
                        <div class="grid gap-6 mb-4">
                            <div
                                v-for="transaction in transactions"
                                :key="transaction.label"
                                class="flex justify-between text-gray-500 align-items-center p-2 border-bottom-1 surface-border border-b-2"
                            >
                                <span class="text-600 font-medium">{{
                                    transaction.label
                                }}</span>
                                <span class="text-900 font-semibold"
                                    >៛ {{ transaction.value }}</span
                                >
                            </div>
                        </div>
                        <div
                            class="flex justify-between align-items-center p-3 surface-100 border-round"
                        >
                            <span class="text-600 font-medium"
                                >Total Invoices Outstanding</span
                            >
                            <span class="text-900 font-bold">៛0</span>
                        </div>
                    </div>
                </div>
                <div class="w-2/3">
                    <!-- <VerticalBar /> -->
                    <StackedBar />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import { Card, Button, SelectButton, Select } from "primevue";
import VerticalBar from "../../Layouts/Charts/VerticalBar.vue";
import StackedBar from "../../Layouts/Charts/StackedBar.vue";
import { usePage } from "@inertiajs/vue3";

const transactions = ref([
    { label: "Invoices", value: "0.00" },
    { label: "Payments", value: "0.00" },
    { label: "Expenses", value: "0.00" },
    { label: "Outstanding", value: "0.00" },
]);

const selectedPeriod = ref({ name: "Day", value: "Day" });
const periodOptions = ref([
    { name: "Day", value: "Day" },
    { name: "Week", value: "Week" },
    { name: "Month", value: "Month" },
]);

const selectedLanguage = ref({ name: "KHR", code: "KHR" });
const languageOptions = ref([
    { name: "USD", code: "USD" },
    { name: "KHR", code: "KHR" },
]);

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
