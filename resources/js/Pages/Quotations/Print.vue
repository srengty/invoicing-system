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


    <DataTable v-if="quotation.products && quotation.products.length > 0" :value="quotation.products" class="">
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

    <p class="pt-6"><strong>Total: </strong>${{ parseFloat(quotation.total).toFixed(2) }}</p>  
  </div>
  
  <div class="flex flex-row justify-center"><Button label="Print" icon="pi pi-print" @click="printPage" class="mt-4"/></div>
</template>

<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import { usePage } from "@inertiajs/vue3";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Button from "primevue/button";
import Image from 'primevue/image';

const { props } = usePage();
const quotation = ref(props.quotation);

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
