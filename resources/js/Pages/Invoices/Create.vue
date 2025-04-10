<template>
    <meta name="_token" content="{{ csrf_token() }}" />
    <Head title="Create Invoice" />
    <GuestLayout>
      <NavbarLayout/>
      <div class="py-3">
              <Breadcrumb :model="items" class="border-none bg-transparent p-0">
                  <template #item="{ item }">
                      <Link
                          :href="item.to"
                          class="text-sm hover:text-primary flex items-start justify-center gap-1"
                      >
                          <i v-if="item.icon" :class="item.icon"></i>
                          {{ item.label }}
                      </Link>
                  </template>
              </Breadcrumb>
          </div>
  
      <div class="create-invoice text-sm">
        <!-- Header Section with Buttons -->
        <div class="flex justify-end items-center p-3 mr-4">
          <div class="flex gap-4">
            <Button
            label="Add Product"
            icon="pi pi-plus"
            class="p-button-success"
            type="button"
            @click="openAddItemDialog"
            size="small"
            />
            <Button
              label="Save Invoice"
              icon="pi pi-check"
              class="p-button-success"
              type="button"
              @click="submitInvoice"
              size="small"
            />
          </div>
        </div>
  
        <!-- Invoice Form Section -->
        <form @submit.prevent="submitInvoice">
          <div class="p-3 grid grid-cols-1 md:grid-cols-3 gap-4 ml-4 mr-4 text-sm">
            <div>
              <label for="quotation_no" class="block font-medium">Quotation No</label>
              <Select
                v-model="form.quotation_no"
                :options="quotations"
                optionLabel="quotation_no"
                optionValue="quotation_no"
                placeholder="Select Quotation"
                class="w-full"
                required
              />
            </div>
            <div>
                <label for="agreement_no" class="block font-medium">Agreement No</label>
                <Select
                  v-model="form.agreement_no"
                  :options="form.agreement_no || !form.quotation_no
                  ? agreements:agreements.filter(a => a.status === 'Open')"
                  optionLabel="agreement_no"
                  optionValue="agreement_no"
                  placeholder="Select Agreement"
                  class="w-full"
                />
              </div>
            <div class="">
                <div class="">
                  <label for="deposit_no" class="block font-medium">Receipt No (for deposit)</label>
                  <div class="flex w-full gap-3">
                    <InputText
                      id="deposit_no"
                      v-model="form.deposit_no"
                      class="w-2/3"
                      placeholder="Enter deposit number"
                      size="small"
                      required
  
                    />
                    <Button class="text-sm p-button-info w-1/3" size="small">Add Receipt</Button>
                </div>
              </div>
            </div>
            <div>
              <label for="customer_id" class="block  font-medium">Customer</label>
              <Select
                v-model="form.customer_id"
                :options="customers"
                optionLabel="name"
                optionValue="id"
                placeholder="Select Customer"
                class="w-full"
                required
              />
            </div>
            <div>
              <label for="status" class="block font-medium">Status</label>
              <Select
                v-model="form.status"
                :options="statusOptions"
                optionLabel="label"
                optionValue="value"
                placeholder="Select Status"
                class="w-full"
                required
              />
            </div>
            <div>
              <label for="address" class="block font-medium">Address</label>
              <InputText
                id="address"
                v-model="form.address"
                class="w-full"
                placeholder="Enter address"
                size="small"
              />
            </div>
            <div>
              <label for="phone" class="block font-medium">Phone</label>
              <InputText
                id="phone"
                v-model="form.phone"
                class="w-full"
                placeholder="Enter phone number"
                size="small"
              />
            </div>
            <div>
              <label for="start_date" class="block font-medium">Date</label>
              <DatePicker id="start_date" v-model="form.start_date" class="w-full" placeholder="Select date" size="small"/>
            </div>
            <div>
              <label for="end_date" class="block font-medium">Due Date</label>
              <DatePicker id="end_date" v-model="form.end_date" class="w-full" placeholder="Select due date" size="small"/>
            </div>
          </div>
        </form>
  
        <!-- Product Table Section -->
        <div class="m-6">
          <DataTable :value="productsList" class="p-datatable-striped" responsiveLayout="scroll">
            <Column field="index" header="No."></Column>
            <Column field="product" header="Product"></Column>
            <Column field="qty" header="Qty">
              <template #body="slotProps">
                <InputText v-model="slotProps.data.qty" @input="updateProductSubtotal(slotProps.data)" class="w-full" />
              </template>
            </Column>
            <Column field="unit" header="Unit"></Column>
            <Column field="unitPrice" header="Unit Price"></Column>
            <Column field="subTotal" header="Sub Total"></Column>
            <Column header="Action">
              <template #body="slotProps">
                <Button label="Remove" icon="pi pi-times" class="p-button-text p-button-danger" @click="removeProduct(slotProps.data.id)" />
              </template>
            </Column>
          </DataTable>
  
          <div class="total-container mt-4 flex justify-between">
            <p class="font-bold">Total</p>
            <p class="font-bold">{{ calculateTotal }}</p>
          </div>
          <div class="grand-total-container flex justify-between">
            <p class="font-bold text-lg">Grand Total</p>
            <p class="font-bold text-lg">{{ calculateGrandTotal }}</p>
          </div>
          <div class="flex justify-between mt-4">
            <p class="font-bold">Instalment Paid</p>
            <p class="font-bold">{{ form.instalmentPaid }}</p>
          </div>
          <div class="terms mt-4">
            <h3 class="text-lg">Terms and Conditions</h3>
            <p>Full payment is required upon quote acceptance.</p>
            <p>This quote is negotiable for one (1) week from the date stated above.</p>
          </div>
          <div class="buttons mt-4 flex justify-end">
            <Button label="Submit request for approval" icon="pi pi-check" class="p-button-rounded p-button-success" @click="submitInvoice" />
            <Button label="Cancel" class="p-button-rounded p-button-secondary ml-2" @click="cancel" />
          </div>
        </div>
  
        <!-- Modal to Select Product -->
        <Dialog
            v-model:visible="isAddItemDialogVisible"
            modal
            header="Add Item (Popup)"
            :style="{ width: '450px' }"
            class="text-sm"
        >
            <div class="p-fluid grid gap-4 text-sm">
                <!-- Division Selection -->
                <div class="field w-full">
                    <label for="division" class="required">Division</label> <br />
                    <Dropdown
                    v-model="selectedDivision"
                    :options="divisionOptions"
                    optionLabel="displayName"
                    optionValue="id"
                    placeholder="Select a Division"
                    :filter="true"
                    filterPlaceholder="Search divisions..."
                    class="w-full"
                    @change="filterProductsByDivision"
                    />
                </div>

                <!-- Item Selection -->
                <div class="field w-full">
                    <label for="item" class="required">Item</label> <br />
                    <AutoComplete
                        v-model="selectedItem"
                        :suggestions="filteredProducts"
                        :dropdown="true"
                        optionLabel="name"
                        placeholder="Search Product"
                        class="w-full text-sm"
                        @complete="searchProducts"
                        @change="updateSelectedProductDetails"
                        :input-props="{ id: 'item' }"
                    />
                </div>
                <!-- Item Category (Auto-complete, Read-Only) -->
                <div class="field">
                    <label for="item-category" class="required"
                        >Item Category</label
                    >
                    <InputText
                        :value="getCategoryName(selectedProduct.category_id)"
                        class="w-full text-sm"
                        size="small"
                        readonly
                    />
                </div>
                <!-- Unit Price (Auto-complete, Editable) -->
                <div class="field">
                    <label for="unit-price" class="required">Unit Price</label>
                    <InputNumber
                        v-model="selectedProduct.price"
                        :min="0"
                        @keydown="preventMinus"
                        size="small"
                        class="w-full text-sm"
                    />
                </div>
                <!-- Account Code (Auto-complete, Read-Only) -->
                <div class="field">
                    <label for="account-code" class="required">Account Code</label>
                    <InputText
                        v-model="selectedProduct.acc_code"
                        class="w-full text-sm"
                        size="small"
                        readonly
                    />
                </div>
                <!-- Quantity -->
                <div class="field">
                    <label for="quantity" class="required">Quantity</label>
                    <InputNumber
                        v-model="selectedProduct.quantity"
                        class="w-full text-sm"
                        size="small"
                        :min="1"
                    />
                </div>
                <!-- View Catalog -->
                <div v-if="selectedProduct.pdf_url" class="text-start">
                    <label for="quantity">Catalog</label>
                    <a
                        :href="`/pdfs/${selectedProduct.pdf_url.split('/').pop()}`"
                        target="_blank"
                        class="text-blue-500 hover:text-blue-700 transition duration-200"
                    >
                        📄 View Catelog
                    </a>
                </div>
                <p v-else class="text-center text-gray-400">No PDF available</p>
                <!-- Additional Remark -->
                <div class="field">
                    <label for="additional-remark">Additional Remark</label>
                    <InputText
                        v-model="selectedProduct.remark"
                        class="w-full text-sm"
                        size="small"
                    />
                </div>
            </div>
            <!-- Dialog Footer -->
            <template #footer>
                <Button
                    label="Cancel"
                    icon="pi pi-times"
                    class="p-button-text"
                    raised
                    @click="closeAddItemDialog()"
                />
                <Button
                    :label="editingProduct ? 'Update Item' : 'Add Item'"
                    icon="pi pi-check"
                    raised
                    @click="addItemToTable"
                />
            </template>
        </Dialog>
      </div>
    </GuestLayout>
  </template>
  
  <script setup>
  import { ref, computed, watch } from 'vue';
  import { useForm } from '@inertiajs/vue3';
  import { Button, InputText, DataTable, Column, Dialog, DatePicker, Select, Calendar, Dropdown, AutoComplete, InputNumber } from 'primevue';
  import { usePage } from '@inertiajs/vue3';
  import GuestLayout from '@/Layouts/GuestLayout.vue';
  import { Head } from '@inertiajs/vue3';
  import { Inertia } from '@inertiajs/inertia';
  import Breadcrumb from "primevue/breadcrumb";
  import NavbarLayout from "@/Layouts/NavbarLayout.vue";
  
  const { products, agreements, quotations, customers, product_quotations, divisions } = usePage().props;

  const props = defineProps({
    customers: Array,
    products: Array,
    agreements: Array,
    quotations: Array,
    product_quotations: Array,
    divisions: Array,
    productCategories: Array
});
  
  const form = useForm({
    invoice_no: '',
    agreement_no: '',
    quotation_no: '',
    deposit_no: '',
    customer_id: '',
    address: '',
    phone: '',
    start_date: '',
    end_date: '',
    grand_total: 0,
    instalmentPaid: 0,
    status: '',
    productQuotations:[],
  });
  
  const page = usePage();
  const items = computed(() => [
      {
          label: "",
          to: "/",
          icon: "pi pi-home",
      },
      { label: page.props.title || "Invoices", to: route("invoices.index") },
      { label: page.props.title || "Create Invoices", to: route("invoices.create") },
  ]);
  
  const statusOptions = [
    { label: 'Pending', value: 'Pending' },
    { label: 'Approved', value: 'Approved' },
    { label: 'Revise', value: 'Revise' },
  ];
  
  // Product List and Dialog Management
  const productsList = ref([]);
  const isAddItemDialogVisible = ref(false);
  const filteredAgreements = ref([]);
  const editingProduct = ref(null);
  // Product Selection Dialog State
  const selectedDivision = ref(null);
  const selectedItem = ref('');
  const filteredProducts = ref([]);
  const selectedProduct = ref({
  id: null,
  name: '',
  category_id: null,
  price: 0,
  acc_code: '',
  quantity: 1,
  remark: '',
  pdf_url: null,
  unit: ''
});

const divisionOptions = computed(() => {
    return props.divisions?.map(division => ({
        id: division.id,
        displayName: division.name
    })) || [];
});

const openAddItemDialog = () => {
    console.log('Products:', props.products);
    console.log('Divisions:', props.divisions);
    isAddItemDialogVisible.value = true;
    filteredProducts.value = props.products;
    resetSelectedProduct();
};
  
  const closeAddItemDialog = () => {
    isAddItemDialogVisible.value = false;
    resetSelectedProduct();
  };
  
  const resetSelectedProduct = () => {
    selectedProduct.value = {
      id: null,
      name: '',
      category_id: null,
      price: 0,
      acc_code: '',
      quantity: 1,
      remark: '',
      pdf_url: null,
      unit: ''
    };
    selectedItem.value = '';
    selectedDivision.value = null;
    editingProduct.value = null;
  };
  
const searchProducts = (event) => {
    if (!event.query.trim()) {
        filteredProducts.value = [];
        return;
    }
    
    const query = event.query.toLowerCase();
    filteredProducts.value = props.products.filter(product => 
        product.name.toLowerCase().includes(query) &&
        (selectedDivision.value ? product.division_id === selectedDivision.value : true)
    );
};

const filterProductsByDivision = () => {
    if (selectedDivision.value) {
        filteredProducts.value = props.products.filter(
            product => product.division_id === selectedDivision.value
        );
    } else {
        filteredProducts.value = props.products; // Show all products when no division selected
    }
    selectedItem.value = '';
    resetSelectedProduct();
};
  
const updateSelectedProductDetails = () => {
  if (!selectedItem.value) return;

  const product = products.find(p => p.name === selectedItem.value);
  if (product) {
    selectedProduct.value = {
      id: product.id,
      name: product.name,
      category_id: product.category_id,
      price: product.price,
      acc_code: product.acc_code,
      quantity: 1,
      remark: '',
      pdf_url: product.pdf_url,
      unit: product.unit
    };
  }
};
  
  const getCategoryName = (categoryId) => {
    // You'll need to implement this based on your category data structure
    return categoryId ? `Category ${categoryId}` : 'N/A';
  };
  
  const preventMinus = (e) => {
    if (e.key === '-') e.preventDefault();
  };
  
  // Add/Update Product to List
  const addItemToTable = () => {
    if (!selectedProduct.value.id) {
      alert('Please select a valid product');
      return;
    }
    
    const existingIndex = editingProduct.value !== null 
      ? editingProduct.value 
      : productsList.value.findIndex(p => p.id === selectedProduct.value.id);
    
    if (existingIndex >= 0 && editingProduct.value !== null) {
      // Update existing product
      productsList.value[existingIndex] = {
        id: selectedProduct.value.id,
        product: selectedProduct.value.name,
        qty: selectedProduct.value.quantity,
        unit: selectedProduct.value.unit,
        unitPrice: selectedProduct.value.price,
        subTotal: selectedProduct.value.quantity * selectedProduct.value.price,
        remark: selectedProduct.value.remark
      };
    } else {
      // Add new product
      productsList.value.push({
        id: selectedProduct.value.id,
        product: selectedProduct.value.name,
        qty: selectedProduct.value.quantity,
        unit: selectedProduct.value.unit,
        unitPrice: selectedProduct.value.price,
        subTotal: selectedProduct.value.quantity * selectedProduct.value.price,
        remark: selectedProduct.value.remark
      });
    }
    
    closeAddItemDialog();
  };
  
  // Existing methods from original component
  watch(() => form.quotation_no, (newQuotationId) => {
    if (newQuotationId) {
      const selectedQuotation = quotations.find(q => q.quotation_no === newQuotationId);
  
      if (selectedQuotation) {
        console.log("Selected Quotation:", selectedQuotation);
  
        // Auto-fill customer details
        form.customer_id = selectedQuotation.customer_id || '';
        form.address = selectedQuotation.address || '';
        form.phone = selectedQuotation.phone_number || '';
        form.status = selectedQuotation.status || '';
  
        if (selectedQuotation.agreement) {
          console.log("working on agreement")
          // If quotation has an agreement, set and disable agreement selection
          form.agreement_no = selectedQuotation.agreement.agreement_no;
          filteredAgreements.value = [selectedQuotation.agreement];
        } else {
          console.log("working on not agreement")
          // If no agreement, list only agreements that are not linked to any quotation
          form.agreement_no = ''; // Reset agreement selection
          filteredAgreements.value = agreements.filter(a => a.quotation == null && a.status === 'Open');
        }
  
        // Auto-fill products based on quotation
        if (Array.isArray(selectedQuotation.product_quotations) && selectedQuotation.product_quotations.length > 0) {
          productsList.value = selectedQuotation.product_quotations.map((pq, index) => ({
            index: index + 1,
            product: pq.product.name || 'Unknown Product',
            qty: pq.quantity || 1,
            unit: pq.product.unit || 'Unit',
            unitPrice: pq.price || 0,
            subTotal: (pq.quantity || 1) * (pq.price || 0),
          }));
        } else {
          productsList.value = [];
        }
  
        // Recalculate Grand Total
        form.grand_total = calculateTotal.value - form.instalmentPaid;
  
        console.log("Updated Form Data after Quotation Selection:", form);
      }
    } else {
      // If no quotation is selected, reset agreements list
      filteredAgreements.value = agreements.filter(a => a.status === 'Open');
      form.agreement_no = '';
      form.customer_id = '';
      form.address = '';
      form.phone = '';
      form.start_date = '';
      form.end_date = '';
      form.instalmentPaid = 0;
      productsList.value = [];
    }
  }, { deep: true });
  
  watch(() => form.agreement_no, (newAgreementNo) => {
    if (newAgreementNo) {
      const selectedAgreement = agreements.find(a => a.agreement_no === newAgreementNo);
  
      if (selectedAgreement) {
        console.log("Selected Agreement:", selectedAgreement);
  
        // Set quotation if the agreement is linked to one
        form.quotation_no = selectedAgreement.quotation_no || '';
  
        // Auto-fill address only if empty
        if (!form.address) {
          form.address = selectedAgreement.address || '';
        }
  
        // Auto-fill start and end dates
        form.start_date = selectedAgreement.start_date || '';
        form.end_date = selectedAgreement.end_date || '';
  
        // Calculate instalment paid (sum of all invoice amounts related to this agreement)
        form.instalmentPaid = Array.isArray(selectedAgreement.invoices)
          ? selectedAgreement.invoices.reduce((sum, invoice) => sum + invoice.amount, 0)
          : 0;
  
        // Recalculate Grand Total
        form.grand_total = calculateTotal.value - form.instalmentPaid;
      }
    } else {
      console.log("Agreement Deselected - Keeping existing data");
    }
  }, { deep: true });
  
  const indexTemplate = (rowData, { index }) => {
    return index + 1; // Return the index + 1 for 1-based index display
  };
  
  const calculateTotal = computed(() => {
    return productsList.value.reduce((acc, product) => acc + product.subTotal, 0);
  });
  
  const calculateGrandTotal = computed(() => {
    return calculateTotal.value - form.instalmentPaid;
  });
  
  const actionTemplate = (data) => {
    return {
      template: `
        <Button label="Remove" icon="pi pi-times" class="p-button-text p-button-danger" @click="removeProduct(${data.id})" />
      `
    };
  };
  
  const removeProduct = (productId) => {
    productsList.value = productsList.value.filter((product) => product.id !== productId);
  };
  
  const updateProductSubtotal = (product) => {
    product.subTotal = product.qty * product.unitPrice;
  };
  
  const cancel = () => {
    form.reset();
    productsList.value = [];
  };
  
  const submitInvoice = async () => {
    if (productsList.value.length === 0) {
      alert('Please add at least one product.');
      return;
    }
  
    const invoiceData = {
      agreement_no: form.agreement_no,
      quotation_no: form.quotation_no,
      customer_id: form.customer_id,
      address: form.address,
      phone: form.phone,
      start_date: form.start_date,
      end_date: form.end_date,
      grand_total: form.grand_total,
      status: form.status,
      products: productsList.value.map(product => ({
        id: product.id,
        quantity: product.qty,
      })),
    };
  
    const csrfToken = document.querySelector('meta[name="csrf_token"]').getAttribute('content');
  
    try {
      const response = await fetch('/invoices', {
        method: "POST",
        body: JSON.stringify(invoiceData),
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          "Content-type": "application/json",
        },
      });
  
      if (!response.ok) {
        console.error('Failed to submit invoice:', response.statusText);
        return;
      }// Redirect after submission
    } catch (error) {
      console.error('Error submitting invoice:', error);
    }
  };
  
  </script>