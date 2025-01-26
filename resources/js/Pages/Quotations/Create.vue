<template>
    <Head title="Create Quotation" />
    <GuestLayout>
        <form @submit.prevent="submit">
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
        
    <!-- ----------------------------------------------------------------------------------------------------------------------------------------------------------------------- -->
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
            <div class="p-8 grid md:grid-rows-2 gap-4"> <!--Rows Customer/ Organization-->
                <div class="flex flex-row gap-4 items-end w-1/3">             
                    <div class="flex flex-col gap-2 w-full" >
                        <label for="c_name">Customer/ Organization</label>
                        <!-- <Select v-model="form.c_name" editable :options="customer" optionLabel="c_name" placeholder="Input" class="w-65"/>                      -->
                        <Select
                            v-model="form.c_name"
                            :options="customers"
                            optionLabel="name"  
                            optionValue="name"  
                            placeholder="Select a Customer"
                            class="w-65"
                        />                 
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
                        <Select v-model="form.p_name" :options="products" optionLabel="name" optionValue="name" placeholder="Select Product" class="w-full md:w-65" />
                    </div>
                    <div class="w-60 "><Button icon="pi pi-plus" label="Add Item" type="submit" rounded /></div>             
                </div>

            </div> 
            <div class="buttons mt-4 mr-4 flex justify-end">
                    <Button label="Submit" icon="pi pi-check" @click="submit" class="p-button-rounded p-button-success" />
                    <Button label="Cancel" class="p-button-rounded p-button-secondary ml-2" />
            </div>
            <Payment></Payment>
      
        </form>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from "vue";
import Select from 'primevue/select';
import DatePicker from 'primevue/datepicker';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import Payment from '@/Components/Payment.vue';

import { useForm } from '@inertiajs/vue3'


const icondisplay = ref();

const selectedCity = ref();
const items = ref([
    { p_name: 'Item 1', code: ' ' },
    { p_name: 'Item 2', code: ' ' },
    { p_name: 'Item 3', code: ' ' },
    { p_name: 'Item 4', code: ' ' },
    { p_name: 'Item 5', code: ' ' }
]);

const form = useForm({
    quotation_no: null,
    quotation_date: null,
    address: null,
    phone_number: null,  
    c_name: null,
    p_name: null,
})

// Submit form to create a new quotation
const submit = () => {
    form.post(route('quotations.store'), {
        onSuccess: () => {
            // Optionally handle success, e.g., redirect to customers list page
            alert('Quotations is created successfully!');
        },
        onError: (errors) => {
            // Handle validation errors, e.g., show error messages
            console.error(errors);
        },
    });
};
</script>

<script>
export default {
    props: {
        customers: Array, // Receive customers data from backend
        products: Array,
    },
    data() {
        return {
            form: {
                c_name: null, // Bind selected customer here
                p_name: null,
            },
        };
    },
};
</script>


