<template>
  <Head title="Invoices" />
  <GuestLayout>
    <div class="invoices">
      <div class="flex justify-between items-center p-3">
        <h1 class="text-2xl">Invoices</h1>
        <div class="flex gap-2">
          <Button icon="pi pi-plus" label="Create Invoice" rounded @click="navigateToCreate" />
          <ChooseColumns :columns="columns" v-model="selectedColumns" @apply="updateColumns" rounded />
        </div>
      </div>

      <!-- Filters -->
      <div class="mb-8 pl-3">
    <div class="text-xl mb-3">Filter by</div>
    
    <div class="grid gap-10 w-full grid-cols-4 text-lg mb-2">
      <!-- Invoice No. Start - Restrict to Numbers -->
      <div class="grid grid-cols-2">
        <label class="grid content-center" for="invoice_no_start">Invoice No. Start:</label>
        <InputText v-model="filters.invoice_no_start" placeholder="Input Invoice No Start" v-keyfilter="['num']" />
      </div>

      <!-- Invoice No. End - Restrict to Numbers -->
      <div class="grid grid-cols-2">
        <label class="grid content-center" for="invoice_no_end">Invoice No. End:</label>
        <InputText v-model="filters.invoice_no_end" placeholder="Input Invoice No End" v-keyfilter="['num']" />
      </div>

      <!-- Currency Dropdown -->
      <div class="grid grid-cols-2">
        <label class="grid content-center" for="currency">Currency:</label>
        <Dropdown v-model="filters.currency" :options="currencyOptions" placeholder="Select Currency" optionLabel="label" optionValue="value" />
      </div>
    </div>

    <div class="grid gap-10 w-full grid-cols-4 text-lg mb-2">
      <!-- Start Date -->
      <div class="grid grid-cols-2">
        <label class="grid content-center" for="start_date">Start Date:</label>
        <DatePicker v-model="filters.start_date" placeholder="Pick Start Date"/>
      </div>

      <!-- End Date -->
      <div class="grid grid-cols-2">
        <label class="grid content-center" for="end_date">End Date:</label>
        <DatePicker v-model="filters.end_date" placeholder="Pick End Date" />
      </div>

      <!-- Payment Status -->
      <div class="grid grid-cols-2">
        <label class="grid content-center" for="status">Payment Status:</label>
        <Dropdown v-model="filters.status" :options="statusOptions" placeholder="Select Status" optionLabel="label" optionValue="value" />
      </div>
    </div>

    <div class="grid gap-10 w-full grid-cols-4 text-lg">
      <!-- Customer Name - Restrict to Letters Only -->
      <div class="grid grid-cols-2">
        <label class="grid content-center" for="customer">Customer:</label>
        <InputText v-model="filters.customer" placeholder="Input Customer Name" v-keyfilter="['alpha']" />
      </div>

      <!-- Customer Type -->
      <div class="grid grid-cols-2">
        <label class="grid content-center" for="category_name_english">Customer Type:</label>
        <Dropdown v-model="filters.category_name_english" :options="customerTypeOptions" placeholder="Select Customer Type" optionLabel="label" optionValue="value" />
      </div>

      <!-- Income Type (No keyfilter as it's a dropdown) -->
      <div class="grid grid-cols-2">
        <label class="grid content-center" for="income_type">Income Type:</label>
        <Dropdown placeholder="Select Income Type" optionLabel="label" optionValue="value" />
      </div>

      <!-- Search & Clear Buttons -->
      <div class="grid gap-4 grid-cols-2">
        <Button label="Search" icon="pi pi-search" @click="searchInvoices" rounded/>
        <Button label="Clear" icon="pi pi-times" class="p-button-secondary" @click="clearFilters" rounded/>
      </div>
    </div>
  </div>


      <!-- Data Table -->
      <DataTable :value="invoices.data" paginator :rows="5" :rowsPerPageOptions="[5, 10, 20, 50]">
        <Column class="" v-for="col in showColumns" :key="col.field" :field="col.field" :header="col.header" sortable />

        <Column header="Amount Due">
          <template #body="{ data }">
            <template v-if="moment(data.due_date).isBefore(moment(), 'day')">
              {{ computeAmountDue(data) }}
            </template>
            <template v-else>
              Not past due
            </template>
          </template>
        </Column>

        <Column header="Overdue">
          <template #body="{ data }">
            {{ over_due(data) }} days ago
          </template>
        </Column>

        <!-- Actions -->
        <Column header="Actions" headerStyle="text-align: center" bodyStyle="text-align: center">
          <template #body="{ data }">
            <div class="flex gap-2 justify-center">
              <Button icon="pi pi-print" class="p-button-info" aria-label="Print" @click="printInvoice(data.invoice_no)" rounded />
              <Button icon="pi pi-pencil" class="p-button-warning" aria-label="Edit" @click="editInvoice(data.invoice_no)" rounded />
              <Button icon="pi pi-trash" class="p-button-danger" aria-label="Delete" @click="deleteInvoice(data.invoice_no)" rounded />
            </div>
          </template>
        </Column>
      </DataTable>
    </div>
  </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Button, DataTable, Column, DatePicker, InputText, Dropdown, } from 'primevue';
import KeyFilter from 'primevue/keyfilter';
import ChooseColumns from '@/Components/ChooseColumns.vue';
import { ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import moment from 'moment';
import Customers from '@/Components/Customers.vue';

// Props
defineProps({
  invoices: {
    type: Object,
    required: true,
  },
});

const storedFilters = localStorage.getItem("invoiceFilters");
const filters = ref(
  storedFilters
    ? JSON.parse(storedFilters)
    : {
        invoice_no_start: null,
        invoice_no_end: null,
        category_name_english: null,
        currency: null,
        start_date: null,
        end_date: null,
        customer: null,
        status: null,
        income_type: null,
      }
);

const customerTypeOptions = ref([
  { label: 'Individual', value: 'Individual' },
  { label: 'Public Organization', value: 'Public Organization' },
  { label: 'NGO', value: 'NGO' },
  { label: 'Private Company', value: 'Private Company' },
]);

const currencyOptions = ref([
  { label: 'USD', value: 'USD' },
  { label: 'KHR', value: 'KHR' },
]);

const columns = [
  { field: 'invoice_no', header: 'Invoice No' },
  { field: 'date', header: 'Date' },
  { field: 'due_date', header: 'Due Date' },
  { field: 'customer.name', header: 'Customer' },
  { field: 'grand_total', header: 'Amount' },
  { field: 'agreement.amount', header: 'Amount Paid' },
  { field: 'status', header: 'Status' },
];

const selectedColumns = ref(columns.slice());
const showColumns = ref(columns);

const statusOptions = ref([
  { label: 'Paid', value: 'Paid' },
  { label: 'Pending', value: 'Pending' },
  { label: 'Cancelled', value: 'Cancelled' },
]);

const updateColumns = () => {
  showColumns.value = selectedColumns.value;
};

const navigateToCreate = () => {
  Inertia.visit('/invoices/create');
};

const editInvoice = (id) => {
  Inertia.visit(`/invoices/${id}/edit`);
};

const deleteInvoice = (id) => {
  if (confirm("Are you sure you want to delete this invoice?")) {
    Inertia.delete(`/invoices/${id}`);
  }
};

const over_due = (rowData) => {
  if (!rowData.due_date) return '-';
  const dueDate = moment(rowData.due_date);
  const currentDate = moment();
  const overdue = currentDate.diff(dueDate, 'days');
  return overdue > 0 ? overdue : 0;
};

const printInvoice = (id) => {
  const invoiceUrl = `/invoices/${id}`;
  const printWindow = window.open(invoiceUrl, '_blank');
  setTimeout(() => {
    printWindow.print();
  }, 1000);
};

const searchInvoices = () => {
  const formattedStartDate = filters.value.start_date
    ? moment(filters.value.start_date).format('YYYY-MM-DD')
    : null;
  const formattedEndDate = filters.value.end_date
    ? moment(filters.value.end_date).format('YYYY-MM-DD')
    : null;

  // Create an object with all filter values
  const invoiceFilters = {
    invoice_no_start: filters.value.invoice_no_start,
    invoice_no_end: filters.value.invoice_no_end,
    category_name_english: filters.value.category_name_english,
    currency: filters.value.currency,
    start_date: formattedStartDate,
    end_date: formattedEndDate,
    customer: filters.value.customer,
    status: filters.value.status,
    income_type: filters.value.income_type,
  };

  // Save all filter values to localStorage as a JSON string
  localStorage.setItem("invoiceFilters", JSON.stringify(invoiceFilters));

  Inertia.get('/invoices', invoiceFilters);
};

const clearFilters = () => {
  filters.value = {
    invoice_no_start: null,
    invoice_no_end: null,
    category_name_english: null, // Changed from customer_type to category_name_english
    currency: null,
    start_date: null,
    end_date: null,
    customer: null,
    status: null,
  };
  searchInvoices();
};

const computeAmountDue = (invoice) => {
  const grandTotal = invoice.grand_total || 0;
  const amountPaid = invoice.agreement?.amount || 0;
  return (grandTotal - amountPaid).toFixed(2);
};
</script>

<style scoped>
.invoices {
  padding: 1rem;
}
</style>