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
            <!-- Header Section with Buttons -->
            <!-- <div class="flex justify-end items-center px-4 mr-4">
                <div class="flex gap-4">
                    <Button
                        label="Add Product"
                        icon="pi pi-plus"
                        type="button"
                        @click="openAddItemDialog"
                        size="small"
                    />
                    <Button
                        label="Add Receipts"
                        icon="pi pi-plus"
                        @click="openCreate"
                        size="small"
                    />
                </div>
            </div> -->

            <!-- Invoice Form Section -->
            <form @submit.prevent="submitInvoice">
                <div
                    class="p-4 grid grid-cols-1 md:grid-cols-4 gap-4 ml-4 mr-4 text-sm"
                >
                    <div>
                        <label for="quotation_no" class="block font-medium"
                            >Quotation No</label
                        >
                        <Select
                            v-model="form.quotation_no"
                            :options="availableQuotations"
                            optionLabel="quotation_no"
                            optionValue="quotation_no"
                            placeholder="Select Quotation"
                            class="w-full"
                        />
                    </div>

                    <div class="">
                        <label for="receipt_no" class="block font-medium"
                            >Receipt No (for deposit)</label
                        >
                        <Select
                            v-model="form.receipt_no"
                            :options="availableReceipts"
                            optionLabel="receipt_no"
                            optionValue="receipt_no"
                            placeholder="Select Receipt"
                            class="w-full"
                        />
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
                            :disabled="isReadOnly"
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
                            :readonly="isReadOnly"
                        />
                    </div>

                    <div>
                        <label for="phone" class="block font-medium"
                            >Phone</label
                        >
                        <InputText
                            id="phone"
                            v-model="form.phone"
                            class="w-full"
                            placeholder="Enter phone number"
                            size="small"
                            :readonly="isReadOnly"
                        />
                    </div>

                    <!-- <div>
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
                            :readonly="isReadOnly"
                        />
                    </div> -->

                    <!-- <div>
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
                            :readonly="isReadOnly"
                        />
                    </div> -->
                </div>
                <div class="flex justify-start items-center px-8 mt-4">
                    <div class="flex gap-4">
                        <Button
                            label="Add Item"
                            icon="pi pi-plus"
                            type="button"
                            @click="openAddItemDialog"
                            size="small"
                            class="w-36"
                        />
                    </div>
                </div>
            </form>

            <!-- Product Table Section -->
            <div class="m-6">
                <DataTable
                    :value="productsList"
                    class="p-datatable-striped"
                    responsiveLayout="scroll"
                    paginator
                    :rows="5"
                    :rowsPerPageOptions="[5, 10, 20, 50]"
                >
                    <Column header="No.">
                        <template #body="slotProps">
                            {{ slotProps.index + 1 }}
                        </template>
                    </Column>
                    <Column field="product" header="Name"></Column>
                    <Column field="qty" header="Qty">
                        <template #body="slotProps">
                            <InputText
                                v-model="slotProps.data.qty"
                                @input="updateProductSubtotal(slotProps.data)"
                                :step="1"
                                :min="1"
                                :useGrouping="false"
                                :maxFractionDigits="0"
                                @keydown="preventMinus"
                                class="w-5/4"
                                size="small"
                                :readonly="isReadOnly"
                            />
                        </template>
                    </Column>
                    <Column field="unit" header="Unit"></Column>
                    <Column field="unitPrice" header="Unit Price">
                        <template #body="slotProps">
                            <InputNumber
                                v-model="slotProps.data.unitPrice"
                                mode="decimal"
                                :minFractionDigits="2"
                                :maxFractionDigits="2"
                                :useGrouping="true"
                                @input="updateProductSubtotal(slotProps.data)"
                                @keydown="preventMinus"
                                class="w-full"
                                :readonly="isReadOnly"
                            />
                        </template>
                    </Column>
                    <Column field="subTotal" header="Subtotal">
                        <template #body="slotProps">
                            <span>
                                {{ formatCurrency(slotProps.data.subTotal) }}
                                <span class="text-xs text-gray-500 ml-1"
                                    >KHR</span
                                >
                            </span>
                        </template>
                    </Column>
                    <!-- <Column header="Action">
                        <template #body="slotProps">
                            <Button
                                label="Remove"
                                icon="pi pi-times"
                                class="p-button-text p-button-danger"
                                @click="removeProduct(slotProps.data.id)"
                                :disabled="isReadOnly"
                            />
                        </template>
                    </Column> -->
                    <Column header="Actions">
                        <template #body="slotProps">
                            <div class="flex gap-2 items-center">
                                <Button
                                    icon="pi pi-trash"
                                    class="p-button-danger w-[10px] custom-button"
                                    title="remove"
                                    @click="removeProduct(slotProps.data.id)"
                                    :disabled="isReadOnly"
                                    size="small"
                                    raised
                                />
                                <Button
                                    icon="pi pi-pencil"
                                    severity="info"
                                    title="edit"
                                    @click="editProduct(slotProps.data.id)"
                                    size="small"
                                    class="custom-button"
                                    :disabled="isReadOnly"
                                    raised
                                />
                                <Button
                                    icon="pi pi-print"
                                    class="custom-button"
                                    title="Print Catalog"
                                    :disabled="!slotProps.data.pdf_url"
                                    @click="printCatalog(slotProps.data)"
                                    size="small"
                                    raised
                                />
                            </div>
                        </template>
                    </Column>
                    <Column header="Catalog">
                        <template #body="slotProps">
                            <div class="flex items-center gap-2">
                                <Checkbox
                                    v-model="slotProps.data.include_catalog"
                                    :binary="true"
                                    @change="
                                        () => {
                                            checkCatalogAvailability(
                                                slotProps.data
                                            );
                                        }
                                    "
                                    :readonly="isReadOnly"
                                />
                                <span>
                                    {{
                                        slotProps.data.include_catalog
                                            ? "Included"
                                            : "Include"
                                    }}
                                </span>
                            </div>
                        </template>
                    </Column>
                </DataTable>

                <div class="pl-2 pr-6">
                    <!-- <div
                        v-if="form.installment_paid > 0"
                        class="total-container mt-4 flex justify-between items-center"
                    >
                        <p class="font-bold">Installment Paid:</p>
                        <p class="font-bold text-right">
                            ៛{{ formatCurrency(form.installment_paid) }}
                        </p>
                    </div> -->

                    <!-- Total KHR from all products -->

                    <div
                        class="total-container mt-4 flex justify-between items-center"
                    >
                        <p class="font-bold">Total KHR:</p>
                        <p class="font-bold text-right">
                            ៛{{ formatCurrency(calculateTotalKHR) }}
                        </p>
                    </div>

                    <!-- Final Total KHR (editable) -->

                    <div
                        class="total-container mt-4 flex justify-between items-center"
                    >
                        <p class="font-bold">Deposit Total:</p>
                        <p class="font-bold text-right">
                            ៛{{ formatCurrency(form.deposit_amount) }}
                        </p>
                    </div>

                    <!-- <div
                        class="total-container mt-4 flex justify-between items-center"
                    >
                        <p class="font-bold">Remaining deposit:</p>
                        <p class="font-bold text-right">
                            ៛{{ formatCurrency(form.installment_paid) }}
                        </p>
                    </div> -->

                    <div
                        class="total-container mt-4 flex justify-between items-center"
                    >
                        <p class="font-bold">Final Total:</p>
                        <p class="font-bold text-right">
                            ៛{{ formatCurrency(form.installment_paid) }}
                        </p>
                    </div>

                    <!-- Final Total USD (editable) -->

                    <div
                        class="total-container mt-4 flex justify-between items-center"
                    >
                        <p class="font-bold">Final Total USD:</p>
                        <input
                            type="number"
                            v-model.number="form.total_usd"
                            placeholder="Enter USD"
                            step="0.01"
                            class="h-9 text-sm border border-gray-300 rounded px-2 text-right w-40"
                        />
                    </div>

                    <!-- Exchange Rate -->

                    <div
                        class="grand-total-container flex justify-between mt-4"
                    >
                        <p class="font-bold">Exchange Rate:</p>
                        <p class="font-bold text-right">
                            {{ formatCurrency(calculateExchangeRate) }}
                        </p>
                    </div>

                    <!-- Placeholder Bank Info -->

                    <div
                        class="grand-total-container flex justify-between mt-4"
                    >
                        <p class="font-bold">Bank Name:</p>
                        <p class="text-right text-gray-400 italic">Not set</p>
                    </div>

                    <div
                        class="grand-total-container flex justify-between mt-4"
                    >
                        <p class="font-bold">Bank Account Name:</p>
                        <p class="text-right text-gray-400 italic">Not set</p>
                    </div>

                    <div
                        class="grand-total-container flex justify-between mt-4"
                    >
                        <p class="font-bold">Bank Account Number:</p>
                        <p class="text-right text-gray-400 italic">Not set</p>
                    </div>
                </div>

                <!-- <div class="terms mt-4">
          <h3 class="text-lg">Terms and Conditions</h3>
          <p>Full payment is required upon quote acceptance.</p>
          <p>This quote is negotiable for one (1) week from the date stated above.</p>
        </div>
        <div class="buttons mt-4 flex justify-end">
          <Button label="Submit request for approval" icon="pi pi-check" class="p-button-success" @click="submitInvoice" />
          <Button label="Cancel" class="p-button-secondary ml-2" @click="cancel" />
        </div> -->
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
            <!-- Modal to Select Product -->
            <Dialog
                v-model:visible="isAddItemDialogVisible"
                modal
                header="Add Item (Popup)"
                :style="{ width: '450px' }"
                class="text-sm"
                :draggable="false"
                :resizable="false"
                :position="'center'"
                :closeOnEscape="false"
            >
                <div class="p-fluid grid gap-4 text-sm">
                    <!-- Division Selection -->
                    <div class="field w-full">
                        <label for="division" class="required">Division</label>
                        <br />
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
                        <label for="item" class="required">Item</label> <br />
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
                    <!-- Item Category (Auto-complete, Read-Only) -->
                    <div class="field">
                        <label for="item-category" class="required"
                            >Item Category</label
                        >
                        <InputText
                            :value="
                                getCategoryName(selectedProduct.category_id)
                            "
                            class="w-full text-sm"
                            size="small"
                            readonly
                        />
                    </div>
                    <!-- Unit Price (Auto-complete, Editable) -->
                    <div class="field">
                        <label for="unit-price" class="required"
                            >Unit Price</label
                        >
                        <InputNumber
                            v-model="selectedProduct.price"
                            :min="0"
                            :minFractionDigits="2"
                            :maxFractionDigits="2"
                            @keydown="preventMinus"
                            size="small"
                            class="w-full text-sm"
                        />
                    </div>
                    <!-- Account Code (Auto-complete, Read-Only) -->
                    <div class="field">
                        <label for="account-code" class="required"
                            >Account Code</label
                        >
                        <InputText
                            v-model="selectedProduct.acc_code"
                            class="w-full text-sm"
                            size="small"
                            readonly
                        />
                    </div>
                    <!-- Quantity -->
                    <div class="field">
                        <label for="quantity" class="required">Quantity</label>
                        <InputNumber
                            v-model="selectedProduct.quantity"
                            class="w-full text-sm"
                            size="small"
                            :min="1"
                        />
                    </div>
                    <!-- View Catalog -->
                    <div v-if="selectedProduct.pdf_url" class="text-start">
                        <label for="quantity">Catalog</label>
                        <a
                            :href="`/pdfs/${selectedProduct.pdf_url
                                .split('/')
                                .pop()}`"
                            target="_blank"
                            class="text-blue-500 hover:text-blue-700 transition duration-200"
                        >
                            📄 View Catelog
                        </a>
                    </div>
                    <p v-else class="text-center text-gray-400">
                        No PDF available
                    </p>
                    <!-- Additional Remark -->
                    <div class="field">
                        <label for="additional-remark">Additional Remark</label>
                        <InputText
                            v-model="selectedProduct.remark"
                            class="w-full text-sm"
                            size="small"
                        />
                    </div>
                </div>
                <!-- Dialog Footer -->
                <template #footer>
                    <Button
                        label="Cancel"
                        icon="pi pi-times"
                        class="p-button-text"
                        @click="closeAddItemDialog()"
                    />
                    <Button
                        :label="editingProduct ? 'Update Item' : 'Add Item'"
                        icon="pi pi-check"
                        @click="addItemToTable"
                    />
                </template>
            </Dialog>

            <CreateReceiptDialog
                v-model:visible="isReceiptDialogVisible"
                :customer-id="form.customer_id"
                @receipt-created="handleReceiptCreated"
            />
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
    Toast,
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
import { Inertia } from "@inertiajs/inertia";
import Breadcrumb from "primevue/breadcrumb";
import NavbarLayout from "@/Layouts/NavbarLayout.vue";
import CreateReceiptDialog from "@/Pages/Receipts/Create.vue";
import { getDepartment } from "../../data";
import { useToast } from "primevue/usetoast";
import { router } from "@inertiajs/vue3";

const toast = useToast();

const {
    products,
    agreements,
    quotations,
    customers,
    paymentSchedules,
    receipts,
    invoices,
    usedReceiptNos,
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
});

const form = useForm({
    invoice_no: "", // Optional: generated if blank
    quotation_no: "",
    agreement_no: "",
    customer_id: "",
    address: "",
    phone: "",
    terms: "",
    amount: 0,
    payment_schedules: [],
    start_date: "",
    end_date: "",
    grand_total: "",
    total_usd: "",
    exchange_rate: "",
    invoice_date: new Date().toISOString(), // Send in ISO format
    status: "Pending",
    installment_paid: 0,
    paid_amount: 0,
    products: [],
    receipt_no: "",
    userModifiedPaidAmount: false,
});

const page = usePage();
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

const isReadOnly = computed(() => {
    return form.quotation_no ? true : false;
});

// Product List and Dialog Management
const productsList = ref([]);
const isAddItemDialogVisible = ref(false);
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

const usedReceiptNosRef = ref(usedReceiptNos || []);
const customerCategories = ref([]);
const isReceiptDialogVisible = ref(false);
const openCreate = () => {
    router.visit(route("receipts"));
};
const handleReceiptCreated = async ({ receipt, shouldReload }) => {
    if (receipt) {
        // Add the new receipt to the receipts list
        receipts.value = [...receipts.value, receipt];

        // Optionally auto-select the new receipt
        form.receipt_no = receipt.receipt_no;

        toast.add({
            severity: "success",
            summary: "Success",
            detail: `Receipt ${receipt.receipt_no} created successfully`,
            life: 3000,
        });
    }

    if (shouldReload) {
        // Only reload if absolutely necessary
        await Inertia.reload({ only: ["receipts"] });
    }

    isReceiptDialogVisible.value = false;
};

const availableReceipts = computed(() => {
    return receipts.filter((receipt) => {
        // Exclude receipts that are already used (exists in usedReceiptNos)
        if (usedReceiptNosRef.value.includes(receipt.receipt_no)) {
            return false;
        }

        // Optionally filter by selected customer to only show matching receipts
        if (form.customer_id && receipt.customer_id !== form.customer_id) {
            return false;
        }

        return true; // include all other receipts
    });
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

function getOrdinalSuffix(number) {
    const suffixes = ["th", "st", "nd", "rd"];
    const remainder = number % 100;

    return (
        suffixes[(remainder - 20) % 10] || suffixes[remainder] || suffixes[0]
    );
}

const calculateTotalKHR = computed(() => {
    return productsList.value.reduce(
        (acc, product) => acc + product.subTotal,
        0
    );
});

const calculateExchangeRate = computed(() => {
    if (!form.total_usd || form.total_usd <= 0 || form.grand_total <= 0)
        return 0;
    return (form.installment_paid / form.total_usd).toFixed(2);
});

onMounted(async () => {
    const response = await getDepartment();
    const data = response.data;

    // Filter departments with status === "service"
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
        // Filter by selected division and exclude already selected products
        filteredProducts.value = props.products.filter(
            (product) =>
                product.division_id === divisionId &&
                product.status === "approved" &&
                !selectedProductIds.includes(product.id)
        );
    } else if (editingProduct.value) {
        // If editing, use the product's division
        selectedDivision.value = editingProduct.value.division_id;
        filteredProducts.value = props.products.filter(
            (product) =>
                product.division_id === editingProduct.value.division_id &&
                product.status === "approved" &&
                !selectedProductIds.includes(product.id)
        );
    } else {
        // Show all approved products not already selected
        filteredProducts.value = props.products.filter(
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

    // Start with all approved products not already selected
    let productsToSearch = props.products.filter(
        (product) =>
            product.status === "approved" &&
            !selectedProductIds.includes(product.id)
    );

    // Apply division filter if one is selected
    if (divisionId) {
        productsToSearch = productsToSearch.filter(
            (product) => product.division_id === divisionId
        );
    }

    // Apply search query filter
    filteredProducts.value = productsToSearch.filter((product) =>
        product.name.toLowerCase().includes(query)
    );
};

watch(
    () => form.receipt_no,
    async (newReceiptNo) => {
        if (!newReceiptNo) {
            form.installment_paid = 0;
            form.paid_amount = 0;
            form.payment_schedules = [];
            return;
        }

        const receipt = receipts.find((r) => r.receipt_no === newReceiptNo);
        if (!receipt) return;

        // Auto set customer if none selected
        if (form.customer_id && form.customer_id !== receipt.customer_id) {
            toast.add({
                severity: "warn",
                summary: "Customer Mismatch",
                detail: `Selected receipt belongs to a different customer.`,
                life: 3000,
            });
            form.receipt_no = "";
            return;
        }

        const usedInInvoice = receipt.invoice_no;

        // Determine total price based on quotation or manual product total
        let totalPrice = 0;
        if (form.quotation_no) {
            const selectedQuotation = quotations.find(
                (q) => q.quotation_no === form.quotation_no
            );
            totalPrice = selectedQuotation?.total || 0;
        } else {
            totalPrice = calculateTotalKHR.value;
        }

        // Update customer and contact info
        form.customer_id = receipt.customer_id;
        const customer = customers.find((c) => c.id === receipt.customer_id);
        form.address = customer?.address || "";
        form.phone = customer?.phone || customer?.phone_number || "";
        form.deposit_amount = receipt.paid_amount;

        // Case 1: Receipt reused with remaining installment
        if (usedInInvoice && receipt.installment_paid > 0) {
            if (receipt.paid_amount > totalPrice) {
                form.paid_amount = totalPrice;
                form.installment_paid = 0;
                receipt.installment_paid = receipt.paid_amount - totalPrice;
            } else if (receipt.paid_amount < totalPrice) {
                form.paid_amount = receipt.paid_amount;
                form.installment_paid = totalPrice - receipt.paid_amount;
                receipt.installment_paid = 0;
            } else {
                form.paid_amount = receipt.paid_amount;
                form.installment_paid = 0;
                receipt.installment_paid = 0;
            }

            form.grand_total = totalPrice;

            toast.add({
                severity: "info",
                summary: "Installment Applied",
                detail: `Used ${formatCurrency(
                    form.paid_amount
                )} from previous receipt`,
                life: 3000,
            });
        }

        // Case 2: Receipt has no remaining installment
        else if (usedInInvoice && receipt.installment_paid === 0) {
            if (form.customer_id === receipt.customer_id) {
                form.paid_amount = receipt.paid_amount;
                form.installment_paid = 0;
                form.grand_total = totalPrice;

                if (receipt.payment_schedule_ids) {
                    form.payment_schedules = receipt.payment_schedule_ids;
                }

                toast.add({
                    severity: "info",
                    summary: "Receipt Selected",
                    detail: `Receipt ${receipt.receipt_no} reused for the same customer.`,
                    life: 3000,
                });
            } else {
                toast.add({
                    severity: "warn",
                    summary: "Receipt Already Used",
                    detail: `Receipt ${receipt.receipt_no} has no remaining balance and belongs to a different customer.`,
                    life: 3000,
                });
                form.receipt_no = "";
                return;
            }
        }

        // Case 3: New receipt (not used in invoice)
        else {
            if (receipt.paid_amount > totalPrice) {
                form.paid_amount = totalPrice;
                form.installment_paid = 0;
                receipt.installment_paid = receipt.paid_amount - totalPrice;
            } else if (receipt.paid_amount < totalPrice) {
                form.paid_amount = receipt.paid_amount;
                form.installment_paid = totalPrice - receipt.paid_amount;
                receipt.installment_paid = 0;
            } else {
                form.paid_amount = receipt.paid_amount;
                form.installment_paid = 0;
                receipt.installment_paid = 0;
            }

            form.grand_total = totalPrice;

            if (receipt.payment_schedule_ids) {
                form.payment_schedules = receipt.payment_schedule_ids;
            }

            toast.add({
                severity: "info",
                summary: "Receipt Selected",
                detail: `Auto-filled info from receipt ${receipt.receipt_no}`,
                life: 2500,
            });
        }

        // ✅ NEW: If there's no quotation, and products are already added, re-apply calculation
        if (!form.quotation_no && productsList.value.length > 0) {
            const dynamicTotal = calculateTotalKHR.value;

            if (receipt.paid_amount > dynamicTotal) {
                form.paid_amount = dynamicTotal;
                form.installment_paid = 0;
                receipt.installment_paid = receipt.paid_amount - dynamicTotal;
            } else if (receipt.paid_amount < dynamicTotal) {
                form.paid_amount = receipt.paid_amount;
                form.installment_paid = dynamicTotal - receipt.paid_amount;
                receipt.installment_paid = 0;
            } else {
                form.paid_amount = receipt.paid_amount;
                form.installment_paid = 0;
                receipt.installment_paid = 0;
            }

            form.grand_total = dynamicTotal;

            toast.add({
                severity: "info",
                summary: "Recalculated",
                detail: `Receipt amount adjusted to product total.`,
                life: 2500,
            });
        }
    },
    { immediate: true }
);

watch(
    productsList,
    () => {
        // Refresh the filtered list when productsList changes
        filterProductsByDivision();
    },
    { deep: true }
);

const updateSelectedProductDetails = () => {
    if (!selectedItem.value) return;

    const product = selectedItem.value; // Already the full object

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

const getCategoryName = (categoryId) => {
    if (!categoryId || !Array.isArray(props.productCategories)) {
        return "";
    }
    const category = props.productCategories.find(
        (cat) => cat.id === categoryId
    );
    return category
        ? category.category_name_english || category.category_name_khmer
        : "";
};

const preventMinus = (event) => {
    if (event.key === "-" || event.key === "." || event.key === ",") {
        event.preventDefault();
    }
};

// Add/Update Product to List
const addItemToTable = () => {
    if (!selectedProduct.value.id) {
        alert("Please select a valid product");
        return;
    }

    const existingIndex =
        editingProduct.value !== null
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
        include_catalog: false, // default
        pdf_url: selectedProduct.value.pdf_url,
        category_id: selectedProduct.value.category_id,
    };

    if (existingIndex >= 0 && editingProduct.value !== null) {
        productsList.value[existingIndex] = productData;
    } else {
        productsList.value.push(productData);
    }

    closeAddItemDialog();
};

watch(
    () => form.customer_id,
    (newCustomerId) => {
        if (newCustomerId) {
            const selectedCustomer = customers.find(
                (c) => c.id === newCustomerId
            );

            if (selectedCustomer) {
                form.address = selectedCustomer.address || "";
                form.phone =
                    selectedCustomer.phone ||
                    selectedCustomer.phone_number ||
                    "";
            }
        } else {
            form.address = "";
            form.phone = "";
        }
    }
);

const availableQuotations = computed(() => {
    return quotations.filter((quotation) => {
        // Find all invoices related to this quotation
        const relatedInvoices = invoices.filter(
            (inv) => inv.quotation_no === quotation.quotation_no
        );

        // Check if any related invoice fully paid the quotation total
        const isFullyPaid = relatedInvoices.some(
            (inv) => Number(inv.paid_amount) === Number(quotation.total)
        );

        // Exclude fully paid quotations
        return !isFullyPaid;
    });
});

// Existing methods from original component
watch(
    () => form.quotation_no,
    (newQuotationNo) => {
        if (newQuotationNo) {
            const selectedQuotation = quotations.find(
                (q) => q.quotation_no === newQuotationNo
            );

            if (selectedQuotation) {
                // Auto-fill customer and contact info
                form.customer_id = selectedQuotation.customer_id || "";
                form.address = selectedQuotation.address || "";
                form.phone = selectedQuotation.phone_number || "";

                // Set start_date to quotation_date (expecting ISO string)
                if (selectedQuotation.quotation_date) {
                    form.start_date = selectedQuotation.quotation_date;

                    // Calculate end_date as 14 days after quotation_date
                    const startDate = new Date(
                        selectedQuotation.quotation_date
                    );
                    startDate.setDate(startDate.getDate() + 14);

                    // Format as YYYY-MM-DD for consistent model binding
                    const pad = (n) => (n < 10 ? "0" + n : n);
                    const formattedEndDate = `${startDate.getFullYear()}-${pad(
                        startDate.getMonth() + 1
                    )}-${pad(startDate.getDate())}`;

                    form.end_date = formattedEndDate;
                } else {
                    form.start_date = "";
                    form.end_date = "";
                }

                // Set agreement if exists
                if (selectedQuotation.agreement) {
                    form.agreement_no =
                        selectedQuotation.agreement.agreement_no;
                    filteredAgreements.value = [selectedQuotation.agreement];
                } else {
                    form.agreement_no = "";
                    filteredAgreements.value = agreements.filter(
                        (a) => a.quotation == null && a.status === "Open"
                    );
                }

                // Set products list if any
                if (
                    Array.isArray(selectedQuotation.product_quotations) &&
                    selectedQuotation.product_quotations.length > 0
                ) {
                    productsList.value =
                        selectedQuotation.product_quotations.map((pq) => ({
                            id: pq.product.id,
                            product: pq.product.name || "Unknown Product",
                            qty: pq.quantity || 1,
                            unit: pq.product.unit || "Unit",
                            unitPrice: pq.price || 0,
                            subTotal: (pq.quantity || 1) * (pq.price || 0),
                            remark: pq.remark || "",
                            category_id: pq.product.category_id || null,
                            acc_code: pq.product.acc_code || "",
                            include_catalog: false,
                            pdf_url: pq.product.pdf_url || null,
                        }));
                } else {
                    productsList.value = [];
                }

                // Set grand total from quotation total or sum of products
                const total =
                    selectedQuotation.total ??
                    selectedQuotation.product_quotations?.reduce((sum, pq) => {
                        return sum + (pq.quantity || 1) * (pq.price || 0);
                    }, 0) ??
                    0;

                form.grand_total = total;
            }
        } else {
            // Reset form when no quotation selected
            filteredAgreements.value = agreements.filter(
                (a) => a.status === "Open"
            );
            form.agreement_no = "";
            form.customer_id = "";
            form.address = "";
            form.phone = "";
            form.start_date = "";
            form.end_date = "";
            productsList.value = [];
            form.grand_total = 0;
        }
    },
    { immediate: true }
);

const indexTemplate = (rowData, { index }) => {
    return index + 1; // Return the index + 1 for 1-based index display
};

const calculateTotal = computed(() => {
    return productsList.value.reduce(
        (acc, product) => acc + product.subTotal,
        0
    );
});

const calculateGrandTotal = computed(() => {
    return calculateTotal.value;
});

const actionTemplate = (data) => {
    return {
        template: `
              <Button label="Remove" icon="pi pi-times" class="p-button-text p-button-danger" @click="removeProduct(${data.id})" />
            `,
    };
};

const removeProduct = (productId) => {
    productsList.value = productsList.value.filter(
        (product) => product.id !== productId
    );
};

const editProduct = (productId) => {
    const index = productsList.value.findIndex((p) => p.id === productId);
    if (index === -1) return;
    editingProduct.value = index;
    const row = productsList.value[index];
    selectedProduct.value = {
        id: row.id,
        name: row.product,
        category_id: row.category_id,
        price: row.unitPrice,
        acc_code: row.acc_code || "73048 ផលពីសេវាផ្សេងៗ",
        quantity: row.qty,
        remark: row.remark || "",
        pdf_url: row.pdf_url || null,
        unit: row.unit,
    };
    const original = props.products.find((p) => p.id === productId);
    selectedDivision.value = original?.division_id || null;
    selectedItem.value = original || null;
    filterProductsByDivision();
    isAddItemDialogVisible.value = true;
};

const printCatalog = (product) => {
    if (!product.pdf_url) {
        showToast(
            "warn",
            "No Catalog",
            "This product does not have a catalog available.",
            3000
        );
        return;
    }

    const pdfUrl = `/pdfs/${product.pdf_url.split("/").pop()}`;
    window.open(pdfUrl, "_blank");
};

const updateProductSubtotal = (product) => {
    product.subTotal = product.qty * product.unitPrice;
};

// const cancel = () => {
//     form.reset();
//     productsList.value = [];
// };

const submitInvoice = async () => {
    if (!form.customer_id) {
        toast.add({
            severity: "error",
            summary: "Validation Error",
            detail: "Please select a customer before submitting.",
            life: 3000,
        });
        return;
    }

    if (productsList.value.length === 0) {
        toast.add({
            severity: "error",
            summary: "Validation Error",
            detail: "Please add at least one product.",
            life: 3000,
        });
        return;
    }

    if (form.receipt_no) {
        const receipt = receipts.find((r) => r.receipt_no === form.receipt_no);
        if (receipt && receipt.customer_id !== form.customer_id) {
            toast.add({
                severity: "error",
                summary: "Receipt Error",
                detail: "The selected receipt belongs to a different customer.",
                life: 4000,
            });
            return;
        }
    }

    // Add additional validation if needed (quotation/receipt customer mismatch)...

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

    form.payment_schedules = form.payment_schedules.map((id) => {
        const schedule = paymentSchedules.find((ps) => ps.id === id);
        return { id: id, amount: schedule?.amount || 0 };
    });

    if (form.quotation_no) {
        const selectedQuotation = quotations.find(
            (q) => q.quotation_no === form.quotation_no
        );
        if (selectedQuotation) form.grand_total = selectedQuotation.total;
    } else {
        form.grand_total = Number(form.grand_total);
    }

    if (!form.total_usd && form.exchange_rate > 0) {
        form.total_usd = (calculateTotalKHR.value / form.exchange_rate).toFixed(
            2
        );
    }

    form.installment_paid = Number(form.installment_paid) || 0;
    form.paid_amount = Number(form.paid_amount) || 0;
    form.total = calculateTotalKHR.value;
    form.exchange_rate = Number(form.exchange_rate) || 0;

    try {
        await form.post("/invoices");
    } catch (error) {
        console.error("Error submitting invoice:", error);
        toast.add({
            severity: "error",
            summary: "Submission Error",
            detail: "An error occurred while submitting the invoice.",
            life: 4000,
        });
    }
};
const cancel = () => {
    toast.add({
        severity: "secondary",
        summary: "Cancelled",
        detail: "Agreement creation cancelled",
        life: 3000,
    });
    setTimeout(() => {
        router.visit(route("invoices.list"));
    }, 500);
};
const updatePaymentDetails = () => {
    const selectedPaymentSchedule = paymentSchedules.find(
        (ps) => ps.id === form.payment_schedules
    );
    if (selectedPaymentSchedule) {
        form.installment_paid = selectedPaymentSchedule.amount;
        form.paid_amount = selectedPaymentSchedule.amount;
    }
};

const checkCatalogAvailability = (product) => {
    if (product.include_catalog && !product.pdf_url) {
        console.warn("Product does not include catalog or missing PDF URL.");
        showToast(
            "error",
            "Error",
            "Catalog PDF is missing for an included product.",
            3000
        );
        return false;
    }
    console.log("Product is ready for catalog PDF.");
    return true;
};

watch(
    () => form.start_date,
    (newStartDate) => {
        if (newStartDate) {
            const startDate = new Date(newStartDate);
            startDate.setDate(startDate.getDate() + 14);

            const pad = (n) => (n < 10 ? "0" + n : n);
            const newEndDate = `${startDate.getFullYear()}-${pad(
                startDate.getMonth() + 1
            )}-${pad(startDate.getDate())}`;

            form.end_date = newEndDate;
        }
    }
);

const updateInstallmentPaid = () => {
    let total = 0;

    if (form.quotation_no) {
        const selectedQuotation = quotations.find(
            (q) => q.quotation_no === form.quotation_no
        );
        total = selectedQuotation?.total ?? 0;
    } else {
        total = productsList.value.reduce(
            (sum, p) => sum + (p.subTotal || 0),
            0
        );
    }

    form.grand_total = total;

    if (form.receipt_no) {
        const receipt = receipts.find((r) => r.receipt_no === form.receipt_no);
        if (receipt) {
            if (receipt.paid_amount > total) {
                form.paid_amount = total;
                form.installment_paid = 0;
            } else if (receipt.paid_amount < total) {
                form.paid_amount = receipt.paid_amount;
                form.installment_paid = total - receipt.paid_amount;
            } else {
                form.paid_amount = receipt.paid_amount;
                form.installment_paid = 0;
            }
            return;
        }
    }

    // No receipt or no matching receipt
    form.paid_amount = 0;
    form.installment_paid = total;
};

watch(() => form.quotation_no, updateInstallmentPaid);
watch(() => form.receipt_no, updateInstallmentPaid);
watch(productsList, updateInstallmentPaid, { deep: true });
</script>
