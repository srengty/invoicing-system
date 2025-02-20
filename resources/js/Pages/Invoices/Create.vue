<template>
  <meta name="_token" content="{{ csrf_token() }}" />
  <Head title="Create Invoice" />
  <GuestLayout>
    <div class="create-invoice">
      <!-- Header Section with Buttons -->
      <div class="flex justify-between items-center p-3 mr-4">
        <h1 class="text-2xl">Create Invoice</h1>
        <div class="flex gap-4">
          <Button
            label="Add Product"
            icon="pi pi-plus"
            class="p-button-success"
            type="button"
            @click="showProductModal = true"
            rounded
          />
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
              optionValue="agreement_no"
              placeholder="Select Agreement"
              class="w-full"
              required
            />
          </div>
          <div>
            <label for="deposit_no" class="block text-lg font-medium">Receipt No (for deposit)</label>
            <InputText
              id="deposit_no"
              v-model="form.deposit_no"
              class="w-1/2"
              placeholder="Enter deposit number"
              required
            />
            <Button class="w-1/3 ml-4 p-button-info" rounded>Add Receipt</Button>
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
          <Column field="index" header="No."></Column>
          <Column field="product" header="Product"></Column>
          <Column field="qty" header="Qty">
            <template #body="slotProps">
              <InputText v-model="slotProps.data.qty" @input="updateProductSubtotal(slotProps.data)" class="w-full" />
            </template>
          </Column>
          <Column field="unit" header="Unit"></Column>
          <Column field="unitPrice" header="Unit Price"></Column>
          <Column field="subTotal" header="Sub Total"></Column>
          <Column header="Action">
            <template #body="slotProps">
              <Button label="Remove" icon="pi pi-times" class="p-button-text p-button-danger" @click="removeProduct(slotProps.data.id)" />
            </template>
          </Column>
        </DataTable>

        <div class="total-container mt-4 flex justify-between">
          <p class="font-bold">Total</p>
          <p class="font-bold">{{ calculateTotal }}</p>
        </div>
        <div class="grand-total-container flex justify-between">
          <p class="font-bold text-lg">Grand Total</p>
          <p class="font-bold text-lg">{{ calculateGrandTotal }}</p>
        </div>
        <div class="flex justify-between mt-4">
          <p class="font-bold">Instalment Paid</p>
          <p class="font-bold">{{ form.instalmentPaid }}</p>
        </div>
        <div class="terms mt-4">
          <h3 class="text-lg">Terms and Conditions</h3>
          <p>Full payment is required upon quote acceptance.</p>
          <p>This quote is negotiable for one (1) week from the date stated above.</p>
        </div>
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

const { products, agreements, quotations, customers, product_quotations } = usePage().props;

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
  grand_total: 0,
  instalmentPaid: 0,
  status: '',
  productQuotations:[], 
});

const statusOptions = [
  { label: 'Pending', value: 'Pending' },
  { label: 'Approved', value: 'Approved' },
  { label: 'Revise', value: 'Revise' },
];

const productsList = ref([]);
const showProductModal = ref(false);

watch(() => form.quotation_no, (newQuotationId) => {
  if (newQuotationId) {
    const selectedQuotation = quotations.find(q => q.quotation_no === newQuotationId);
    if (selectedQuotation) {
      console.log('Selected Quotation:', selectedQuotation); // Debugging log

      // Auto-fill customer, address, phone, and status
      form.customer_id = selectedQuotation.customer_id || '';
      form.address = selectedQuotation.address || '';
      form.phone = selectedQuotation.phone_number || '';
      form.status = selectedQuotation.status || '';
      
      // Auto-fill agreement details if available
      form.agreement_no = selectedQuotation.agreement?.agreement_no || '';
      form.start_date = selectedQuotation.agreement?.start_date || '';
      form.end_date = selectedQuotation.agreement?.end_date || '';

      // Ensure `productQuotations` exists and contains products
      if (Array.isArray(selectedQuotation.product_quotations) && selectedQuotation.product_quotations.length > 0) {
        productsList.value = selectedQuotation.product_quotations.map((pq, index) => {
          return {
            index: index + 1, // Auto-numbering for DataTable
            product: pq.name || 'Unknown Product', // Get product name safely
            qty: pq.quantity || 1, // Default quantity to 1 if missing
            unit: pq.unit || 'Unit', // Ensure unit is defined
            unitPrice: pq.price || 0, // Default price to 0 if missing
            subTotal: (pq.quantity || 1) * (pq.price || 0), // Calculate subtotal safely
          };
        });
      } else {
        productsList.value = [];
      }

      console.log('Updated DataTable Products:', productsList.value); // Debugging log
    }
  }
}, { deep: true });


const indexTemplate = (rowData, { index }) => {
  return index + 1; // Return the index + 1 for 1-based index display
};

const calculateTotal = computed(() => {
  return productsList.value.reduce((acc, product) => acc + product.subTotal, 0);
});

const calculateGrandTotal = computed(() => {
  return calculateTotal.value - form.instalmentPaid;
});

const actionTemplate = (data) => {
  return {
    template: `
      <Button label="Remove" icon="pi pi-times" class="p-button-text p-button-danger" @click="removeProduct(${data.id})" />
    `
  };
};

const addProduct = (productId) => {
  const product = products.find((prod) => prod.id === productId);
  if (product) {
    const newProduct = {
      id: product.id,
      product: product.name,
      qty: 1,
      unit: product.unit,
      unitPrice: product.price,
      subTotal: product.price,
    };
    productsList.value.push(newProduct);
    showProductModal.value = false;
  }
};

const removeProduct = (productId) => {
  productsList.value = productsList.value.filter((product) => product.id !== productId);
};

const updateProductSubtotal = (product) => {
  product.subTotal = product.qty * product.unitPrice;
};

const cancel = () => {
  form.reset();
  productsList.value = [];
};

const submitInvoice = async () => {
  if (productsList.value.length === 0) {
    alert('Please add at least one product.');
    return;
  }

  const invoiceData = {
    invoice_no: form.invoice_no,
    agreement_no: form.agreement_no,
    quotation_no: form.quotation_no,
    customer_id: form.customer_id,
    address: form.address,
    phone: form.phone,
    start_date: form.start_date,
    end_date: form.end_date,
    grand_total: form.grand_total,
    status: form.status,
    products: productsList.value.map(product => ({
      id: product.id,
      quantity: product.qty,
    })),
  };

  const csrfToken = document.querySelector('meta[name="csrf_token"]').getAttribute('content');

  try {
    const response = await fetch('/invoices', {
      method: "POST",
      body: JSON.stringify(invoiceData),
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        "Content-type": "application/json",
      },
    });

    if (!response.ok) {
      console.error('Failed to submit invoice:', response.statusText);
      return;
    }

    Inertia.visit('/invoices'); // Redirect after submission
  } catch (error) {
    console.error('Error submitting invoice:', error);
  }
};

const submitInvoiceAndRedirect = () => {
  submitInvoice();
  Inertia.visit('/invoices');
};
</script>