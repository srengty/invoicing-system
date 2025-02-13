<template>
  <meta name="_token" content="{{ csrf_token() }}" />
  <Head title="Create Invoice" />
  <GuestLayout>
    <div class="create-invoice">
      <!-- Header Section with Buttons -->
      <div class="flex justify-between items-center p-3 mr-4">
        <h1 class="text-2xl">Create Invoice</h1>
        <div class="flex gap-4">
          <div>
            <!-- Add Product Button (Opens product selection modal) -->
            <Button
              label="Add Product"
              icon="pi pi-plus"
              class="p-button-success"
              type="button"
              @click="showProductModal = true"
              rounded
            />
          </div>
          <div>
            <Button
              label="Save Invoice"
              icon="pi pi-check"
              class="p-button-success"
              type="button"
              @click="submitInvoiceAndRedirect"
              rounded
            />
          </div>
        </div>
      </div>

      <!-- Invoice Form Section -->
      <form @submit.prevent="submitInvoice">
        <div class="p-3 grid grid-cols-1 md:grid-cols-3 gap-4 ml-4 mr-4">
          <div>
            <label for="quotation_no" class="block text-lg font-medium">Quotation No</label>
            <Select
              v-model="form.quotation_no"
              :options="quotations"
              optionLabel="quotation_no"
              optionValue="id"
              placeholder="Select Quotation"
              class="w-full"
              required
            />
          </div>
          <div>
            <label for="agreement_no" class="block text-lg font-medium">Agreement No</label>
            <Select
              v-model="form.agreement_no"
              :options="agreements"
              optionLabel="agreement_no"
              optionValue="id"
              placeholder="Select Agreement"
              class="w-full"
              required
            />
          </div>
          <!-- <div>
            <label for="invoice_no" class="block text-lg font-medium">Invoice No</label>
            <InputText
              id="invoice_no"
              v-model="form.invoice_no"
              class="w-full"
              placeholder="Enter invoice number"
              required
            />
          </div> -->
          <div>
            <label for="deposit_no" class="block text-lg font-medium">Receipt No (for deposit)</label>
            <InputText v-tooltip="'ប្រាក់កក់មុន'"
              id="deposit_no"
              v-model="form.deposit_no"
              class="w-1/2"
              placeholder="Enter deposit number"
              required
            />
            <Button class="w-1/2">Add Receipt</Button>
          </div>
          <div>
            <label for="customer_id" class="block text-lg font-medium">Customer</label>
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
            <label for="status" class="block text-lg font-medium">Status</label>
            <Select
              v-model="form.status"
              :options="statusOptions"
              optionLabel="label"
              optionValue="value"
              placeholder="Select Status"
              class="w-full"
              required
            />
          </div>
          <div>
            <label for="address" class="block text-lg font-medium">Address</label>
            <InputText
              id="address"
              v-model="form.address"
              class="w-full"
              placeholder="Enter address"
            />
          </div>
          <div>
            <label for="phone" class="block text-lg font-medium">Phone</label>
            <InputText
              id="phone"
              v-model="form.phone"
              class="w-full"
              placeholder="Enter phone number"
            />
          </div>
          <div>
            <label for="start_date" class="block text-lg font-medium">Date</label>
            <DatePicker id="start_date" v-model="form.start_date" class="w-full" placeholder="Select date" />
          </div>
          <div>
            <label for="end_date" class="block text-lg font-medium">Due Date</label>
            <DatePicker id="end_date" v-model="form.end_date" class="w-full" placeholder="Select due date" />
          </div>
        </div>
      </form>

      <!-- Product Table Section -->
      <div class="m-6">
        <DataTable :value="productsList" class="p-datatable-striped" responsiveLayout="scroll">
          <Column field="id" header="No." :body="indexTemplate"></Column>
          <Column field="product" header="Product"></Column>
          <Column field="qty" header="Qty">
            <template #body="slotProps">
              <InputText v-model="slotProps.data.qty" @input="updateProductSubtotal(slotProps.data)" class="w-full" />
            </template>
          </Column>
          <Column field="unit" header="Unit"></Column>
          <Column field="unitPrice" header="Unit Price"></Column>
          <Column field="subTotal" header="Sub Total"></Column>
          <Column header="Action" :body="actionTemplate"></Column>
        </DataTable>
        <div class="total-container mt-4 flex justify-between">
          <p class="font-bold">Total</p>
          <p class="font-bold">{{ calculateTotal }}</p>
        </div>

        <div class="grand-total-container flex justify-between">
          <p class="font-bold text-lg">Grand Total</p>
          <p class="font-bold text-lg">{{ calculateGrandTotal }}</p>
        </div>

        <!-- Terms and Conditions -->
        <div class="terms mt-4">
          <h3 class="text-lg">Terms and Conditions</h3>
          <p>Full payment is required upon quote acceptance.</p>
          <p>This quote is negotiable for one (1) week from the date stated above.</p>
        </div>

        <!-- Buttons Section -->
        <div class="buttons mt-4 flex justify-end">
          <Button label="Submit request for approval" icon="pi pi-check" class="p-button-rounded p-button-success" @click="submitInvoice" />
          <Button label="Cancel" class="p-button-rounded p-button-secondary ml-2" @click="cancel" />
        </div>
      </div>

      <!-- Modal to Select Product -->
      <Dialog v-model:visible="showProductModal" header="Select Product">
        <template #footer>
          <Button
            label="Close"
            icon="pi pi-times"
            class="p-button-text"
            @click="showProductModal = false"
          />
        </template>

        <div class="product-list">
          <p v-if="!products || products.length === 0">No products available.</p>
          <DataTable :value="products" class="p-datatable-striped" responsiveLayout="scroll">
            <Column field="name" header="Product Name"></Column>
            <Column field="unit" header="Unit"></Column>
            <Column field="price" header="Price"></Column>

            <Column header="Action">
              <template #body="slotProps">
                <Button
                  label="Add"
                  icon="pi pi-plus"
                  class="p-button-text p-button-success"
                  @click="addProduct(slotProps.data.id)"
                />
              </template>
            </Column>
          </DataTable>
        </div>
      </Dialog>
    </div>
  </GuestLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Button, InputText, DataTable, Column, Dialog, DatePicker, Select } from 'primevue';
import { usePage } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia';

const { products, agreements, quotations, customers } = usePage().props;

const form = useForm({
  invoice_no: '',
  agreement_no: '',
  quotation_no: '',
  deposit_no: '',
  customer_id: '',
  address: '',
  phone: '',
  start_date: '',
  end_date: '',
  grand_total: 0,  // Ensure grand_total is initialized as 0 (number)
  status: '',
  products:[],
});

const statusOptions = [
  { label: 'Pending', value: 'pending' },
  { label: 'Paid', value: 'paid' },
  { label: 'Cancelled', value: 'cancelled' },
];

const submitInvoiceAndRedirect = () => {
  submitInvoice(); // Call the existing submitInvoice logic
  Inertia.visit('/invoices'); // Redirect to the invoice index page
};

const productsList = ref([]);
const showProductModal = ref(false);

const openProductModal = () => {
  showProductModal.value = true;
};

const indexTemplate = (data, options) => options.rowIndex + 1;

const actionTemplate = (data) => {
  return `<Button label="Remove" icon="pi pi-times" class="p-button-text p-button-danger" @click="removeProduct(${data.id})" />`;
};

const addProduct = (productId) => {
  const product = products.find((prod) => prod.id === productId);
  if (product) {
    const newProduct = {
      id: product.id,            // Ensure the ID is set correctly
      product: product.name,      // Use the correct product properties
      qty: 1,                     // Set the quantity properly
      unit: product.unit,
      unitPrice: product.price,
      subTotal: 1 * product.price,
    };
    productsList.value.push(newProduct);
    showProductModal.value = false;
  } else {
    console.log('Product not found:', productId);
  }
};

const removeProduct = (productId) => {
  productsList.value = productsList.value.filter((product) => product.id !== productId);
};

const calculateTotal = computed(() => {
  return productsList.value.reduce((total, product) => total + product.subTotal, 0);
});

const calculateGrandTotal = computed(() => {
  return calculateTotal.value;
});

const updateProductSubtotal = (product) => {
  product.subTotal = product.qty * product.unitPrice;
};

const submitInvoice = () => {
  if (productsList.value.length === 0) {
    alert('Please add at least one product.');
    return;
  }

  // Explicitly calculate grand_total
  const grandTotal = productsList.value.reduce((total, product) => total + product.subTotal, 0);

  const invoiceData = {
    invoice_no: form.invoice_no,
    agreement_no: form.agreement_no,
    quotation_no: form.quotation_no,
    customer_id: form.customer_id,
    address: form.address,
    phone: form.phone,
    start_date: new Date(form.start_date).toISOString(),
    end_date: new Date(form.end_date).toISOString(),
    grand_total: grandTotal, // Explicitly set grand_total
    status: form.status,
    products: productsList.value.map(product => ({
      id: product.id,        // Map product ID
      quantity: product.qty, // Map product quantity
    })),
  };

  const csrfToken = document.querySelector('meta[name="csrf_token"]').getAttribute('content');

  fetch('/invoices', {
    method: "POST",
    body: JSON.stringify(invoiceData),
    headers: {
      'X-CSRF-TOKEN': csrfToken,
      "Content-type": "application/json"
    }
  })
};

const cancel = () => {
  form.reset();
  productsList.value = [];
};
</script>

<style scoped>
.total-container,
.grand-total-container {
  padding: 1rem 0;
}
</style>
