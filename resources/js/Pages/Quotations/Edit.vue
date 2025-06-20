<template>
    <Head title="Edit Quotation" />
    <GuestLayout>
        <NavbarLayout
            title="Edit Quotation"
            class="fixed top-0 z-50 w-5/6 pr-3"
        />
        <!-- PrimeVue Breadcrumb -->
        <div class="py-3 mt-16">
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
        <form @submit.prevent="submit">
            <input type="hidden" name="_token" :value="page.props.csrf_token" />
            <div
                class="px-4 grid grid-cols-1 md:grid-cols-2 gap-4 w-full text-xs"
            >
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
                            placeholder="Auto-generated"
                            class="w-full md:w-60"
                            @update:model-value="updateDate"
                            size="small"
                        />
                    </div>
                </div>
            </div>
            <div class="pl-8 grid grid-cols-1 md:grid-cols-4 gap-4 text-xs">
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
                            class="w-36 h-8 start"
                            size="small"
                        />
                    </div>
                </div>

                <div class="flex flex-col gap-2 w-full md:ml-32">
                    <label for="address" class="required">Address:</label>
                    <IconField class="w-full md:w-60">
                        <InputText
                            id="address"
                            v-model="form.address"
                            placeholder="Auto-generated"
                            class="w-full md:w-60 md:h-9"
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
                            placeholder="Auto-generated"
                            class="w-full md:w-60 md:h-9"
                            readonly
                            size="small"
                        />
                        <InputIcon
                            v-if="form.phone_number"
                            class="pi pi-phone"
                        />
                    </IconField>
                </div>
            </div>
            <!-- AddItem & Language -->
            <div
                class="pl-8 pt-10 grid grid-cols-1 md:grid-cols-4 gap-4 text-xs"
            >
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-2 w-full">
                    <div class="flex gap-32 items-center">
                        <div class="w-10">
                            <Button
                                icon="pi pi-plus"
                                label="Add Item"
                                raised
                                @click="isAddItemDialogVisible = true"
                                class="w-36 h-8"
                                size="small"
                            />
                        </div>
                        <div
                            class="flex flex-row gap-2 w-full md:w-80 items-center"
                        >
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
                    class="p-datatable-striped"
                    paginator
                    :rows="5"
                    :rowsPerPageOptions="[5, 10, 20, 50]"
                    striped
                >
                    <Column header="No." style="width: 5%; font-size: 12px">
                        <template #body="slotProps">
                            {{ slotProps.index + 1 }}
                        </template>
                    </Column>
                    <Column
                        field="name"
                        header="Name"
                        style="width: 10%; font-size: 12px"
                    >
                        <template #body="slotProps">
                            <span>
                                {{
                                    isKhmer
                                        ? slotProps.data.name_kh
                                        : slotProps.data.name
                                }}
                            </span>
                            <br />
                            <span>
                                {{
                                    isKhmer
                                        ? slotProps.data.desc_kh
                                        : slotProps.data.desc
                                }}
                            </span>
                            <br />
                            <span class="font-bold">
                                {{ getRemarkSnippet(slotProps.data.remark) }}
                            </span>
                        </template>
                    </Column>
                    <Column
                        field="quantity"
                        header="Qty"
                        style="width: 10%; font-size: 12px"
                    >
                        <template #body="slotProps">
                            <InputText
                                v-model="slotProps.data.quantity"
                                @input="updateProductSubtotal(slotProps.data)"
                                :step="1"
                                :min="1"
                                :useGrouping="false"
                                :maxFractionDigits="0"
                                @keydown="preventMinus"
                                class="w-5/4"
                                size="small"
                            />
                        </template>
                    </Column>
                    <Column
                        field="unit"
                        header="Unit"
                        style="width: 10%; font-size: 12px"
                    ></Column>
                    <Column
                        field="price"
                        header="Unit Price"
                        style="width: 10%; font-size: 12px"
                    >
                        <template #body="slotProps">
                            <InputNumber
                                v-model="slotProps.data.price"
                                @input="updateProductSubtotal(slotProps.data)"
                                :minFractionDigits="2"
                                :maxFractionDigits="2"
                                :min="0"
                                @keydown="preventMinus"
                                placeholder="Enter amount"
                                class="w-5/4"
                                size="small"
                            />
                        </template>
                    </Column>
                    <Column
                        field="subTotal"
                        header="Subtotal"
                        style="width: 10%; font-size: 12px"
                    >
                        <template #body="slotProps">
                            <span>
                                {{ formatCurrency(slotProps.data.subTotal) }}
                                <span class="text-xs text-gray-500 ml-1"
                                    >KHR</span
                                >
                            </span>
                        </template>
                    </Column>
                    <Column
                        header="Actions"
                        style="width: 10%; font-size: 12px"
                    >
                        <template #body="slotProps">
                            <div class="flex gap-2 items-center">
                                <Button
                                    icon="pi pi-trash"
                                    class="p-button-danger w-[10px] custom-button"
                                    title="remove"
                                    @click="removeProduct(slotProps.data.id)"
                                    size="small"
                                    style="width: 30px; height: 30px"
                                    raised
                                />
                                <Button
                                    icon="pi pi-pencil"
                                    severity="info"
                                    title="edit"
                                    @click="editProduct(slotProps.data.id)"
                                    size="small"
                                    class="custom-button"
                                    style="width: 30px; height: 30px"
                                    raised
                                />
                                <Button
                                    icon="pi pi-print"
                                    class="custom-button"
                                    title="Print Catalog"
                                    :disabled="!slotProps.data.pdf_url"
                                    @click="printCatalog(slotProps.data)"
                                    size="small"
                                    style="width: 30px; height: 30px"
                                    raised
                                />
                            </div>
                        </template>
                    </Column>
                    <Column
                        header="Catalog"
                        style="width: 10%; font-size: 12px"
                    >
                        <template #body="slotProps">
                            <Checkbox
                                v-model="slotProps.data.include_catalog"
                                :binary="true"
                                @change="
                                    checkCatalogAvailability(slotProps.data)
                                "
                            />
                            <span class="ml-2">
                                {{
                                    slotProps.data.include_catalog
                                        ? "Included"
                                        : "Include"
                                }}
                                {{ slotProps.data.include_catalog ? "" : "" }}
                            </span>
                        </template>
                    </Column>
                </DataTable>

                <!-- Totals Summary -->
                <div class="pl-2 pr-6">
                    <div
                        class="total-container mt-4 flex justify-between text-sm"
                    >
                        <p class="font-bold">Total KHR</p>
                        <p class="font-bold text-md">
                            ៛{{ formatCurrency(calculateTotalKHR) }}
                        </p>
                    </div>
                    <div
                        class="total-container mt-4 flex justify-between text-sm"
                    >
                        <p class="font-bold">Total USD</p>
                        <p class="font-bold">
                            <input
                                type="number"
                                v-model.number="form.total_usd"
                                placeholder="Enter USD"
                                step="0.01"
                                class="h-9 text-sm border border-gray-300 rounded px-2 text-right w-40"
                            />
                        </p>
                    </div>
                    <div
                        class="grand-total-container flex justify-between text-sm"
                    >
                        <p class="font-bold">Exchange rate</p>
                        <p class="font-bold text-">
                            {{ calculateExchangeRate }}
                        </p>
                    </div>
                </div>
            </div>
            <!-- Terms & Conditions -->
            <div class="pl-8 pt-10 grid grid-cols-1 md:grid-cols-1 gap-4">
                <label for="terms" class="font-bold"
                    >Terms &amp; Conditions:</label
                >
                <InputText
                    id="terms"
                    v-model="form.terms"
                    rows="5"
                    cols="20"
                    class="w-full md:w-2/5"
                    placeholder="Enter or edit your Terms & Conditions here"
                    size="small"
                />
            </div>
            <!-- Form Buttons -->
            <div class="buttons mt-4 mr-4 mb-10 flex justify-end gap-2">
                <Button
                    label="Update"
                    type="submit"
                    class="w-full md:w-28"
                    severity=""
                    size="small"
                    icon="pi pi-check"
                    raised
                />
                <Button
                    v-ripple
                    class="w-full md:w-28"
                    label="Cancel"
                    size="small"
                    icon="pi pi-times"
                    @click="cancelOperation"
                    severity="success"
                    variant="outlined"
                    raised
                />
            </div>
        </form>
    </GuestLayout>

    <!-- Add Customer Dialog -->
    <Dialog
        v-model:visible="isCreateCustomerVisible"
        modal
        header="Add Customer"
        class="w-2/3"
        :draggable="false"
        :resizable="false"
        :position="'center'"
        :closeOnEscape="false"
    >
        <template #header>
            <div class="flex items-center gap-2">
                <img src="/User.png" alt="Item Customer" class="h-8 w-8 ml-2" />
                <span class="text-xl font-semibold bor">Create Customers</span>
            </div>
        </template>
        <Customers
            :customerCategories="customerCategories"
            redirect_route="quotations.edit"
            :mode="'create'"
        />
    </Dialog>

    <!-- Add Item Dialog -->
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
                <label for="division" class="required">Division</label> <br />
                <Dropdown
                    v-model="selectedDivision"
                    :options="divisionOptions"
                    optionLabel="displayName"
                    optionValue="id"
                    placeholder="Select a Division"
                    :filter="true"
                    filterPlaceholder="Search divisions..."
                    class="w-full h-8 text-sm"
                    size="small"
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
                    class="w-full h-8 text-sm"
                    @complete="searchProducts"
                    @change="updateSelectedProductDetails"
                    :input-props="{ id: 'item' }"
                    size="small"
                />
            </div>
            <!-- Item Category (Auto-complete, Read-Only) -->
            <div class="field">
                <label for="item-category" class="required"
                    >Item Category</label
                >
                <InputText
                    :value="getCategoryName(selectedProduct.category_id)"
                    class="w-full h-8 text-sm"
                    size="small"
                    placeholder="Display Category"
                    readonly
                />
            </div>
            <!-- Unit Price (Auto-complete, Editable) -->
            <div class="field">
                <label for="unit-price" class="required">Unit Price</label>
                <InputNumber
                    v-model="selectedProduct.price"
                    :min="0"
                    :minFractionDigits="2"
                    :maxFractionDigits="2"
                    @keydown="preventMinus"
                    size="small"
                    placeholder="Display Unit Price"
                    class="w-full h-8 text-sm"
                />
            </div>
            <!-- Account Code (Auto-complete, Read-Only) -->
            <div class="field">
                <label for="account-code" class="required">Account Code</label>
                <InputText
                    v-model="selectedProduct.acc_code"
                    class="w-full h-8 text-sm"
                    size="small"
                    placeholder="Display Account Code"
                    readonly
                />
            </div>
            <!-- Quantity -->
            <div class="field">
                <label for="quantity" class="required">Quantity</label>
                <InputNumber
                    v-model="selectedProduct.quantity"
                    :step="1"
                    :min="1"
                    :useGrouping="false"
                    :maxFractionDigits="0"
                    placeholder="Display Quantity"
                    @input="
                        selectedProduct.quantity = Math.floor(
                            selectedProduct.quantity || 1
                        )
                    "
                    class="w-full h-8 text-sm"
                    size="small"
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
                    class="w-full h-8 text-sm"
                    size="small"
                    placeholder="Enter Remark"
                />
            </div>
        </div>
        <!-- Dialog Footer -->
        <template #footer>
            <Button
                label="Cancel"
                icon="pi pi-times"
                class="p-button-text"
                size="small"
                raised
                @click="closeAddItemDialog()"
            />
            <Button
                :label="editingProduct ? 'Update Item' : 'Add Item'"
                icon="pi pi-check"
                size="small"
                raised
                @click="addItemToTable"
            />
        </template>
    </Dialog>
</template>

<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import Customers from "@/Components/Customers.vue";
import NavbarLayout from "@/Layouts/NavbarLayout.vue";
import { getDepartment } from "../../data";
import { ref, computed, watch, onMounted } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import { useForm } from "@inertiajs/vue3";
import { Inertia } from "@inertiajs/inertia";
import { router } from "@inertiajs/vue3";
import { usePage } from "@inertiajs/vue3";
import { Form } from "@primevue/forms";
import { useToast } from "primevue/usetoast";
import {
    Dialog,
    ToggleSwitch,
    Select,
    AutoComplete,
    Checkbox,
    MultiSelect,
    InputNumber,
    DatePicker,
    InputText,
    IconField,
    InputIcon,
    Button,
    Dropdown,
    DataTable,
    Column,
    Toast,
    Breadcrumb,
} from "primevue";

const props = defineProps({
    quotation: Object,
    customers: Array,
    products: Array,
    customerCategories: Array,
    productCategories: Array,
    divisions: Array,
});

const toast = useToast();
const selectedProductIds = ref([]);

// The Breadcrumb Quotations
const page = usePage();
const items = computed(() => [
    {
        label: "",
        to: "/dashboard",
        icon: "pi pi-home",
    },
    {
        label: "Quotations",
        to: route("quotations.list"),
    },
    {
        label: `Edit Quotation`,
        to: route("quotations.edit", { id: props.quotation.id }),
    },
]);

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
    if (event.key === "-" || event.key === ".") {
        event.preventDefault();
    }
};

// Define the Inertia form
const form = useForm({
    id: props.quotation?.id || null,
    quotation_no: props.quotation?.quotation_no || "",
    quotation_date: props.quotation?.quotation_date || "",
    customer_id: props.quotation?.customer_id || "",
    address: props.quotation?.address || "",
    phone_number: props.quotation?.phone_number || "",
    total: props.quotation?.total || 0,
    total_usd: props.quotation?.total_usd || "",
    exchange_rate: props.quotation?.exchange_rate || 0,
    products: props.quotation?.products || [],
    terms: props.quotation?.terms || "",
    status: props.quotation?.status || "Pending", // Add status field
});

// Initialize selected products
const selectedProductsData = ref(
    props.quotation?.products?.map((product) => ({
        ...product,
        quantity: product.pivot?.quantity || 1,
        price: product.pivot?.price || product.price,
        subTotal:
            (product.pivot?.quantity || 1) *
            (product.pivot?.price || product.price),
        remark: product.pivot?.remark || "",
        include_catalog: Boolean(product.pivot?.include_catalog),
    })) || []
);
// Update customer details when customer_id changes
watch(
    () => form.customer_id,
    (newVal) => {
        const selectedCustomer = props.customers.find((c) => c.id == newVal);
        if (selectedCustomer) {
            form.address = selectedCustomer.address || "";
            form.phone_number = selectedCustomer.phone_number || "";
        }
    }
);

const isKhmer = ref(false);
const isAddItemDialogVisible = ref(false);
const selectedItemIds = ref([]);
const selectedProduct = ref({});
const selectedQuantity = ref(1);
const additionalRemark = ref("");
const selectedAccountCode = ref("");
const selectedItemId = ref(null);
const selectedDivision = ref(null);
const divisionOptions = ref(props.divisions);
const filteredProducts = ref([]);
const selectedItem = ref(null);
const customerCategories = ref(props.customerCategories);
const isCreateCustomerVisible = ref(false);

// Remove this line (the problematic JSON.parse):
// const newProps = JSON.parse(props.quotation || null);

// Replace with direct object access:
onMounted(() => {
    if (props.quotation) {
        form.id = props.quotation.id || null;
        form.quotation_no = props.quotation.quotation_no || "";
        form.quotation_date = props.quotation.quotation_date || "";
        form.customer_id = String(props.quotation.customer_id) || "";
        form.address = props.quotation.address || "";
        form.phone_number = props.quotation.phone_number || "";
        form.total = props.quotation.total || 0;
        form.total_usd = props.quotation.total_usd || 0;
        form.exchange_rate = props.quotation.exchange_rate || 0;

        // Initialize products
        selectedProductsData.value =
            props.quotation.products?.map((product) => ({
                ...product,
                quantity: product.pivot?.quantity ?? 1,
                subTotal:
                    (product.pivot?.quantity ?? 1) * Number(product.price || 0),
                remark: product.remark || "",
                include_catalog: Boolean(product.pivot?.include_catalog),
            })) || [];
    }
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
// Initialize customer details
onMounted(() => {
    if (props.quotation?.customer_id) {
        const selectedCustomer = props.customers.find(
            (c) => c.id == props.quotation.customer_id
        );
        if (selectedCustomer) {
            form.address =
                selectedCustomer.address || props.quotation.address || "";
            form.phone_number =
                selectedCustomer.phone_number ||
                props.quotation.phone_number ||
                "";
        }
    }
});
const updateCustomerDetails = () => {
    const selectedCustomer = formattedCustomers.value.find(
        (customer) => customer.id == form.customer_id
    );

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
                division_id: product.division_id ?? null,
            };
            selectedDivision.value = product.division_id;
            filterProductsByDivision();
        }
    } else {
        selectedProduct.value = {};
        selectedDivision.value = null;
    }
};

const formattedCustomers = computed(() => {
    return props.customers.map((customer) => ({
        id: customer.id.toString(),
        name: customer.name,
        address: customer.address || "",
        phone_number: customer.phone_number || "",
        is_active: customer.is_active !== false,
    }));
});

const getCategoryName = (categoryId) => {
    if (!categoryId) return "";
    const category = props.productCategories.find(
        (cat) => cat.id === categoryId
    );
    return category
        ? category.category_name_english || category.category_name_khmer
        : "";
};

const filterProductsByDivision = () => {
    if (selectedDivision.value) {
        filteredProducts.value = props.products.filter(
            (product) => product.division_id === selectedDivision.value
        );
    } else if (editingProduct.value) {
        selectedDivision.value = editingProduct.value.division_id;
        filteredProducts.value = props.products.filter(
            (product) =>
                product.division_id === editingProduct.value.division_id
        );
    } else {
        filteredProducts.value = [];
    }
};

const searchProducts = (event) => {
    const selectedProductIds = selectedProductsData.value.map(
        (prod) => prod.id
    );

    filteredProducts.value = props.products.filter(
        (product) =>
            product.status === "approved" &&
            !selectedProductIds.includes(product.id) &&
            product.name.toLowerCase().includes(event.query.toLowerCase()) &&
            (selectedDivision.value
                ? product.division_id === selectedDivision.value
                : true)
    );
};

watch(selectedProductsData, () => {
    searchProducts({ query: selectedItem.value?.name || "" });
});

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
        quantity: selectedProduct.value.quantity || 1,
        subTotal:
            Number(selectedProduct.value.price) *
            selectedProduct.value.quantity,
        remark: selectedProduct.value.remark || "",
        include_catalog: selectedProduct.value.include_catalog ?? false,
        division_id: selectedDivision.value,
        isNew: true,
    };

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
    selectedItem.value = null;
    filteredProducts.value = [];
    closeAddItemDialog();
};

const closeAddItemDialog = () => {
    isAddItemDialogVisible.value = false;
    resetAddItemDialog();
    filteredProducts.value = [];
    selectedDivision.value = null;
};

const resetAddItemDialog = () => {
    selectedItemIds.value = [];
    selectedProduct.value = {};
    selectedQuantity.value = 1;
    additionalRemark.value = "";
    selectedAccountCode.value = "";
    selectedItem.value = null;
    selectedDivision.value = null;
    filteredProducts.value = [];
    editingProduct.value = null;
};

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

const updateDate = (selectedDate) => {
    form.value.quotation_date = selectedDate;
};

const selectCustomer = () => {
    isCreateCustomerVisible.value = false;
    form.customer_id = props.customers[props.customers.length - 1].id;
};

const updateProductSubtotal = (product) => {
    product.quantity = Math.max(1, product.quantity || 1);
    product.subTotal = (product.price || 0) * product.quantity;
    form.total = calculateTotalKHR.value;
    if (form.total_usd && form.total_usd > 0) {
        form.exchange_rate = calculateExchangeRate.value;
    }
};

const calculateProductSubtotals = computed(() => {
    return selectedProductsData.value.map((product) => {
        return {
            ...product,
            subTotal: (product.price || 0) * (product.quantity || 1),
        };
    });
});

const calculateTotalKHR = computed(() => {
    return calculateProductSubtotals.value.reduce((sum, product) => {
        return sum + (product.subTotal || 0);
    }, 0);
});

const calculateExchangeRate = computed(() => {
    if (form.total_usd && form.total_usd > 0) {
        return (calculateTotalKHR.value / form.total_usd).toFixed(4);
    }
    return "";
});

watch(
    () => form.total_usd,
    (newValue) => {
        if (newValue && newValue > 0) {
            form.exchange_rate = calculateExchangeRate.value;
        }
    }
);

const formatCurrency = (value) => {
    if (isNaN(value)) return "0.00";
    return new Intl.NumberFormat("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(value);
};

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
        selectedDivision.value = productToEdit.division_id;
        selectedProduct.value = {
            ...productToEdit,
            quantity: productToEdit.quantity ?? 1,
            subTotal: productToEdit.subTotal ?? 0,
            remark: productToEdit.remark || "",
            include_catalog: Boolean(productToEdit.include_catalog),
        };
        selectedItem.value = {
            id: productToEdit.id,
            name: productToEdit.name,
            division_id: productToEdit.division_id,
        };
        filterProductsByDivision();
        isAddItemDialogVisible.value = true;
        editingProduct.value = productToEdit;
    }
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

const submit = async (event) => {
    if (event && typeof event.preventDefault === "function") {
        event.preventDefault();
    }

    if (!validateForm()) return;

    for (let product of selectedProductsData.value) {
        if (product.include_catalog && !product.pdf_url) {
            showToast(
                "error",
                "Error",
                "Catalog PDF is missing for an included product.",
                3000
            );
            return;
        }
    }

    // Prepare the payload
    form.products = selectedProductsData.value.map((prod) => ({
        id: prod.id,
        quantity: prod.quantity ?? 1,
        price: prod.price ?? 0,
        remark: prod.remark ?? "",
        include_catalog: Boolean(prod.include_catalog),
        pdf_url: prod.pdf_url ?? null,
        _token: page.props.csrf_token,
    }));

    form.total = calculateTotalKHR.value;
    form.total_usd = form.total_usd || 0;
    form.exchange_rate = calculateExchangeRate.value;

    // If the quotation was in "Revise" status, change it to "Updated"
    if (props.quotation.status === "Revise") {
        form.status = "Updated"; // Or "Pending" if you prefer
    }

    if (!form.total_usd && form.exchange_rate > 0) {
        form.total_usd = (calculateTotalKHR.value / form.exchange_rate).toFixed(
            2
        );
    }

    try {
        await form.put(route("quotations.update", { id: form.id }), {
            headers: {
                "X-CSRF-TOKEN": page.props.csrf_token,
                "X-Requested-With": "XMLHttpRequest",
            },
            onSuccess: () => {
                showToast(
                    "success",
                    "Updated",
                    "Quotation updated successfully!"
                );
                router.get(route("quotations.list"));
            },
            onError: (errors) => {
                console.error("Update Error:", errors);
                showToast(
                    "error",
                    "Update Failed",
                    "Could not update quotation."
                );
            },
        });
    } catch (error) {
        console.error("Unexpected Error in Update:", error);
        showToast("error", "Unexpected Error", "Could not update quotation.");
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
    padding: 7px 7px !important;
    font-size: 12px !important;
    min-width: 30px !important;
}
.p-checkbox .p-checkbox-box {
    visibility: visible !important;
}
:deep(.p-datatable .p-column-subtotal),
:deep(.p-datatable .p-column-price) {
    text-align: right !important;
}

:deep(.p-inputnumber-input) {
    text-align: right;
}
</style>
