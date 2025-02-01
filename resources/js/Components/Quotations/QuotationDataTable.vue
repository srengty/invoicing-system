<template>
    <div>
      <DataTable :value="items" class="p-datatable-striped" responsiveLayout="scroll">
        <Column field="index" header="No." :body="indexTemplate"></Column>
        <Column field="item" header="ITEM" :body="itemTemplate"></Column>
        <Column field="qty" header="Qty"></Column>
        <Column field="unit" header="Unit"></Column>
        <Column field="unitPrice" header="Unit Price" :body="priceTemplate"></Column>
        <Column field="subTotal" header="SUB-TOTAL" :body="subTotalTemplate"></Column>
        <Column header="Action" :body="actionTemplate"></Column>
      </DataTable>      
      <!-- <Button label="Submit" @click="handleSubmit" /> -->
    </div>
  </template>

<script setup>
import { DataTable, Column, Button } from "primevue";
  // Reactive variable to hold items
const items = ref([]);

</script>
