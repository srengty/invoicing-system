<template>
    <Head title="Create Quotation" />
    <GuestLayout>
        <Toast position="top-center" group="tc" />
        <Toast position="top-right" group="tr" />

        <!-- Use the PrimeVue Form wrapper (with @submit.prevent) -->
        <Form @submit.prevent="submit" class="text-sm">
            <!-- Quotation Info -->
            <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-2">
                        <label for="quotation_no">Quotation No:</label>
                        <InputText
                            :disabled="isApproved"
                            id="quotation_no"
                            v-model="form.quotation_no"
                            placeholder="Auto-generated"
                            class="w-full md:w-60"
                            size="small"
                        />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="quotation_date">Date:</label>
                        <DatePicker
                            :disabled="isApproved"
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
                        <label for="customer_id">Customer/Organization</label>
                        <Select
                            :filter="true"
                            v-model="form.customer_id"
                            :options="formattedCustomers"
                            optionLabel="label"
                            optionValue="id"
                            id="customer_id"
                            placeholder="Select a customer"
                            class="w-full md:w-60"
                        />
                    </div>
                    <div class="w-10">
                        <Button
                            icon="pi pi-plus"
                            title="add customer"
                            label="addcustomer"
                            rounded
                            @click="isCreateCustomerVisible = true"
                            class="w-36 start"
                            size="small"
                        />
                    </div>
                </div>

                <!-- <div class="grid grid-cols-1 md:grid-cols-5"> -->
                <div class="flex flex-col gap-2 w-full md:ml-28">
                    <label for="address">Address:</label>
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
                    <label for="phone_number">Contact:</label>
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
            <div class="pl-8 pt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-2 w-full">
                    <div class="flex gap-4 items-end">
                        <div class="flex flex-col gap-2">
                            <label for="item">Item</label>
                            <MultiSelect
                                :filter="true"
                                v-model="selectedProductIds"
                                :options="products"
                                optionLabel="name"
                                optionValue="id"
                                placeholder="Select Product"
                                class="w-full md:w-60"
                            />
                        </div>
                        <div class="w-10">
                            <Button
                                icon="pi pi-plus"
                                label="Add Item"
                                rounded
                                @click="isAddItemDialogVisible = true"
                                class="w-36"
                                size="small"
                            />
                        </div>
                    </div>

                    <!-- <div class="w-full">
                <Link :href="route('products.store')">
                  <Button icon="pi pi-plus" label="Choose Item" rounded />
                </Link>x
                <Button icon="pi pi-plus" label="Add Item" rounded @click="isCreateItemVisible"/>
              </div> -->
                </div>
            </div>
            <div class="pl-8 flex flex-row gap-4 items-end w-1/3">
                <div class="flex flex-row gap-2 w-full">
                    <label for="p_name">English/Khmer</label>
                    <ToggleSwitch v-model="isKhmer" @change="toggleLanguage" />
                </div>
                <div class="w-60"></div>
            </div>

            <!-- Selected Products Table -->
            <div class="pl-6 pt-5">
                <DataTable
                    :value="selectedProductsData"
                    paginator
                    :rows="5"
                    striped
                >
                    <Column field="id" header="No." />
                    <Column field="name" header="Name">
                        <template #body="slotProps">
                            <span>{{
                                isKhmer
                                    ? slotProps.data.name_kh
                                    : slotProps.data.name
                            }}</span>
                        </template>
                    </Column>
                    <Column field="quantity" header="Qty">
                        <template #body="slotProps">
                            <InputText
                                v-model="slotProps.data.quanity"
                                @input="updateProductSubtotal(slotProps.data)"
                                class="w-full"
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
                                :minFractionDigits="2"
                                :maxFractionDigits="2"
                                class="w-full"
                                placeholder="Enter in USD"
                                size="small"
                            />
                        </template>
                    </Column>
                    <Column field="subTotal" header="Subtotal">
                        <template #body="slotProps">
                            <span
                                >{{
                                    slotProps.data.subTotal.toFixed(2)
                                }}
                                KHR</span
                            >
                        </template>
                    </Column>
                    <Column header="Actions">
                        <template #body="slotProps">
                            <div class="flex gap-2">
                                <Button
                                    icon="pi pi-trash"
                                    class="p-button-danger w-[10px]"
                                    title="remove"
                                    size="small"
                                    @click="removeProduct(slotProps.data.id)"
                                    rounded
                                />
                                <Button
                                    icon="pi pi-pencil"
                                    severity="info"
                                    title="edit"
                                    size="small"
                                    @click="viewQuotation(slotProps.data.id)"
                                    rounded
                                />
                                <Button
                                    icon="pi pi-print"
                                    severity="success"
                                    title="print"
                                    size="small"
                                    @click="viewQuotation(slotProps.data.id)"
                                    rounded
                                />
                            </div>
                        </template>
                    </Column>
                </DataTable>

                <!-- Totals Summary -->
                <div class="pl-2 pr-60">
                    <div class="total-container mt-4 flex justify-between">
                        <p class="font-bold">Total KHR</p>
                        <p class="font-bold">
                            ៛{{ formatCurrency(calculateTotal.toFixed(2)) }}
                        </p>
                    </div>
                    <div class="total-container mt-4 flex justify-between">
                        <p class="font-bold">Total USD</p>
                        <p class="font-bold">
                            <InputNumber
                                v-model="calculateTotalUSD"
                                class="text-right"
                                :minFractionDigits="2"
                                :maxFractionDigits="2"
                                placeholder="Enter USD"
                                size="small"
                            />
                        </p>
                    </div>
                    <div class="grand-total-container flex justify-between">
                        <p class="font-bold">Exchange rate</p>
                        <p class="font-bold text-lg">
                            {{ calculateExchangeRate }}
                        </p>
                    </div>
                    <div class="mt-2 flex justify-between items-center">
                        <!-- Tax -->
                        <!--                <div>-->
                        <!--                  <label for="tax" class="font-bold">Tax</label>-->
                        <!--                  <InputText-->
                        <!--                    id="tax"-->
                        <!--                    v-model="form.tax"-->
                        <!--                    placeholder="0"-->
                        <!--                    class="w-24 ml-2"-->
                        <!--                    @input="updateTax"-->
                        <!--                  />-->
                        <!--                </div>-->
                    </div>
                </div>
            </div>

            <!-- Form Buttons -->
            <div class="buttons mt-4 mr-4 flex justify-end">
                <Button
                    v-ripple
                    label="Submit"
                    icon="pi pi-check"
                    type="submit"
                    class="p-button-rounded p-button-success"
                    @click="submit"
                />
                <Button
                    v-ripple
                    label="Cancel"
                    class="p-button-rounded p-button-secondary ml-2"
                />
            </div>
        </Form>
    </GuestLayout>
    <Dialog
        v-model:visible="isCreateCustomerVisible"
        modal
        header="Add Customer"
        class="w-2/3"
    >
        <Customers
            redirect_route="quotations.create"
            @success="selectCustomer"
        ></Customers>
    </Dialog>
    <Dialog
        v-model:visible="isCreateItemVisible"
        modal
        header="Add Item"
        class="w-1/2"
    >
        <Customers redirect_route="quotations.create"></Customers>
    </Dialog>

    <!-- Add Item Dialog -->
    <Dialog
        v-model:visible="isAddItemDialogVisible"
        modal
        header="Add Item (Popup)"
        :style="{ width: '500px' }"
    >
        <div class="p-fluid grid gap-4 text-sm">
            <!-- Item Selection -->
            <div class="field w-full">
                <label for="item">Item *</label> <br />
                <MultiSelect
                    :filter="true"
                    v-model="selectedProductsData"
                    :options="products"
                    optionLabel="name"
                    optionValue="id"
                    placeholder="Select Product"
                    class="w-full text-sm"
                    size="small"
                    @change="updateSelectedProductDetails"
                />
            </div>

            <!-- Item Category (Auto-complete, Read-Only) -->
            <div class="field">
                <label for="item-category">Item Category *</label>
                <InputText
                    v-model="products.category"
                    class="w-full text-sm"
                    size="small"
                    readonly
                />
            </div>

            <!-- Unit Price (Auto-complete, Editable) -->
            <div class="field">
                <label for="unit-price">Unit Price *</label>
                <InputNumber v-model="products.price" size="small" class="w-full text-sm" />
            </div>

            <!-- Account Code (Auto-complete, Read-Only) -->
            <div class="field">
                <label for="account-code">Account Code *</label>
                <InputText
                    v-model="selectedAccountCode"
                    class="w-full text-sm"
                    size="small"
                    readonly
                />
            </div>

            <!-- Quantity -->
            <div class="field">
                <label for="quantity">Quantity *</label>
                <InputNumber
                    v-model="selectedQuantity"
                    class="w-full text-sm"
                    size="small"
                    :min="1"
                />
            </div>

            <!-- View Catalog -->
            <div class="field">
                <label>View Catalog</label>
                <a
                    href="https://yourcatalog.com"
                    target="_blank"
                    class="text-blue-500 underline"
                    >Link to open catalog</a
                >
            </div>

            <!-- Additional Remark -->
            <div class="field">
                <label for="additional-remark">Additional Remark</label>
                <Textarea
                    v-model="additionalRemark"
                    rows="3"
                    class="w-full text-sm"
                />
            </div>
        </div>

        <!-- Dialog Footer -->
        <template #footer>
            <Button
                label="Cancel"
                icon="pi pi-times"
                class="p-button-text"
                @click="isAddItemDialogVisible = false"
            />
            <Button
                label="Add Item"
                icon="pi pi-check"
                class="p-button-success"
                @click="addItemToTable"
            />
        </template>
    </Dialog>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import { Head } from "@inertiajs/vue3";
import { useForm } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import Select from "primevue/select";
import MultiSelect from "primevue/multiselect";
import InputNumber from "primevue/inputnumber";
import DatePicker from "primevue/datepicker";
import InputText from "primevue/inputtext";
import IconField from "primevue/iconfield";
import InputIcon from "primevue/inputicon";
import Button from "primevue/button";
import { Dialog, ToggleSwitch } from "primevue";
import { Form } from "@primevue/forms";
import Toast from "primevue/toast";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import { useToast } from "primevue/usetoast";
import Customers from "@/Components/Customers.vue";

const props = defineProps({
    customers: Array,
    products: Array,
});
console.log(props.products);

const status = ref("");
const isApproved = ref(false);
const today = new Date();
const isKhmer = ref(false);
const statusOptions = ref(["Pending", "Approved"]);
const isAddItemDialogVisible = ref(false);
const selectedItem = ref({});
const selectedQuantity = ref(1);
const additionalRemark = ref("");
const filteredItems = ref([]);

const addItemToTable = () => {
    if (selectedItem.value.name) {
        console.log("Item Added:", {
            ...selectedItem.value,
            quantity: selectedQuantity.value,
            remarks: additionalRemark.value,
        });
        isAddItemDialogVisible.value = false;
    }
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

// Toast for notifications
const toast = useToast();

// Define the Inertia form
const form = useForm({
    quotation_no: null,
    quotation_date: null,
    status: "Pending",
    address: "",
    phone_number: "",
    customer_id: null,
    total: 0,
    tax: 0,
    grand_total: 0,
    products: [], // Will be an array of objects: { id, quantity }
});

const isCreateCustomerVisible = ref(false);
const selectCustomer = () => {
    isCreateCustomerVisible.value = false;
    form.customer_id = props.customers[props.customers.length - 1].id;
};

const selectedProductIds = ref([]);
const selectedProductsData = ref([]);

// const columns = ref([
//   { field: "id", header: "No." },
//   { field: "name", header: "Name" },
//   { field: "unit", header: "Unit" },
//   { field: "price", header: "Unit Price" },
// ]);

watch(selectedProductIds, (newIds) => {
    newIds.forEach((id) => {
        if (!selectedProductsData.value.find((prod) => prod.id === id)) {
            const prod = props.products.find((p) => p.id === id);
            if (prod) {
                selectedProductsData.value.push({
                    ...prod,
                    quanity: 1,
                    subTotal: Number(prod.price) * 1,
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

watch(
    () => form.customer_id,
    (newCustomerId) => {
        const selectedCustomer = formattedCustomers.value.find(
            (customer) => customer.id === newCustomerId
        );
        if (selectedCustomer) {
            form.address = selectedCustomer.address || "";
            form.phone_number = selectedCustomer.phone_number || "";
        } else {
            form.address = "";
            form.phone_number = "";
        }
    }
);

const updateProductSubtotal = (row) => {
    row.quanity = parseInt(row.quanity) || 0;
    row.subTotal = Number(row.price) * row.quanity;
    form.total = calculateTotal.value;
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
    if (calculateTotalUSD.value == null || calculateTotalUSD.value === 0)
        return "";
    const exchangeRate = calculateTotal.value / calculateTotalUSD.value;
    return exchangeRate.toFixed(2);
});

const calculateTotal = computed(() => {
    return selectedProductsData.value.reduce(
        (sum, prod) => sum + prod.subTotal,
        0
    );
});

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

const formattedCustomers = computed(() => {
    return props.customers.map((customer) => ({
        id: customer.id,
        label: `${customer.name} (${customer.code})`,
        address: customer.address,
        phone_number: customer.phone_number,
    }));
});

const removeProduct = (id) => {
    selectedProductsData.value = selectedProductsData.value.filter(
        (prod) => prod.id !== id
    );
    // Also update the selected IDs so the MultiSelect reflects the change.
    selectedProductIds.value = selectedProductIds.value.filter(
        (prodId) => prodId !== id
    );
};

const submit = (event) => {
    if (event && typeof event.preventDefault === "function") {
        event.preventDefault(); // Ensure the event exists before calling preventDefault
    }
    // Basic validation: all required fields must be filled and at least one product selected.
    if (
        !form.address ||
        !form.phone_number ||
        !form.customer_id ||
        selectedProductsData.value.length === 0
    ) {
        toast.add({
            severity: "error",
            summary: "Please fill all information!",
            detail: "Missing required fields",
            group: "tc",
            life: 3000,
        });
        return;
    }
    selectedProductsData.value.forEach((prod) => {
        console.log(prod.price);
    });
    // Prepare the products array for submission.
    form.products = selectedProductsData.value.map((prod) => ({
        id: prod.id,
        quantity: prod.quanity,
        price: prod.price,
    }));
    console.log(form.products);
    // Update totals.
    form.total = calculateTotal.value;
    form.grand_total = calculateGrandTotal.value;

    // Post the form data using Inertia.
    form.post(route("quotations.store"), {
        onSuccess: () => {
            alert("Customer created successfully!");
        },
        onError: (errors) => {
            console.error(errors);
        },
    });
};

const isCreateItemVisible = ref(false);
</script>
