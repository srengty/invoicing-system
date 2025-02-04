<template>
    <div ref="printArea" class="print-container">
        <div class="details">
            <div>
                <label class="block text-lg font-medium">Invoice No</label>
                <p>{{ invoice.invoice_no }}</p>
            </div>
            <p><strong>ID:</strong> {{ quotations.id }}</p>
            <!-- <p><strong>Quotation No.:</strong> {{ quotations.quotation_no }}</p>
            <p><strong>Quotation Date:</strong> {{ quotations.quotation_date }}</p>
            <p><strong>Customer Name:</strong> {{ quotations.customer?.name || 'N/A' }}</p>
            <p><strong>Address:</strong> {{ quotations.address }}</p>
            <p><strong>Phone Number:</strong> {{ quotations.phone_number }}</p> -->
        </div>


<!--   
      <h3>Items:</h3>
      <table>
        <thead>
          <tr>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Price</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="product in quotation.products" :key="product.id">
            <td>{{ product.name }}</td>
            <td>{{ product.pivot.quantity }}</td>
            <td>${{ parseFloat(product.price).toFixed(2) }}</td>
          </tr>
        </tbody>
      </table> -->
  
      <h3>Grand Total: ${{ parseFloat(quotations.grand_total).toFixed(2) }}</h3>
    </div>
  </template>

<script setup>
import { Head, Link } from "@inertiajs/vue3";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Button from "primevue/button";

  const props = defineProps({
    customers: Array,
    products: Array,
    quotations: Array,
    });
</script>

