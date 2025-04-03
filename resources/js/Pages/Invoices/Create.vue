<template>
    <meta name="_token" content="{{ csrf_token() }}" />
    <Head title="Create Invoice" />
    <GuestLayout>
        <NavbarLayout />
        <!-- PrimeVue Breadcrumb -->
        <div class="py-3 pb-0">
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
        <div class="create-invoice">
            <!-- Header Section with Buttons -->
            <div class="flex justify-end items-center p-3 pt-0 mr-4">
                <div class="flex gap-4">
                    <Button
                        label="Add Product"
                        icon="pi pi-plus"
                        class="p-button-success"
                        type="button"
                        @click="showProductModal = true"
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
                <div
                    class="p-3 grid grid-cols-1 md:grid-cols-4 gap-4 ml-4 mr-4"
                >
                    <div>
                        <label
                            for="invoice_no"
                            class="block text-sm font-medium"
                            >Invoice no</label
                        >
                        <InputText
                            id="invoice_no"
                            v-model="form.invoice_no"
                            placeholder="Enter invoice no"
                            required
                            class="w-full md:w-72"
                            size="small"
                        />
                    </div>
                    <div>
                        <label
                            for="quotation_no"
                            class="block text-sm font-medium"
                            >Quotation No</label
                        >
                        <Select
                            v-model="form.quotation_no"
                            :options="quotations"
                            optionLabel="quotation_no"
                            optionValue="quotation_no"
                            placeholder="Select Quotation"
                            class="w-full md:w-72"
                            size="small"
                            required
                        />
                    </div>
                    <div>
                        <label
                            for="agreement_no"
                            class="block text-sm font-medium"
                            >Agreement No</label
                        >
                        <Select
                            v-model="form.agreement_no"
                            :options="
                                form.agreement_no || !form.quotation_no
                                    ? agreements
                                    : agreements.filter(
                                          (a) => a.status === 'Open'
                                      )
                            "
                            optionLabel="agreement_no"
                            optionValue="agreement_no"
                            placeholder="Select Agreement"
                            class="w-full md:w-72"
                            size="small"
                            required
                        />
                    </div>
                    <div >
                        <label
                            for="deposit_no"
                            class="block text-sm font-medium"
                            >Receipt No (for deposit)</label
                        >
                        <div class="flex items-center gap-2">
                            <InputText
                                id="deposit_no"
                                v-model="form.deposit_no"
                                class="w-1/2"
                                placeholder="Enter deposit number"
                                required
                                size="small"
                            />
                            <Button class="grid md:w-1/2 gap-2 p-button-info" size="small"
                                > Add Receipt</Button
                            >
                        </div>
                    </div>
                    <div>
                        <label
                            for="customer_id"
                            class="block text-sm font-medium"
                            >Customer</label
                        >
                        <Select
                            v-model="form.customer_id"
                            :options="customers"
                            optionLabel="name"
                            optionValue="id"
                            placeholder="Select Customer"
                            class="w-full md:w-72"
                            size="small"
                            required
                        />
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium"
                            >Status</label
                        >
                        <Select
                            v-model="form.status"
                            :options="statusOptions"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Select Status"
                            class="w-full md:w-72"
                            size="small"
                            required
                        />
                    </div>
                    <div>
                        <label for="address" class="block text-sm font-medium"
                            >Address</label
                        >
                        <InputText
                            id="address"
                            v-model="form.address"
                            class="w-full md:w-72"
                            size="small"
                            placeholder="Enter address"
                        />
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium"
                            >Phone</label
                        >
                        <InputText
                            id="phone"
                            v-model="form.phone"
                            class="w-full md:w-72"
                            size="small"
                            placeholder="Enter phone number"
                        />
                    </div>
                    <div>
                        <label
                            for="start_date"
                            class="block text-sm font-medium"
                            >Start Date</label
                        >
                        <Calendar
                            id="start_date"
                            v-model="form.start_date"
                            class="w-full md:w-72"
                            size="small"
                            placeholder="Select start date"
                            dateFormat="yy-mm-dd"
                            showTime
                            hourFormat="24"
                        />
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium"
                            >End Date</label
                        >
                        <Calendar
                            id="end_date"
                            v-model="form.end_date"
                            class="w-full md:w-72"
                            size="small"
                            placeholder="Select end date"
                            dateFormat="yy-mm-dd"
                            showTime
                            hourFormat="24"
                        />
                    </div>
                </div>
            </form>

            <!-- Product Table Section -->
            <div class="m-6">
                <DataTable
                    :value="indexedProducts"
                    class="p-datatable-striped"
                    responsiveLayout="scroll"
                    size="small"
                >
                    <Column field="index" header="No.">
                        <template #body="slotProps">
                            {{ slotProps.index + 1 }}
                        </template>
                    </Column>
                    <Column field="name" header="Product"></Column>
                    <Column field="quantity" header="Qty">
                        <template #body="slotProps">
                            <InputNumber
                                v-model="slotProps.data.quantity"
                                @input="updateProductSubtotal(slotProps.data)"
                                class="w-full"
                                mode="decimal"
                                :min="1"
                            />
                        </template>
                    </Column>
                    <Column field="unit" header="Unit"></Column>
                    <Column field="price" header="Unit Price">
                        <template #body="slotProps">
                            <InputNumber
                                v-model="slotProps.data.price"
                                @input="updateProductSubtotal(slotProps.data)"
                                class="w-full"
                                mode="decimal"
                                :minFractionDigits="2"
                                :maxFractionDigits="2"
                                :min="1"
                            />
                        </template>
                    </Column>
                    <Column field="subTotal" header="Sub Total"></Column>
                    <Column header="Include Catalog">
                        <template #body="slotProps">
                            <Checkbox 
                                v-model="slotProps.data.include_catalog" 
                                :binary="true" 
                            />
                        </template>
                    </Column>
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
                    <div class="total-container mt-4 flex justify-between">
                        <p class="font-bold">Total KHR</p>
                        <p class="font-bold">
                            ៛{{ calculateTotalKHR }}
                        </p>
                    </div>
                    <div class="total-container mt-4 flex justify-between">
                        <p class="font-bold">Total USD</p>
                        <p class="font-bold">
                            <input
                                type="number"
                                v-model.number="form.total_usd"
                                placeholder="Enter USD"
                                :minFractionDigits="2"
                                :maxFractionDigits="2"
                                class="w-1/7 h-9 text-sm"
                            />
                        </p>
                    </div>
                    <div class="grand-total-container flex justify-between">
                        <p class="font-bold">Exchange rate</p>
                        <p class="font-bold">
                            {{ calculateExchangeRate }}
                        </p>
                    </div>
                </div>
                <div class="terms mt-4">
                    <h3 class="text-lg">Terms and Conditions</h3>
                    <Textarea v-model="form.terms" rows="3" class="w-full" />
                </div>
                <div class="buttons mt-4 flex justify-end">
                    <Button
                        label="Save Invoice"
                        icon="pi pi-check"
                        class="p-button-success shadow-md"
                        @click="submitInvoice"
                    />
                    <Button
                        label="Cancel"
                        class="p-button-secondary ml-2 shadow-md"
                        @click="cancel"
                    />
                </div>
            </div>

            <!-- Modal to Select Product -->
            <Dialog 
                v-model:visible="showProductModal" 
                header="Select Product"
                :modal="true"
                :style="{ width: '70vw' }"
            >
                <DataTable
                    :value="filteredProducts"
                    class="p-datatable-striped"
                    responsiveLayout="scroll"
                    size="small"
                    selectionMode="single"
                    v-model:selection="selectedProduct"
                    dataKey="id"
                >
                    <Column field="name" header="Product Name"></Column>
                    <Column field="unit" header="Unit"></Column>
                    <Column field="price" header="Price">
                        <template #body="slotProps">
                            {{ formatCurrency(slotProps.data.price) }}
                        </template>
                    </Column>
                    <Column field="status" header="Status"></Column>
                </DataTable>
                <template #footer>
                    <Button
                        label="Cancel"
                        icon="pi pi-times"
                        class="p-button-text"
                        @click="showProductModal = false"
                    />
                    <Button
                        label="Add Product"
                        icon="pi pi-plus"
                        class="p-button-success"
                        @click="addSelectedProduct"
                        :disabled="!selectedProduct"
                    />
                </template>
            </Dialog>
        </div>
    </GuestLayout>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import {
    Button,
    InputText,
    InputNumber,
    DataTable,
    Column,
    Dialog,
    Calendar,
    Select,
    Checkbox,
    Textarea,
} from "primevue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import NavbarLayout from "@/Layouts/NavbarLayout.vue";
import Breadcrumb from "primevue/breadcrumb";
import { usePage } from "@inertiajs/vue3";

const page = usePage();
const items = computed(() => [
    {
        label: "",
        to: "/",
        icon: "pi pi-home",
    },
    {
        label: page.props.title || " Invoices",
        to: route("invoices.index"),
    },
    {
        label: page.props.title || "Create Invoices",
        to: route("invoices.create"),
    },
]);

const { products: allProducts, agreements, quotations, customers } = usePage().props;

// Initialize form first
const form = useForm({
    invoice_no: "",
    agreement_no: "",
    quotation_no: "",
    customer_id: "",
    address: "",
    phone_number: "",
    terms: "",
    total_usd: null,
    exchange_rate: null,
    start_date: null,
    end_date: null,
    products: [],
});

const calculateTotalUSD = ref(null);
const productsList = ref([]);
const showProductModal = ref(false);
const filteredAgreements = ref(agreements.filter(a => a.status === 'Open'));
const selectedProduct = ref(null);

const calculateProductSubtotals = computed(() => {
    return productsList.value.map((product) => {
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

// Filter only approved products
const filteredProducts = computed(() => {
    return allProducts.filter(product => product.status === 'approved');
});

const indexedProducts = computed(() => 
    productsList.value.map((product, index) => ({
        ...product,
        index: index + 1,
        subTotal: (product.quantity || 1) * (product.price || 0)
    }))
);

const calculateTotal = computed(() => {
    return productsList.value.reduce(
        (acc, product) => acc + (product.quantity * product.price),
        0
    );
});

watch(
    () => form.quotation_no,
    (newQuotationNo) => {
        if (newQuotationNo) {
            const selectedQuotation = quotations.find(
                q => q.quotation_no === newQuotationNo
            );

            if (selectedQuotation) {
                form.customer_id = selectedQuotation.customer_id || "";
                form.address = selectedQuotation.address || "";
                form.phone_number = selectedQuotation.phone_number || "";
                
                if (selectedQuotation.agreement) {
                    form.agreement_no = selectedQuotation.agreement.agreement_no;
                } else {
                    form.agreement_no = "";
                }

                // Add products from quotation
                if (selectedQuotation.product_quotations?.length > 0) {
                    productsList.value = selectedQuotation.product_quotations.map(pq => ({
                        id: pq.product.id,
                        name: pq.product.name,
                        quantity: pq.quantity,
                        unit: pq.product.unit,
                        price: pq.price,
                        include_catalog: false,
                        pdf_url: null
                    }));
                }
            }
        } else {
            form.customer_id = "";
            form.address = "";
            form.phone_number = "";
            form.agreement_no = "";
            productsList.value = [];
        }
    }
);

watch(
    () => form.agreement_no,
    (newAgreementNo) => {
        if (newAgreementNo) {
            const selectedAgreement = agreements.find(
                a => a.agreement_no === newAgreementNo
            );

            if (selectedAgreement) {
                form.start_date = selectedAgreement.start_date || null;
                form.end_date = selectedAgreement.end_date || null;
            }
        }
    }
);

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(value);
};

const addSelectedProduct = () => {
    if (selectedProduct.value) {
        const existingProduct = productsList.value.find(
            p => p.id === selectedProduct.value.id
        );

        if (!existingProduct) {
            productsList.value.push({
                id: selectedProduct.value.id,
                name: selectedProduct.value.name,
                quantity: 1,
                unit: selectedProduct.value.unit,
                price: selectedProduct.value.price,
                include_catalog: false,
                pdf_url: null
            });
        } else {
            existingProduct.quantity += 1;
        }

        showProductModal.value = false;
        selectedProduct.value = null;
    }
};

const removeProduct = (productId) => {
    productsList.value = productsList.value.filter(
        product => product.id !== productId
    );
};

const updateProductSubtotal = (product) => {
    // Subtotal is calculated in the computed property
};

const cancel = () => {
    form.reset();
    productsList.value = [];
    window.location.href = route('invoices.index');
};

const submitInvoice = () => {
    if (productsList.value.length === 0) {
        alert("Please add at least one product.");
        return;
    }

    // Format dates to match backend expectation
    const formattedData = {
        ...form.data(),
        start_date: form.start_date ? new Date(form.start_date).toISOString() : null,
        end_date: form.end_date ? new Date(form.end_date).toISOString() : null,
        products: productsList.value.map(product => ({
            id: product.id,
            quantity: product.quantity,
            price: product.price,
            include_catalog: product.include_catalog,
            acc_code: null, // Add if needed
            category_id: null, // Add if needed
            remark: null, // Add if needed
            pdf_url: product.pdf_url
        }))
    };

    form.products = productsList.value;
    form.total = calculateTotal.value;

    form.post(route('invoices.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            productsList.value = [];
        },
        onError: (errors) => {
            console.error('Error creating invoice:', errors);
        }
    });
};
</script>

<style scoped>
.create-invoice {
    padding: 1rem;
}
.total-container, .grand-total-container {
    padding: 0.5rem 1rem;
    background-color: #f8f9fa;
    border-radius: 4px;
}
</style>