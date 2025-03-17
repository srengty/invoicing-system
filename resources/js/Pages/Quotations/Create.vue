<template>
    <Head :title="isEditing ? 'Edit Quotation' : 'Create Quotation'" />
    <GuestLayout>
        <Toast position="top-center" group="tc" />
        <Toast position="top-right" group="tr" />

        <!-- Use the PrimeVue Form wrapper (with @submit.prevent) -->
        <form @submit.prevent="submit" class="text-sm">
            <!-- Quotation Info -->
            <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-2">
                        <label for="quotation_no">Quotation No:</label>
                        <InputText
                            disabled
                            id="quotation_no"
                            v-model="form.quotation_no"
                            placeholder="Auto-generated"
                            class="w-full md:w-60"
                            size="small"
                        />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="quotation_date" class="required"
                            >Date:</label
                        >
                        <DatePicker
                            disabled
                            v-model="form.quotation_date"
                            :model-value="formatDate(form.quotation_date)"
                            showIcon
                            fluid
                            iconDisplay="input"
                            inputId="quotation_date"
                            placeholder="Select"
                            class="w-full md:w-60"
                            @update:model-value="updateDate"
                            size="small"
                        />
                    </div>
                </div>
            </div>

            <!-- Customer & Product Selection -->
            <div class="pl-8 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div
                    class="flex flex-row gap-4 items-end md:grid-cols-4 w-full"
                >
                    <div class="flex flex-col gap-2">
                        <label for="customer_id" class="required"
                            >Customer/Organization</label
                        >
                        <Select
                            :filter="true"
                            v-model="form.customer_id"
                            :options="formattedCustomers"
                            optionLabel="name"
                            optionValue="id"
                            id="customer_id"
                            placeholder="Select a customer"
                            class="w-full md:w-60"
                            @change="updateCustomerDetails"
                        />
                    </div>
                    <div class="w-10">
                        <Button
                            icon="pi pi-plus"
                            title="add customer"
                            label="Add Customer"
                            raised
                            @click="isCreateCustomerVisible = true"
                            class="w-36 start"
                            size="small"
                        />
                    </div>
                </div>

                <!-- <div class="grid grid-cols-1 md:grid-cols-5"> -->
                <div class="flex flex-col gap-2 w-full md:ml-32">
                    <label for="address" class="required">Address:</label>
                    <IconField class="w-full md:w-60">
                        <InputText
                            id="address"
                            v-model="form.address"
                            placeholder="Input"
                            class="w-full md:w-60"
                            readonly
                            size="small"
                        />
                        <InputIcon
                            v-if="form.address"
                            class="pi pi-map-marker"
                        />
                    </IconField>
                </div>

                <div class="flex flex-col gap-2 w-full md:ml-28">
                    <label for="phone_number" class="required"
                        >Phone number:</label
                    >
                    <IconField class="w-full md:w-60">
                        <InputText
                            id="phone_number"
                            v-model="form.phone_number"
                            placeholder="Input"
                            class="w-full md:w-60"
                            readonly
                            size="small"
                        />
                        <InputIcon
                            v-if="form.phone_number"
                            class="pi pi-phone"
                        />
                    </IconField>
                </div>
                <!-- </div> -->
            </div>
            <div class="pl-8 pt-10 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-2 w-full">
                    <div class="flex gap-32 items-center">
                        <div class="w-10">
                            <Button
                                icon="pi pi-plus"
                                label="Add Item"
                                raised
                                @click="isAddItemDialogVisible = true"
                                class="w-36"
                                size="small"
                            />
                        </div>
                        <div class="flex flex-row gap-2 w-full md:w-80">
                            <label for="p_name">English/Khmer</label>
                            <ToggleSwitch
                                v-model="isKhmer"
                                @change="toggleLanguage"
                            />
                        </div>
                        <div class="w-80"></div>
                    </div>
                </div>
            </div>

            <!-- Selected Products Table -->
            <div class="pl-6 pt-5">
                <DataTable
                    :value="selectedProductsData"
                    paginator
                    :rows="5"
                    striped
                >
                    <Column header="No.">
                        <template #body="slotProps">
                            {{ slotProps.index + 1 }}
                        </template>
                    </Column>
                    <Column field="name" header="Name">
                        <template #body="slotProps">
                            <span class="text-base">
                                {{
                                    isKhmer
                                        ? slotProps.data.name_kh
                                        : slotProps.data.name
                                }}
                            </span>
                            <br />
                            <span class="text-base">
                                {{
                                    isKhmer
                                        ? slotProps.data.desc_kh
                                        : slotProps.data.desc
                                }}
                            </span>
                            <br />
                            <span class="text-sm font-bold">
                                {{ getRemarkSnippet(slotProps.data.remark) }}
                            </span>
                        </template>
                    </Column>
                    <Column field="quantity" header="Qty">
                        <template #body="slotProps">
                            <InputText
                                v-model="slotProps.data.quantity"
                                @input="updateProductSubtotal(slotProps.data)"
                                class="w-5/4"
                                size="small"
                            />
                        </template>
                    </Column>
                    <Column field="unit" header="Unit" />
                    <Column field="price" header="Unit Price">
                        <template #body="slotProps">
                            <InputText
                                v-model="slotProps.data.price"
                                @input="updateProductSubtotal(slotProps.data)"
                                :min="0"
                                @keydown="preventMinus"
                                :minFractionDigits="2"
                                :maxFractionDigits="2"
                                placeholder="Enter in USD"
                                class="w-5/6"
                                size="small"
                            />
                        </template>
                    </Column>
                    <Column field="subTotal" header="Subtotal">
                        <template #body="slotProps">
                            <span>
                                {{
                                    slotProps.data.subTotal
                                        ? slotProps.data.subTotal.toFixed(2)
                                        : "0.00"
                                }}
                                KHR
                            </span>
                        </template>
                    </Column>

                    <Column header="Actions">
                        <template #body="slotProps">
                            <div class="flex gap-2 items-center">
                                <Button
                                    icon="pi pi-trash"
                                    class="p-button-danger w-[10px] custom-button"
                                    title="remove"
                                    @click="removeProduct(slotProps.data.id)"
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
                    <Column header="Print Catalog">
                        <template #body="slotProps">
                            <Checkbox
                                v-model="slotProps.data.includeCatalog"
                                :binary="true"
                                @change="updateIncludeCatalog(slotProps.data)"
                            />
                        </template>
                    </Column>
                </DataTable>

                <!-- Totals Summary -->
                <div class="pl-2 pr-6">
                    <div class="total-container mt-4 flex justify-between">
                        <p class="font-bold">Total KHR</p>
                        <p class="font-bold">
                            ៛{{ formatCurrency(calculateTotal.toFixed(2)) }}
                        </p>
                    </div>
                    <div class="total-container mt-4 flex justify-between">
                        <p class="font-bold">Total USD</p>
                        <p class="font-bold">
                            <!-- The v-model binding automatically updates calculateTotalUSD -->
                            <input
                                type="number"
                                v-model.number="calculateTotalUSD"
                                placeholder="Enter USD"
                                :minFractionDigits="2"
                                :maxFractionDigits="2"
                                class="w-1/7 h-9 text-sm"
                            />
                            <!-- <InputNumber
                                v-model.number="calculateTotalUSD"
                                class="text-right"
                                :minFractionDigits="2"
                                :maxFractionDigits="2"
                                size="small"
                                placeholder="Enter USD"
                            /> -->
                        </p>
                    </div>
                    <div class="grand-total-container flex justify-between">
                        <p class="font-bold">Exchange rate</p>
                        <p class="font-bold text-">
                            {{ calculateExchangeRate }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="pl-8 pt-10 grid grid-cols-1 md:grid-cols-1 gap-4">
                <label for="terms" class="font-bold"
                    >Terms &amp; Conditions:</label
                >
                <InputText
                    id="terms"
                    v-model="form.terms"
                    rows="5"
                    cols="30"
                    class="w-full md:w-2/3"
                    placeholder="Enter or edit your Terms & Conditions here"
                    size="small"
                />
            </div>

            <!-- Form Buttons -->
            <div class="buttons mt-4 mr-4 mb-10 flex justify-end">
                <Button
                    :label="isEditing ? 'Update' : 'Create'"
                    icon="pi pi-check"
                    type="submit"
                    class="p-button-raised"
                    size="small"
                />
                <Button
                    v-ripple
                    icon="pi pi-times"
                    label="Cancel"
                    class="p-button-raised p-button-secondary ml-2"
                    @click="cancelOperation"
                    size="small"
                />
            </div>
        </form>
    </GuestLayout>
    <Dialog
        v-model:visible="isCreateCustomerVisible"
        modal
        header="Add Customer"
        class="w-2/3"
    >
        <template #header>
            <div class="flex items-center gap-2">
                <img src="/User.png" alt="Item Customer" class="h-8 w-8 ml-2" />
                <span class="text-xl font-semibold bor">Create Customers</span>
            </div>
        </template>
        <Customers
            :customerCategories="customerCategories"
            redirect_route="customers.index"
            :mode="'create'"
        />
    </Dialog>
    <!-- <Dialog
        v-model:visible="isCreateItemVisible"
        modal
        header="Add Item"
        class="w-1/2"
    >
        <Customers redirect_route="quotations.create"></Customers>
    </Dialog> -->
    <!-- Add Item Dialog -->
    <Dialog
        v-model:visible="isAddItemDialogVisible"
        modal
        header="Add Item (Popup)"
        :style="{ width: '400px' }"
        class="text-sm"
    >
        <div class="p-fluid grid gap-4 text-sm">
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
                />
            </div>

            <!-- Item Category (Auto-complete, Read-Only) -->
            <div class="field">
                <label for="item-category" class="required"
                    >Item Category</label
                >
                <InputText
                    :value="getCategoryName(selectedProduct.category_id)"
                    class="w-full text-sm"
                    size="small"
                    readonly
                />
            </div>

            <!-- Unit Price (Auto-complete, Editable) -->
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

            <!-- Account Code (Auto-complete, Read-Only) -->
            <div class="field">
                <label for="account-code" class="required">Account Code</label>
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
                    :href="`/pdfs/${selectedProduct.pdf_url.split('/').pop()}`"
                    target="_blank"
                    class="text-blue-500 hover:text-blue-700 transition duration-200"
                >
                    📄 View Catelog
                </a>
            </div>
            <p v-else class="text-center text-gray-400">No PDF available</p>

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
                raised
                @click="closeAddItemDialog()"
            />
            <Button
                :label="editingProduct ? 'Update Item' : 'Add Item'"
                icon="pi pi-check"
                raised
                @click="addItemToTable"
            />
        </template>
    </Dialog>
</template>

<script setup>
import { ref, computed, watch, onMounted } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import { useForm } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import MultiSelect from "primevue/multiselect";
import InputNumber from "primevue/inputnumber";
import DatePicker from "primevue/datepicker";
import InputText from "primevue/inputtext";
import IconField from "primevue/iconfield";
import InputIcon from "primevue/inputicon";
import Button from "primevue/button";
import Dropdown from "primevue/dropdown";
import { Dialog, ToggleSwitch, Select, AutoComplete, Checkbox } from "primevue";
import { Form } from "@primevue/forms";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Toast from "primevue/toast";
import { useToast } from "primevue/usetoast";
import Customers from "@/Components/Customers.vue";
import { Inertia } from "@inertiajs/inertia";
import { router } from "@inertiajs/vue3";
import { usePage } from "@inertiajs/vue3";

// Toast for notifications
const toast = useToast();
const showToast = (
    type = "success",
    title = "Success",
    message = "Operation completed",
    duration = 3000
) => {
    toast.add({
        severity: type,
        detail: message,
        life: duration,
        group: "tr",
    });
};
const preventMinus = (event) => {
    if (event.key === "-") {
        event.preventDefault();
    }
};
const pageProps = usePage().props;
const quotation = ref(pageProps.quotation || null);
// const isEditing = ref(!!quotation.value);
const isEditing = computed(() => !!props.quotation);

onMounted(() => {
    if (props.quotation) {
        console.log("🛠 Debug: Quotation received", props.quotation);
        const newProps = JSON.parse(props.quotation);
        form.quotation_no = newProps.quotation_no || "";
        form.quotation_date = newProps.quotation_date || "";
        form.customer_id = String(newProps.customer_id) || "";
        form.address = newProps.address || "";
        form.phone_number = newProps.phone_number || "";
        form.total = newProps.total || 0;
        form.tax = newProps.tax || 0;
        form.grand_total = newProps.grand_total || 0;

        // Populate selectedProductsData with existing products
        if (newProps.products && Array.isArray(newProps.products)) {
            selectedProductsData.value = newProps.products.map((product) => ({
                ...product,
                quantity: product.quantity || 1,
                subTotal: (product.quantity || 1) * Number(product.price || 0),
                remark: product.remark || "",
                includeCatalog: product.includeCatalog ?? false,
            }));
            console.log(selectedProductsData.value);
        } else {
            selectedProductsData.value = [];
        }

        updateCustomerDetails();
    }
});

const props = defineProps({
    customers: Array,
    products: Array,
    customerCategories: Array,
    productCategories: Array,
    quotation: Object, // Accept quotation data when editing
});

// Define the Inertia form
const form = useForm({
    id: props.quotation?.id || null,
    quotation_no: props.quotation?.quotation_no || "",
    quotation_date: props.quotation?.quotation_date || "",
    customer_id: props.quotation?.customer_id || "",
    address: props.quotation?.address || "",
    phone_number: props.quotation?.phone_number || "",
    total: props.quotation?.total || 0,
    products: props.quotation?.products || [],
    terms: props.quotation?.terms || "",
});

const updateCustomerDetails = () => {
    console.log("Customer id: ", form.customer_id);
    const selectedCustomer = formattedCustomers.value.find(
        (customer) => customer.id    == form.customer_id
    );
    console.log("selected customer: ", selectedCustomer);

    if (selectedCustomer) {
        form.address = selectedCustomer.address || "";
        form.phone_number = selectedCustomer.phone_number || "";
    }
};

watch(
    () => form.customer_id,
    () => {
        updateCustomerDetails();
    }
);

const status = ref("");
const isApproved = ref(false);
const today = new Date();
const isKhmer = ref(false);
const statusOptions = ref(["Pending", "Approved"]);
const isAddItemDialogVisible = ref(false);
const selectedItemIds = ref([]);
const selectedProduct = ref({});
const selectedQuantity = ref(1);
const additionalRemark = ref("");
const selectedAccountCode = ref("");
const selectedItemId = ref(null);
const filteredProducts = ref([]);
const selectedItem = ref(null);
const customerCategories = ref(props.customerCategories);

const updateSelectedProductDetails = () => {
    if (selectedItem.value) {
        const product = props.products.find(
            (p) => p.id === selectedItem.value.id
        );
        if (product) {
            selectedProduct.value = {
                ...product,
                quantity: 1,
                subTotal: Number(product.price),
                category_id: product.category_id ?? null,
            };
        }
    } else {
        selectedProduct.value = {};
    }
};
watch(selectedProduct, (newVal) => {
    console.log("Updated selected product:", newVal);
});

const searchProducts = (event) => {
    filteredProducts.value = props.products.filter(
        (product) =>
            product.status === "approved" &&
            product.name.toLowerCase().includes(event.query.toLowerCase())
    );
};

const validateForm = () => {
    if (!form.address) {
        showToast(
            "warn",
            "Validation Error",
            "Customer address is required!",
            4000
        );
        return false;
    }
    if (!form.phone_number) {
        showToast(
            "warn",
            "Validation Error",
            "Customer phone number is required!",
            4000
        );
        return false;
    }
    if (!form.customer_id) {
        showToast(
            "warn",
            "Validation Error",
            "Please select a customer!",
            4000
        );
        return false;
    }
    if (selectedProductsData.value.length === 0) {
        showToast(
            "warn",
            "Validation Error",
            "Please add at least one product!",
            4000
        );
        return false;
    }
    return true;
};

const getCategoryName = (categoryId) => {
    if (!categoryId) return "Unknown";
    const category = props.productCategories.find(
        (cat) => cat.id === categoryId
    );
    return category
        ? category.category_name_english || category.category_name_khmer
        : "Unknown";
};

const addItemToTable = () => {
    if (!selectedProduct.value.name) {
        showToast("error", "Error", "Please select an item.", 3000);
        return;
    }

    if (!selectedProduct.value.quantity || selectedProduct.value.quantity < 1) {
        selectedProduct.value.quantity = 1;
    }

    const newItem = {
        ...selectedProduct.value,
        subTotal:
            Number(selectedProduct.value.price) *
            selectedProduct.value.quantity,
        remark: selectedProduct.value.remark || "",
        includeCatalog: false,
        isNew: true, // ✅ Mark this item as new
    };

    // Check if the same product name already exists
    const duplicateItem = selectedProductsData.value.find(
        (prod) => prod.name === newItem.name
    );

    if (duplicateItem && !editingProduct.value) {
        showToast(
            "error",
            "Error",
            "This item is already in the quotation.",
            3000
        );
        return;
    }

    if (editingProduct.value) {
        const existingIndex = selectedProductsData.value.findIndex(
            (prod) => prod.id === editingProduct.value.id
        );
        if (existingIndex !== -1) {
            selectedProductsData.value[existingIndex] = newItem;
        }
        editingProduct.value = null;
        showToast(
            "success",
            "Item Updated",
            "The item has been updated successfully.",
            3000
        );
    } else {
        selectedProductsData.value.push(newItem);
        showToast(
            "success",
            "Item Added",
            "The item has been added to the table.",
            3000
        );
    }

    closeAddItemDialog();
};

// Close Add Item dialog
const closeAddItemDialog = () => {
    isAddItemDialogVisible.value = false;
    resetAddItemDialog();
};

const resetAddItemDialog = () => {
    selectedItemIds.value = [];
    selectedProduct.value = {};
    selectedQuantity.value = 1;
    additionalRemark.value = "";
    selectedAccountCode.value = "";
    editingProduct.value = null;
};

// toggle language
const toggleLanguage = () => {
    locale.value = isKhmer.value ? "name_kh" : "name";
};

const formatDate = (date) => {
    if (!date) return "";
    return new Date(date).toLocaleDateString("en-US", {
        month: "2-digit",
        day: "2-digit",
        year: "numeric",
    });
};
watch(status, (newStatus) => {
    if (newStatus === "Approved") {
        form.value.quotation_date = today;
        isApproved.value = true;
    } else {
        isApproved.value = false;
    }
});

const updateDate = (selectedDate) => {
    form.value.quotation_date = selectedDate;
};

const isCreateCustomerVisible = ref(false);
const selectCustomer = () => {
    isCreateCustomerVisible.value = false;
    form.customer_id = props.customers[props.customers.length - 1].id;
};

const selectedProductIds = ref([]);
const selectedProductsData = ref([]);

watch(selectedProductsData, (newProducts) => {
    newProducts.forEach((product) => {
        if (typeof product.includeCatalog !== "boolean") {
            product.includeCatalog = false; // ✅ Ensures boolean type
        }
    });
});

const updateIncludeCatalog = (product) => {
    product.includeCatalog = !product.includeCatalog; // Toggle the state
    console.log("Updated includeCatalog:", product.includeCatalog);
};

watch(selectedProductIds, (newIds) => {
    newIds.forEach((id) => {
        if (!selectedProductsData.value.find((prod) => prod.id === id)) {
            const prod = props.products.find((p) => p.id === id);
            if (prod) {
                selectedProductsData.value.push({
                    id: product.id,
                    quantity: 1,
                    price: product.price,
                    remark: "",
                    includeCatalog: product.includeCatalog ?? 0, // ✅ Default state is unchecked (0)
                });
            }
            console.log(prod);
        }
    });
    // Remove products that have been deselected
    selectedProductsData.value = selectedProductsData.value.filter((prod) =>
        newIds.includes(prod.id)
    );
});

const formattedCustomers = computed(() => {
    return props.customers.map((customer) => ({
        id: customer.id.toString(), // Ensure ID is a string for proper binding
        name: customer.name, // Display name in dropdown
        address: customer.address,
        phone_number: customer.phone_number,
    }));
});

watch(
    () => form.customer_id,
    (newVal) => {
        console.log("Selected Customer ID:", newVal);
    }
);
const selectedCustomer = computed(() => {
    return (
        formattedCustomers.value.find((c) => c.id === form.customer_id) || null
    );
});

const selectedCustomerName = computed(() => {
    const customer = formattedCustomers.value.find(
        (c) => c.id == form.customer_id
    );
    return customer ? customer.name : "";
});

watch(
    () => form.customer_id,
    (newCustomerId) => {
        if (newCustomerId) {
            const customer = props.customers.find((c) => c.id == newCustomerId);
            if (customer) {
                form.address = customer.address || "";
                form.phone_number = customer.phone_number || "";
            }
        }
    }
);

const updateProductSubtotal = (row) => {
    row.quantity = parseInt(row.quantity) || 1;
    row.subTotal = Number(row.price) * row.quantity || 0;
    form.total = selectedProductsData.value.reduce(
        (sum, prod) => sum + prod.subTotal,
        0
    );
    form.grand_total = calculateGrandTotal.value;

    const updatedProduct = selectedProductsData.value.find(
        (prod) => prod.id === row.id
    );
    if (updatedProduct) {
        updatedProduct.price = row.price;
    }
};

const updateTax = () => {
    form.grand_total = calculateGrandTotal.value;
};

const calculateTotalUSD = ref(null);
const calculateExchangeRate = computed(() => {
    if (!calculateTotalUSD.value) {
        return "";
    }
    const exchangeRate = calculateTotal.value / calculateTotalUSD.value;
    return exchangeRate.toFixed(2);
});
const calculateTotal = computed(() => {
    return selectedProductsData.value.reduce(
        (sum, prod) => sum + prod.subTotal,
        0
    );
});
const handleUSDInput = (value) => {
    calculateTotalUSD.value = value;
};

const exchangeRate = ref(4100);
const calculateTotalKHR = computed(() => {
    if (!calculateTotal.value || !calculateExchangeRate.value) return "0.00";
    return (calculateTotal.value * calculateExchangeRate.value).toFixed(2);
});

const formatCurrency = (value) => {
    if (!value) return "0.00";
    return new Intl.NumberFormat("en-US", { minimumFractionDigits: 2 }).format(
        value
    );
};

const calculateGrandTotal = computed(() => {
    return (
        calculateTotal.value +
        Number((form.tax * calculateTotal.value) / 100 || 0)
    );
});

const removeProduct = (id) => {
    selectedProductsData.value = selectedProductsData.value.filter(
        (prod) => prod.id !== id
    );
    showToast("info", "Item Removed", "Product removed from quotation.", 2500);
};

const editingProduct = ref(null);
const editProduct = (productId) => {
    const productToEdit = selectedProductsData.value.find(
        (prod) => prod.id === productId
    );

    if (productToEdit) {
        selectedProduct.value = { ...productToEdit };
        isAddItemDialogVisible.value = true;
        editingProduct.value = productToEdit;
        const selectedCustomer = props.customers.find(
            (customer) => customer.id === productToEdit.customer_id
        );
        if (selectedCustomer) {
            form.customer_id = selectedCustomer.id;
            form.address = selectedCustomer.address || "";
            form.phone_number = selectedCustomer.phone_number || "";
        }

        isAddItemDialogVisible.value = true;
    }
};

const submit = async (event) => {
    if (event && typeof event.preventDefault === "function") {
        event.preventDefault();
    }

    if (!validateForm()) return;

    // Prepare product data before submitting
    form.products = selectedProductsData.value.map((prod) => ({
        id: prod.id,
        quantity: prod.quantity ?? 1,
        price: prod.price ?? 0,
        remark: prod.remark ?? "",
        includeCatalog: prod.includeCatalog ? 1 : 0, // ✅ Convert to 1 or 0
    }));

    form.total = calculateTotal.value;
    form.grand_total = calculateGrandTotal.value;

    console.log("Submitting Products:", JSON.stringify(form.products, null, 2));

    const routePath = form.id
        ? route("quotations.update", { id: form.id })
        : route("quotations.store");

    try {
        await form.post(routePath, {
            onSuccess: () => {
                showToast(
                    "success",
                    form.id ? "Updated" : "Created",
                    `Quotation ${form.id ? "updated" : "created"} successfully!`
                );
                router.get(route("quotations.list"));
            },
            onError: (errors) => {
                console.error("Submission Error:", errors);
                showToast(
                    "error",
                    "Submission Failed",
                    `Could not ${form.id ? "update" : "create"} quotation.`
                );
            },
        });
    } catch (error) {
        console.error("Unexpected Error:", error);
        showToast("error", "Unexpected Error", "Something went wrong.");
    }
};

const cancelOperation = () => {
    toast.add({
        severity: "secondary",
        summary: "Cancelled",
        detail: "Operation was cancelled.",
        life: 3000,
        group: "tr",
    });
    setTimeout(() => {
        router.get(route("quotations.list"), {}, { replace: true });
    }, "");
};

const isCreateItemVisible = ref(false);
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
const getRemarkSnippet = (remark) => {
    if (!remark) return "";
    return remark.length > 15 ? remark.slice(0, 15) + "..." : remark;
};
</script>
<style>
.custom-button {
    padding: 7px 7px !important; /* Smaller padding */
    font-size: 12px !important; /* Smaller icon size */
    min-width: 30px !important; /* Reduce button width */
}
</style>
