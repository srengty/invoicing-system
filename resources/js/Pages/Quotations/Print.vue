<template>
  <Head title="Quotations Printing" />
  <div ref="printArea" class="flex flex-col justify-center">
    <div class="flex flex-row m-4">
        <Image src="https://itc.edu.kh/wp-content/uploads/2021/02/cropped-Logo-ITC.png" alt="Image" width="120" />
    </div>
    <h1 class="flex flex-row justify-center text-3xl font-bold pb-7">Quotation</h1>
    <div class="flex flex-row justify-between mb-4">
      <div class="flex flex-col w-1/2 gap-4">
        <p><strong>Customer Name:</strong> {{ quotation.customer?.name || 'N/A' }}</p>
        <p><strong>Address:</strong> {{ quotation.address }}</p>
        <p><strong>Phone Number:</strong> {{ quotation.phone_number }}</p>
      </div>
      <div class="flex flex-col w-1/2 items-end gap-4">
        <p><strong>Quotation No.:</strong> {{ quotation.quotation_no }}</p>
        <p><strong>Quotation Date:</strong> {{ quotation.quotation_date }}</p>
      </div>
    </div>
      <!-- Print Section -->
      <div ref="printTable" class="print-area">
          <DataTable v-if="quotation.products && quotation.products.length > 0" :value="quotation.products">
              <Column header="Order No.">
                  <template #body="slotProps">
                      {{ slotProps.index + 1 }}
                  </template>
              </Column>
              <Column field="name" header="ITEM" />
              <Column field="pivot.quantity" header="QTY" />
              <Column field="price" header="Unit Price">
                  <template #body="slotProps">
                      ${{ parseFloat(slotProps.data.price).toFixed(2) }}
                  </template>
              </Column>
              <Column header="Sub-Total">
                  <template #body="slotProps">
                      ${{ (parseFloat(slotProps.data.price) * slotProps.data.pivot.quantity).toFixed(2) }}
                  </template>
              </Column>
          </DataTable>

          <!-- Total Amount -->
          <p class="pt-6 flex justify-end">Total (USD/KHR): ${{ parseFloat(quotation.total).toFixed(2) }}</p>

          <!-- Terms and Conditions -->
          <div class="mt-6">
              <p class="font-bold">Terms and Conditions</p>
              <div class="w-full border rounded-md p-3 text-sm bg-gray-100 text-gray-800 overflow-y-auto max-h-32">
                  {{ quotation.terms }}
              </div>
          </div>

          <!-- Customer Acceptance & Authorization -->
          <div class="mt-6 flex justify-between text-sm">
              <div>
                  <p class="font-bold">Customer Acceptance</p>
                  <p class="text-xs italic">I hereby accept the quotation and agree on Terms and Conditions.</p>
                  <div class="mt-3">
                      <p><strong>Date:</strong> {{ quotation.quotation_date || '___________' }}</p>
                  </div>
              </div>
              <div class="text-right">
                  <p class="font-bold">Authorized by</p>
                  <div class="mt-5">
                      <p> {{ quotation.customer?.name || '___________' }}</p>
                  </div>
              </div>
          </div>
      </div>
  </div>
    <!-- Print Button -->
  <div class="flex flex-row justify-center"><Button label="Print" icon="pi pi-print" @click="printPage" class="mt-4"/></div>
</template>

<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import { usePage } from "@inertiajs/vue3";
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from "primevue/button";
import Image from 'primevue/image';

const { props } = usePage();
const quotation = ref(props.quotation);
const printTable = ref(null);
const printArea = ref(null);


// Store the original title
const originalTitle = ref(document.title);

// Print Page function
const printPage = () => {
  window.print();

};

</script>

<style scoped>
.print-container {
    padding: 20px;
    font-family: Arial, sans-serif;
}

@media print {
    .mt-4 {
        display: none; /* Hide the Print button when printing */
    }
}
</style>
