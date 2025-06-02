<template>
    <meta name="_token" content="{{ csrf_token() }}" />
    <Head title="Create Invoice" />
    <GuestLayout>
        <NavbarLayout />
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
        <div class="create-invoice text-sm">
            <!-- Invoice Form Section -->
            <form @submit.prevent="submitInvoice">
                <div
                    class="p-3 grid grid-cols-1 md:grid-cols-4 gap-4 ml-4 mr-4 text-sm"
                >
                    <div>
                        <label for="quotation_no" class="block font-medium"
                            >Quotation No</label
                        >
                        <Select
                            v-model="form.quotation_no"
                            :options="quotations"
                            optionLabel="quotation_no"
                            optionValue="quotation_no"
                            placeholder="Select Quotation"
                            class="w-full"
                            required
                        />
                    </div>
                    <div>
                        <label for="agreement_no" class="block font-medium"
                            >Agreement No</label
                        >
                        <Select
                            v-model="form.agreement_no"
                            :options="agreements"
                            optionLabel="agreement_no"
                            optionValue="agreement_no"
                            placeholder="Select Agreement"
                            class="w-full"
                        />
                    </div>
                    <div>
                        <label for="payment_schedule" class="block font-medium"
                            >Payment Schedule</label
                        >
                        <!-- <MultiSelect
                            v-model="form.payment_schedules"
                            :options="formattedPaymentSchedules"
                            optionLabel="label"
                            optionValue="id"
                            placeholder="Select Payment Schedule"
                            class="w-full"
                            :disabled="!!selectedPaymentSchedule"
                        /> -->
                        <MultiSelect
                            v-model="form.payment_schedules"
                            :options="formattedPaymentSchedules"
                            optionLabel="label"
                            optionValue="id"
                            placeholder="Select Payment Schedule"
                            class="w-full"
                        />
                    </div>
                    <div>
                        <label for="receipt_no" class="block font-medium">Receipt No (for deposit)</label>
                        <div class="flex w-full gap-3">
                            <Select
                            v-model="form.receipt_no"
                            :options="availableReceipts"
                            optionLabel="receipt_no"
                            optionValue="receipt_no"
                            placeholder="Select Receipt"
                            class="w-full"
                            />
                        </div>
                    </div>
                    <div>
                        <label for="customer_id" class="block font-medium"
                            >Customer</label
                        >
                        <Select
                            v-model="form.customer_id"
                            :options="customers"
                            optionLabel="name"
                            optionValue="id"
                            placeholder="Select Customer"
                            class="w-full"
                            required
                        />
                    </div>
                    <div>
                        <label for="status" class="block font-medium"
                            >Status</label
                        >
                        <Select
                            v-model="form.status"
                            :options="StatusOptions"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Select Status"
                            class="w-full"
                            required
                        />
                    </div>
                    <div>
                        <label for="address" class="block font-medium"
                            >Address</label
                        >
                        <InputText
                            id="address"
                            v-model="form.address"
                            class="w-full"
                            placeholder="Enter address"
                            size="small"
                        />
                    </div>
                    <div>
                        <label for="phone" class="block font-medium"
                            >Phone Number</label
                        >
                        <InputText
                            id="phone"
                            v-model="form.phone"
                            class="w-full"
                            placeholder="Enter phone number"
                            size="small"
                        />
                    </div>
                    <div>
                        <label for="start_date" class="block font-medium"
                            >Date</label
                        >
                        <DatePicker
                            id="start_date"
                            v-model="form.start_date"
                            class="w-full"
                            placeholder="Select date"
                            size="small"
                            dateFormat="dd/mm/yy"
                        />
                    </div>

                    <div>
                        <label for="end_date" class="block font-medium"
                            >Due Date</label
                        >
                        <DatePicker
                            id="end_date"
                            v-model="form.end_date"
                            class="w-full"
                            placeholder="Select due date"
                            size="small"
                            dateFormat="dd/mm/yy"
                        />
                    </div>
                </div>
            </form>

            <!-- Payment Schedules Table -->
            <div class="mb-6 p-6">
                <div class="text-lg font-bold mb-3">Payment Schedule</div>
                <DataTable
                    v-if="selectedScheduleArray.length"
                    :value="selectedScheduleArray"
                    class="mb-6 p-datatable-sm p-datatable-gridlines"
                    responsiveLayout="scroll"
                >
                    <Column field="id" header="ID" />
                    <Column field="due_date" header="Due Date" />
                    <Column field="amount" header="Amount">
                        <template #body="{ data }">
                            ៛{{ formatCurrency(data.amount) }}
                        </template>
                    </Column>
                    <Column field="percentage" header="%" />
                    <Column field="short_description" header="Description" />
                    <Column header="Status" sortable>
                        <template #body="slotProps">
                            <Tag
                                :value="getPaymentStatus(slotProps.data)"
                                :severity="
                                    getStatusSeverityPayment(slotProps.data)
                                "
                                class="text-transform: uppercase"
                            />
                        </template>
                    </Column>
                </DataTable>
            </div>

            <div class="flex justify-end items-center p-3 mr-4">
                <div class="flex gap-4">
                    <Button
                        label="Save"
                        type="submit"
                        raised
                        class="w-full md:w-28"
                        icon="pi pi-check"
                        size="small"
                        @click="submitInvoice"
                    />
                    <Button
                        label="Cancel"
                        severity="secondary"
                        raised
                        class="w-full md:w-28"
                        @click="cancel"
                        icon="pi pi-times"
                        size="small"
                    ></Button>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<script setup>
import { ref, computed, watch, onMounted } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";
import {
    Button,
    InputText,
    DataTable,
    Column,
    Dialog,
    DatePicker,
    Select,
    MultiSelect,
    Checkbox,
    Dropdown,
    AutoComplete,
    InputNumber,
    Tag,
} from "primevue";
import { usePage } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head,router } from "@inertiajs/vue3";
import { Inertia } from "@inertiajs/inertia";
import Breadcrumb from "primevue/breadcrumb";
import NavbarLayout from "@/Layouts/NavbarLayout.vue";
import CreateReceiptDialog from "@/Pages/Receipts/Create.vue";
import { getDepartment } from "../../data";
import moment from "moment";

const toast = useToast();
const page = usePage();
const selectedPaymentSchedule = computed(
    () => page.props.selectedPaymentSchedule
);
const selectedAgreement = computed(
    () => selectedPaymentSchedule.value?.agreement || null
);
const selectedScheduleArray = computed(() => {
    if (selectedPaymentSchedule.value) {
        return [selectedPaymentSchedule.value];
    }
    if (form.payment_schedules && form.payment_schedules.length > 0) {
        return paymentSchedules.filter((ps) =>
            form.payment_schedules.includes(ps.id)
        );
    }
    return [];
});
const items = computed(() => [
    {
        label: "",
        to: "/",
        icon: "pi pi-home",
    },
    { label: page.props.title || "Invoices", to: route("invoices.index") },
    {
        label: page.props.title || "Create Invoices",
        to: route("invoices.create"),
    },
]);
const {
    products,
    agreements,
    quotations,
    customers,
    paymentSchedules,
    receipts,
    usedReceiptNos
} = usePage().props;

const props = defineProps({
    customers: Array,
    products: Array,
    agreements: Array,
    quotations: Array,
    product_quotations: Array,
    divisions: Array,
    productCategories: Array,
    paymentSchedules: Array,
    receipts: Array,
    selectedPaymentSchedule: Object,
});

const form = useForm({
    invoice_no: "",
    quotation_no: selectedPaymentSchedule.value?.agreement?.quotation_no || "",
    agreement_no: selectedPaymentSchedule.value?.agreement?.agreement_no || "",
    customer_id: selectedPaymentSchedule.value?.agreement?.customer_id || "",
    address: selectedPaymentSchedule.value?.agreement?.address || "",
    phone:
        selectedPaymentSchedule.value?.agreement?.customer?.phone_number || "",
    terms: "",
    amount: selectedPaymentSchedule.value?.amount || 0,
    payment_schedules: selectedPaymentSchedule.value
        ? [selectedPaymentSchedule.value.id]
        : [],
    start_date: selectedPaymentSchedule.value?.agreement?.start_date || "",
    end_date: selectedPaymentSchedule.value?.due_date || "",
    grand_total: "",
    total_usd: "",
    exchange_rate: "",
    invoice_date: new Date().toISOString(),
    status: "Pending",
    installment_paid: 0,
    paid_amount: selectedPaymentSchedule.value?.amount || 0,
    products: [],
    receipt_no: "",
    userModifiedPaidAmount: false,
});

const StatusOptions = [
    { label: "Pending", value: "Pending" },
    { label: "Approved", value: "Approved" },
    { label: "Revise", value: "Revise" },
];

// Product List and Dialog Management
const usedReceiptNosRef = ref(usedReceiptNos || []);
const receiptsRef = ref(receipts || []);
const productsList = ref([]);
const filteredAgreements = ref([]);
const editingProduct = ref(null);
// Product Selection Dialog State
const selectedDivision = ref(null);
const selectedItem = ref(null);
const filteredProducts = ref([]);
const selectedProductsData = ref([]);
const selectedProduct = ref({
    id: null,
    name: "",
    category_id: null,
    price: 0,
    acc_code: "",
    quantity: 1,
    remark: "",
    pdf_url: null,
    unit: "",
});
const formatCurrency = (value) => {
    if (isNaN(value)) return "0.00";
    return new Intl.NumberFormat("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(value);
};

const filteredPaymentSchedules = ref([]);

function getOrdinalSuffix(number) {
    const suffixes = ["th", "st", "nd", "rd"];
    const remainder = number % 100;
    return (
        suffixes[(remainder - 20) % 10] || suffixes[remainder] || suffixes[0]
    );
}

const selectedPaymentSchedulesTotal = computed(() => {
  if (!form.payment_schedules || form.payment_schedules.length === 0) return null;
  return form.payment_schedules.reduce((total, id) => {
    const schedule = paymentSchedules.find(ps => ps.id === id);
    return total + (schedule?.amount || 0);
  }, 0);
});

const availableReceipts = computed(() => {
  const totalAmount = selectedPaymentSchedulesTotal.value;

  return receipts.filter(receipt => {
    // Exclude used receipts
    if (usedReceiptNosRef.value.includes(receipt.receipt_no)) {
      return false;
    }

    // Filter by customer if set
    if (form.customer_id && receipt.customer_id !== form.customer_id) {
      return false;
    }

    // If no payment schedules selected, maybe show all receipts (or return empty)
    if (totalAmount === null) {
      // return true; // show all receipts if you want
      return false;  // or return false to show none until payment schedules selected
    }

    // Compare numeric values
    if (Number(receipt.paid_amount) !== Number(totalAmount)) {
      return false;
    }

    return true;
  });
});



const formattedPaymentSchedules = computed(() => {
    if (selectedPaymentSchedule.value) {
        return [
            {
                id: selectedPaymentSchedule.value.id,
                label: `Payment for ${selectedPaymentSchedule.value.agreement_no}`,
                disabled: false,
            },
        ];
    }

    return paymentSchedules
        .filter((ps) => ps.status !== "PAID")
        .map((ps) => {
            const sameAgreementSchedules = paymentSchedules.filter(
                (s) => s.agreement_no === ps.agreement_no
            );
            const rankIndex = sameAgreementSchedules.findIndex(
                (s) => s.id === ps.id
            );
            const rankID = rankIndex + 1;
            const suffix = getOrdinalSuffix(rankID);
            const isDisabled = rankID === 1 && ps.is_created;

            return {
                id: ps.id,
                label: `${ps.agreement_no} (${rankID}${suffix} Payment)`,
                disabled: isDisabled,
            };
        });
});

const calculateTotalKHR = computed(() => {
    return productsList.value.reduce(
        (acc, product) => acc + product.subTotal,
        0
    );
});

const calculateExchangeRate = computed(() => {
    if (!form.total_usd || form.total_usd <= 0 || form.paid_amount <= 0)
        return 0;
    return (form.paid_amount / form.total_usd).toFixed(2);
});
onMounted(() => {
    if (selectedPaymentSchedule.value) {
        form.amount = selectedPaymentSchedule.value.amount;
        form.paid_amount = selectedPaymentSchedule.value.amount;
        form.end_date = selectedPaymentSchedule.value.due_date;

        // If agreement has products, add them to the products list
        if (selectedPaymentSchedule.value.agreement?.products) {
            productsList.value =
                selectedPaymentSchedule.value.agreement.products.map(
                    (product) => ({
                        id: product.id,
                        product: product.name,
                        qty: product.pivot.quantity || 1,
                        unit: product.unit,
                        unitPrice: product.pivot.price || product.price,
                        subTotal:
                            (product.pivot.quantity || 1) *
                            (product.pivot.price || product.price),
                        remark: product.pivot.remark || "",
                        category_id: product.category_id,
                        acc_code: product.acc_code,
                        include_catalog: false,
                        pdf_url: product.pdf_url,
                    })
                );
        }
    }
});
const calculateTotal = computed(() => {
    return productsList.value.reduce(
        (acc, product) => acc + product.subTotal,
        0
    );
});

const calculateGrandTotal = computed(() => {
    return calculateTotal.value - form.installment_paid;
});

const submitInvoice = async () => {
    form.invoice_no = form.invoice_no || "";
    form.start_date = form.start_date || "";
    form.end_date = form.end_date || "";
    form.payment_schedules = form.payment_schedules.map((id) => {
        const schedule = paymentSchedules.find((ps) => ps.id === id);
        return {
            id: id,
            amount: schedule?.amount || 0,
        };
    });

    // Ensure numeric values
    form.installment_paid = Number(form.installment_paid) || 0;
    form.paid_amount = Number(form.paid_amount) || 0;

    form.total = calculateTotal.value;
    form.grand_total = calculateGrandTotal.value;
    form.total_usd = form.total_usd || 0;
    form.total = calculateTotalKHR.value;
    form.exchange_rate = calculateExchangeRate.value;
    form.receipt_no = form.receipt_no || "";
    console.log(form.installment_paid);
    if (!form.total_usd && form.exchange_rate > 0) {
        form.total_usd = (calculateTotalKHR.value / form.exchange_rate).toFixed(
            2
        );
    }

    console.log(form.installment_paid);
    try {
        form.post("/invoices");
    } catch (error) {
        console.error("Error submitting invoice:", error);
        alert("An error occurred while submitting the invoice.");
    }
};
const cancel = () => {
    toast.add({
        severity: "secondary",
        summary: "Cancelled",
        detail: "Generate invoice cancelled",
        life: 3000,
    });
    setTimeout(() => {
        router.visit(route("invoices.list"));
    }, 500);
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

    // Then check if past due
    const today = moment();
    const dueDate = moment(
        schedule.due_date,
        ["YYYY-MM-DD", "DD/MM/YYYY", moment.ISO_8601],
        true
    );
    if (dueDate.isValid() && dueDate.isBefore(today, "day")) {
        return "PAST DUE";
    }

    // Default to upcoming
    return schedule.status;
};
const getStatusSeverityPayment = (schedule) => {
    const status = getPaymentStatus(schedule);
    switch (status) {
        case "PAID":
            return "success";
        case "PAST DUE":
            return "danger";
        case "UPCOMING":
            return "info";
        default:
            return "warning";
    }
};
</script>
