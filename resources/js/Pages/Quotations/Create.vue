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
                            <!-- <p>Selected Customer code: {{ form.customer_id }}</p> -->

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
                        <Select 
                        v-model="form.p_name" 
                        :options="products" 
                        optionLabel="name" 
                        optionValue="name" 
                        placeholder="Select Product" 
                        class="w-full md:w-65" />
                    </div>
                    <div class="w-60 "><Link :href="route('products.create')"><Button icon="pi pi-plus" label="Add Item" type="submit" rounded /></Link></div>             
                </div>

            </div> 

            <div>
                <DataTable :value="products" paginator :rows="5" :rowsPerPageOptions="[5, 10, 20, 50]" striped>
                    <Column v-for="col of columns" :key="col.field" :field="col.field" :header="col.header" sortable />
                    <Column header="Actions">
                        <template #body="slotProps">
                            <div class="flex gap-2">
                                <Button
                                    icon="pi pi-pencil"
                                    class="p-button-warning"
                                    label="Edit"
                                    @click="openForm(slotProps.data)"
                                    rounded
                                />
                                <Button
                                    icon="pi pi-trash"
                                    class="p-button-danger"
                                    label="Delete"
                                    @click="deleteProduct(slotProps.data.id)"
                                    rounded
                                />
                            </div>
                        </template>
                    </Column>
                </DataTable>
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
import { ref } from "vue";
import Select from 'primevue/select';
import DatePicker from 'primevue/datepicker';
import InputText from 'primevue/inputtext';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import Button from 'primevue/button';
import { Form } from '@primevue/forms';
import { useForm } from '@inertiajs/vue3';
import Toast from 'primevue/toast';
import { DataTable, Column} from "primevue";
import { useToast } from "primevue/usetoast";

defineProps({
    products: {
        type: Array,
        required: true,
    },
    customers: {
        type: Array,
        required: true,
    },
});

const toast = useToast();

const form = useForm({
      quotation_no: '',
      quotation_date: '',
      address: '',
      phone_number: '',
      customer_id: '',
      products: [],
});

// Define columns for DataTable
const columns = [
    { field: 'id', header: 'ID' },
    { field: 'code', header: 'Code' },
    { field: 'name', header: 'Name' },
    { field: 'unit', header: 'Unit' },
    { field: 'price', header: 'Price' },
    { field: 'quantity', header: 'Quantity' },
    { field: 'category', header: 'Category' },
];


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

    computed: {
        formattedCustomers() {
            return this.customers.map(customer => ({
                id: customer.id,
                label: `${customer.name} (${customer.code})` // Combine name and email
            }));
        },

    },
    mounted() {
        this.$primevue.config.ripple = true;
    },
 
};

</script> 

