<template>
    <div class="create-customer">
        <div class="flex justify-between items-center p-3 mr-4">
            <h1 class="text-2xl">Create Customer</h1>
            <div class="flex gap-2">
                <!-- Back Button (Navigation to the Customers List page) -->
                <Link :href="route('customers.index')">
                    <Button label="Back" icon="pi pi-times" class="p-button" rounded />
                </Link>

                <!-- Save Button to Trigger Form Submission -->
                <Button label="Save" icon="pi pi-check" class="p-button" @click="submit" rounded />
            </div>
        </div>

        <form @submit.prevent="submit">
            <div class="p-3 grid grid-cols-1 md:grid-cols-3 gap-4 ml-4 mr-4">
                <div>
                    <label for="name" class="block text-lg font-medium required">Name</label>
                    <InputText id="name" v-model="form.name" class="w-full" placeholder="Enter Customer Name" />
                    <Message v-if="form.errors.name" severity="error" size="small" variant="simple" class="col-span-2">{{ form.errors.name }}</Message>
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
import { InputText, Button, Message } from 'primevue';
import { router, useForm } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    errors: Object,
    redirect_route: String,
});
const emit = defineEmits(['success']);
// Create a form object using useForm hook from Inertia
const form = useForm({
    name: '',
    code: '',
    address: '',
    email: '',
    phone: '',
    telegram: '',
    website: '',
    bank_name: '',
    bank_address: '',
    bank_account_name: '',
    bank_account_number: '',
    bank_swift: '',
    redirect_route: props.redirect_route?? 'customers.index',
});

// Submit form to create a new customer
const submit = () => {
    form.post(route('customers.store'), {
        onSuccess: () => {
            // Optionally handle success, e.g., redirect to customers list page
            alert('Customer created successfully!');
            emit('success');
        },
        onError: (errors) => {
            // Handle validation errors, e.g., show error messages
            console.error(errors);
        },
    });
};
</script>
