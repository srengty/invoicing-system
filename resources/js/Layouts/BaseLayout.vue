<template>
    <div class="surface-ground min-h-screen p-5">
        <div>
            <h1 class="text-md font-medium text-gray-400 mb-5">
                Welcome! Dashboard to see you...
            </h1>
        </div>
        <div class="mt-6">
            <div class="grid grid-cols-4 gap-2 w-full">
                <!-- Display New Quotations -->
                <div
                    class="p-4 border-round surface-card border-2 shadow-md border-200 shadow-2"
                >
                    <div class="grid">
                        <div class="text-gray-500 font-medium text-sm mb-2">
                            New Quotations
                        </div>
                        <div class="text-900 font-semibold text-3xl">
                            {{ quotations.length }} Quotations
                        </div>
                    </div>
                </div>

                <!-- Display New Agreements -->
                <div
                    class="p-4 border-round surface-card border-2 shadow-md border-200 shadow-2"
                >
                    <div class="grid">
                        <div class="text-gray-500 font-medium text-sm mb-2">
                            New Agreements
                        </div>
                        <div class="text-900 font-semibold text-3xl">
                            {{ agreements.length }} Agreements
                        </div>
                    </div>
                </div>

                <!-- Display Customers -->
                <div
                    class="p-4 border-round surface-card border-2 shadow-md border-200 shadow-2"
                >
                    <div class="grid">
                        <div class="text-gray-500 font-medium text-sm mb-2">
                            Customers
                        </div>
                        <div class="text-900 font-semibold text-3xl">
                            {{ customers.length }} Customers
                        </div>
                    </div>
                </div>

                <!-- Display Items -->
                <div
                    class="p-4 border-round surface-card border-2 shadow-md border-200 shadow-2"
                >
                    <div class="grid">
                        <div class="text-gray-500 font-medium mb-2 text-sm">
                            Items
                        </div>
                        <div class="text-900 font-semibold text-3xl">
                            {{ products.length }} Items
                        </div>
                    </div>
                </div>

                <!-- Display Invoices -->
                <div
                    class="p-4 border-round surface-card border-2 shadow-md border-200 shadow-2"
                >
                    <div class="grid">
                        <div class="text-500 font-medium text-sm mb-2">
                            Invoices
                        </div>
                        <div class="text-gray-900 font-semibold text-3xl">
                            {{ invoices.length }} Invoices
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="w-full">
            <DashboardChart :data="chartData" />
        </div>
        <div class="mt-6">
            <DashboardTable :data="tableData" />
        </div>
    </div>
</template>

<script setup>
import { defineProps, onMounted } from 'vue';
import DashboardChart from "../Pages/Dashboards/DashboardBarChart.vue";
import DashboardTable from "../Pages/Dashboards/DashboardTable.vue";


// Receive props from Inertia
const props = defineProps({
    customers: {
        type: Array,
        default: () => []
    },
    products: {
        type: Array,
        default: () => []
    },
    quotations: {
        type: Array,
        default: () => []
    },
    agreements: {
        type: Array,
        default: () => []
    },
    invoices: {
        type: Array,
        default: () => []
    },
    receipts: {
        type: Array,
        default: () => []
    },
});


onMounted(() => {
  console.log('Customers:', props.customers);
  console.log('Products:', props.products);
  console.log('Quotations:', props.quotations);
  console.log('Agreements:', props.agreements);
  console.log('Invoices:', props.invoices);
});

</script>

<style scoped></style>
