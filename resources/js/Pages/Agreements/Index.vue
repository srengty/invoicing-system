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
                    <!-- Start Date Filter -->
                    <Calendar
                        v-if="searchType === 'start_date'"
                        v-model="startDateFilter"
                        dateFormat="yy-mm-dd"
                        showIcon
                        class="w-40 text-xs"
                    />
                    <!-- End Date Filter -->
                    <Calendar
                        v-if="searchType === 'end_date'"
                        v-model="endDateFilter"
                        dateFormat="yy-mm-dd"
                        showIcon
                        class="w-40 text-xs"
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
                scrollable
                scrollHeight="flex"
                paginator
                :rows="5"
                :rowsPerPageOptions="[5, 10, 20, 50]"
                :stripedRows="true"
                :showGridlines="true"
                resizableColumns
                columnResizeMode="fit"
                class="mt-5"
                :rowClass="rowClass"
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
                                'due_payment',
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
                        v-if="['agreement_date'].includes(col.field)"
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
                            <span
                                v-if="isExpiringSoon(slotProps.data)"
                                class="ml-2 text-xs text-yellow-600"
                            >
                                (Expires in
                                {{ daysUntilExpiration(slotProps.data) }} days)
                            </span>
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
                    <!-- Update the due_payment column to show the calculated due payment -->
                    <Column
                        v-if="col.field === 'due_payment'"
                        :field="col.field"
                        :header="col.header"
                        sortable
                    >
                        <template #body="slotProps">
                            <span>
                                {{ formatCurrency(slotProps.data.due_payment) }}
                                <span class="text-xs text-gray-500 ml-1">
                                    ({{ slotProps.data.currency }})
                                </span>
                                <Tag
                                    v-if="slotProps.data.due_payment > 0"
                                    value="PAST DUE"
                                    severity="danger"
                                    class="ml-2"
                                />
                            </span>
                        </template>
                    </Column>

                    <!-- Update the total_progress_payment column to show the sum of all receipts -->
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
                                            slotProps.data
                                                .total_progress_payment
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

                    <!-- Update the total_progress_payment_percentage column to show the percentage -->
                    <Column
                        v-if="col.field === 'total_progress_payment_percentage'"
                        :field="col.field"
                        :header="col.header"
                        sortable
                        style="width: 5%; font-size: 14px"
                    >
                        <template #body="slotProps">
                            <div class="progress-bar-wrapper flex items-center">
                                <ProgressBar
                                    :value="
                                        slotProps.data
                                            .total_progress_payment_percentage ||
                                        0
                                    "
                                    :showValue="false"
                                    :class="
                                        progressBarClass(
                                            slotProps.data
                                                .total_progress_payment_percentage ||
                                                0
                                        )
                                    "
                                    style="flex-grow: 1"
                                />
                                <span class="progress-bar-text ml-2">
                                    {{
                                        (
                                            slotProps.data
                                                .total_progress_payment_percentage ||
                                            0
                                        ).toFixed(0)
                                    }}%
                                </span>
                            </div>
                        </template>
                    </Column>
                    <!-- Start Date Column -->
                    <Column
                        v-if="col.field === 'start_date'"
                        :field="col.field"
                        :header="col.header"
                        sortable
                        style="width: 5%; font-size: 14px"
                    >
                        <template>
                            <div class="flex flex-col gap-1">
                                <span class="font-medium">Start Date</span>
                                <Calendar
                                    v-model="startDateFilter"
                                    dateFormat="yy-mm-dd"
                                    showIcon
                                    class="w-full text-xs"
                                />
                            </div>
                        </template>
                        <template #body="slotProps">
                            {{ formatDate(slotProps.data.start_date) }}
                        </template>
                    </Column>
                    <Column
                        v-else-if="col.field === 'end_date'"
                        :field="col.field"
                        :header="col.header"
                        sortable
                        style="width: 5%; font-size: 14px"
                    >
                        <template>
                            <div class="flex flex-col gap-1">
                                <span class="font-medium">End Date</span>
                                <Calendar
                                    v-model="endDateFilter"
                                    dateFormat="yy-mm-dd"
                                    showIcon
                                    class="w-full text-xs"
                                />
                            </div>
                        </template>
                        <template #body="slotProps">
                            {{ formatDate(slotProps.data.end_date) }}
                        </template>
                    </Column>
                    <!-- column for view/edit -->
                    <Column
                        v-if="col.field === 'actions'"
                        :field="col.field"
                        :header="col.header"
                        frozen
                        alignFrozen="right"
                        style="width: 8rem; font-size: 14px; z-index: 2"
                    >
                        <template #body="slotProps">
                            <Button
                                severity="info"
                                size="small"
                                icon="pi pi-pencil"
                                outlined
                                class="mr-2"
                                :disabled="slotProps.data.status === 'Closed'"
                                v-tooltip="'Cannot edit closed agreement'"
                                @click="
                                    router.get(
                                        route('agreements.edit', {
                                            agreement_no:
                                                slotProps.data.agreement_no,
                                        })
                                    )
                                "
                            />
                            <Button
                                severity=""
                                size="small"
                                icon="pi pi-eye"
                                outlined
                                class="mr-2"
                                :disabled="slotProps.data.status === 'Closed'"
                                v-tooltip="'Cannot view closed agreement'"
                                @click="viewAgreementDetails(slotProps.data)"
                            />
                            <!-- <Button
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
                            /> -->
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
                    <Column field="index" header="No." sortable class="text-sm">
                        <template #body="slotProps">
                            {{ slotProps.index + 1 }}
                        </template>
                    </Column>
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
            <!-- Agreement Details Dialog (View)-->
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
                            <!-- Agreement Documents -->
                            <div class="col-12">
                                <div class="field">
                                    <label class="font-semibold block mb-1"
                                        >Agreement Documents:</label
                                    >
                                    <ul class="ml-1 space-y-1">
                                        <li
                                            v-for="(
                                                doc, index
                                            ) in selectedAgreementDetails?.agreement_doc"
                                            :key="'doc-' + index"
                                            class="flex items-center gap-2"
                                        >
                                            <i
                                                class="pi pi-file-pdf text-red-500"
                                            ></i>
                                            <a
                                                :href="doc.path"
                                                target="_blank"
                                                class="text-blue-600 hover:underline"
                                            >
                                                {{
                                                    doc.name ||
                                                    `Document ${index + 1}`
                                                }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Attachments -->
                            <div class="col-12">
                                <div class="field">
                                    <label class="font-semibold block mb-1"
                                        >Attachments:</label
                                    >
                                    <ul class="ml-1 space-y-1">
                                        <li
                                            v-for="(
                                                file, index
                                            ) in selectedAgreementDetails?.attachments"
                                            :key="'attach-' + index"
                                            class="flex items-center gap-2"
                                        >
                                            <i
                                                class="pi pi-file-pdf text-red-500"
                                            ></i>
                                            <a
                                                :href="file.path"
                                                target="_blank"
                                                class="text-blue-600 hover:underline"
                                            >
                                                {{
                                                    file.name ||
                                                    `Attachment ${index + 1}`
                                                }}
                                            </a>
                                        </li>
                                    </ul>
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
                            :rowClass="paymentScheduleRowClass"
                        >
                            <Column field="id" header="No" sortable>
                                <template #body="slotProps">
                                    {{ slotProps.index + 1 }}
                                </template>
                            </Column>
                            <Column field="due_date" header="Due Date" sortable>
                                <template #body="slotProps">
                                    <span
                                        :class="{
                                            'text-red-500 font-semibold':
                                                isPastDue(
                                                    slotProps.data.due_date
                                                ),
                                        }"
                                    >
                                        {{
                                            formatDate(slotProps.data.due_date)
                                        }}
                                    </span>
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
                            <Column header="Status" sortable>
                                <template #body="slotProps">
                                    <Tag
                                        :value="
                                            getPaymentStatus(slotProps.data)
                                        "
                                        :severity="
                                            getStatusSeverityPayment(
                                                slotProps.data
                                            )
                                        "
                                        class="text-transform: uppercase"
                                    />
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
import { route } from "ziggy-js";
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
    Calendar,
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
        header: "Edit/View/Print",
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
// function filter agreements
const searchTerm = ref("");
const searchType = ref("");
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
const startDateFilter = ref(null);
const endDateFilter = ref(null);
// function to calculate due payment
const rowClass = (data) => {
    return {
        "bg-red-50": data.due_payment > 0,
        "border-l-4 border-red-500": data.due_payment > 0,
    };
};
// In your list agreement component
const calculateDuePayment = (agreement) => {
    if (
        !agreement.payment_schedules ||
        agreement.payment_schedules.length === 0
    ) {
        return 0;
    }

    const today = moment();
    let duePayment = 0;

    agreement.payment_schedules.forEach((schedule) => {
        const dueDate = moment(schedule.due_date, [
            "YYYY-MM-DD",
            "DD/MM/YYYY",
            moment.ISO_8601,
        ]);
        if (dueDate.isValid() && dueDate.isBefore(today, "day")) {
            // Add the amount if the payment is past due
            duePayment += parseFloat(schedule.amount) || 0;
        }
    });

    return duePayment;
};
// Process your agreements data to include due_payment
const processedAgreements = computed(() => {
    return props.agreements.map((agreement) => {
        const totalPaid = (agreement.progress_payments || []).reduce(
            (sum, receipt) => sum + (parseFloat(receipt.amount) || 0),
            0
        );

        const totalPercentage =
            agreement.amount > 0 ? (totalPaid / agreement.amount) * 100 : 0;

        return {
            ...agreement,
            due_payment: calculateDuePayment(agreement),
            total_progress_payment: totalPaid,
            total_progress_payment_percentage: totalPercentage,
        };
    });
});

// Filter agreements locally (alternative to server-side search)
const filteredAgreements = computed(() => {
    return processedAgreements.value.filter((agreement) => {
        // Start and End Date Filtering
        const start = moment(agreement.start_date, [
            "YYYY-MM-DD",
            "DD/MM/YYYY",
            moment.ISO_8601,
        ]);
        const end = moment(agreement.end_date, [
            "YYYY-MM-DD",
            "DD/MM/YYYY",
            moment.ISO_8601,
        ]);

        const matchStart =
            !startDateFilter.value ||
            start.isSameOrAfter(moment(startDateFilter.value));
        const matchEnd =
            !endDateFilter.value ||
            end.isSameOrBefore(moment(endDateFilter.value));
        // Other field search logic
        const fieldValue = getFieldValue(agreement, searchType.value);
        const matchesSearch =
            !searchTerm.value || !searchType.value
                ? true
                : (typeof fieldValue === "string" &&
                      fieldValue
                          .toLowerCase()
                          .includes(searchTerm.value.toLowerCase())) ||
                  (typeof fieldValue === "number" &&
                      fieldValue.toString().includes(searchTerm.value));

        return matchesSearch && matchStart && matchEnd;
    });
});

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
            route("agreements.show", { agreement_no: agreement.agreement_no })
        );

        const formattedData = {
            ...response.data,
            agreement_doc: response.data.agreement_doc ?? [],
            attachments: response.data.attachments ?? [],
            payment_schedules:
                response.data.payment_schedules?.map((schedule) => ({
                    ...schedule,
                    due_date: moment(schedule.due_date, [
                        "YYYY-MM-DD",
                        "DD/MM/YYYY",
                        moment.ISO_8601,
                    ]).format("YYYY-MM-DD"),
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
// Add this method in your script setup
const isPastDue = (date) => {
    if (!date) return false;
    const today = moment();
    const dueDate = moment(
        date,
        ["YYYY-MM-DD", "DD/MM/YYYY", moment.ISO_8601],
        true
    );
    return dueDate.isValid() && dueDate.isBefore(today, "day");
};
const paymentScheduleRowClass = (data) => {
    return {
        "bg-red-50": isPastDue(data.due_date),
    };
};
// function to display expries date
const isExpiringSoon = (agreement) => {
    if (agreement.status !== "Open") return false;

    const endDate = moment(agreement.end_date, "DD/MM/YYYY");
    const today = moment();
    const daysRemaining = endDate.diff(today, "days");

    return daysRemaining <= 30 && daysRemaining >= 0;
};

const daysUntilExpiration = (agreement) => {
    const endDate = moment(agreement.end_date, "DD/MM/YYYY");
    const today = moment();
    return endDate.diff(today, "days");
};
// Status methods for agreement
const getStatusSeverity = (status) => {
    const upperStatus = status?.toUpperCase();
    switch (upperStatus) {
        case "OPEN":
            return "success";
        case "CLOSED":
            return "danger";
        case "ABNORMAL CLOSED":
            return "warn";
        default:
            return "info";
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

const getPaymentStatus = (schedule) => {
    if ((schedule.paid_amount ?? 0) >= schedule.amount) {
        return "PAID";
    }

    const dueDate = moment(schedule.due_date, [
        "YYYY-MM-DD",
        "DD/MM/YYYY",
        moment.ISO_8601,
    ]);
    if (dueDate.isValid() && dueDate.isBefore(moment(), "day")) {
        return "PAST DUE";
    }

    return "UPCOMING";
};
const getStatusSeverityPayment = (schedule) => {
    const status = getPaymentStatus(schedule);
    switch (status) {
        case "PAID":
            return "success"; // Green for PAID
        case "PAST DUE":
            return "danger"; // Red for PAST DUE
        case "UPCOMING":
            return "info"; // Blue for UPCOMING
        default:
            return "warning"; // Default severity for undefined statuses
    }
};
</script>

<style scoped>
.text-primary {
    color: var(--primary-color);
}
.text-primary:hover {
    text-decoration: underline;
}
</style>
