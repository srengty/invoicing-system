<template>
    <div class="create-invoice">
            <div class="flex justify-between items-center p-3 mr-4">
                <h1 class="text-2xl">Create Invoice</h1>
                <div class="flex gap-4">
                    <div class="">
                        <Button label="Add Items" icon="pi pi-plus" class="p-button-success" type="submit" rounded/>
                    </div>
                    <div>
                            <Button label="Add New Customer" icon="pi pi-plus" class="p-button-success" type="submit" rounded/>
                    </div>
                    <div class="">
                        <Button label="Save Invoice" icon="pi pi-check" class="p-button-success" type="submit" rounded/>
                    </div>
                </div>
            </div>
            <form @submit.prevent="submit">
                <div class="p-3 grid grid-cols-1 md:grid-cols-3 gap-4 ml-4 mr-4">
                    <div>
                        <label for="invoice_no" class="block text-lg font-medium">Invoice No</label>
                        <InputText id="invoice_no" v-model="form.invoice_no" class="w-full" placeholder="Enter invoice number" />
                    </div>
                    <div>
                        <label for="agreement_no" class="block text-lg font-medium">Agreement No</label>
                        <Dropdown id="agreement_no" v-model="form.agreement_no" :options="agreementOptions" optionLabel="label" optionValue="value" class="w-full" placeholder="Select agreement number" />
                    </div>
                    <div>
                        <label for="quotation_no" class="block text-lg font-medium">Quotation No</label>
                        <Dropdown id="quotation_no" v-model="form.quotation_no" :options="quotationOptions" optionLabel="label" optionValue="value" class="w-full" placeholder="Select quotation number" />
                    </div>
                    <div>
                        <label for="customer_id" class="block text-lg font-medium">Customer/Organization name</label>
                        <Dropdown id="customer_id" v-model="form.customer_id" :options="customerOptions" optionLabel="label" optionValue="value" class="w-full" placeholder="Select customer" />
                    </div>
                    <div>
                        <label for="address" class="block text-lg font-medium">Address</label>
                        <InputText id="address" v-model="form.address" class="w-full" placeholder="Enter address" />
                    </div>
                    <div>
                        <label for="phone" class="block text-lg font-medium">Phone</label>
                        <InputText id="phone" v-model="form.phone" class="w-full" placeholder="Enter phone number" />
                    </div>
                    <div>
                        <label for="start_date" class="block text-lg font-medium">Start Date</label>
                        <Calendar id="start_date" v-model="form.start_date" class="w-full" placeholder="Select start date" />
                    </div>
                    <div>
                        <label for="end_date" class="block text-lg font-medium">End Date</label>
                        <Calendar id="end_date" v-model="form.end_date" class="w-full" placeholder="Select end date" />
                    </div>
                </div>
            </form>
        </div>
</template>

<script setup>
import { InputText, Calendar, Dropdown, Button } from 'primevue';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    invoice_no: '',
    agreement_no: '',
    quotation_no: '',
    customer_id: '',
    address: '',
    phone: '',
    start_date: '',
    end_date: '',
    sub_total: '',
    status: '',
});

// Options for dropdowns
const agreementOptions = [
    { label: 'Agreement 001', value: '001' },
    { label: 'Agreement 002', value: '002' },
    { label: 'Agreement 003', value: '003' },
];

const quotationOptions = [
    { label: 'Quotation 1001', value: '1001' },
    { label: 'Quotation 1002', value: '1002' },
    { label: 'Quotation 1003', value: '1003' },
];

const customerOptions = [
    { label: 'Customer A', value: 'A' },
    { label: 'Customer B', value: 'B' },
    { label: 'Customer C', value: 'C' },
];

// Submit form
const submit = () => {
    form.post('/invoices', {
        onSuccess: () => {
            // Optional: Add success handling
            alert('Invoice created successfully!');
        },
        onError: (errors) => {
            // Optional: Handle errors
            console.error(errors);
        }
    });
};
</script>
