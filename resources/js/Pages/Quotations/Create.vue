<template>
    <Head title="Create Quotation" />
    <GuestLayout>
        <div class="p-4 grid  md:grid-cols-2 gap-0 w-2/3 "> <!--Column -->
            <div class="p-4 grid md:grid-rows-2 gap-4">  <!--Rows in column -->
                <div class="flex flex-col gap-2">
                    <label for="quotationNo">Quotation No:</label>    
                    <InputText id="quotationnumber" v-model="value"  placeholder="Input" class="w-full md:w-60"  />
                </div>
                <div class="flex flex-col gap-2">
                    <label for="icondisplay">Date:</label>
                    <DatePicker v-model="icondisplay" showIcon fluid iconDisplay="input" inputId="icondisplay" placeholder="Select" class="w-full md:w-60"/>
                </div>
            </div>
       
<!-- ----------------------------------------------------------------------------------------------------------------------------------------------------------------------- -->
            <div class="p-4 grid md:grid-rows-2 gap-4">   
                <div class="flex flex-col gap-2 ">
                    <label for="address">Address</label>
                    <IconField  class="w-full md:w-60" >
                        <InputText id="address" v-model="value"  placeholder="Input" class="w-full md:w-60"  />
                        <InputIcon class="pi pi-times-circle" style=""/>
                    </IconField>
                </div>
                <div class="flex flex-col gap-2 ">
                    <label for="address">Contact</label>
                    <IconField  class="w-full md:w-60" >
                        <InputText id="contact" v-model="value"  placeholder="Input" class="w-full md:w-60"  />
                        <InputIcon class="pi pi-times-circle" style=""/>
                    </IconField>
                </div>
            </div>   
        </div>
        <div class="p-8 grid md:grid-rows-2 gap-4"> <!--Rows Customer/ Organization-->
            <div class="flex flex-row gap-4 items-end w-1/3">             
                <div class="flex flex-col gap-2 w-full" >
                    <label for="customer">Customer/ Organization</label>
                    <Select v-model="selectedCustomer" editable :options="customer" optionLabel="name" placeholder="Input" class="w-65"/>                     
                </div>
               <div class="w-60 "><Button icon="pi pi-plus" label="Add customer" rounded  type="submit" /></div>
            </div>
               
            <div class="flex flex-row gap-4 items-end w-1/3">             
                <div class="flex flex-col gap-2 w-full" >
                    <label for="item">Item</label>
                    <Select v-model="selectedCity" editable :options="cities" optionLabel="name" placeholder="Input" class="w-full md:w-65" />
                </div>
                <div class="w-60 "><Button icon="pi pi-plus" label="Add Item" type="submit" rounded /></div>             
            </div>

        </div>  
        <Payment></Payment>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from "vue";
import Select from 'primevue/select';
import DatePicker from 'primevue/datepicker';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import Payment from '@/Components/Payment.vue';

const icondisplay = ref();

const selectedCity = ref();
const cities = ref([
    { name: 'Item 1', code: ' ' },
    { name: 'Item 2', code: ' ' },
    { name: 'Item 3', code: ' ' },
    { name: 'Item 4', code: ' ' },
    { name: 'Item 5', code: ' ' }
]);
const selectedCustomer = ref();
const customer = ref([
    { name: 'Mr. Lee', code: ' ' },
    { name: 'Mr. Kim', code: ' ' },
    { name: 'Mrs. Leng', code: ' ' },
    { name: 'Ms. Pich', code: ' ' },
    { name: 'Mr. Makara', code: ' ' }
]);
</script>

<style scoped>

</style>
