<template>
  <Head title="Invoices" />
  <GuestLayout>
    <div class="invoices">
      <div class="flex justify-between items-center p-3">
        <h1 class="text-2xl">Invoices</h1>
        <div>
          <!-- Use Inertia to navigate to the Create page -->
          <Button icon="pi pi-plus" label="Create Invoice" rounded @click="navigateToCreate" />
          <ChooseColumns :columns="columns" v-model="selectedColumns" @apply="updateColumns" rounded />
        </div>
      </div>
      <div>
        <h1>Filter</h1>
        <div class="flex gap-2">
          <DatePicker v-model="filters.dates" placeholder="Date" />
          <InputText v-model="filters.invoice_no" placeholder="Invoice No" />
          <InputText v-model="filters.customer" placeholder="Customer" />
          <InputText v-model="filters.status" placeholder="Status" />
          <Button label="Search" icon="pi pi-search" @click="searchInvoices" />
          </div>
      </div>
      <DataTable :value="invoices.data" paginator :rows="5" :rowsPerPageOptions="[5, 10, 20, 50]">
        <!-- Dynamic Columns -->
        <Column
          v-for="col in showColumns"
          :key="col.field"
          :field="col.field"
          :header="col.header"
          sortable
        />
        <!-- Centered Action Column -->
        <Column header="Actions" headerStyle="text-align: center" bodyStyle="text-align: center">
          <template #body="{ data }">
            <div class="flex gap-2 justify-center">
              <Button
                icon="pi pi-print"
                class="p-button-info"
                :label="'Print'"
                @click="printInvoice(data.id)"
                rounded
              />
              <Button
                icon="pi pi-pencil"
                class="p-button-warning"
                :label="'Edit'"
                @click="editInvoice(data.id)"
                rounded
              />
              <Button
                icon="pi pi-trash"
                class="p-button-danger"
                :label="'Delete'"
                @click="deleteInvoice(data.id)"
                rounded
              />
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
import { Button, DataTable, Column, DatePicker, InputText } from 'primevue';
import ChooseColumns from '@/Components/ChooseColumns.vue';
import { ref } from 'vue';
import { Inertia } from '@inertiajs/inertia'; // Ensure Inertia is correctly imported

// Props passed from the server
defineProps({
  invoices: {
    type: Object,
    required: true,
  },
});
const filters = ref({
  dates: null,
  invoice_no: null,
  customer: null,
  status: null,
});

// Columns configuration
const columns = [
  { field: 'id', header: 'ID' },
  { field: 'date', header: 'Date' },
  { field: 'due_date', header: 'Due Date' },
  { field: 'customer.name', header: 'Customer' },
  { field: 'grand_total', header: 'Amount' },
  { field: 'amount_paid', header: 'Amount Paid' },
  { field: 'amount_due', header: 'Amount Due' },
  { field: 'invoice_no', header: 'Invoice No' },
  { field: 'over_due', header: 'Over due (days)' },
  { field: 'status', header: 'Status' },
];

const selectedColumns = ref(columns);
const showColumns = ref(columns);

// Update columns based on selected columns
const updateColumns = () => {
  showColumns.value = selectedColumns.value;
};

// Actions for editing and deleting
const editInvoice = (id) => {
  Inertia.visit(`/invoices/${id}/edit`);
};

const deleteInvoice = (id) => {
  if (confirm("Are you sure you want to delete this invoice?")) {
    Inertia.delete(`/invoices/${id}`);
  }
};

const printInvoice = (id) => {
  // Create a new window and load the invoice data for printing
  const invoiceUrl = `/invoices/${id}`; // Assuming you have a route that serves the printable view
  const printWindow = window.open(invoiceUrl, '_blank');
  
  // Wait for the document to load and trigger the print dialog
  printWindow.onload = () => {
    printWindow.print();
  };
};

// Navigate to Create Invoice page using Inertia
const navigateToCreate = () => {
  Inertia.visit('/invoices/create');
};
</script>

<style scoped>
.invoices {
  padding: 1rem;
}
</style>
