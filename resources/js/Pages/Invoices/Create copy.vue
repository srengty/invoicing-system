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
            <div class="flex justify-end items-center p-3 mr-4">
                <div class="flex gap-4">
                    <Button
                        label="Add Product"
                        icon="pi pi-plus"
                        class="p-button-success"
                        type="button"
                        @click="openAddItemDialog"
                        size="small"
                    />
                    <Button
                        label="Save Invoice"
                        icon="pi pi-check"
                        class="p-button-success"
                        type="button"
                        @click="submitInvoice"
                        size="small"
                    />
                </div>
            </div>

            <form @submit.prevent="submitInvoice">
                <div class="p-3 grid grid-cols-1 md:grid-cols-4 gap-4 ml-4 mr-4 text-sm">
                    <div>
                        <label for="quotation_no" class="block font-medium">Quotation No</label>
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
                        <label for="agreement_no" class="block font-medium">Agreement No</label>
                        <Select
                            v-model="form.agreement_no"
                            :options="form.agreement_no || !form.quotation_no ? agreements : agreements.filter(a => a.status === 'Open')"
                            optionLabel="agreement_no"
                            optionValue="agreement_no"
                            placeholder="Select Agreement"
                            class="w-full"
                        />
                    </div>
                    <div>
                        <label for="payment_schedule" class="block font-medium">Payment Schedule</label>
                        <MultiSelect
                            v-model="form.payment_schedule_id"
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
                                :options="receipts"
                                optionLabel="receipt_no"
                                optionValue="receipt_no"
                                placeholder="Select Receipt"
                                class="w-full"
                            />
                            <Button
                                class="text-sm p-button-info w-1/3"
                                size="small"
                            >Receipt</Button>
                        </div>
                    </div>
                    <div>
                        <label for="customer_id" class="block font-medium">Customer</label>
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
                        <label for="status" class="block font-medium">Status</label>
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
                        <label for="address" class="block font-medium">Address</label>
                        <InputText
                            id="address"
                            v-model="form.address"
                            class="w-full"
                            placeholder="Enter address"
                            size="small"
                        />
                    </div>
                    <div>
                        <label for="phone" class="block font-medium">Phone</label>
                        <InputText
                            id="phone"
                            v-model="form.phone"
                            class="w-full"
                            placeholder="Enter phone number"
                            size="small"
                        />
                    </div>
                    <div>
                        <label for="start_date" class="block font-medium">Date</label>
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
                        <label for="end_date" class="block font-medium">Due Date</label>
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

            <div class="m-6">
                <DataTable :value="productsList" class="p-datatable-striped" responsiveLayout="scroll">
                    <Column header="No.">
                        <template #body="slotProps">
                            {{ slotProps.index + 1 }}
                        </template>
                    </Column>
                    <Column field="product" header="Product"></Column>
                    <Column field="qty" header="Qty">
                        <template #body="slotProps">
                            <InputText
                                v-model="slotProps.data.qty"
                                @input="updateProductSubtotal(slotProps.data)"
                                class="w-full"
                            />
                        </template>
                    </Column>
                    <Column field="unit" header="Unit"></Column>
                    <Column field="unitPrice" header="Unit Price">
                        <template #body="slotProps">
                            <InputText
                                v-model="slotProps.data.unitPrice"
                                @input="updateProductSubtotal(slotProps.data)"
                                class="w-full"
                            />
                        </template>
                    </Column>
                    <Column field="subTotal" header="Sub Total"></Column>
                    <Column header="Action">
                        <template #body="slotProps">
                            <Button
                                label="Remove"
                                icon="pi pi-times"
                                class="p-button-text p-button-danger"
                                @click="removeProduct(slotProps.data.id)"
                            />
                        </template>
                    </Column>
                </DataTable>

                <div class="pl-2 pr-6">
                    <div v-if="form.installment_paid > 0" class="total-container mt-4 flex justify-between items-center">
                        <p class="font-bold">Installment Paid:</p>
                        <p class="font-bold text-right">៛{{ formatCurrency(form.installment_paid) }}</p>
                    </div>

                    <div class="total-container mt-4 flex justify-between items-center">
                        <p class="font-bold">Total KHR:</p>
                        <p class="font-bold text-right">៛{{ calculateTotalKHR }}</p>
                    </div>

                    <div class="total-container mt-4 flex justify-between items-center">
                        <p class="font-bold">Final Total KHR:</p>
                        <input
                            type="number"
                            v-model.number="form.paid_amount"
                            placeholder="Enter Amount"
                            step="0.01"
                            class="h-9 text-sm border border-gray-300 rounded px-2 text-right w-40"
                        />
                    </div>

                    <div class="total-container mt-4 flex justify-between items-center">
                        <p class="font-bold">Final Total USD:</p>
                        <input
                            type="number"
                            v-model.number="form.total_usd"
                            placeholder="Enter USD"
                            step="0.01"
                            class="h-9 text-sm border border-gray-300 rounded px-2 text-right w-40"
                        />
                    </div>

                    <div class="grand-total-container flex justify-between mt-4">
                        <p class="font-bold">Exchange Rate:</p>
                        <p class="font-bold text-right">{{ calculateExchangeRate }}</p>
                    </div>

                    <div class="grand-total-container flex justify-between mt-4">
                        <p class="font-bold">Bank Name:</p>
                        <p class="text-right text-gray-400 italic">Not set</p>
                    </div>

                    <div class="grand-total-container flex justify-between mt-4">
                        <p class="font-bold">Bank Account Name:</p>
                        <p class="text-right text-gray-400 italic">Not set</p>
                    </div>

                    <div class="grand-total-container flex justify-between mt-4">
                        <p class="font-bold">Bank Account Number:</p>
                        <p class="text-right text-gray-400 italic">Not set</p>
                    </div>
                </div>
            </div>

            <Dialog v-model:visible="isAddItemDialogVisible" modal header="Add Item (Popup)" :style="{ width: '550px' }" class="text-sm">
                <!-- Product Selection Form -->
                <div class="p-fluid grid gap-4 text-sm">
                    <!-- Division Selection -->
                    <div class="field w-full">
                        <label for="division" class="required">Division</label>
                        <Dropdown
                            v-model="selectedDivision"
                            :options="divisionOptions"
                            optionLabel="displayName"
                            optionValue="id"
                            placeholder="Select a Division"
                            :filter="true"
                            filterPlaceholder="Search divisions..."
                            class="w-full"
                            @change="filterProductsByDivision"
                        />
                    </div>

                    <!-- Item Selection -->
                    <div class="field w-full">
                        <label for="item" class="required">Item</label>
                        <AutoComplete
                            v-model="selectedItem"
                            :suggestions="filteredProducts"
                            :dropdown="true"
                            optionLabel="name"
                            placeholder="Search Product"
                            class="w-full text-sm"
                            @complete="searchProducts"
                            @change="updateSelectedProductDetails"
                            :input-props="{ id: 'item' }"
                        />
                    </div>

                    <div class="field">
                        <label for="unit-price" class="required">Unit Price</label>
                        <InputNumber
                            v-model="selectedProduct.price"
                            :min="0"
                            @keydown="preventMinus"
                            size="small"
                            class="w-full text-sm"
                        />
                    </div>

                    <div class="field">
                        <label for="quantity" class="required">Quantity</label>
                        <InputNumber
                            v-model="selectedProduct.quantity"
                            class="w-full text-sm"
                            size="small"
                            :min="1"
                        />
                    </div>
                </div>
                <template #footer>
                    <Button label="Cancel" icon="pi pi-times" class="p-button-text" @click="closeAddItemDialog" />
                    <Button :label="editingProduct ? 'Update Item' : 'Add Item'" icon="pi pi-check" @click="addItemToTable" />
                </template>
            </Dialog>
        </div>
    </GuestLayout>
</template>

<script setup>
import { ref, computed, watch, onMounted } from "vue";
import { useForm } from "@inertiajs/vue3";
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
} from "primevue";
import { usePage } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head } from "@inertiajs/vue3";
import { getDepartment } from "../../data";

const {
    products,
    agreements,
    quotations,
    customers,
    paymentSchedules,
    receipts,
} = usePage().props;

const form = useForm({
    invoice_no: "",
    quotation_no: "",
    agreement_no: "",
    customer_id: "",
    address: "",
    phone: "",
    terms: "",
    amount: 0,
    payment_schedule_id: [],
    start_date: "",
    end_date: "",
    grand_total: "",
    total_usd: "",
    exchange_rate: "",
    invoice_date: new Date().toISOString(),
    status: "Pending",
    installment_paid: "",
    paid_amount: "",
    products: [],
    receipt_no: "",
    userModifiedPaidAmount: false,
});

const page = usePage();
const items = computed(() => [
    { label: "", to: "/" },
    { label: page.props.title || "Invoices", to: route("invoices.index") },
    { label: page.props.title || "Create Invoices", to: route("invoices.create") },
]);

const StatusOptions = [
    { label: "Pending", value: "Pending" },
    { label: "Approved", value: "Approved" },
    { label: "Revise", value: "Revise" },
];

const productsList = ref([]);
const isAddItemDialogVisible = ref(false);
const filteredAgreements = ref([]);
const editingProduct = ref(null);
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

const divisionOptions = ref([]);

const formatCurrency = (value) => {
    if (isNaN(value)) return "0.00";
    return new Intl.NumberFormat("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(value);
};

const filteredPaymentSchedules = ref([]);

const getOrdinalSuffix = (number) => {
    const suffixes = ["th", "st", "nd", "rd"];
    const remainder = number % 100;
    return suffixes[(remainder - 20) % 10] || suffixes[remainder] || suffixes[0];
};

const formattedPaymentSchedules = computed(() => {
    return filteredPaymentSchedules.value.map((ps, index) => {
        const rankID = index + 1;
        const suffix = getOrdinalSuffix(rankID);
        const isDisabled = index === 0 && ps.is_created;
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

onMounted(async () => {
    const response = await getDepartment();
    const data = response.data;

    const serviceDepartments = data.filter((dept) => dept.status === "service");

    if (Array.isArray(serviceDepartments) && serviceDepartments.length > 0) {
        divisionOptions.value = serviceDepartments.map((dept) => ({
            name: dept.name,
            id: dept.id,
            code: dept.code,
            displayName: `${dept.code} - ${dept.name}`,
        }));
    } else {
        console.warn("No service departments found.");
        divisionOptions.value = [];
    }
});

const openAddItemDialog = () => {
    isAddItemDialogVisible.value = true;
    if (editingProduct.value) {
        selectedDivision.value = editingProduct.value.division_id;
    }
    filterProductsByDivision();
};

const closeAddItemDialog = () => {
    isAddItemDialogVisible.value = false;
    resetSelectedProduct();
};

const resetSelectedProduct = () => {
    selectedProduct.value = {
        id: null,
        name: "",
        category_id: null,
        price: 0,
        acc_code: "",
        quantity: 1,
        remark: "",
        pdf_url: null,
        unit: "",
    };
    selectedItem.value = "";
    selectedDivision.value = null;
    editingProduct.value = null;
};

const filterProductsByDivision = () => {
    const divisionId = selectedDivision.value;
    const selectedProductIds = productsList.value.map((p) => p.id);

    if (divisionId) {
        filteredProducts.value = products.filter(
            (product) =>
                product.division_id === divisionId &&
                product.status === "approved" &&
                !selectedProductIds.includes(product.id)
        );
    } else if (editingProduct.value) {
        selectedDivision.value = editingProduct.value.division_id;
        filteredProducts.value = products.filter(
            (product) =>
                product.division_id === editingProduct.value.division_id &&
                product.status === "approved" &&
                !selectedProductIds.includes(product.id)
        );
    } else {
        filteredProducts.value = products.filter(
            (product) =>
                product.status === "approved" &&
                !selectedProductIds.includes(product.id)
        );
    }
};

const searchProducts = (event) => {
    const query = event.query?.toLowerCase() || "";
    const divisionId = selectedDivision.value;
    const selectedProductIds = productsList.value.map((p) => p.id);

    let productsToSearch = products.filter(
        (product) =>
            product.status === "approved" &&
            !selectedProductIds.includes(product.id)
    );

    if (divisionId) {
        productsToSearch = productsToSearch.filter(
            (product) => product.division_id === divisionId
        );
    }

    filteredProducts.value = productsToSearch.filter((product) =>
        product.name.toLowerCase().includes(query)
    );
};

const updateSelectedProductDetails = () => {
    if (!selectedItem.value) return;

    const product = selectedItem.value;
    selectedProduct.value = {
        id: product.id,
        name: product.name,
        category_id: product.category_id,
        price: product.price,
        acc_code: product.acc_code,
        quantity: 1,
        remark: "",
        pdf_url: product.pdf_url,
        unit: product.unit,
    };

    selectedDivision.value = product.division_id || null;
};

const preventMinus = (e) => {
    if (e.key === "-") e.preventDefault();
};

const addItemToTable = () => {
    if (!selectedProduct.value.id) {
        alert("Please select a valid product");
        return;
    }

    const existingIndex = editingProduct.value
        ? editingProduct.value
        : productsList.value.findIndex(
              (p) => p.id === selectedProduct.value.id
          );

    const productData = {
        id: selectedProduct.value.id,
        product: selectedProduct.value.name,
        qty: selectedProduct.value.quantity,
        unit: selectedProduct.value.unit,
        unitPrice: selectedProduct.value.price,
        subTotal: selectedProduct.value.quantity * selectedProduct.value.price,
        remark: selectedProduct.value.remark,
        include_catalog: false,
        pdf_url: selectedProduct.value.pdf_url,
    };

    if (existingIndex >= 0 && editingProduct.value !== null) {
        productsList.value[existingIndex] = productData;
    } else {
        productsList.value.push(productData);
    }

    closeAddItemDialog();
};

// Watch payment schedule changes to update installment_paid and paid_amount
watch(
    () => form.payment_schedule_id,
    (selectedIds) => {
        if (!selectedIds || selectedIds.length === 0) {
            form.installment_paid = 0;
            form.paid_amount = 0;
            return;
        }

        const selectedSchedules = filteredPaymentSchedules.value.filter(
            (schedule) => selectedIds.includes(schedule.id)
        );

        // Calculate total installment paid from selected schedules
        form.installment_paid = selectedSchedules.reduce(
            (sum, schedule) => sum + (Number(schedule.amount) || 0),
            0
        );

        // If user hasn't manually modified paid amount, set it equal to installment_paid
        if (!form.userModifiedPaidAmount) {
            form.paid_amount = form.installment_paid;
        }
    },
    { deep: true }
);

// Track if user modifies paid_amount manually
watch(
    () => form.paid_amount,
    (newVal) => {
        if (newVal !== form.installment_paid) {
            form.userModifiedPaidAmount = true;
        }
    }
);

// Submit the invoice
const submitInvoice = async () => {
    if (productsList.value.length === 0) {
        alert("Please add at least one product.");
        return;
    }

    // Ensure that installment_paid and paid_amount are calculated correctly
    console.log("Installment Paid: ", form.installment_paid);
    console.log("Paid Amount: ", form.paid_amount);

    form.products = productsList.value.map((prod) => ({
        id: prod.id,
        quantity: prod.qty ?? 1,
        price: prod.unitPrice ?? 0,
        acc_code: prod.acc_code ?? "",
        category_id: prod.category_id ?? null,
        remark: prod.remark ?? "",
        include_catalog: Boolean(prod.include_catalog),
        pdf_url: prod.pdf_url ?? null,
    }));

    form.total = calculateTotal.value;
    form.grand_total = calculateGrandTotal.value;
    form.total_usd = form.total_usd || 0;
    form.total = calculateTotalKHR.value;
    form.exchange_rate = calculateExchangeRate.value;
    form.paid_amount = form.paid_amount || 0;
    form.installment_paid = Number(form.installment_paid) || 0;
    form.receipt_no = form.receipt_no || "";

    if (!form.total_usd && form.exchange_rate > 0) {
        form.total_usd = (calculateTotalKHR.value / form.exchange_rate).toFixed(2);
    }

    try {
        await form.post(route("invoices.store"));
    } catch (error) {
        console.error("Error submitting invoice:", error);
        alert("An error occurred while submitting the invoice.");
    }
};
</script>
