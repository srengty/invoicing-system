"<template> <meta name="_token" content="{{ csrf_token() }}"> <Head title="Create Invoice" /> <GuestLayout> <NavbarLayout/> <div class="py-3"> <Breadcrumb :model="items" class="border-none bg-transparent p-0">
  \<template #item="{ item }"> <Link
                         :href="item.to"
                         class="text-sm hover:text-primary flex items-start justify-center gap-1"
                     > <i v-if="item.icon" :class="item.icon"></i>
  {{ item.label }} </Link> </template> </Breadcrumb> </div>
  
  ```
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
        <div class="p-3 grid grid-cols-1 md:grid-cols-4 gap-4 ml-4 mr-4 text-sm">
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
          <div>
            <label for="payment_schedule" class="block font-medium">Payment Schedule</label>
            <MultiSelect
              v-model="form.payment_schedule_id" 
              :options="formattedPaymentSchedules" 
              optionLabel="label"
              optionValue="id" 
              placeholder="Select Payment Schedule"
              class="w-full"
            />
          </div>
          <div class="">
            <div class="">
              <!-- Display label as 'Receipt No' but bind the id to the model -->
              <label for="receipt_no" class="block font-medium">Receipt No (for deposit)</label>
              <div class="flex w-full gap-3">
                <Select
                  v-model="form.receipt_no"  
                  :options="receipts"
                  optionLabel="receipt_no"  
                  optionValue="receipt_no" 
                  placeholder="Select Receipt"
                  class="w-full"
                />
                <Button class="text-sm p-button-info w-1/3" size="small">Receipt</Button>
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
              :options="StatusOptions"
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
            <DatePicker 
              id="start_date" 
              v-model="form.start_date" 
              class="w-full" 
              placeholder="Select date" 
              size="small" 
              dateFormat="dd/mm/yy" 
            />
          </div>
  
          <div>
            <label for="end_date" class="block font-medium">Due Date</label>
            <DatePicker 
              id="end_date" 
              v-model="form.end_date" 
              class="w-full" 
              placeholder="Select due date" 
              size="small" 
              dateFormat="dd/mm/yy" 
            />
          </div>
        </div>
      </form>
  
      <!-- Product Table Section -->
      <div class="m-6">
        <DataTable :value="productsList" class="p-datatable-striped" responsiveLayout="scroll">
          <Column header="No.">
            <template #body="slotProps">
              {{ slotProps.index + 1 }}
            </template>
          </Column>
          <Column field="product" header="Product"></Column>
          <Column field="qty" header="Qty">
            <template #body="slotProps">
              <InputText v-model="slotProps.data.qty" @input="updateProductSubtotal(slotProps.data)" class="w-full" />
            </template>
          </Column>
          <Column field="unit" header="Unit"></Column>
          <Column field="unitPrice" header="Unit Price">
            <template #body="slotProps">
              <InputText v-model="slotProps.data.unitPrice" @input="updateProductSubtotal(slotProps.data)" class="w-full" />
            </template>
          </Column>
          <Column field="subTotal" header="Sub Total"></Column>
          <Column header="Action">
            <template #body="slotProps">
              <Button label="Remove" icon="pi pi-times" class="p-button-text p-button-danger" @click="removeProduct(slotProps.data.id)" />
            </template>
          </Column>
          <Column header="Catalog">
            <template #body="slotProps">
              <div class="flex items-center gap-2">
                <Checkbox
                  v-model="slotProps.data.include_catalog"
                  :binary="true"
                  @change="() => {
                   checkCatalogAvailability(slotProps.data)
                  }"
                />
                <span>
                  {{ slotProps.data.include_catalog ? 'Included' : 'Include' }}
                </span>
              </div>
            </template>
          </Column>
        </DataTable>
  
        <div class="pl-2 pr-6">
  ```
  
    <!-- Installment Paid (calculated from selected payment schedules) -->
  
    <div v-if="form.installment_paid > 0" class="total-container mt-4 flex justify-between items-center">
      <p class="font-bold">Installment Paid:</p>
      <p class="font-bold text-right">៛{{ formatCurrency(form.installment_paid) }}</p>
    </div>
  
    <!-- Total KHR from all products -->
  
    <div class="total-container mt-4 flex justify-between items-center">
      <p class="font-bold">Total KHR:</p>
      <p class="font-bold text-right">៛{{  (calculateTotalKHR) }}</p>
    </div>
  
    <!-- Final Total KHR (editable) -->
  
    <div class="total-container mt-4 flex justify-between items-center">
      <p class="font-bold">Final Total KHR:</p>
      <input
        type="number"
        v-model.number="form.paid_amount"
        placeholder="Enter Amount"
        step="0.01"
        class="h-9 text-sm border border-gray-300 rounded px-2 text-right w-40"
      />
    </div>
  
    <!-- Final Total USD (editable) -->
  
    <div class="total-container mt-4 flex justify-between items-center">
      <p class="font-bold">Final Total USD:</p>
      <input
        type="number"
        v-model.number="form.total_usd"
        placeholder="Enter USD"
        step="0.01"
        class="h-9 text-sm border border-gray-300 rounded px-2 text-right w-40"
      />
    </div>
  
    <!-- Exchange Rate -->
  
    <div class="grand-total-container flex justify-between mt-4">
      <p class="font-bold">Exchange Rate:</p>
      <p class="font-bold text-right">{{ calculateExchangeRate }}</p>
    </div>
  
    <!-- Placeholder Bank Info -->
  
    <div class="grand-total-container flex justify-between mt-4">
      <p class="font-bold">Bank Name:</p>
      <p class="text-right text-gray-400 italic">Not set</p>
    </div>
  
    <div class="grand-total-container flex justify-between mt-4">
      <p class="font-bold">Bank Account Name:</p>
      <p class="text-right text-gray-400 italic">Not set</p>
    </div>
  
    <div class="grand-total-container flex justify-between mt-4">
      <p class="font-bold">Bank Account Number:</p>
      <p class="text-right text-gray-400 italic">Not set</p>
    </div>
  </div>
  
  ```
        <!-- <div class="terms mt-4">
          <h3 class="text-lg">Terms and Conditions</h3>
          <p>Full payment is required upon quote acceptance.</p>
          <p>This quote is negotiable for one (1) week from the date stated above.</p>
        </div>
        <div class="buttons mt-4 flex justify-end">
          <Button label="Submit request for approval" icon="pi pi-check" class="p-button-success" @click="submitInvoice" />
          <Button label="Cancel" class="p-button-secondary ml-2" @click="cancel" />
        </div> -->
      </div>
  
      <!-- Modal to Select Product -->
      <Dialog
          v-model:visible="isAddItemDialogVisible"
          modal
          header="Add Item (Popup)"
          :style="{ width: '550px' }"
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
                  @click="closeAddItemDialog()"
              />
              <Button
                  :label="editingProduct ? 'Update Item' : 'Add Item'"
                  icon="pi pi-check"
                  @click="addItemToTable"
              />
          </template>
      </Dialog>
    </div>
  </GuestLayout>
  ```
  
    </template>
  
    <script setup>
    import { ref, computed, watch, onMounted } from 'vue';
    import { useForm } from '@inertiajs/vue3';
    import { Button, InputText, DataTable, Column, Dialog, DatePicker, Select, MultiSelect, Checkbox, Dropdown, AutoComplete, InputNumber } from 'primevue';
    import { usePage } from '@inertiajs/vue3';
    import GuestLayout from '@/Layouts/GuestLayout.vue';
    import { Head } from '@inertiajs/vue3';
    import { Inertia } from '@inertiajs/inertia';
    import Breadcrumb from "primevue/breadcrumb";
    import NavbarLayout from "@/Layouts/NavbarLayout.vue";
    import { getDepartment } from "../../data";
    
    const { products, agreements, quotations, customers, paymentSchedules, receipts } = usePage().props;
  
    const props = defineProps({
      customers: Array,
      products: Array,
      agreements: Array,
      quotations: Array,
      product_quotations: Array,
      divisions: Array,
      productCategories: Array,
      paymentSchedules: Array,
      receipts: Array,
  });
    
  const form = useForm({
    invoice_no: '',               // Optional: generated if blank
    quotation_no: '',
    agreement_no: '',
    customer_id: '',
    address: '',
    phone: '',
    terms: '',
    amount: 0,
    payment_schedule_id: '',
    start_date: '',
    end_date: '',
    grand_total: '',
    total_usd: '',
    exchange_rate: '',
    invoice_date: new Date().toISOString(), // Send in ISO format
    status: 'Pending',
    installment_paid: 0,  
    paid_amount: 0,
    products: [],
    receipt_no: '',
    userModifiedPaidAmount: false,
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
    
    const StatusOptions = [
    { label: 'Pending', value: 'Pending' },
    { label: 'Approved', value: 'Approved' },
    { label: 'Revise', value: 'Revise' }
  ];
    
    // Product List and Dialog Management
    const productsList = ref([]);
    const isAddItemDialogVisible = ref(false);
    const filteredAgreements = ref([]);
    const editingProduct = ref(null);
    // Product Selection Dialog State
    const selectedDivision = ref(null);
    const selectedItem = ref(null);
    const filteredProducts = ref([]);
    const selectedProductsData = ref([]);
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
  
  const divisionOptions = ref([]);
  
  const formatCurrency = (value) => {
      if (isNaN(value)) return "0.00";
      return new Intl.NumberFormat("en-US", {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2,
      }).format(value);
  };
  
  const filteredPaymentSchedules = ref([]);
  
  function getOrdinalSuffix(number) {
    const suffixes = ['th', 'st', 'nd', 'rd'];
    const remainder = number % 100;
  
    return suffixes[(remainder - 20) % 10] || suffixes[remainder] || suffixes[0];
  }
  
  const formattedPaymentSchedules = computed(() => {
    return filteredPaymentSchedules.value.map((ps, index) => {
      const rankID = index + 1; // Starting from 1
      const suffix = getOrdinalSuffix(rankID); // Get the correct ordinal suffix
      
      // Disable the first payment schedule if it's already created
      const isDisabled = index === 0 && ps.is_created;
  
      return {
        id: ps.id, // The original ID
        label: `${ps.agreement_no} (${rankID}${suffix} Payment)`, // Use rankID with ordinal suffix
        disabled: isDisabled // Disable if it's the first payment and is already created
      };
    });
  });
  
  const calculateTotalKHR = computed(() => {
    return productsList.value.reduce((acc, product) => acc + product.subTotal, 0);
  });
  
  const calculateExchangeRate = computed(() => {
    if (!form.total_usd || form.total_usd <= 0 || form.paid_amount <= 0) return 0;
    return (form.paid_amount / form.total_usd).toFixed(2);
  });
  
  onMounted(async () => {
    const response = await getDepartment();
    const data = response.data;
  
    // Filter departments with status === "service"
    const serviceDepartments = data.filter((dept) => dept.status === "service");
  
    if (Array.isArray(serviceDepartments) && serviceDepartments.length > 0) {
      divisionOptions.value = serviceDepartments.map((dept) => ({
        name: dept.name,
        id: dept.id,
        code: dept.code,
        displayName: `${dept.code} - ${dept.name}`,
      }));
    } else {
      console.warn("No service departments found.");
      divisionOptions.value = [];
    }
  });
  
  const openAddItemDialog = () => {
    isAddItemDialogVisible.value = true;
  
    if (editingProduct.value) {
      selectedDivision.value = editingProduct.value.division_id;
    }
  
    filterProductsByDivision();
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
  
    const filterProductsByDivision = () => {
      const divisionId = selectedDivision.value;
      const selectedProductIds = productsList.value.map(p => p.id);
      
      if (divisionId) {
          // Filter by selected division and exclude already selected products
          filteredProducts.value = props.products.filter(
              product => 
                  product.division_id === divisionId &&
                  product.status === "approved" &&
                  !selectedProductIds.includes(product.id))
      } 
      else if (editingProduct.value) {
          // If editing, use the product's division
          selectedDivision.value = editingProduct.value.division_id;
          filteredProducts.value = props.products.filter(
              product =>
                  product.division_id === editingProduct.value.division_id &&
                  product.status === "approved" &&
                  !selectedProductIds.includes(product.id))
      } 
      else {
          // Show all approved products not already selected
          filteredProducts.value = props.products.filter(
              product => 
                  product.status === "approved" &&
                  !selectedProductIds.includes(product.id))
      }
  };
  
  const searchProducts = (event) => {
      const query = event.query?.toLowerCase() || "";
      const divisionId = selectedDivision.value;
      const selectedProductIds = productsList.value.map(p => p.id);
      
      // Start with all approved products not already selected
      let productsToSearch = props.products.filter(
          product => 
              product.status === "approved" &&
              !selectedProductIds.includes(product.id)
      );
      
      // Apply division filter if one is selected
      if (divisionId) {
          productsToSearch = productsToSearch.filter(
              product => product.division_id === divisionId
          );
      }
      
      // Apply search query filter
      filteredProducts.value = productsToSearch.filter(product =>
          product.name.toLowerCase().includes(query)
      );
  };
  
  watch(productsList, () => {
      // Refresh the filtered list when productsList changes
      filterProductsByDivision();
  }, { deep: true });
    
  const updateSelectedProductDetails = () => {
    if (!selectedItem.value) return;
  
    const product = selectedItem.value; // Already the full object
  
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
  
    selectedDivision.value = product.division_id || null;
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
  
    const productData = {
      id: selectedProduct.value.id,
      product: selectedProduct.value.name,
      qty: selectedProduct.value.quantity,
      unit: selectedProduct.value.unit,
      unitPrice: selectedProduct.value.price,
      subTotal: selectedProduct.value.quantity * selectedProduct.value.price,
      remark: selectedProduct.value.remark,
      include_catalog: false, // default
      pdf_url: selectedProduct.value.pdf_url,
    };
  
    if (existingIndex >= 0 && editingProduct.value !== null) {
      productsList.value[existingIndex] = productData;
    } else {
      productsList.value.push(productData);
    }
  
    closeAddItemDialog();
  };
  
  watch(() => form.customer_id, (newCustomerId) => {
    if (newCustomerId) {
      const selectedCustomer = customers.find(c => c.id === newCustomerId);
  
      if (selectedCustomer) {
        form.address = selectedCustomer.address || '';
        form.phone = selectedCustomer.phone || selectedCustomer.phone_number || '';
      }
    } else {
      form.address = '';
      form.phone = '';
    }
  });
  
    
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
              id: pq.product.id,
              product: pq.product.name || 'Unknown Product',
              qty: pq.quantity || 1,
              unit: pq.product.unit || 'Unit',
              unitPrice: pq.price || 0,
              subTotal: (pq.quantity || 1) * (pq.price || 0),
              remark: pq.remark || '',
              category_id: pq.product.category_id || null,
              acc_code: pq.product.acc_code || '',
              include_catalog: false, // default when loaded from quotation
              pdf_url: pq.product.pdf_url || null,
            }));
          } else {
            productsList.value = [];
          }
    
          // Recalculate Grand Total
          form.grand_total = calculateTotal.value - form.installment_paid;
          
    
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
        form.installment_paid = 0;
        form.paid_amount = '';
        productsList.value = [];
      }
    }, { deep: true });
  
    
  watch(() => form.agreement_no, async (newAgreementNo) => {
    if (newAgreementNo) {
      const selectedAgreement = agreements.find(a => a.agreement_no === newAgreementNo);
  
      if (selectedAgreement) {
        console.log("Selected Agreement:", selectedAgreement);
  
        // Set quotation if the agreement is linked to one
        form.quotation_no = selectedAgreement.quotation_no || '';
        filteredPaymentSchedules.value = props.paymentSchedules.filter(
          (ps) => ps.agreement_no === newAgreementNo // Assuming paymentSchedules have agreement_no field
        );
  
        // Auto-fill address only if empty
        if (!form.address) {
          form.address = selectedAgreement.address || '';
        }
  
        // Auto-fill start and end dates with correct format (yyyy-mm-dd)
        form.start_date = selectedAgreement.start_date ? selectedAgreement.start_date : '';
        form.end_date = selectedAgreement.end_date ? selectedAgreement.end_date : '';
  
        // Auto-fill payment schedule if available
        if (selectedAgreement) {
        // Filter paymentSchedules based on the selected agreement
        filteredPaymentSchedules.value = props.paymentSchedules
          .filter((ps) => ps.agreement_no === newAgreementNo) // Assuming paymentSchedules have agreement_no field
          .sort((a, b) => a.id - b.id); // Sort by ID to determine rankID (1-based)
      }
  
        // Calculate instalment paid (sum of all invoice amounts related to this agreement)
        form.installment_paid = Array.isArray(selectedAgreement.invoices)
          ? selectedAgreement.invoices.reduce((sum, invoice) => sum + invoice.amount, 0)
          : 0;
  
        // Recalculate Grand Total
        form.grand_total = calculateTotal.value - form.installment_paid;
  
        console.log("Updated Form Data after Agreement Selection:", form);
      }
    } else {
      console.log("Agreement Deselected - Keeping existing data");
  
      // Reset payment schedule when agreement is deselected
      form.payment_schedule_id = '';  // Reset the payment schedule field
      filteredPaymentSchedules.value = [];  // Reset available payment schedules
  
      // Reset other fields if necessary
      form.start_date = '';
      form.end_date = '';
      form.installment_paid = 0;
      filteredPaymentSchedules.value = [];
  
      console.log("Reset Form Data after Agreement Deselection:", form);
    }
  }, { deep: true });
  
    
    const indexTemplate = (rowData, { index }) => {
      return index + 1; // Return the index + 1 for 1-based index display
    };
    
    const calculateTotal = computed(() => {
      return productsList.value.reduce((acc, product) => acc + product.subTotal, 0);
    });
    
    const calculateGrandTotal = computed(() => {
      return calculateTotal.value - form.installment_paid;
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
  
    form.invoice_no = form.invoice_no || '';
  
    for (let product of selectedProductsData.value) {
          if (product.include_catalog && !product.pdf_url) {
              showToast(
                  "error",
                  "Error",
                  "Catalog PDF is missing for an included product.",
                  3000
              );
              return;
          }
      }
      form.start_date = form.start_date || '';
      form.end_date = form.end_date || '';
  
      // Prepare the payload
      form.products = productsList.value.map((prod) => ({
          id: prod.id,
          quantity: prod.qty ?? 1,
          price: prod.unitPrice ?? 0,
          acc_code: prod.acc_code ?? '',
          category_id: prod.category_id ?? null,
          remark: prod.remark ?? '',
          include_catalog: Boolean(prod.include_catalog),
          pdf_url: prod.pdf_url ?? null,
      }));
  
  
      form.total = calculateTotal.value;
      form.grand_total = calculateGrandTotal.value;
      form.total_usd = form.total_usd || 0;
      form.total = calculateTotalKHR.value;
      form.exchange_rate = calculateExchangeRate.value;
      form.paid_amount = form.paid_amount || 0;
      form.installment_paid = Number(form.installment_paid) || 0;
      form.receipt_no = form.receipt_no || '';
  
      // If USD total wasn't set, set it based on exchange rate if available
      if (!form.total_usd && form.exchange_rate > 0) {
          form.total_usd = (calculateTotalKHR.value / form.exchange_rate).toFixed(
              2
          );
      }
      // Check if we are editing or creating
      if (form.id) {
          // PUT request for updating existing quotation
          try {
              await form.put(route("quotations.update", { id: form.id }), {
                  onSuccess: () => {
                      showToast(
                          "success",
                          "Updated",
                          "Quotation updated successfully!"
                      );
                      router.get(route("quotations.list"));
                  },
                  onError: (errors) => {
                      console.error("Update Error:", errors);
                      showToast(
                          "error",
                          "Update Failed",
                          "Could not update quotation."
                      );
                  },
              });
          } catch (error) {
              console.error("Unexpected Error in Update:", error);
              showToast(
                  "error",
                  "Unexpected Error",
                  "Could not update quotation."
              );
          }
      } else {
          // POST request for creating new quotation
          try {
              await form.post(route("quotations.store"), {
                  onSuccess: () => {
                      showToast(
                          "success",
                          "Created",
                          "Quotation created successfully!"
                      );
                      router.get(route("quotations.list"));
                  },
                  onError: (errors) => {
                      console.error("Creation Error:", errors);
                      showToast(
                          "error",
                          "Creation Failed",
                          "Could not create quotation."
                      );
                  },
              });
          } catch (error) {
              console.error("Unexpected Error in Create:", error);
              showToast(
                  "error",
                  "Unexpected Error",
                  "Could not create quotation."
              );
          }
      }
  
    try {
      form.post('/invoices');
  
  
    } catch (error) {
      console.error('Error submitting invoice:', error);
      alert('An error occurred while submitting the invoice.');
    }
  };
  
    const updatePaymentDetails = () => {
      const selectedPaymentSchedule = paymentSchedules.find(ps => ps.id === form.payment_schedule_id);
      if (selectedPaymentSchedule) {
        form.installment_paid = selectedPaymentSchedule.amount;
        form.paid_amount = selectedPaymentSchedule.amount;
      }
    };
  
  const checkCatalogAvailability = (product) => {
      if (product.include_catalog && !product.pdf_url) {
          console.warn("Product does not include catalog or missing PDF URL.");
          showToast(
              "error",
              "Error",
              "Catalog PDF is missing for an included product.",
              3000
          );
          return false;
      }
      console.log("Product is ready for catalog PDF.");
      return true;
  };
  
  watch(() => form.start_date, (newStartDate) => {
    if (newStartDate) {
      // If newStartDate is a Date object, we format it to "mm/dd/yyyy"
      if (newStartDate instanceof Date) {
        const month = String(newStartDate.getMonth() + 1).padStart(2, '0');
        const day = String(newStartDate.getDate()).padStart(2, '0');
        const year = newStartDate.getFullYear();
        newStartDate = `${month}/${day}/${year}`;
      }
  
      // Now safely split newStartDate if it's a string in mm/dd/yyyy format
      const [month, day, year] = newStartDate.split("/");
  
      // Create a Date object from the split values
      const startDate = new Date(`${year}-${month}-${day}`);
  
      // Add 14 days to the startDate
      startDate.setDate(startDate.getDate() + 14);
  
      // Format the end date as "mm/dd/yyyy"
      const endDateFormatted =
        String(startDate.getMonth() + 1).padStart(2, '0') + "/" +
        String(startDate.getDate()).padStart(2, '0') + "/" +
        startDate.getFullYear();
  
      // Set form.end_date automatically
      form.end_date = endDateFormatted;
  
      console.log('Auto-set end_date:', form.end_date);
    }
  
    const autoFillEndDate = () => {
    // Auto-calculate end date based on start date
    if (form.start_date) {
      const startDate = new Date(form.start_date);
      startDate.setDate(startDate.getDate() + 14);
      form.end_date = `${startDate.getMonth() + 1}/${startDate.getDate()}/${startDate.getFullYear()}`;
    }
  };
  });
  
  watch(() => form.payment_schedule_id, (selectedIds) => {
    if (!Array.isArray(selectedIds) || selectedIds.length === 0) {
      form.installment_paid = 0;
      if (!form.userModifiedPaidAmount) {
        form.paid_amount = 0;
      }
      return;
    }
  
    // Use reactive filtered list (filteredPaymentSchedules)
    const selectedSchedules = filteredPaymentSchedules.value.filter(schedule =>
      selectedIds.includes(schedule.id)
    );
  
    const totalPaid = selectedSchedules.reduce((sum, schedule) => {
      return sum + (Number(schedule.amount) || 0);
    }, 0);
  
    form.installment_paid = totalPaid;
  
    if (!form.userModifiedPaidAmount) {
      form.paid_amount = totalPaid;
    }
  }, { deep: true });
  
  
  // Track if user modifies paid_amount manually to prevent auto-overriding it
  watch(() => form.paid_amount, (newVal) => {
    if (newVal !== form.installment_paid) {
      form.userModifiedPaidAmount = true;
    }
  });
  
  watch(() => form.receipt_no, (newValue) => {
    console.log('Selected Receipt ID:', newValue);
  })
  </script>
  