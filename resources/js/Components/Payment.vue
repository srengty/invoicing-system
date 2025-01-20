<template>
  <div class="m-6">
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
    </div>

    <div class="total-container mt-4 flex justify-between">
      <p class="font-bold">Total</p>
      <p class="font-bold">{{ calculateTotal }}</p>
    </div>

    <div class="grand-total-container flex justify-between">
      <p class="font-bold text-lg">Grand Total</p>
      <p class="font-bold text-lg">{{ calculateTotal }}</p>
    </div>

    <div class="terms mt-4">
      <h3 class="text-lg">Terms and Conditions</h3>
      <p>Full payment is required upon quote acceptance.</p>
      <p>This quote is negotiable for one (1) week from the date stated above.</p>
    </div>

    <div class="buttons mt-4 flex justify-end">
      <Button label="Submit request for approval" icon="pi pi-check" class="p-button-rounded p-button-success" />
      <Button label="Cancel" class="p-button-rounded p-button-secondary ml-2" />
    </div>
  </div>
</template>
  
<script setup>
import { ref, computed } from "vue"; // Import computed
import { DataTable, Column, Button } from "primevue";

// Mock data
const items = ref([
  {
    id: 1,
    item: "Item 2",
    description: "Item description",
    remark: "Additional remark",
    qty: 1,
    unit: "Device",
    unitPrice: 2000,
    subTotal: 2000,
  },
  {
    id: 2,
    item: "Item 3",
    description: "Item description",
    remark: "Additional remark",
    qty: 5,
    unit: "Kg",
    unitPrice: 1000,
    subTotal: 5000,
  },
  {
    id: 3,
    item: "Item 4",
    description: "Item description",
    remark: "Additional remark",
    qty: 2,
    unit: "Pcs",
    unitPrice: 1500,
    subTotal: 3000,
  },
]);

// Index column template
const indexTemplate = (data, options) => options.rowIndex + 1;

// Item column template
const itemTemplate = (data) =>
  `${data.item}\n${data.description}\n${data.remark}`;

// Subtotal column template
const subTotalTemplate = (data) => `₹${data.subTotal.toLocaleString()}`;

// Price template
const priceTemplate = (data) => `₹${data.unitPrice.toLocaleString()}`;

// Action template (Add and Delete buttons)
const actionTemplate = (data) => {
  return `
    <Button label="Edit" icon="pi pi-pencil" class="p-button-text p-button-sm p-button-info mr-2" />
    <Button label="Catalog" icon="pi pi-book" class="p-button-text p-button-sm p-button-success mr-2" />
    <Button label="Add" icon="pi pi-plus" class="p-button-text p-button-sm p-button-success mr-2" @click="addItem(data)" />
    <Button label="Delete" icon="pi pi-trash" class="p-button-text p-button-sm p-button-danger" @click="deleteItem(data.id)" />
  `;
};

// Add item function
const addItem = (data) => {
  // Logic to add an item to the table (can be customized based on needs)
  const newItem = {
    id: Date.now(),
    item: "New Item",
    description: "New item description",
    remark: "New remark",
    qty: 1,
    unit: "Pcs",
    unitPrice: 1000,
    subTotal: 1000,
  };
  items.value.push(newItem);
};

// Delete item function
const deleteItem = (id) => {
  // Logic to remove an item based on its ID
  items.value = items.value.filter(item => item.id !== id);
};

// Calculate total
const calculateTotal = computed(() =>
  items.value.reduce((sum, item) => sum + item.subTotal, 0)
);
</script>
  
  <style>
  .total-container,
  .grand-total-container {
    padding: 1rem 0;
  }
  .terms {
    border: 1px solid #ddd;
    padding: 1rem;
    border-radius: 8px;
  }
  .buttons {
    margin-top: 2rem;
  }
  </style>
  