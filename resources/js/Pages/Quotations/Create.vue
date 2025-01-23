<template>
    <Head title="Create Invoice" />
    <GuestLayout>
        <div class="p-4 grid  md:grid-cols-2 gap-0 w-2/3 "> <!--Column -->
            <div class="p-4 grid md:grid-rows-2 gap-4">  <!--Rows in column -->
                <div class="flex flex-col gap-2">
                    <label for="quotationNo">Quotation No:</label>                       
                    <Select v-model="selectedCustomer" editable :options="customer" optionLabel="name" placeholder="Input" class="w-full md:w-60" />
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
                        <InputIcon class="pi pi-times" style="color: red"/>
                    </IconField>
                </div>
                <div class="flex flex-col gap-2 ">
                    <label for="address">Contact</label>
                    <IconField  class="w-full md:w-60" >
                        <InputText id="contact" v-model="value"  placeholder="Input" class="w-full md:w-60"  />
                      
                    </IconField>
                </div>
            </div>
               
        </div>


        <div class="p-4 grid md:grid-rows-2 gap-4"> <!--Rows -->
                <div class="flex flex-col gap-2">
                    <label for="customer">Customer/ Organization</label>
                    <Select v-model="selectedCustomer" editable :options="customer" optionLabel="name" placeholder="Input" class="w-full md:w-56" />
               </div>
               <div class="flex flex-col gap-2 ">
                    <label for="item">Item</label>
                    <Select v-model="selectedCity" editable :options="cities" optionLabel="name" placeholder="Input" class="w-full md:w-56" />
               </div>
        </div>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from "vue";
import Select from 'primevue/select';
import DatePicker from 'primevue/datepicker';
import InputText from 'primevue/inputtext';
 
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';


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
