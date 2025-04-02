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
            <DataTable
                :value="filteredAgreements"
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
            <!-- Progress Payment Dialog -->
            <Dialog
                v-model:visible="displayProgressPaymentDialog"
                modal
                header="Progress Payments Details"
                :style="{ width: '50vw' }"
            >
                <div v-if="selectedAgreement" class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="font-semibold">Agreement Number:</p>
                        <p>{{ selectedAgreement.agreement_no }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Customer:</p>
                        <p>{{ selectedAgreement.customer.name }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Total Amount:</p>
                        <p>{{ selectedAgreement.amount }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Total Progress Payment:</p>
                        <p>{{ selectedAgreement.total_progress_payment }}</p>
                    </div>
                    <!-- Add more details or a table of progress payments here if available -->
                </div>
            </Dialog>
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
    Dialog,
} from "primevue";
import moment from "moment";
import { ref, watch, computed } from "vue";
import { debounce } from "lodash";
import NavbarLayout from "@/Layouts/NavbarLayout.vue";
import Breadcrumb from "primevue/breadcrumb";
import { usePage } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";

const toast = useToast();
const props = defineProps({
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
    { field: "short_description", header: "Short description" },
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

const searchType = ref(""); // Set a default search type
const searchTerm = ref("");
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
const getFieldValue = (obj, path) => {
    return path.split(".").reduce((o, p) => (o || {})[p], obj) || "";
};
// Filter agreements locally (alternative to server-side search)
const filteredAgreements = computed(() => {
    if (!searchTerm.value || !searchType.value) {
        return props.agreements;
    }

    return props.agreements.filter((agreement) => {
        const fieldValue = getFieldValue(agreement, searchType.value);

        if (fieldValue === null || fieldValue === undefined) {
            return false;
        }

        // Handle numeric fields
        if (typeof fieldValue === "number") {
            return fieldValue.toString().includes(searchTerm.value);
        }

        // Handle date fields
        if (searchType.value.includes("date") && fieldValue) {
            const dateStr = moment(fieldValue).format("DD/MM/YYYY");
            return dateStr.includes(searchTerm.value);
        }

        // Handle string fields (case insensitive)
        return fieldValue
            .toString()
            .toLowerCase()
            .includes(searchTerm.value.toLowerCase());
    });
});
// Dialog state
const displayProgressPaymentDialog = ref(false);
const selectedAgreement = ref(null);

const showProgressPayments = (agreement) => {
    selectedAgreement.value = agreement;
    displayProgressPaymentDialog.value = true;
};
</script>
