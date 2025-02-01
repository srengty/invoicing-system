<template>
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <Head title="Edit Invoice" />
  <GuestLayout>
    <div class="create-invoice">
      <!-- Header Section with Buttons -->
      <div class="flex justify-between items-center p-3 mr-4">
        <h1 class="text-2xl">Edit Invoice</h1>
        <div class="flex gap-4">
          <div>
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
              label="Save Changes"
              icon="pi pi-check"
              class="p-button-success"
              type="button"
              @click="submitInvoice"
              rounded
            />
          </div>
        </div>
      </div>

      <!-- Invoice Form Section -->
      <form @submit.prevent="submitInvoice">
        <div class="p-3 grid grid-cols-1 md:grid-cols-3 gap-4 ml-4 mr-4">
          <div>
            <label for="invoice_no" class="block text-lg font-medium">Invoice No</label>
            <InputText
              id="invoice_no"
              v-model="form.invoice_no"
              class="w-full"
              placeholder="Enter invoice number"
              required
            />
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
            <label for="start_date" class="block text-lg font-medium">Start Date</label>
            <DatePicker id="start_date" v-model="form.start_date" class="w-full" placeholder="Select start date" />
          </div>
          <div>
            <label for="end_date" class="block text-lg font-medium">End Date</label>
            <DatePicker id="end_date" v-model="form.end_date" class="w-full" placeholder="Select end date" />
          </div>
        </div>
      </form>

      <!-- Product Table Section -->
      <div class="m-6">
        <DataTable :value="productsList" class="p-datatable-striped" responsiveLayout="scroll">
          <Column field="id" header="No." :body="indexTemplate"></Column>
          <Column field="name" header="Product" :body="productBody"></Column>
          <Column field="qty" header="Qty">
            <template #body="slotProps">
              <InputText v-model="slotProps.data.qty" @input="updateProductSubtotal(slotProps.data)" class="w-full" />
            </template>
          </Column>
          <Column field="unit" header="Unit"></Column>
          <Column field="price" header="Unit Price"></Column>
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
                  @click="addProduct(slotProps.data)"
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

// Receive the props from the server
const { invoice, products, agreements, quotations, customers } = usePage().props;

// Form initialization with existing invoice data
const form = useForm({
  invoice_no: invoice.invoice_no || '',
  agreement_no: invoice.agreement_no || '',
  quotation_no: invoice.quotation_no || '',
  customer_id: invoice.customer_id || '',
  address: invoice.address || '',
  phone: invoice.phone || '',
  start_date: invoice.start_date || '',
  end_date: invoice.end_date || '',
  grand_total: invoice.grand_total || 0,
  status: invoice.status || '',
});

const statusOptions = [
  { label: 'Pending', value: 'pending' },
  { label: 'Paid', value: 'paid' },
  { label: 'Cancelled', value: 'cancelled' },
];
console.log(invoice.products)
const productsList = ref([...invoice.products]);
const showProductModal = ref(false);

// Add Product
const addProduct = (product) => {
  productsList.value.push({
    id: product.id,
    product: product.name,
    qty: 1,
    unit: product.unit,
    unitPrice: product.price,
    subTotal: 1 * product.price,
  });
  showProductModal.value = false;
};

// Update product subtotal
const updateProductSubtotal = (product) => {
  product.subTotal = product.qty * product.price;
};

// Calculate the total for the invoice
const calculateTotal = computed(() => {
  return productsList.value.reduce((total, product) => total + product.subTotal, 0);
});

const calculateGrandTotal = computed(() => {
  return calculateTotal.value; // Add taxes, discounts, etc. here if needed
});

// Handle the Save Changes button click
const submitInvoice = async () => {
  if (productsList.value.length === 0) {
    alert('Please add at least one product.');
    return;
  }

  const grandTotal = productsList.value.reduce((total, product) => total + product.subTotal, 0);

  const invoiceData = {
    invoice_no: form.invoice_no,
    agreement_no: form.agreement_no,
    quotation_no: form.quotation_no,
    customer_id: form.customer_id,
    address: form.address,
    phone: form.phone,
    start_date: form.start_date,
    end_date: form.end_date,
    grand_total: grandTotal,
    status: form.status,
    products: productsList.value.map(product => ({
      id: product.id,
      quantity: product.qty,
    })),
  };

  console.log("invoiceData: ", invoiceData);

  try {
    await form.put(`/invoices/${invoice.id}`, invoiceData, {
      onSuccess: () => {
        alert('Invoice updated successfully!');
        Inertia.visit('/invoices');
      },
      onError: (errors) => {
        alert('Error updating invoice: ' + (errors.message || 'Unknown error'));
      },
    });
  } catch (error) {
    console.error('Error:', error);
    alert('There was an error updating the invoice.');
  }
};

const cancel = () => {
  Inertia.visit('/invoices'); // Redirect to the invoice index page
};
</script>

<style scoped>
.total-container,
.grand-total-container {
  padding: 1rem 0;
}
</style>