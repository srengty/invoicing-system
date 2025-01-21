<template>
    <div class="create-invoice">
            <div class="flex justify-between items-center p-3 mr-4">
                <h1 class="text-2xl">Create Customer</h1>
                <div class="flex gap-2">
                    <div class="">
                        <Button label="Back" icon="pi pi-times" class="p-button" type="submit" rounded/>
                    </div>
                    <div class="">
                        <Button label="Save" icon="pi pi-check" class="p-button" type="submit" rounded/>
                    </div>
                </div>
            </div>
            <form @submit.prevent="submit">
                <div class="p-3 grid grid-cols-1 md:grid-cols-3 gap-4 ml-4 mr-4">
                    <div>
                        <label for="name" class="block text-lg font-medium">Name</label>
                        <InputText id="invoice_no" v-model="form.name" class="w-full" placeholder="Enter Customer Name" />
                    </div>
                    <div>
                        <label for="code" class="block text-lg font-medium">Code</label>
                        <InputText id="code" v-model="form.code" class="w-full" placeholder="Enter code" />
                    </div>
                    <div>
                        <label for="address" class="block text-lg font-medium">Address</label>
                        <InputText id="address" v-model="form.address" class="w-full" placeholder="Enter address" />
                    </div>
                    <div>
                        <label for="email" class="block text-lg font-medium">Email</label>
                        <InputText id="email" v-model="form.email" class="w-full" placeholder="Enter email" />
                    </div>
                    <div>
                        <label for="phone" class="block text-lg font-medium">Phone</label>
                        <InputText id="phone" v-model="form.phone" class="w-full" placeholder="Enter phone number" />
                    </div>
                    <div>
                        <label for="telegram" class="block text-lg font-medium">Bank Telegram Number</label>
                        <InputText id="telegram" v-model="form.telegram" class="w-full" placeholder="Enter telegram number" />
                    </div>
                    <div>
                        <label for="website" class="block text-lg font-medium">Website</label>
                        <InputText id="website" v-model="form.website" class="w-full" placeholder="Enter website" />
                    </div>
                    <div>
                        <label for="bank_name" class="block text-lg font-medium">Bank Name</label>
                        <InputText id="bank_name" v-model="form.bank_name" class="w-full" placeholder="Enter bank name" />
                    </div>
                    <div>
                        <label for="bank_address" class="block text-lg font-medium">Bank Address</label>
                        <InputText id="bank_address" v-model="form.bank_address" class="w-full" placeholder="Enter bank address" />
                    </div>
                    <div>
                        <label for="bank_account_name" class="block text-lg font-medium">Bank Account Name</label>
                        <InputText id="bank_account_name" v-model="form.bank_account_name" class="w-full" placeholder="Enter bank account name" />
                    </div>
                    <div>
                        <label for="bank_account_number" class="block text-lg font-medium">Bank Account Number</label>
                        <InputText id="bank_account_number" v-model="form.bank_account_number" class="w-full" placeholder="Enter bank account number" />
                    </div>
                    <div>
                        <label for="bank_swift" class="block text-lg font-medium">Bank Swift</label>
                        <InputText id="bank_swift" v-model="form.bank_swift" class="w-full" placeholder="Enter bank swift" />
                    </div>
                </div>
            </form>
        </div>
</template>

<script setup>
import { InputText, Calendar, Dropdown, Button } from 'primevue';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    code: '',
    address: '',
    email: '',
    phone: '',
    telegram: '',
    bank_name: '',
    bank_address: '',
    bank_account_name: '',
    bank_account_number: '',
    bank_swift: '',
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
