<template>
    <Head title="Create Quotation" />
    <GuestLayout>
        <Toast position="top-center" group="tc"/>
        <Toast position="top-right" group="tr" />
        <Form @submit.prevent="submit">
            <div class="p-4 grid  md:grid-cols-2 gap-0 w-2/3 "> <!--Column -->
                <div class="p-4 grid md:grid-rows-2 gap-4">  <!--Rows in column -->
                    <div class="flex flex-col gap-2">
                        <label for="quotation_no">Quotation No:</label>    
                        <InputText id="quotation_no" v-model="form.quotation_no"  placeholder="Input" class="w-full md:w-60"  />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="quotation_date">Date:</label>
                        <DatePicker v-model="form.quotation_date" showIcon fluid iconDisplay="input" inputId="quotation_date" placeholder="Select" class="w-full md:w-60"/>
                    </div>
                </div>     
                <div class="p-4 grid md:grid-rows-2 gap-4">   
                    <div class="flex flex-col gap-2 ">
                        <label for="address">Address</label>
                        <IconField  class="w-full md:w-60" >
                            <InputText id="address" v-model="form.address"  placeholder="Input" class="w-full md:w-60"  />
                            <InputIcon class="pi pi-times-circle" style=""/>
                        </IconField>
                    </div>
                    <div class="flex flex-col gap-2 ">
                        <label for="phone_number">Contact</label>
                        <IconField  class="w-full md:w-60" >
                            <InputText id="phone_number" v-model="form.phone_number"  placeholder="Input" class="w-full md:w-60"  />
                            <InputIcon class="pi pi-times-circle" style=""/>
                        </IconField>
                    </div>
                </div>   
            </div>
            <div class="p-8 grid md:grid-rows-2 gap-4">  
                <div class="flex flex-row gap-4 items-end w-1/3">             
                    <div class="flex flex-col gap-2 w-full" >
                        <label for="customer_id">Customer/ Organization</label>
                        <Select
                            v-model="form.customer_id"
                            :options="formattedCustomers"
                            optionLabel="label"   
                            optionValue="id"      
                            id="customer_id"
                            placeholder="Select a customer"
                            class="w-full w-65"/>    
                            <!-- <p>Selected Customer ID: {{ form.customer_id }}</p> -->

                    </div>
                <div class="w-60 ">
                    <Link :href="route('customers.create')">
                        <Button icon="pi pi-plus" label="Add customer" rounded  type="submit" />
                    </Link>
                </div>
                </div>
                
                <div class="flex flex-row gap-4 items-end w-1/3">             
                    <div class="flex flex-col gap-2 w-full" >
                        <label for="p_name">Item</label>
                        <MultiSelect 
                            v-model="selectedProductIds"
                            
                            :options="products"
                            optionLabel="name"
                            optionValue="id"
                            placeholder="Select Product"
                            class="w-full md:w-65"
                            @change="onProductSelect"
                        />
                    </div>
                    <div class="w-60 "><Link :href="route('products.create')"><Button icon="pi pi-plus" label="Add Item" type="submit" rounded /></Link></div>                                
                </div>
                <!-- <p>Selected product ID: {{ selectedProductIds }}</p> -->
            </div> 

            <div class="pl-6">
                <DataTable :value="selectedProductsData" paginator :rows="5" :rowsPerPageOptions="[5, 10, 20, 50]" striped >
                    <Column v-for="col of columns" :key="col.field" :field="col.field" :header="col.header" />
                    <Column field="subTotal" header="SUB-TOTAL" :body="subTotalTemplate"></Column>
                    
                    <Column header="Actions">
                        <template #body="slotProps">
                            <div class="flex gap-2 items-center ">
                                <Button
                                    icon="pi pi-pencil"
                                    class="p-button-warning"
                                    label="Edit"
                                    @click="openForm(slotProps.data)"
                                    rounded
                                />
                                <div class="card flex ">
                                    <Checkbox v-model="checked" binary />
                                </div>

                            </div>
                        </template>
                    </Column>
                </DataTable>
                <div class="pl-2">
                    <div class="total-container mt-4 flex justify-between">
                        <p class="font-bold">Total</p>
                        <p class="font-bold">{{ calculateTotal }}</p>
                    </div>

                    <div class="grand-total-container flex justify-between">
                        <p class="font-bold text-lg">Grand Total</p>
                        <p class="font-bold text-lg">{{ calculateGrandTotal }}</p>
                    </div>
                </div>
            </div>

            <div class="buttons mt-4 mr-4 flex justify-end">
                    <Button v-ripple label="Submit" icon="pi pi-check" @click="submit" class="p-button-rounded p-button-success" />
                    <Button v-ripple label="Cancel" class="p-button-rounded p-button-secondary ml-2" />
            </div>

                
        </Form>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref} from "vue";
import Select from 'primevue/select';
import MultiSelect from 'primevue/multiselect';
import DatePicker from 'primevue/datepicker';
import InputText from 'primevue/inputtext';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import Checkbox from 'primevue/checkbox';
import Button from 'primevue/button';
import { Form } from '@primevue/forms';
import { useForm } from '@inertiajs/vue3';
import Toast from 'primevue/toast';
import { DataTable, Column} from "primevue";
import { useToast } from "primevue/usetoast";
import { Inertia } from "@inertiajs/inertia";

const props = defineProps({
  customers: Array,
  products: Array,
});

const toast = useToast();
// Reactive state
const selectedProduct = ref(null);
const checked = ref(false);


// Handle product selection
const onProductSelect = (event) => {
  selectedProduct.value = event.value;
};

const form = useForm({
      quotation_no: '', 
      quotation_date: '',
      address: '',
      phone_number: '',
      customer_id: '',
      products: [],
});

// Define columns for DataTable



const submit = () => {
 
    if ((!form.quotation_no || !form.quotation_date || !form.address || !form.phone_number || !form.customer_id || !form.products)) {
        toast.add({ severity: 'error', summary: 'please fill all information !!', detail: 'Message Content',group: 'tc',life: 3000 }); 
    }

    form.post(route('quotations.store'), {
        onSuccess: () => {
            alert('Customer created successfully!');
        },
        onError: (errors) => {

            console.error(errors);
        },
    });
};
 


</script>
  

<script>
export default {
    props: {
        products: Array, // Products passed from the backend
    },
    data() {
        return {
            selectedProductIds: [], // Selected product IDs
            checked: false, // Checkbox state
            columns: [
                { field: 'id', header: 'No.' },
                { field: 'name', header: 'Name' },
                { field: 'unit', header: 'Unit' },
                { field: 'price', header: 'Unit Price' },
                { field: 'quantity', header: 'Qty' },
            ],
        };
    },
    computed: {
        formattedCustomers() {
            return this.customers.map(customer => ({
                id: customer.id,
                label: `${customer.name} (${customer.code})` // Combine name and email
            }));
        },
        // Filter products to get the selected product
        selectedProductsData() {
        if (this.selectedProductIds.length > 0) {
            return this.products.filter(product => this.selectedProductIds.includes(product.id));
        }
        return []; // Return an empty array if no products are selected
        },
    },
    
    methods: {
        onProductSelect(event) { // Handle product selection
            this.selectedProduct = event.value; 
        },

    },
    mounted() {
        this.$primevue.config.ripple = true;
    },
 
};

</script> 

