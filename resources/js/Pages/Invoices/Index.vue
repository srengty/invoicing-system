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
                icon="pi pi-pencil"
                class="p-button-warning p-button-text"
                :label="'Edit'"
                @click="editInvoice(data.id)"
              />
              <Button
                icon="pi pi-trash"
                class="p-button-danger p-button-text"
                :label="'Delete'"
                @click="deleteInvoice(data.id)"
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
import { Button, DataTable, Column } from 'primevue';
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

// Columns configuration
const columns = [
  { field: 'id', header: 'ID' },
  { field: 'invoice_no', header: 'Invoice No' },
  { field: 'customer.name', header: 'Customer' },
  { field: 'grand_total', header: 'Grand total' },
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
