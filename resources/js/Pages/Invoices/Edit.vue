<template>
  <GuestLayout>
    <NavbarLayout />
    <Head title="Edit Invoice" />

    <div class="py-3">
      <Breadcrumb :model="[{ label: 'Invoices', to: '/invoices' }, { label: 'Edit Invoice' }]" />
    </div>

    <div class="p-6 text-sm">

      <div class="flex justify-end items-center mb-4">
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
                    />
                </div>
            </div>
      <!-- Invoice Form Section -->
      <form @submit.prevent="submitInvoice">
        <div class=" grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
          <div>
            <label for="quotation_no">Quotation No</label>
            <Select
              v-model="form.quotation_no"
              :options="props.quotations"
              optionLabel="quotation_no"
              optionValue="quotation_no"
              placeholder="Select Quotation"
              :disabled="true"
              class="w-full"
            />
          </div>

          <!-- Remove this duplicate block -->
          <div>
            <label for="receipt_no">Receipt No</label>
            <Select
              v-model="form.receipt_no"
              :options="props.receipts"
              optionLabel="receipt_no"
              optionValue="receipt_no"
              placeholder="Select Quotation"
              class="w-full"
              :key="form.receipt_no"
            />
          </div>

          <div>
            <label for="customer_id">Customer</label>
            <Select
              v-model="form.customer_id"
              :options="props.customers"
              optionLabel="name"
              optionValue="id"
              placeholder="Select Customer"
              :disabled="isReadOnly"
              class="w-full"
            />
          </div>

          <div class="text-sm">
            <label for="address">Address</label>
            <InputText v-model="form.address" class="w-full" :readonly="isReadOnly" size="small"/>
          </div>

          <div>
            <label for="phone">Phone</label>
            <InputText v-model="form.phone" class="w-full" :readonly="isReadOnly" size="small"/>
          </div>

          <div>
            <label for="start_date">Start Date</label>
            <DatePicker v-model="form.start_date" class="w-full" :readonly="isReadOnly" size="small"/>
          </div>

          <div>
            <label for="end_date">End Date</label>
            <DatePicker v-model="form.end_date" class="w-full" :readonly="isReadOnly" size="small"/>
          </div>
        </div>
      </form>

      <!-- Product Table -->
      <div class="mt-6">
        <DataTable :value="productsList" responsiveLayout="scroll">
          <Column header="No">
            <template #body="slotProps">{{ slotProps.index + 1 }}</template>
          </Column>
          <Column field="product" header="Product" />
          <Column field="qty" header="Qty">
            <template #body="slotProps">
              <InputText
                v-model="slotProps.data.qty"
                @input="updateProductSubtotal(slotProps.data)"
                class="w-full"
              />
            </template>
          </Column>
          <Column field="unit" header="Unit" />
          <Column field="unitPrice" header="Unit Price">
            <template #body="slotProps">
              <InputText
                v-model="slotProps.data.unitPrice"
                @input="updateProductSubtotal(slotProps.data)"
                class="w-full"
              />
            </template>
          </Column>
          <Column field="subTotal" header="Sub Total">
            <template #body="slotProps">
              {{ slotProps.data.qty * slotProps.data.unitPrice }}
            </template>
          </Column>
          <Column header="Action">
                        <template #body="slotProps">
                            <Button
                                label="Remove"
                                icon="pi pi-times"
                                class="p-button-text p-button-danger"
                                @click="removeProduct(slotProps.data.id)"
                                :disabled="isReadOnly"
                            />
                        </template>
                    </Column>
        </DataTable>
      </div>

      <!-- Totals -->
      <div class="pl-2 pr-6">
        <!-- Total KHR from all products -->
        <div class="total-container mt-4 flex justify-between items-center">
          <p class="font-bold">Total KHR:</p>
          <p class="font-bold text-right">៛{{ formatCurrency(calculateTotalKHR) }}</p>
        </div>

        <!-- Editable Deposit Total -->
        <div class="total-container mt-4 flex justify-between items-center">
          <label for="deposit_amount" class="font-bold">Deposit Total:</label>
          <p class="font-bold text-right">
             ៛{{ formatCurrency(form.deposit_amount) }}
          </p>
        </div>

        <!-- Editable Final Total -->
        <div class="total-container mt-4 flex justify-between items-center">
          <label for="installment_paid" class="font-bold">Final Total:</label>
          <p class="font-bold text-right">
            ៛{{ formatCurrency(form.installment_paid) }}
          </p>
        </div>

        <!-- Final Total USD (already editable) -->
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

        <!-- Exchange Rate -->
        <div class="grand-total-container flex justify-between mt-4">
          <p class="font-bold">Exchange Rate:</p>
          <p class="font-bold text-right">{{ formatCurrency(calculateExchangeRate) }}</p>
        </div>

        <!-- Placeholder Bank Info -->
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


      <!-- Save and Cancel -->
      <div class="mt-6 flex justify-end gap-4">
        <Button label="Update" icon="pi pi-check" @click="submitInvoice" />
        <Button label="Cancel" icon="pi pi-times" severity="secondary" @click="cancel" />
      </div>
    </div>

    <Dialog
                v-model:visible="isAddItemDialogVisible"
                modal
                header="Add Item (Popup)"
                :style="{ width: '550px' }"
                class="text-sm"
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
  DatePicker,
  Select,
  Dialog,
  Dropdown,
  AutoComplete,
  InputNumber
} from "primevue";
import { Inertia } from "@inertiajs/inertia";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import NavbarLayout from "@/Layouts/NavbarLayout.vue";
import Breadcrumb from "primevue/breadcrumb";
import { getDepartment } from "../../data";

const props = defineProps({
  invoice: Object,
  customers: Array,
  products: Array,
  agreements: Array,
  quotations: Array,
  paymentSchedules: Array,
  receipts: Array,
});

const form = useForm({
  invoice_no: "",
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
  grand_total: 0,
  total_usd: 0,
  exchange_rate: 0,
  invoice_date: new Date().toISOString(),
  status: "Pending",
  installment_paid: 0,
  paid_amount: 0,
  deposit_amount: 0,
  products: [],
  receipt_no: "",
  userModifiedPaidAmount: false,
});

const isAddItemDialogVisible = ref(false);
const filteredAgreements = ref([]);
const editingProduct = ref(null);
const divisionOptions = ref([]);
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
    unit :"",
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

const getCategoryName = (categoryId) => {
    // You'll need to implement this based on your category data structure
    return categoryId ? `Category ${categoryId}` : "N/A";
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



const productsList = ref([]);

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

watch(
    productsList,
    () => {
        // Refresh the filtered list when productsList changes
        filterProductsByDivision();
    },
    { deep: true }
);

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
    };

    if (existingIndex >= 0 && editingProduct.value !== null) {
        productsList.value[existingIndex] = productData;
    } else {
        productsList.value.push(productData);
    }

    closeAddItemDialog();
};



const isQuotationLocked = computed(() => !!form.quotation_no);

onMounted(() => {
  if (props.invoice) {
    const inv = props.invoice;
    Object.assign(form, inv);
    form.receipt_no = Number(inv.receipt_no);
    productsList.value = inv.products.map(p => ({
      id: p.id,
      product: p.name,
      qty: Number(p.pivot.quantity),
      unit: p.unit,
      unitPrice: Number(p.pivot.price),
      subTotal: Number(p.pivot.quantity) * Number(p.pivot.price),
      remark: p.pivot.remark,
      include_catalog: p.pivot.include_catalog,
      pdf_url: p.pdf_url,
    }));
    form.payment_schedules = inv.paymentSchedules?.map((ps) => ps.id) || [];
  }
});

const updateProductSubtotal = (product) => {
  product.qty = Number(product.qty) || 0;
  product.unitPrice = Number(product.unitPrice) || 0;
  product.subTotal = product.qty * product.unitPrice;
};

const formatCurrency = (value) => {
  if (!value) return '0.00';
  return new Intl.NumberFormat('en-US', { minimumFractionDigits: 2 }).format(value);
};

const calculateTotalKHR = computed(() => {
  return productsList.value.reduce((acc, p) => acc + (p.subTotal || 0), 0);
});

const calculateExchangeRate = computed(() => {
  if (!form.total_usd || form.total_usd <= 0 || form.grand_total <= 0) return 0;
  return (form.grand_total / form.total_usd).toFixed(2);
});

const removeProduct = (id) => {
  productsList.value = productsList.value.filter(p => p.id !== id);
};

const submitInvoice = async () => {
  if (productsList.value.length === 0) {
    alert("Please add at least one product.");
    return;
  }

  form.products = productsList.value.map((prod) => ({
    id: prod.id,
    quantity: prod.qty,
    price: prod.unitPrice,
    remark: prod.remark ?? "",
    include_catalog: Boolean(prod.include_catalog),
    pdf_url: prod.pdf_url ?? null,
  }));

  form.payment_schedules = form.payment_schedules.map((id) => {
    const schedule = props.paymentSchedules.find((ps) => ps.id === id);
    return {
      id,
      amount: schedule?.amount || 0,
    };
  });

  form.grand_total = productsList.value.reduce((acc, p) => acc + p.subTotal, 0);

  if (!form.userModifiedPaidAmount) {
    form.paid_amount = form.grand_total;
  }

  form.total_usd = form.total_usd ||
    (form.exchange_rate > 0 ? (form.grand_total / form.exchange_rate).toFixed(2) : 0);
  form.exchange_rate = form.exchange_rate ||
    (form.total_usd > 0 ? (form.grand_total / form.total_usd).toFixed(2) : 0);

  await form.put(route('invoices.update', props.invoice.id), {
    onSuccess: () => Inertia.visit(route("invoices.list")),
  });
};

const fillReceiptFields = receiptNo => {
  if (!receiptNo) return;
  const receipt = props.receipts.find(r => r.receipt_no === receiptNo);
  if (!receipt) return;

  form.deposit_amount = receipt.paid_amount || 0;
  form.paid_amount = receipt.paid_amount || 0;
  form.installment_paid = receipt.installment_paid || 0;

  if (receipt.customer_id && receipt.customer_id !== form.customer_id) {
    form.customer_id = receipt.customer_id;
    const customer = props.customers.find(c => c.id === receipt.customer_id);
    if (customer) {
      form.address = customer.address || "";
      form.phone = customer.phone || customer.phone_number || "";
    }
  }
};

watch(() => form.receipt_no, fillReceiptFields);

const cancel = () => Inertia.visit(route("invoices.list"));
</script>

