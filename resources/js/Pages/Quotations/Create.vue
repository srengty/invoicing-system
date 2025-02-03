<template>
    <Head title="Create Quotation" />
      <GuestLayout>
        <Toast position="top-center" group="tc" />
        <Toast position="top-right" group="tr" />
    
        <!-- Use the PrimeVue Form wrapper (with @submit.prevent) -->
        <Form @submit.prevent="submit">
          <!-- Quotation Info -->
          <div class="p-4 grid md:grid-cols-2 gap-0 w-2/3">
        
            <div class="p-4 grid md:grid-rows-2 gap-4">
              <div class="flex flex-col gap-2">
                <label for="quotation_no">Quotation No:</label>
                <InputText
                  id="quotation_no"
                  v-model="form.quotation_no"
                  placeholder="Input"
                  class="w-full md:w-60"
                />
              </div>
              <div class="flex flex-col gap-2">
                <label for="quotation_date">Date:</label>
                <DatePicker
                  v-model="form.quotation_date"
                  showIcon
                  fluid
                  iconDisplay="input"
                  inputId="quotation_date"
                  placeholder="Select"
                  class="w-full md:w-60"
                />
              </div>
            </div>
      
            <div class="p-4 grid md:grid-rows-2 gap-4">
              <div class="flex flex-col gap-2">
                <label for="address">Address</label>
                <IconField class="w-full md:w-60">
                  <InputText
                    id="address"
                    v-model="form.address"
                    placeholder="Input"
                    class="w-full md:w-60"
                  />
                  <InputIcon class="pi pi-times-circle" />
                </IconField>
              </div>
              <div class="flex flex-col gap-2">
                <label for="phone_number">Contact</label>
                <IconField class="w-full md:w-60">
                  <InputText
                    id="phone_number"
                    v-model="form.phone_number"
                    placeholder="Input"
                    class="w-full md:w-60"
                  />
                  <InputIcon class="pi pi-times-circle" />
                </IconField>
              </div>
            </div>
          </div>
    
          <!-- Customer & Product Selection -->
          <div class="p-8 grid md:grid-rows-2 gap-4">
        
            <div class="flex flex-row gap-4 items-end w-1/3">
              <div class="flex flex-col gap-2 w-full">
                <label for="customer_id">Customer/Organization</label>
                <Select
                  v-model="form.customer_id"
                  :options="formattedCustomers"
                  optionLabel="label"
                  optionValue="id"
                  id="customer_id"
                  placeholder="Select a customer"
                  class="w-full md:w-65"
                />
              </div>
              <div class="w-60">
                <!-- <Link :href="route('customers.create')">
                  <Button icon="pi pi-plus" label="Add customer" rounded />
                </Link> -->
                <Button icon="pi pi-plus" label="Add customer" rounded @click="isCreateCustomerVisible=true" />
              </div>
            </div>
    
            <div class="flex flex-row gap-4 items-end w-1/3">
              <div class="flex flex-col gap-2 w-full">
                <label for="p_name">Item</label>
                <MultiSelect
                  v-model="selectedProductIds"
                  :options="products"
                  optionLabel="name"
                  optionValue="id"
                  placeholder="Select Product"
                  class="w-full md:w-65"
                />
              </div>
              <div class="w-60">
                <!-- <Link :href="route('products.store')">
                  <Button icon="pi pi-plus" label="Add Item" rounded />
                </Link> -->
                <Button icon="pi pi-plus" label="Add Item" rounded @click="isCreateItemVisible"/>
              </div>
            </div>
          </div>
    
          <!-- Selected Products Table -->
          <div class="pl-6">
            <DataTable
              :value="selectedProductsData"
              paginator
              :rows="5"
              :rowsPerPageOptions="[5, 10, 20, 50]"
              striped
            >
              <!-- Dynamically render columns (id, name, unit, price) -->
              <Column
                v-for="col in columns"
                :key="col.field"
                :field="col.field"
                :header="col.header"
              />
              <!-- Quantity Column with an input -->
              <Column field="quantity" header="Qty">
                <template #body="slotProps">
                  <InputText
                    v-model="slotProps.data.quanity"
                    @input="updateProductSubtotal(slotProps.data)"
                    class="w-full"
                  />
                </template>
              </Column>
              <!-- Subtotal Column -->
              <Column field="subTotal" header="SUB-TOTAL">
                <template #body="slotProps">
                  <span>{{ slotProps.data.subTotal.toFixed(2) }}</span>
                </template>
              </Column>
              <!-- Actions Column (Edit/Remove) -->
              <Column header="Actions">
                <template #body="slotProps">
                  <div class="flex gap-2 items-center">
                    <!-- For example, a Remove button -->
                    <Button
                      icon="pi pi-trash"
                      class="p-button-danger"
                      label="Remove"
                      @click="removeProduct(slotProps.data.id)"
                      rounded
                    />
                  </div>
                </template>
              </Column>
            </DataTable>
    
            <!-- Totals Summary -->
            <div class="pl-2 pr-60">
              <div class="total-container mt-4 flex justify-between">
                <p class="font-bold">Total</p>
                <p class="font-bold">{{ calculateTotal.toFixed(2) }}</p>
              </div>
              <div class="grand-total-container flex justify-between">
                  <p class="font-bold text-lg">Grand Total</p>
                  <p class="font-bold text-lg">{{ calculateGrandTotal.toFixed(2) }}</p>
                </div>
              <div class="mt-2 flex justify-between items-center">
                <!-- Tax -->
                <!-- <div>
                  <label for="tax" class="font-bold">Tax</label>
                  <InputText
                    id="tax"
                    v-model="form.tax"
                    placeholder="0"
                    class="w-24 ml-2"
                    @input="updateTax"
                  />
                </div> -->

              </div>
            </div>
          </div>
    
          <!-- Form Buttons -->
          <div class="buttons mt-4 mr-4 flex justify-end">
            <Button
              v-ripple
              label="Submit"
              icon="pi pi-check"
              type="submit"
              class="p-button-rounded p-button-success"
              @click="submit"
            />
            <Button
              v-ripple
              label="Cancel"
              class="p-button-rounded p-button-secondary ml-2"
            />
          </div>

        </Form>
      </GuestLayout>
    <Dialog v-model:visible="isCreateCustomerVisible" modal header="Add Customer" class="w-1/2">
      <Customers redirect_route="quotations.create" @success="selectCustomer"></Customers>
    </Dialog>
    <Dialog v-model:visible="isCreateItemVisible" modal header="Add Item" class="w-1/2">
      <Customers redirect_route="quotations.create"></Customers>
    </Dialog>
  </template>

<script setup>
import { ref, computed, watch } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import { useForm } from "@inertiajs/vue3";
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Select from "primevue/select";
import MultiSelect from "primevue/multiselect";
import DatePicker from "primevue/datepicker";
import InputText from "primevue/inputtext";
import IconField from "primevue/iconfield";
import InputIcon from "primevue/inputicon";
import Button from "primevue/button";
import { Dialog } from "primevue";
import { Form } from "@primevue/forms";
import Toast from 'primevue/toast';
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import { useToast } from "primevue/usetoast";
import Customers from '@/Components/Customers.vue';

  const props = defineProps({
    customers: Array,
    products: Array,
  });
  
  // Toast for notifications
  const toast = useToast();

  // Define the Inertia form
  const form = useForm({
    quotation_no: "",
    quotation_date: "",
    address: "",
    phone_number: "",
    customer_id: "",
    total: 0,
    tax: 0,
    grand_total: 0,
    products: [], // Will be an array of objects: { id, quantity }
  });
  
  const selectedProductIds = ref([]);
  const selectedProductsData = ref([]);

  const columns = ref([
    { field: "id", header: "No." },
    { field: "name", header: "Name" },
    { field: "unit", header: "Unit" },
    { field: "price", header: "Unit Price" },
  ]);


  watch(selectedProductIds, (newIds) => { 
    newIds.forEach((id) => {
      if (!selectedProductsData.value.find((prod) => prod.id === id)) {
        const prod = props.products.find((p) => p.id === id);
        if (prod) {
          selectedProductsData.value.push({
            ...prod,
            quanity: 1,
            subTotal: Number(prod.price) * 1,
          });
        }
      }
    });
    // Remove products that have been deselected
    selectedProductsData.value = selectedProductsData.value.filter((prod) =>
      newIds.includes(prod.id)
    );
  });
  
  const updateProductSubtotal = (row) => {
    row.quanity= parseInt(row.quanity) || 0;
    row.subTotal = Number(row.price) * row.quanity;
    form.total = calculateTotal.value;
    form.grand_total = calculateGrandTotal.value;
  };
 
  const updateTax = () => {
    form.grand_total = calculateGrandTotal.value;
  };
 
  const calculateTotal = computed(() => {
    return selectedProductsData.value.reduce(
      (sum, prod) => sum + prod.subTotal,
      0
    );
  });
  
  const calculateGrandTotal = computed(() => {
    return calculateTotal.value + Number((form.tax*calculateTotal.value/100) || 0);
  });
  
  const formattedCustomers = computed(() => {
    return props.customers.map((customer) => ({
      id: customer.id,
      label: `${customer.name} (${customer.code})`,
    }));
  });
  
  const removeProduct = (id) => {
    selectedProductsData.value = selectedProductsData.value.filter(
      (prod) => prod.id !== id
    );
    // Also update the selected IDs so the MultiSelect reflects the change.
    selectedProductIds.value = selectedProductIds.value.filter(
      (prodId) => prodId !== id
    );
  };
  
  const submit = (event) => {
    if (event && typeof event.preventDefault === "function") {
    event.preventDefault(); // Ensure the event exists before calling preventDefault
  }
    // Basic validation: all required fields must be filled and at least one product selected.
    if (!form.quotation_no || !form.quotation_date || !form.address || !form.phone_number || !form.customer_id || selectedProductsData.value.length === 0)  
    {
      toast.add({
        severity: "error",
        summary: "Please fill all information!",
        detail: "Missing required fields",
        group: "tc",
        life: 3000,
      });
      return;
    } 
    // Prepare the products array for submission.
    form.products = selectedProductsData.value.map((prod) => ({
      id: prod.id,
      quantity: prod.quanity,
    }));
  
    // Update totals.
    form.total = calculateTotal.value;
    form.grand_total = calculateGrandTotal.value;

    // Post the form data using Inertia.
    form.post(route('quotations.store'), {
        onSuccess: () => {
            alert('Customer created successfully!');
      },
      onError: (errors) => {
        console.error(errors);
      },
    });
   
  };

  const isCreateCustomerVisible = ref(false);
  const selectCustomer = () => {
    isCreateCustomerVisible.value = false;
    form.customer_id = props.customers[props.customers.length - 1].id;
  };

  const isCreateItemVisible = ref(false);
  </script>
