<template>
    <Head title="Invoice Details" />
      <div class="invoice-view">
        <!-- Header Section -->
        
        <!-- Invoice Details -->
        <div class="p-3 grid grid-cols-2 gap-4 ml-4 mr-4 mt-4">
          <div>
            <label class="block text-lg font-medium">Invoice No</label>
            <p>{{ invoice.invoice_no }}</p>
          </div>
          <div>
            <label class="block text-lg font-medium justify-self-end">Customer</label>
            <p class="justify-self-end">{{ invoice.customer_name }}</p>
          </div>
          <div>
            <label class="block text-lg font-medium">Agreement No</label>
            <p>{{ invoice.agreement_no }}</p>
          </div>
          <div>
            <label class="block text-lg font-medium justify-self-end">Quotation No</label>
            <p class="justify-self-end">{{ invoice.quotation_no }}</p>
          </div>
          <div>
            <label class="block text-lg font-medium">Status</label>
            <p>{{ invoice.status }}</p>
          </div>
          <div>
            <label class="block text-lg font-medium justify-self-end">Address</label>
            <p class="justify-self-end">{{ invoice.address }}</p>
          </div>
          <div>
            <label class="block text-lg font-medium">Phone</label>
            <p>{{ invoice.phone }}</p>
          </div>
          <div>
            <label class="block text-lg font-medium justify-self-end">Start Date</label>
            <p class="justify-self-end">{{ invoice.start_date }}</p>
          </div>
          <div>
            <label class="block text-lg font-medium ">End Date</label>
            <p>{{ invoice.end_date }}</p>
          </div>
        </div>

        <div class="flex items-center justify-center w-full">
          <h1 class="text-2xl">Invoice</h1>
        </div>
        
        <!-- Product Table Section -->
        <div class="m-6">
          <DataTable :value="invoice.products" class="p-datatable-striped" responsiveLayout="scroll">
            <Column field="index" header="No." :body="indexTemplate"></Column>
            <Column field="product" header="Product"></Column>
            <Column field="qty" header="Qty"></Column>
            <Column field="unit" header="Unit"></Column>
            <Column field="unitPrice" header="Unit Price"></Column>
            <Column field="subTotal" header="Sub-Total"></Column>
          </DataTable>
          
          <div class="total-container mt-4 flex justify-between">
            <p class="font-bold">Total</p>
            <p class="font-bold">{{ invoice.total }}</p>
          </div>
          
          <div class="grand-total-container flex justify-between">
            <p class="font-bold text-lg">Grand Total</p>
            <p class="font-bold text-lg">{{ invoice.grand_total }}</p>
          </div>
        </div>
        
        <!-- Terms and Conditions -->
        <div class="terms mt-4">
          <h3 class="text-lg">Terms and Conditions</h3>
          <p>Full payment is required upon quote acceptance.</p>
          <p>This quote is negotiable for one (1) week from the date stated above.</p>
        </div>
      </div>
  </template>
  
  <script setup>
  import { usePage } from '@inertiajs/vue3';
  import GuestLayout from '@/Layouts/GuestLayout.vue';
  import { Head } from '@inertiajs/vue3';
  import { ref } from 'vue';
  import { Button, DataTable, Column } from 'primevue';
  
  const { invoice } = usePage().props;
  
  const indexTemplate = (data, options) => options.rowIndex + 1;
  
  const goBack = () => {
    window.history.back();
  };
  </script>
  
  <style scoped>
  .total-container,
  .grand-total-container {
    padding: 1rem 0;
  }
  </style>  