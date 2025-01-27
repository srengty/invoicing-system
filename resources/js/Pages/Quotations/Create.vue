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
                    <div class="w-60 "><Button icon="pi pi-plus" label="Add Item" type="submit" rounded /></div>             
                </div>

            </div> 
            <div class="buttons mt-4 mr-4 flex justify-end">
                    <Button v-ripple label="Submit" icon="pi pi-check" @click="submit" class="p-button-rounded p-button-success" />
                    <Button v-ripple label="Cancel" class="p-button-rounded p-button-secondary ml-2" />
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
import { useToast } from "primevue/usetoast";

import { useForm } from '@inertiajs/vue3';


const form = useForm({
      quotation_no: '',
      quotation_date: '',
      address: '',
      phone_number: '',
      customer_id: '',
      products: [],
});

const submit = ({ valid }) => {
    // Check if any required fields are empty
    if (!form.quotation_no || !form.quotation_date || !form.address || !form.phone_number || !form.customer_id || !form.products) {
        alert('!!! Please fill it all !!!');
        return;
    }
    // if (valid) {
    //     toast.add({ severity: 'success', summary: 'Form is submitted.', life: 3000 });
    // }

    // Submit the form data to the specified route
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
        customers: Array, // Receive customers data from backend
        products: Array,
    },

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

<!--  
<script>

export default {
    props: {
        customers: Array,
        products: Array,
    },
    setup(props) {
    const form = useForm({
      quotation_no: '',
      quotation_date: '',
      address: '',
      phone_number: '',
      customer_id: '',
      products: [],
    });

    const customerOptions = props.customers;
    // const productOptions = props.products;

    // const submit = () => {
    //   form.post(route('quotations.store'));
    // };

    return { form, customerOptions};
  },
};
</script>   -->


