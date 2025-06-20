<template>
    <Head title="Customer Details" />
    <GuestLayout>
        <div class="show-customer">
            <div class="flex justify-between items-center p-3 mr-4">
                <h1 class="text-2xl">Customer Details</h1>
                <div class="flex gap-2">
                    <!-- Back Button (Navigation to the Customers List page) -->
                    <Link :href="route('customers.index')">
                        <Button label="Back" icon="pi pi-times" class="p-button" rounded />
                    </Link>
                </div>
            </div>

            <div class="p-3 grid grid-cols-1 md:grid-cols-3 gap-4 ml-4 mr-4 mb-4">
                <div>
                    <label class="block text-lg font-medium">Category</label>
                    <div class="p-inputtext p-component w-full border p-2 rounded">{{
                        customerCategory?.category_name_english??'N/A'
                    }} ({{ customerCategory?.category_name_khmer??'N/A' }})</div>
                </div>
                <div>
                    <label class="block text-lg font-medium">Name</label>
                    <div class="p-inputtext p-component w-full border p-2 rounded">{{
                        customer.name
                    }}</div>
                </div>
                <div>
                    <label class="block text-lg font-medium">Code</label>
                    <div class="p-inputtext p-component w-full border p-2 rounded">{{
                        customer.code
                    }}</div>
                </div>
                <div>
                    <label class="block text-lg font-medium">Credit period</label>
                    <div class="p-inputtext p-component w-full border p-2 rounded">{{
                        customer.credit_period
                    }}</div>
                </div>
                <div>
                    <label class="block text-lg font-medium">Email</label>
                    <div class="p-inputtext p-component w-full border p-2 rounded">{{
                        customer.email
                    }}</div>
                </div>
                <div>
                    <label for="address" class="block text-lg font-medium">Address</label>
                    <div class="p-inputtext p-component w-full border p-2 rounded">{{
                        customer.address
                    }}</div>
                </div>
                <div>
                    <label for="phone" class="block text-lg font-medium">Phone</label>
                    <div class="p-inputtext p-component w-full border p-2 rounded">{{
                        customer.phone_number
                    }}</div>
                </div>
                <div>
                    <label for="telegram" class="block text-lg font-medium">Telegram Number</label>
                    <div class="p-inputtext p-component w-full border p-2 rounded">{{
                        customer.telegram_number
                    }}</div>
                </div>
                <div>
                    <label for="website" class="block text-lg font-medium">Website</label>
                    <div class="p-inputtext p-component w-full border p-2 rounded">{{
                        customer.website
                    }}</div>
                </div>
                <div>
                    <label for="bank_name" class="block text-lg font-medium">Bank Name</label>
                    <div class="p-inputtext p-component w-full border p-2 rounded">{{
                        customer.bank_name
                    }}</div>
                </div>
                <div>
                    <label for="bank_address" class="block text-lg font-medium">Bank Address</label>
                    <div class="p-inputtext p-component w-full border p-2 rounded">{{
                        customer.bank_address
                    }}</div>
                </div>
                <div>
                    <label for="bank_account_name" class="block text-lg font-medium">Bank Account Name</label>
                    <div class="p-inputtext p-component w-full border p-2 rounded">{{
                        customer.bank_account_name
                    }}</div>
                </div>
                <div>
                    <label for="bank_account_number" class="block text-lg font-medium">Bank Account Number</label>
                    <div class="p-inputtext p-component w-full border p-2 rounded">{{
                        customer.bank_account_number
                    }}</div>
                </div>
                <div class="mb-4">
                    <label for="bank_swift" class="block text-lg font-medium">Bank Swift</label>
                    <div class="p-inputtext p-component w-full border p-2 rounded">{{
                        customer.bank_swift
                    }}</div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<script>
import { ref } from "vue";
import { Head, Link } from '@inertiajs/vue3';
import { Button } from 'primevue';
import GuestLayout from '@/Layouts/GuestLayout.vue';

export default {
    components: {
        Head,
        Link,
        Button,
        GuestLayout
    },
    props: {
        customer: Object,
        customerCategory: Object
    },
};
</script>

<style scoped>
/* Customize the style to look like a form */
.p-inputtext {
    background-color: #f4f4f4; /* Lighter background for display */
    pointer-events: none; /* Disable interaction */
}
</style>
