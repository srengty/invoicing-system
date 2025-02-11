<template>
  <Head title="Invoices" />
  <GuestLayout>
    <div class="invoices">
      <div class="flex justify-between items-center p-3">
        <h1 class="text-2xl">Invoices</h1>
        <div>
          <Button icon="pi pi-plus" label="Create Invoice" rounded @click="navigateToCreate" />
          <ChooseColumns :columns="columns" v-model="selectedColumns" @apply="updateColumns" rounded />
        </div>
      </div>

      <!-- Filters -->
      <div class="mb-8 pl-3 ">
        <div class="text-xl mb-3">Filter by</div>
        <div class="flex gap-2 w-full">
          <DatePicker v-model="filters.start_date" placeholder="Start Date" />
          <DatePicker v-model="filters.end_date" placeholder="End Date" />
          <InputText v-model="filters.invoice_no" placeholder="Invoice No" />
          <InputText v-model="filters.customer" placeholder="Customer" />
          <Dropdown 
            v-model="filters.status" 
            :options="statusOptions" 
            placeholder="Select Status" 
            optionLabel="label" 
            optionValue="value" 
          />
          <Button label="Search" icon="pi pi-search" @click="searchInvoices" />
          <Button label="Clear Filters" icon="pi pi-times" class="p-button-secondary" @click="clearFilters" />
        </div>
      </div>

      <!-- Data Table -->
      <DataTable :value="invoices.data" paginator :rows="5" :rowsPerPageOptions="[5, 10, 20, 50]">
        <Column v-for="col in showColumns" :key="col.field" :field="col.field" :header="col.header" sortable />

        <Column header="Amount Due">
          <template #body="{ data }">
            <template v-if="moment(data.due_date).isBefore(moment(), 'day')">
              {{ computeAmountDue(data) }}
            </template>
            <template v-else>
              Not pass due
            </template>
          </template>
        </Column>
        <!-- Overdue Days -->
        <Column header="Over due">
          <template #body="{ data }">
            {{ over_due(data) }} days ago
          </template>
        </Column>

        <!-- Actions -->
        <Column header="Actions" headerStyle="text-align: center" bodyStyle="text-align: center">
          <template #body="{ data }">
            <div class="flex gap-2 justify-center">
              <Button icon="pi pi-print" class="p-button-info" label="Print" @click="printInvoice(data.invoice_no)" rounded />
              <Button icon="pi pi-pencil" class="p-button-warning" label="Edit" @click="editInvoice(data.invoice_no)" rounded />
              <Button icon="pi pi-trash" class="p-button-danger" label="Delete" @click="deleteInvoice(data.invoice_no)" rounded />
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
import { Button, DataTable, Column, DatePicker, InputText, Dropdown } from 'primevue';
import ChooseColumns from '@/Components/ChooseColumns.vue';
import { ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import moment from 'moment';

// Props
defineProps({
  invoices: {
    type: Object,
    required: true,
  },
});

// Filters
const filters = ref({
  start_date: null,
  end_date: null,
  invoice_no: null,
  customer: null,
  status: null,
});

const columns = [
  { field: 'invoice_no', header: 'Invoice No' },
  { field: 'date', header: 'Date' },
  { field: 'due_date', header: 'Due Date' },
  { field: 'customer.name', header: 'Customer' },
  { field: 'grand_total', header: 'Amount' },
  { field: 'agreement.amount', header: 'Amount Paid' },
  { field: 'status', header: 'Status' },
];

const selectedColumns = ref(columns);
const showColumns = ref(columns);

const statusOptions = ref([
  { label: 'Paid', value: 'Paid' },
  { label: 'Pending', value: 'Pending' },
  { label: 'Cancelled', value: 'Cancelled' },
]);

// Update columns dynamically
const updateColumns = () => {
  showColumns.value = selectedColumns.value;
};

// Navigate to create invoice
const navigateToCreate = () => {
  Inertia.visit('/invoices/create');
};

// Edit invoice
const editInvoice = (id) => {
  Inertia.visit(`/invoices/${id}/edit`);
};

// Delete invoice
const deleteInvoice = (id) => {
  if (confirm("Are you sure you want to delete this invoice?")) {
    Inertia.delete(`/invoices/${id}`);
  }
};

// Calculate overdue days
const over_due = (rowData) => {
  if (!rowData.due_date) return '-';
  const dueDate = moment(rowData.due_date);
  const currentDate = moment();
  const overdue = currentDate.diff(dueDate, 'days');
  return overdue > 0 ? overdue : 0;
};

// Print invoice
const printInvoice = (id) => {
  const invoiceUrl = `/invoices/${id}`;
  const printWindow = window.open(invoiceUrl, '_blank');
  printWindow.onload = () => {
    printWindow.print();
  };
};

// Search invoices based on filters
const searchInvoices = () => {
  const formattedStartDate = filters.value.start_date ? moment(filters.value.start_date).format('YYYY-MM-DD') : null;
  const formattedEndDate = filters.value.end_date ? moment(filters.value.end_date).format('YYYY-MM-DD') : null;

  // Send all the filters to the backend
  Inertia.get('/invoices', {
    invoice_no: filters.value.invoice_no,
    customer: filters.value.customer,
    status: filters.value.status,
    start_date: formattedStartDate, 
    end_date: formattedEndDate, 
  });
};

// Clear all filters
const clearFilters = () => {
  filters.value = {
    start_date: null,
    end_date: null,
    invoice_no: null,
    customer: null,
    status: null,
  };
  searchInvoices(); // Optionally, reset to initial state
};

const computeAmountDue = (invoice) => {
  const grandTotal = invoice.grand_total || 0;
  const amountPaid = invoice.agreement?.amount || 0;

  // Calculate the due amount
  const amountDue = grandTotal - amountPaid;

  // Return the formatted amount with 3 decimal places
  return amountDue.toFixed(2);
};

</script>

<style scoped>
.invoices {
  padding: 1rem;
}
</style>
