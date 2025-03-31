<template>
    <Head title="Agreements"></Head>
    <GuestLayout>
        <NavbarLayout />
        <!-- PrimeVue Breadcrumb -->
        <div class="py-3">
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
        <div class="agreements pl-4 pr-4">
            <div class="flex justify-end items-center">
                <!-- <h1 class="text-2xl">Agreements list</h1> -->
                <div class="flex gap-2">
                    <div>
                        <InputText
                            v-model="searchTerm"
                            placeholder="Search"
                            class="w-64"
                            size="small"
                            @keyup.enter="performSearch"
                        />
                    </div>
                    <div>
                        <Dropdown
                            v-model="searchType"
                            :options="searchOptions"
                            optionLabel="label"
                            optionValue="value"
                            class="w-48 text-sm"
                            placeholder="Select field to search"
                        />
                    </div>
                    <div>
                        <Button
                            icon="pi pi-plus"
                            label="New"
                            @click="openCreate"
                            raised
                            size="small"
                        ></Button>
                        <ChooseColumns
                            :columns="columns"
                            v-model="selectedColumns"
                            @apply="updateColumns"
                            size="small"
                            raised
                        />
                    </div>
                </div>
            </div>
            <DataTable
                :value="agreements"
                paginator
                :rows="5"
                :rowsPerPageOptions="[5, 10, 20, 50]"
                :stripedRows="true"
                :showGridlines="true"
                resizableColumns
                columnResizeMode="fit"
                class="mt-5"
            >
                <template v-for="col of showColumns" :key="col.field">
                    <!-- column for all other columns -->
                    <Column
                        v-if="
                            ![
                                'actions',
                                'agreement_doc',
                                'agreement_date',
                                'start_date',
                                'end_date',
                            ].includes(col.field)
                        "
                        :field="col.field"
                        :header="col.header"
                        sortable
                        style="width: 5%; font-size: 14px"
                    ></Column>
                    <!-- column for agreement date -->
                    <Column
                        v-if="
                            [
                                'agreement_date',
                                'start_date',
                                'end_date',
                            ].includes(col.field)
                        "
                        :field="col.field"
                        :header="col.header"
                        sortable
                        style="width: 5%; font-size: 14px"
                    >
                        <template #body="slotProps">
                            {{ momentDate(slotProps.data[col.field]) }}
                        </template>
                    </Column>
                    <!-- column for agreement doc -->
                    <Column
                        v-if="col.field === 'agreement_doc'"
                        :field="col.field"
                        :header="col.header"
                        sortable
                        style="width: 5%; font-size: 14px"
                    >
                        <template #body="slotProps">
                            <a
                                v-if="slotProps.data[col.field]"
                                :href="slotProps.data[col.field]"
                                target="_blank"
                                >View doc</a
                            >
                        </template>
                    </Column>
                    <!-- column for view/edit -->
                    <Column
                        v-if="col.field === 'actions'"
                        :field="col.field"
                        :header="col.header"
                        sortable
                        style="width: 5%; font-size: 14px"
                    >
                        <template #body="slotProps">
                            <Button
                                severity=""
                                size="small"
                                @click="
                                    router.get(
                                        route('agreements.show', {
                                            id: slotProps.data.agreement_no,
                                        })
                                    )
                                "
                                icon="pi pi-eye"
                                aria-label="View"
                                outlined
                            ></Button>
                            <Button
                                severity="info"
                                size="small"
                                @click="
                                    router.get(
                                        route('agreements.edit', {
                                            agreement_no:
                                                slotProps.data.agreement_no,
                                        })
                                    )
                                "
                                icon="pi pi-pencil"
                                aria-label="Edit"
                                class="ms-2"
                                outlined
                            ></Button>
                        </template>
                    </Column>
                </template>
            </DataTable>
        </div>
    </GuestLayout>
</template>
<script setup>
import ChooseColumns from "@/Components/ChooseColumns.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import {
    DataTable,
    Column,
    Button,
    Popover,
    Dropdown,
    InputText,
} from "primevue";
import moment from "moment";
import { ref, watch, computed } from "vue";
import { debounce } from "lodash";
import NavbarLayout from "@/Layouts/NavbarLayout.vue";
import Breadcrumb from "primevue/breadcrumb";
import { usePage } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";

const toast = useToast();
defineProps({
    agreements: {
        type: Array,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});
// The Breadcrumb Quotations
const page = usePage();
const items = computed(() => [
    {
        label: "",
        to: "/",
        icon: "pi pi-home",
    },
    { label: page.props.title || "Agreements", to: route("agreements.index") },
]);
const columns = [
    // { field: 'id', header: 'ID' },
    {
        field: "actions",
        header: "View/Edit",
    },
    { field: "quotation_no", header: "Quotation No." },
    { field: "agreement_no", header: "Agreement No." },
    { field: "agreement_ref_no", header: "Agreement Ref No." },
    { field: "customer.name", header: "Customer" },
    { field: "status", header: "Status" },
    { field: "amount", header: "Total amount" },
    { field: "due_payment", header: "Due Payment" },
    { field: "total_progress_payment", header: "Total Progress Payment" }, //sum of all recipes amount
    {
        field: "total_progress_payment_percentage",
        header: "Total Progress Payment %",
    },
    { field: "start_date", header: "Start Date" },
    { field: "end_date", header: "End Date" },
    // { field: "agreement_doc", header: "Agreement Doc" },
    // { field: "agreement_date", header: "Agreement Date" },
    // { field: "address", header: "Address" },
];
const defaultColumns = columns.filter(
    (col) =>
        ![
            "agreement_doc",
            // "total_progress_payment",
            "address",
            // "agreement_ref_no",
        ].includes(col.field)
);
const searchOptions = ref([
    { label: "Agreement Number", value: "agreement_no" },
    { label: "Quotation Number", value: "quotation_no" },
    { label: "Agreement Reference Number", value: "agreement_ref_no" },
    { label: "Customer Name", value: "customer.name" },
    { label: "Status", value: "status" },
    { label: "Total Amount", value: "amount" },
    { label: "Due Payment", value: "due_payment" },
    { label: "Start Date", value: "start_date" },
    { label: "End Date", value: "end_date" },
]);
const searchType = ref(searchOptions.value[0].value);
const searchTerm = ref("");
const performSearch = debounce(() => {
    router.get(
        route("agreements.index"),
        {
            search: searchTerm.value,
            search_type: searchType.value,
        },
        {
            preserveState: true,
            replace: true,
        }
    );
}, 500);
watch([searchTerm, searchType], () => {
    performSearch();
});
const selectedColumns = ref(defaultColumns);
const showColumns = ref(defaultColumns);
const updateColumns = (columns) => {
    showColumns.value = selectedColumns.value;
};
const openCreate = () => {
    router.get(route("agreements.create"));
};
const momentDate = (date) => {
    return moment(date, "DD/MM/YYYY").fromNow();
};
</script>
