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
              optionValue="quotation_no"
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
          <div>
            <label for="deposit_no" class="block text-lg font-medium">Receipt No (for deposit)</label>
            <InputText v-tooltip="'ប្រាក់កក់មុន'"
              id="deposit_no"
              v-model="form.deposit_no"
              class="w-1/2"
              placeholder="Enter deposit number"
              required
            />
            <Button class="w-1/3 ml-4 p-button-info" rounded >Add Receipt</Button>
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

        <!-- Instalment Paid Section -->
        <div class="flex justify-between mt-4">
          <p class="font-bold">Instalment Paid</p>
          <p class="font-bold">{{ form.instalmentPaid }}</p>
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
import { ref, computed, watch } from 'vue';
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
  grand_total: 0, // Initialize grand_total as 0
  instalmentPaid: 0, // Store instalment paid
  status: '',
  products: [], // Empty products array
});

const statusOptions = [
  { label: 'Pending', value: 'Pending' },
  { label: 'Approved', value: 'Approved' },
  { label: 'Revise', value: 'Revise' },
];

const productsList = ref([]); // The list of products to display in the table
const showProductModal = ref(false);

const openProductModal = () => {
  showProductModal.value = true;
};

watch(() => form.quotation_no, async (newQuotationId) => {
  console.log('Selected Quotation ID:', newQuotationId);  // Debugging log
  if (newQuotationId) {
    const selectedQuotation = quotations.find(q => q.quotation_no === newQuotationId);
    if (selectedQuotation) {
      console.log('Selected Quotation:', selectedQuotation);  // Debugging log
      // Auto-fill fields
      form.customer_id = selectedQuotation.customer_id;
      form.address = selectedQuotation.address;
      form.phone = selectedQuotation.phone_number;
      form.status = selectedQuotation.status;

      // Ensure products is an array before using .map()
      if (Array.isArray(selectedQuotation.products)) {
        productsList.value = selectedQuotation.products.map(product => ({
          id: product.id,
          product: product.name,
          qty: product.qty,
          unit: product.unit,
          unitPrice: product.price,
          subTotal: product.qty * product.price,
        }));
      } else {
        console.error('No products found for the selected quotation');
      }

      // Fetch the related Agreement for the selected quotation
      try {
        const agreement = await fetchAgreementForQuotation(newQuotationId);
        if (agreement) {
          form.agreement_no = agreement.agreement_no || '';
          form.start_date = agreement.start_date;
          form.end_date = agreement.end_date;
        }
      } catch (error) {
        console.error('Error fetching agreement:', error);
      }

      // Calculate total (Now accessing the computed property value)
      const total = calculateTotal.value;

      // Get instalment paid (sum of paid invoices for the quotation)
      try {
        const paidInvoices = await fetchPaidInvoices(newQuotationId);
        const instalmentPaid = paidInvoices.reduce((sum, invoice) => sum + invoice.amount_paid, 0);

        console.log('Instalment Paid:', instalmentPaid);  // Debugging log
        form.instalmentPaid = instalmentPaid;
        form.grand_total = total - instalmentPaid; // Update grand total
      } catch (error) {
        console.error('Error fetching paid invoices:', error);  // Error logging
      }
    }
  }
});

// Function to fetch the related agreement based on the selected quotation
const fetchAgreementForQuotation = async (quotationId) => {
  try {
    const response = await fetch(`/quotations/${quotationId}/agreement`);
    const data = await response.json();
    console.log('Agreement data:', data);  // Debugging log
    return data;  // Return the agreement data
  } catch (error) {
    console.error('Error fetching agreement:', error);
    return null;  // Return null if error
  }
};


const fetchPaidInvoices = async (quotationId) => {
  try {
    const response = await fetch(`/quotations/${quotationId}/invoices?status=paid`);
    const data = await response.json();
    console.log('Paid Invoices:', data);  // Debugging log
    return data;
  } catch (error) {
    console.error('Error fetching paid invoices:', error);
    return [];  // Return empty array in case of error
  }
};

// Index template to show row numbers
const indexTemplate = (data, options) => options.rowIndex + 1;

// Action template for removing products
const actionTemplate = (data) => {
  return `<Button label="Remove" icon="pi pi-times" class="p-button-text p-button-danger" @click="removeProduct(${data.id})" />`;
};

// Add product to the products list
const addProduct = (productId) => {
  const product = products.find((prod) => prod.id === productId);
  if (product) {
    const newProduct = {
      id: product.id,
      product: product.name,
      qty: 1,
      unit: product.unit,
      unitPrice: product.price,
      subTotal: 1 * product.price,
    };
    productsList.value.push(newProduct);
    showProductModal.value = false;
  }
};

// Remove product from the products list
const removeProduct = (productId) => {
  productsList.value = productsList.value.filter((product) => product.id !== productId);
};

// Calculate the total of all products
const calculateTotal = computed(() => {
  return productsList.value.reduce((total, product) => total + product.subTotal, 0);
});

// Calculate the grand total, subtracting any instalment paid
const calculateGrandTotal = computed(() => {
  return calculateTotal.value - form.instalmentPaid;
});

// Update product subtotal when quantity is changed
const updateProductSubtotal = (product) => {
  product.subTotal = product.qty * product.unitPrice;
};

// Submit the invoice
const submitInvoice = () => {
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
    start_date: new Date(form.start_date).toISOString(),
    end_date: new Date(form.end_date).toISOString(),
    grand_total: grandTotal,
    status: form.status,
    products: productsList.value.map(product => ({
      id: product.id,
      quantity: product.qty,
    })),
  };

  const csrfToken = document.querySelector('meta[name="csrf_token"]').getAttribute('content');

  fetch('/invoices', {
    method: "POST",
    body: JSON.stringify(invoiceData),
    headers: {
      'X-CSRF-TOKEN': csrfToken,
      "Content-type": "application/json",
    },
  });
};

// Submit invoice and redirect
const submitInvoiceAndRedirect = () => {
  submitInvoice();
  Inertia.visit('/invoices');
};

// Cancel the form
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
