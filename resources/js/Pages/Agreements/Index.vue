list agreement
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
                                'status',
                                'amount',
                                'total_progress_payment',
                                'total_progress_payment_percentage',
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
                            {{ formatDate(slotProps.data[col.field]) }}
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
                    <!-- column for status -->
                    <Column
                        v-if="col.field === 'status'"
                        :field="col.field"
                        :header="col.header"
                        sortable
                        style="width: 5%; font-size: 14px"
                    >
                        <template #body="slotProps">
                            <Tag
                                :value="getStatusLabel(slotProps.data.status)"
                                :severity="
                                    getStatusSeverity(slotProps.data.status)
                                "
                                style="text-transform: uppercase"
                            />
                        </template>
                    </Column>
                    <!-- column for Total Amount -->
                    <Column
                        v-if="col.field === 'amount'"
                        :field="col.field"
                        :header="col.header"
                        sortable
                        style="width: 5%; font-size: 14px"
                    >
                        <template #body="slotProps">
                            {{ formatCurrency(slotProps.data[col.field]) }}
                            <span class="text-xs text-gray-500 ml-1">
                                ({{ slotProps.data.currency }})
                            </span>
                        </template>
                    </Column>
                    <!-- column for total progress payment -->
                    <Column
                        v-if="col.field === 'total_progress_payment'"
                        :field="col.field"
                        :header="col.header"
                        sortable
                        style="width: 5%; font-size: 14px"
                    >
                        <template #body="slotProps">
                            <div class="flex items-center">
                                <span
                                    class="hover:text-primary hover:underline cursor-pointer transition-all duration-200"
                                    @click="
                                        showProgressPayments(slotProps.data)
                                    "
                                >
                                    {{
                                        formatCurrency(
                                            slotProps.data[col.field]
                                        )
                                    }}
                                </span>
                                <Button
                                    v-if="
                                        slotProps.data.progress_payments?.length
                                    "
                                    icon="pi pi-info-circle"
                                    @click="
                                        showProgressPayments(slotProps.data)
                                    "
                                    severity="secondary"
                                    size="small"
                                    text
                                    rounded
                                    class="ml-2"
                                    v-tooltip.top="'View progress payments'"
                                />
                            </div>
                        </template>
                    </Column>
                    <!-- column for total progress payment percentage -->
                    <Column
                        v-if="col.field === 'total_progress_payment_percentage'"
                        :field="col.field"
                        :header="col.header"
                        sortable
                        style="width: 5%; font-size: 14px"
                    >
                        <template #body="slotProps">
                            <ProgressBar
                                v-if="
                                    col.field ===
                                    'total_progress_payment_percentage'
                                "
                                :value="slotProps.data[col.field] || 0"
                                :showValue="true"
                                :class="
                                    progressBarClass(
                                        slotProps.data[col.field] || 0
                                    )
                                "
                            />
                        </template>
                    </Column>
                    <!-- column for view/edit -->
                    <Column
                        v-if="col.field === 'actions'"
                        :field="col.field"
                        :header="col.header"
                        style="width: 5%; font-size: 14px"
                    >
                        <template #body="slotProps">
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
                                class="mr-2"
                                outlined
                            />
                            <Button
                                severity=""
                                size="small"
                                @click="viewAgreementDetails(slotProps.data)"
                                icon="pi pi-eye"
                                aria-label="View"
                                outlined
                                class="mr-2"
                            />
                            <Button
                                severity=""
                                size="small"
                                @click="
                                    router.get(
                                        route('agreements.print', {
                                            id: slotProps.data.agreement_no,
                                        })
                                    )
                                "
                                icon="pi pi-print"
                                aria-label="print"
                                outlined
                                class="mr-2"
                            />
                        </template>
                    </Column>
                </template>
            </DataTable>
            <!-- Total Progress Payment Dialog -->
            <Dialog
                v-model:visible="progressPaymentsDialog"
                :style="{ width: '45vw' }"
                header="Progress Payment Details"
                :modal="true"
                :dismissableMask="true"
                class="text-sm"
            >
                <div class="grid">
                    <div class="col-12">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-12 md:col-6 lg:col-3">
                                <div class="field flex gap-2">
                                    <label class="font-semibold"
                                        >Agreement No:</label
                                    >
                                    <div class="text-sm">
                                        {{ selectedAgreement?.agreement_no }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 md:col-6 lg:col-3">
                                <div class="field flex gap-2">
                                    <label class="font-semibold"
                                        >Customer:</label
                                    >
                                    <div>
                                        {{ selectedAgreement?.customer?.name }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 md:col-6 lg:col-3">
                                <div class="field flex gap-2">
                                    <label class="font-semibold"
                                        >Total Amount:</label
                                    >
                                    <div>
                                        {{
                                            formatCurrency(
                                                selectedAgreement?.amount
                                            )
                                        }}
                                        ({{ selectedAgreement?.currency }})
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 md:col-6 lg:col-3">
                                <div class="field flex gap-2">
                                    <label class="font-semibold">Status:</label>
                                    <Tag
                                        :value="
                                            getStatusLabel(
                                                selectedAgreement?.status
                                            )
                                        "
                                        :severity="
                                            getStatusSeverity(
                                                selectedAgreement?.status
                                            )
                                        "
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <Divider class="my-4" />
                <div class="text-md font-bold mb-3">Progress Payments</div>
                <DataTable
                    :value="selectedProgressPayments"
                    :paginator="true"
                    :rows="5"
                    :rowsPerPageOptions="[5, 10, 20]"
                    :stripedRows="true"
                    :showGridlines="true"
                    class="mt-3 text-sm"
                    size="small"
                >
                    <Column field="payment_no" header="No" sortable></Column>
                    <Column
                        field="receipt_no"
                        header="Receipt No"
                        sortable
                    ></Column>
                    <Column field="amount" header="Amount" sortable>
                        <template #body="slotProps">
                            <span class="font-semibold">
                                {{ formatCurrency(slotProps.data.amount) }}
                            </span>
                        </template>
                    </Column>
                    <Column
                        field="invoice_no"
                        header="Invoice No"
                        sortable
                    ></Column>
                </DataTable>

                <template #footer>
                    <Button
                        label="Close"
                        icon="pi pi-times"
                        @click="progressPaymentsDialog = false"
                        class="p-button-text"
                    />
                </template>
            </Dialog>
            <!-- Agreement Details Dialog -->
            <Dialog
                v-model:visible="agreementDetailsDialog"
                :style="{ width: '45vw' }"
                header="Agreement Details"
                :modal="true"
                :dismissableMask="true"
                class="text-sm"
            >
                <div class="grid">
                    <div class="col-12">
                        <!-- Agreement Information Section -->
                        <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
                            <div class="col-12 md:col-6 lg:col-4">
                                <div class="field flex gap-2">
                                    <label class="font-semibold"
                                        >Agreement No:</label
                                    >
                                    <div class="text-sm">
                                        {{
                                            selectedAgreementDetails?.agreement_no
                                        }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 md:col-6 lg:col-4">
                                <div class="field flex gap-2">
                                    <label class="font-semibold"
                                        >Quotation No:</label
                                    >
                                    <div>
                                        {{
                                            selectedAgreementDetails?.quotation_no ||
                                            "N/A"
                                        }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 md:col-6 lg:col-4">
                                <div class="field flex gap-2">
                                    <label class="font-semibold"
                                        >Agreement Ref No:</label
                                    >
                                    <div>
                                        {{
                                            selectedAgreementDetails?.agreement_ref_no ||
                                            "N/A"
                                        }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 md:col-6 lg:col-4">
                                <div class="field flex gap-2">
                                    <label class="font-semibold"
                                        >Customer:</label
                                    >
                                    <div>
                                        {{
                                            selectedAgreementDetails?.customer
                                                ?.name
                                        }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 md:col-6 lg:col-4">
                                <div class="field flex gap-2">
                                    <label class="font-semibold"
                                        >Total Amount:</label
                                    >
                                    <div>
                                        {{
                                            formatCurrency(
                                                selectedAgreementDetails?.amount
                                            )
                                        }}
                                        ({{
                                            selectedAgreementDetails?.currency
                                        }})
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 md:col-6 lg:col-4">
                                <div class="field flex gap-2">
                                    <label class="font-semibold"
                                        >Agreement Date:</label
                                    >
                                    <div>
                                        {{
                                            formatDate(
                                                selectedAgreementDetails?.agreement_date
                                            )
                                        }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 md:col-6 lg:col-4">
                                <div class="field flex gap-2">
                                    <label class="font-semibold"
                                        >Start Date:</label
                                    >
                                    <div>
                                        {{
                                            formatDate(
                                                selectedAgreementDetails?.start_date
                                            )
                                        }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 md:col-6 lg:col-4">
                                <div class="field flex gap-2">
                                    <label class="font-semibold"
                                        >End Date:</label
                                    >
                                    <div>
                                        {{
                                            formatDate(
                                                selectedAgreementDetails?.end_date
                                            )
                                        }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 md:col-6 lg:col-8">
                                <div class="field flex gap-2">
                                    <label class="font-semibold"
                                        >Description:</label
                                    >
                                    <div>
                                        {{
                                            selectedAgreementDetails?.short_description ||
                                            "N/A"
                                        }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 md:col-6 lg:col-3">
                                <div class="field flex gap-2">
                                    <label class="font-semibold">Status:</label>
                                    <Tag
                                        :value="
                                            getStatusLabel(
                                                selectedAgreementDetails?.status
                                            )
                                        "
                                        :severity="
                                            getStatusSeverity(
                                                selectedAgreementDetails?.status
                                            )
                                        "
                                    />
                                </div>
                            </div>
                        </div>
                        <Divider class="my-4" />
                        <!-- Payment Schedule Section -->
                        <div class="text-md font-bold mb-3">
                            Payment Schedule
                        </div>
                        <DataTable
                            :value="
                                selectedAgreementDetails?.payment_schedules ||
                                []
                            "
                            :paginator="true"
                            :rows="5"
                            :rowsPerPageOptions="[5, 10, 20]"
                            :stripedRows="true"
                            :showGridlines="true"
                            class="mt-3"
                            size="small"
                        >
                            <Column field="id" header="No" sortable>
                                <template #body="slotProps">
                                    {{ slotProps.index + 1 }}
                                </template>
                            </Column>
                            <Column field="due_date" header="Due Date" sortable>
                                <template #body="slotProps">
                                    {{ formatDate(slotProps.data.due_date) }}
                                </template>
                            </Column>
                            <Column
                                field="short_description"
                                header="Description"
                                sortable
                            >
                                <template #body="slotProps">
                                    {{
                                        slotProps.data.short_description ||
                                        "N/A"
                                    }}
                                </template>
                            </Column>
                            <Column
                                field="percentage"
                                header="Percentage"
                                sortable
                            >
                                <template #body="slotProps">
                                    {{ slotProps.data.percentage }}%
                                </template>
                            </Column>
                            <Column field="amount" header="Amount" sortable>
                                <template #body="slotProps">
                                    {{ formatCurrency(slotProps.data.amount) }}
                                    ({{ slotProps.data.currency }})
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                </div>
                <template #footer>
                    <Button
                        label="Close"
                        icon="pi pi-times"
                        @click="agreementDetailsDialog = false"
                        class="p-button-text"
                    />
                </template>
            </Dialog>
        </div>
    </GuestLayout>
</template>

<script setup>
import ChooseColumns from "@/Components/ChooseColumns.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import NavbarLayout from "@/Layouts/NavbarLayout.vue";
import PaymentSchedule from "./PaymentSchedule.vue";
import { ref, watch, computed } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { debounce } from "lodash";
import { usePage } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";
import moment from "moment";
import axios from "axios";
import { route } from 'ziggy-js';
import {
    DataTable,
    Column,
    Button,
    Popover,
    Dropdown,
    InputText,
    Dialog,
    ProgressBar,
    Tag,
    Divider,
    Breadcrumb,
    Badge,
    ProgressSpinner,
    Card,
} from "primevue";

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
    {
        field: "actions",
        header: "Print/View/Edit",
    },
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
const formatDate = (date, format = "YYYY-MM-DD") => {
    if (!date) return "N/A";
    const parsedDate = moment(
        date,
        ["YYYY-MM-DD", "DD/MM/YYYY", moment.ISO_8601],
        true
    );
    return parsedDate.isValid() ? parsedDate.format(format) : "Invalid date";
};


// const momentDate = (date) => {
//     return moment(date, "YYYY/MM/DD").fromNow();
// };
const searchType = ref("");
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

        if (typeof fieldValue === "number") {
            return fieldValue.toString().includes(searchTerm.value);
        }

        if (searchType.value.includes("date") && fieldValue) {
            const dateStr = moment(fieldValue).format("YYYY/MM/DD");
            return dateStr.includes(searchTerm.value);
        }

        return fieldValue
            .toString()
            .toLowerCase()
            .includes(searchTerm.value.toLowerCase());
    });
});
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
const progressBarClass = (percentage) => {
    if (percentage >= 100) return "bg-green-500";
    if (percentage >= 75) return "bg-blue-500";
    if (percentage >= 50) return "bg-yellow-500";
    if (percentage >= 25) return "bg-orange-500";
    return "bg-red-500";
};
// Progress payments dialog
const progressPaymentsDialog = ref(false);
const selectedProgressPayments = ref([]);
const selectedAgreement = ref(null);
const paymentDetailsDialog = ref(false);
const currentPayment = ref(null);

const showProgressPayments = (agreement) => {
    try {
        selectedAgreement.value = { ...agreement };
        selectedProgressPayments.value = [
            ...(agreement.progress_payments || []),
        ];
        progressPaymentsDialog.value = true;
    } catch (error) {
        console.error("Error showing progress payments:", error);
        toast.add({
            severity: "error",
            summary: "Error",
            detail: "Failed to load payment details",
            life: 3000,
        });
    }
};
// Agreement Details Dialog
const agreementDetailsDialog = ref(false);
const selectedAgreementDetails = ref(null);
const viewAgreementDetails = async (agreement) => {
    try {
        const response = await axios.get(
            route("agreements.show", { id: agreement.agreement_no })
        );

        const formattedData = {
            ...response.data,
            payment_schedules:
                response.data.payment_schedules?.map((schedule) => ({
                    ...schedule,
                    due_date: moment(schedule.due_date, "YYYY/MM/DD").format(
                        "YYYY/MM/DD"
                    ),
                })) || [],
        };

        selectedAgreementDetails.value = formattedData;
        agreementDetailsDialog.value = true;
    } catch (error) {
        console.error("Error loading agreement details:", error);
        toast.add({
            severity: "error",
            summary: "Error",
            detail: "Failed to load agreement details",
            life: 3000,
            group: "tr",
        });
    }
};

const getStatusSeverity = (status) => {
    const upperStatus = status?.toUpperCase();
    switch (upperStatus) {
        case "OPEN":
            return "success";
        case "CLOSED":
            return "danger";
        case "ABNORMAL CLOSED":
            return "info";
        default:
            return "warning";
    }
};

const getStatusLabel = (status) => {
    const upperStatus = status?.toUpperCase();
    switch (upperStatus) {
        case "OPEN":
            return "Open";
        case "CLOSED":
            return "Closed";
        case "ABNORMAL CLOSED":
            return "Abnormal Closed";
        default:
            return status || "Unknown";
    }
};
</script>

<style scoped></style>
