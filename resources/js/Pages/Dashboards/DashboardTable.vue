<template>
    <div class="table-1">
        <div class="w-full grid grid-cols-2 gap-8">
            <div class="shadow-md border-2">
                <DataTable :value="customers" scrollable scrollHeight="350px">
                    <template #header>
                        <div
                            class="flex flex-wrap items-center justify-start gap-2"
                        >
                            <i class="pi pi-user text-[#10B981]"></i>
                            <span class="text-lg font-bold">Customers</span>
                        </div>
                    </template>
                    <Column
                        class="text-sm"
                        v-for="col of columns"
                        :key="col.field"
                        :field="col.field"
                        :header="col.header"
                    ></Column>
                </DataTable>
            </div>
            <div class="shadow-md border-2">
                <DataTable :value="items" scrollable scrollHeight="350px">
                    <template #header>
                        <div
                            class="flex flex-wrap items-center justify-start gap-2"
                        >
                            <i class="pi pi-box text-[#10B981]"></i>
                            <span class="text-lg font-bold">Items</span>
                        </div>
                    </template>
                    <Column
                        class="text-sm"
                        v-for="col of columns"
                        :key="col.field"
                        :field="col.field"
                        :header="col.header"
                    ></Column>
                </DataTable>
            </div>
            <div class="shadow-md border-2">
                <DataTable :value="quotations" scrollable scrollHeight="350px">
                    <template #header>
                        <div
                            class="flex flex-wrap items-center justify-start gap-2"
                        >
                            <i class="pi pi-inbox text-[#10B981]"></i>
                            <span class="text-lg font-bold">Quotations</span>
                        </div>
                    </template>
                    <Column
                        class="text-sm"
                        v-for="col of columns"
                        :key="col.field"
                        :field="col.field"
                        :header="col.header"
                    ></Column>
                </DataTable>
            </div>
            <div class="shadow-md border-2">
                <DataTable :value="agreements" scrollable scrollHeight="350px">
                    <template #header>
                        <div
                            class="flex flex-wrap items-center justify-start gap-2"
                        >
                            <i class="pi pi-inbox text-[#10B981]"></i>
                            <span class="text-lg font-bold">Agreements</span>
                        </div>
                    </template>
                    <Column
                        class="text-sm"
                        v-for="col of columns"
                        :key="col.field"
                        :field="col.field"
                        :header="col.header"
                    ></Column>
                </DataTable>
            </div>
            <div class="shadow-md border-2">
                <DataTable :value="invoices" scrollable scrollHeight="350px">
                    <template #header>
                        <div
                            class="flex flex-wrap items-center justify-start gap-2"
                        >
                            <i class="pi pi-inbox text-[#10B981]"></i>
                            <span class="text-lg font-bold">Invoices</span>
                        </div>
                    </template>
                    <Column
                        class="text-sm"
                        v-for="col of columns"
                        :key="col.field"
                        :field="col.field"
                        :header="col.header"
                    ></Column>
                </DataTable>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import DataTable from "primevue/datatable";
import Column from "primevue/column";

// Sample data for each table
const customers = ref([
    { code: "C001", name: "John Doe", category: "Premium", quantity: 12 },
    { code: "C002", name: "Jane Smith", category: "Standard", quantity: 8 },
    { code: "C003", name: "Acme Corp", category: "Corporate", quantity: 25 },
    { code: "C004", name: "Global Inc", category: "Corporate", quantity: 42 },
    { code: "C005", name: "Tech Solutions", category: "Premium", quantity: 7 },
    { code: "C006", name: "Design Studio", category: "Standard", quantity: 15 },
    {
        code: "C007",
        name: "Marketing Pro",
        category: "Corporate",
        quantity: 30,
    },
]);

const items = ref([
    { code: "ITM001", name: "Laptop", category: "Electronics", quantity: 15 },
    { code: "ITM002", name: "Desk Chair", category: "Furniture", quantity: 32 },
    { code: "ITM003", name: "Monitor", category: "Electronics", quantity: 18 },
    { code: "ITM004", name: "Notebook", category: "Stationery", quantity: 75 },
    { code: "ITM005", name: "Keyboard", category: "Electronics", quantity: 23 },
    { code: "ITM006", name: "Mouse", category: "Electronics", quantity: 40 },
    { code: "ITM007", name: "Desk Lamp", category: "Furniture", quantity: 12 },
]);

const quotations = ref([
    { code: "Q001", name: "Annual Service", category: "Service", quantity: 1 },
    { code: "Q002", name: "Bulk Order", category: "Products", quantity: 150 },
    { code: "Q003", name: "Custom Solution", category: "Service", quantity: 1 },
    {
        code: "Q004",
        name: "Software License",
        category: "Products",
        quantity: 50,
    },
    { code: "Q005", name: "Consulting", category: "Service", quantity: 1 },
]);

const agreements = ref([
    { code: "AGR001", name: "Service Level", category: "SLA", quantity: 1 },
    { code: "AGR002", name: "NDA", category: "Legal", quantity: 3 },
    { code: "AGR003", name: "Partnership", category: "Contract", quantity: 1 },
    { code: "AGR004", name: "Reseller", category: "Contract", quantity: 1 },
    { code: "AGR005", name: "Confidentiality", category: "Legal", quantity: 2 },
]);

const invoices = ref([
    {
        code: "INV2023-001",
        name: "March Services",
        category: "Service",
        quantity: 1,
    },
    {
        code: "INV2023-002",
        name: "Product Order",
        category: "Products",
        quantity: 45,
    },
    {
        code: "INV2023-003",
        name: "Annual Subscription",
        category: "Service",
        quantity: 1,
    },
    {
        code: "INV2023-004",
        name: "Maintenance",
        category: "Service",
        quantity: 1,
    },
    {
        code: "INV2023-005",
        name: "Hardware",
        category: "Products",
        quantity: 18,
    },
]);

const columns = ref([
    { field: "code", header: "Code" },
    { field: "name", header: "Name" },
    { field: "category", header: "Category" },
    { field: "quantity", header: "Quantity" },
]);
</script>

<style lang="scss" scoped>
.table-1 {
    .p-datatable {
        font-size: 0.875rem;

        .p-datatable-header {
            background: transparent;
            border: none;
            padding: 1rem 1rem 0.5rem;
        }

        .p-datatable-wrapper {
            border-radius: 0 0 0.5rem 0.5rem;
        }
    }

    .shadow-md {
        border-radius: 0.5rem;
        overflow: hidden;
    }
}
</style>
