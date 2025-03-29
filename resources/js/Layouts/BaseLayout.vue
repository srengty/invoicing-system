<template>
    <div class="surface-ground min-h-screen p-5">
        <div class="w-full">
            <DashboardChart />
        </div>
        <div class="mt-6">
            <div class="grid grid-cols-4 gap-2 w-full">
                <div
                    class="p-4 border-round surface-card border-2 shadow-md border-200 shadow-2"
                >
                    <div class="grid">
                        <div class="text-gray-500 font-medium text-sm mb-2">
                            New Quotations
                        </div>
                        <div class="text-900 font-semibold text-3xl">
                            ៛10000K
                        </div>
                        <div class="text-red-500 mt-2 font-medium text-sm">
                            3% decrease ➤
                        </div>
                    </div>
                </div>
                <div
                    class="p-4 border-round surface-card border-2 shadow-md border-200 shadow-2"
                >
                    <div class="grid">
                        <div class="text-gray-500 font-medium text-sm mb-2">
                            New Agreements
                        </div>
                        <div class="text-900 font-semibold text-3xl">
                            ៛10000K
                        </div>
                        <div class="text-green-500 mt-2 font-medium text-sm">
                            12% increase ➤
                        </div>
                    </div>
                </div>
                <div
                    class="p-4 border-round surface-card border-2 shadow-md border-200 shadow-2"
                >
                    <div class="grid">
                        <div class="text-gray-500 font-medium text-sm mb-2">
                            Cusomters
                        </div>
                        <div class="text-900 font-semibold text-3xl">
                            ៛10000K
                        </div>
                        <div class="text-green-500 mt-2 font-medium text-sm">
                            8% increase ➤
                        </div>
                    </div>
                </div>
                <div
                    class="p-4 border-round surface-card border-2 shadow-md border-200 shadow-2"
                >
                    <div class="grid">
                        <div class="text-gray-500 font-medium mb-2 text-sm">
                            Items
                        </div>
                        <div class="text-900 font-semibold text-3xl">
                            ៛10000K
                        </div>
                        <div class="text-red-500 mt-2 font-medium text-sm">
                            1% decrease ➤
                        </div>
                    </div>
                </div>
                <div
                    class="p-4 border-round surface-card border-2 shadow-md border-200 shadow-2"
                >
                    <div class="grid">
                        <div class="text-500 font-medium text-sm mb-2">
                            Invoicies
                        </div>
                        <div class="text-gray-900 font-semibold text-3xl">
                            ៛10000K
                        </div>
                        <div class="text-red-500 mt-2 font-medium text-sm">
                            1% decrease ➤
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-6">
            <DashboardTable/>
        </div>
    </div>
</template>

<script setup>
import { Head } from "@inertiajs/vue3";
import DashboardChart from "../Pages/Dashboards/DashboardBarChart.vue";
import DashboardTable from "../Pages/Dashboards/DashboardTable.vue";
</script>
<style scoped></style>
