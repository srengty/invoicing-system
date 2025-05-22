<template>
    <div>
        <!-- Due Soon/Past Due Payments -->
        <div
            v-if="hasDueSoonOrPastDuePayments"
            class="mb-4 p-4 border-round"
            :class="
                hasPastDuePayments
                    ? 'bg-red-100 border-red-300 border-1'
                    : 'bg-orange-100 border-orange-300 border-1'
            "
        >
            <div class="flex align-items-center gap-3">
                <i
                    class="pi pi-exclamation-triangle text-xl"
                    :class="
                        hasPastDuePayments ? 'text-red-500' : 'text-orange-500'
                    "
                ></i>
                <span v-if="hasPastDuePayments" class="text-red-800">
                    You have {{ pastDuePaymentsCount }} past due payment(s).
                    Please generate invoices immediately!
                </span>
                <span v-else class="text-orange-800">
                    You have {{ dueSoonPaymentsCount }} payment(s) due soon.
                    Please generate invoices to avoid delays.
                </span>
            </div>
        </div>
        <DataTable
            v-model:editingRows="editingRows"
            editMode="row"
            dataKey="id"
            @row-edit-save="onRowEditSave"
            :value="items"
            class="p-datatable-striped"
            responsiveLayout="scroll"
            :show-gridlines="true"
            :rowClass="paymentRowClass"
        >
            <Column field="index" header="No." sortable class="text-sm">
                <template #body="slotProps">
                    {{ slotProps.index + 1 }}
                </template>
            </Column>
            <Column field="due_date" header="Due Date" sortable>
                <template #body="slotProps">
                    <span>
                        {{ formatDate(slotProps.data.due_date) }}
                    </span>
                </template>
                <template #editor="{ data, field }">
                    <DatePicker
                        v-model="data[field]"
                        fluid
                        date-format="yy/mm/dd"
                    />
                </template>
            </Column>
            <Column
                field="short_description"
                header="Short Description"
                sortable
                class="text-sm"
            >
                <template #editor="{ data, field }">
                    <DatePicker
                        v-model="data[field]"
                        fluid
                        date-format="yy/mm/dd"
                    />
                </template>
            </Column>
            <Column
                field="percentage"
                header="Percentage"
                sortable
                class="text-sm"
            >
                <template #body="slotProps">
                    {{ slotProps.data["percentage"] }} %
                </template>
                <template #editor="{ data, field }">
                    <InputGroup>
                        <InputNumber
                            v-model="data[field]"
                            mode="decimal"
                            locale="en-US"
                            :minFractionDigits="2"
                            :maxFractionDigits="2"
                            fluid
                        />
                        <InputGroupAddon append>%</InputGroupAddon>
                    </InputGroup>
                </template>
            </Column>
            <Column
                field="amount"
                header="Amount"
                style="text-align: right"
                sortable
                class="text-sm"
            >
                <template #body="slotProps">
                    <span>
                        {{ priceTemplate(slotProps.data) }}
                    </span>
                </template>
                <template #editor="{ data, field }">
                    <InputGroup>
                        <InputGroupAddon append>{{
                            props.currency ?? "$"
                        }}</InputGroupAddon>
                        <InputNumber
                            v-model="data[field]"
                            mode="decimal"
                            locale="en-US"
                            :minFractionDigits="2"
                            :maxFractionDigits="2"
                            fluid
                            min="0"
                        />
                    </InputGroup>
                </template>
            </Column>
            <Column header="Status" sortable>
                <template #body="slotProps">
                    <Tag
                        :value="getPaymentStatus(slotProps.data)"
                        :severity="getStatusSeverity(slotProps.data)"
                        class="text-transform: uppercase"
                    />
                </template>
            </Column>
            <Column
                header="Action"
                :exportable="false"
                v-if="!readonly"
                sortable
                class="text-sm"
            >
                <template #body="slotProps">
                    <div class="flex items-center gap-2">
                        <Button
                            icon="pi pi-pencil"
                            class="p-button-success p-button-outlined"
                            label="Edit"
                            @click="
                                doEditPaymentSchedule({ ...slotProps.data })
                            "
                            :disabled="
                                getPaymentStatus(slotProps.data) === 'PAID'
                            "
                        />
                        <Button
                            icon="pi pi-trash"
                            class="p-button-danger p-button-outlined"
                            label="Remove"
                            @click="
                                doDeletePaymentSchedule({ ...slotProps.data })
                            "
                            :disabled="
                                getPaymentStatus(slotProps.data) === 'PAID'
                            "
                        />
                        <Button
                            icon="pi pi-file-pdf"
                            class="p-button-success p-button-outlined"
                            label="Generate invoice"
                            :loading="generatingInvoice"
                            @click="generateInvoice(slotProps.data)"
                            :disabled="
                                getPaymentStatus(slotProps.data) === 'PAID'
                            "
                        />
                    </div>
                </template>
            </Column>
            <ColumnGroup type="footer">
                <Row>
                    <Column
                        footer="Total:"
                        footerStyle="text-align:right"
                        :colspan="4"
                        style="text-align: right"
                    ></Column>
                    <Column
                        :footer="`${currencySign} ${formattedTotal}`"
                        style="text-align: right"
                    ></Column>
                    <Column
                        footer=""
                        style="text-align: right"
                        v-if="!readonly"
                    ></Column>
                </Row>
            </ColumnGroup>
        </DataTable>
        <Dialog
            v-model="isShowing"
            class="w-1/4"
            header="Update payment schedule"
            :visible="isShowing"
            @update:visible="isShowing = $event"
            modal
            :draggable="false"
            :resizable="false"
            :position="'center'"
            :closeOnEscape="false"
        >
            <AddPayment
                v-model="editingSchedule"
                @cancel="doCancel"
                @save="doSave"
            />
        </Dialog>
    </div>
</template>

<script setup>
import AddPayment from "@/Components/Agreements/AddPayment.vue";
import { ref, defineModel, computed, onMounted } from "vue";
import { currencies } from "@/constants";
import { useToast } from "primevue/usetoast";
import moment from "moment";
import { router } from "@inertiajs/vue3";
import { Inertia } from "@inertiajs/inertia";
import {
    DataTable,
    Column,
    Button,
    InputNumber,
    InputGroup,
    InputGroupAddon,
    DatePicker,
    Row,
    ColumnGroup,
    Dialog,
    Toast,
    Tag,
} from "primevue";

const toast = useToast();
const items = defineModel({
    default: [
        /* {
        id: 1,
        due_date: "28/01/2025",
        short_description: "Item description",
        percentage: 50,
        remark: "Additional remark",
        currency: "KHR",
        amount: 2000,
    },
    {
        id: 2,
        due_date: "04/01/2025",
        short_description: "Item description",
        percentage: 50,
        currency: "KHR",
        remark: "Additional remark",
        amount: 5000,
    },
    {
        id: 3,
        due_date: "11/01/2025",
        short_description: "Item description",
        percentage: 50,
        currency: "KHR",
        remark: "Additional remark",
        amount: 3000,
    }, */
    ],
});
const props = defineProps({
    currency: String,
    readonly: Boolean,
    agreement_amount: Number,
    startDate: [String, Date],
    endDate: [String, Date],
});
const editingRows = ref([]);
const exchange_rate = ref(4100);
const onRowEditSave = (event) => {
    let { newData, index } = event;

    // Calculate the status before saving the updated schedule
    newData.status = getPaymentStatus(newData); // Make sure this is correctly set to "PAST DUE" or "PAID"

    // Update the local items array with the new data
    items.value[index] = newData;

    // Now send the updated data to the backend
    updatePaymentSchedule(newData);

    toast.add({
        severity: "success",
        summary: "Success",
        detail: "Payment schedule updated successfully",
        life: 3000,
    });
};

const updatePaymentSchedule = async (schedule) => {
    try {
        const response = await axios.put(`/payment-schedules/${schedule.id}`, {
            due_date: schedule.due_date,
            short_description: schedule.short_description,
            amount: schedule.amount,
            status: schedule.status, // Send updated status
            percentage: schedule.percentage,
        });

        console.log("Payment schedule updated", response.data);
    } catch (error) {
        console.error("Error updating payment schedule", error);
    }
};

const minDate = computed(() => {
    return props.startDate ? new Date(props.startDate) : new Date();
});

const maxDate = computed(() => {
    return props.endDate ? new Date(props.endDate) : null;
});

const formatDate = (date) => {
    if (!date) return "";
    return moment(date).format("YYYY/MM/DD");
};

const currencySign = computed(
    () => currencies.filter((v) => v.value == props.currency)[0]?.sign ?? "$"
);
const priceTemplate = (data) => {
    const currencySign = data.currency === "KHR" ? "៛" : "$";
    return `${currencySign} ${data.amount.toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    })}`;
};

const calculateRate = (propsCurrency, itemCurrency) => {
    if (propsCurrency == "USD" && itemCurrency == "KHR")
        return 1 / exchange_rate.value;
    else if (propsCurrency == "KHR" && itemCurrency == "USD")
        return exchange_rate.value;
    return 1;
};
const totalAmount = computed(() =>
    items.value.reduce(
        (acc, item) =>
            acc + calculateRate(props.currency, item.currency) * item.amount,
        0
    )
);
const formattedTotal = computed(() =>
    totalAmount.value.toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    })
);
const isShowing = ref(false);
const editingSchedule = ref({
    agreement_amount: 200,
    due_date: new Date(),
    short_description: "Item description",
    percentage: 100,
    remark: "Additional remark",
    amount: 2000,
    currency: "KHR",
    agreement_currency: "KHR",
    exchange_rate: 4200,
    status: "UPCOMING",
});
const generatingInvoice = ref(false);
const generateInvoice = (paymentItem) => {
    const paymentScheduleId = paymentItem.id;
    if (!paymentScheduleId) {
        toast.add({
            severity: "error",
            summary: "Error",
            detail: "Cannot generate invoice - payment schedule id is missing",
            life: 3000,
        });
        return;
    }
    router.visit(route("invoices.generate", { id: paymentScheduleId }));
};

const getPaymentStatus = (schedule) => {
    // First check if fully paid
    if (schedule.status === "PAID" || schedule.paid_amount >= schedule.amount) {
        return "PAID";
    }

    // Then check if partially paid
    if (schedule.paid_amount > 0) {
        return "PARTIALLY_PAID";
    }

    // Check due date status
    const today = moment();
    const dueDate = moment(
        schedule.due_date,
        ["YYYY-MM-DD", "DD/MM/YYYY", moment.ISO_8601],
        true
    );

    if (!dueDate.isValid()) {
        return schedule.status || "UPCOMING";
    }

    // Check if past due
    if (dueDate.isBefore(today, "day")) {
        return "PAST DUE";
    }

    // Check if due within 14 days
    const daysUntilDue = dueDate.diff(today, "days");
    if (daysUntilDue <= 13 && daysUntilDue >= 0) {
        return "DUE SOON";
    }

    // Default to upcoming
    return "UPCOMING";
};

const getStatusSeverity = (schedule) => {
    const status = getPaymentStatus(schedule);
    switch (status) {
        case "PAID":
            return "success";
        case "PARTIALLY_PAID":
            return "warning";
        case "PAST DUE":
            return "danger";
        case "DUE SOON":
            return "warn";
        case "UPCOMING":
            return "info";
        default:
            return "warning";
    }
};

const paymentRowClass = (data) => {
    const status = getPaymentStatus(data);
    return {
        "bg-green-50": status === "PAID",
        "border-l-4 border-green-500": status === "PAID",
        "bg-yellow-50": status === "PARTIALLY_PAID",
        "border-l-4 border-yellow-500": status === "PARTIALLY_PAID",
        "bg-orange-50": status === "DUE SOON",
        "border-l-4 border-orange-500": status === "DUE SOON",
        "bg-red-50": status === "PAST DUE",
        "border-l-4 border-red-500": status === "PAST DUE",
        "bg-blue-50": status === "UPCOMING",
        "border-l-4 border-blue-500": status === "UPCOMING",
    };
};

const doEditPaymentSchedule = (data) => {
    Object.assign(editingSchedule.value, data);
    editingSchedule.value.agreement_currency = data.currency;
    editingSchedule.value.agreement_amount = props.agreement_amount ?? 2000;
    console.log(props.agreement_amount);
    isShowing.value = true;
};
const doDeletePaymentSchedule = (data) => {
    const index = items.value.findIndex((v) => v.id == data.id);
    if (index >= 0) {
        items.value.splice(index, 1);
        toast.add({
            severity: "warn",
            summary: "Deleted",
            detail: "Payment schedule removed successfully",
            life: 3000,
        });
    }
};
const doCancel = () => {
    isShowing.value = false;
    toast.add({
        severity: "info",
        summary: "Cancelled",
        detail: "Update was cancelled",
        life: 3000,
    });
};
const doSave = () => {
    isShowing.value = false;
    const schedule = items.value.find((v) => v.id == editingSchedule.value.id);
    if (schedule) {
        Object.assign(schedule, editingSchedule.value);
        toast.add({
            severity: "success",
            summary: "Success",
            detail: "Payment schedule updated successfully",
            life: 3000,
        });
    } else {
        toast.add({
            severity: "error",
            summary: "Error",
            detail: "Failed to update payment schedule",
            life: 3000,
        });
    }
};
const hasDueSoonOrPastDuePayments = computed(() => {
    return items.value.some((item) => {
        const status = getPaymentStatus(item);
        return status === "DUE SOON" || status === "PAST DUE";
    });
});

const hasPastDuePayments = computed(() => {
    return items.value.some((item) => getPaymentStatus(item) === "PAST DUE");
});

const pastDuePaymentsCount = computed(() => {
    return items.value.filter((item) => getPaymentStatus(item) === "PAST DUE")
        .length;
});

const dueSoonPaymentsCount = computed(() => {
    return items.value.filter((item) => getPaymentStatus(item) === "DUE SOON")
        .length;
});

</script>

<style lang="scss" scoped></style>
