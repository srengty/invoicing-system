<template>
    <Head title="Update Customer" />
    <GuestLayout>
        <div class="update-customer">
            <div class="flex justify-between items-center p-3 mr-4">
                <h1 class="text-2xl">Update Customer</h1>
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
                        <label for="name" class="block text-lg font-medium">Name</label>
                        <InputText id="name" v-model="form.name" class="w-full" placeholder="Enter Customer Name" />
                    </div>
                    <div>
                        <label for="email" class="block text-lg font-medium">Email</label>
                        <InputText id="email" v-model="form.email" class="w-full" placeholder="Enter Email" />
                    </div>
                    <div>
                        <label for="address" class="block text-lg font-medium">Address</label>
                        <InputText id="address" v-model="form.address" class="w-full" placeholder="Enter Address" />
                    </div>
                    <div>
                        <label for="phone" class="block text-lg font-medium">Phone</label>
                        <InputText id="phone" v-model="form.phone" class="w-full" placeholder="Enter Phone Number" />
                    </div>
                    <div>
                        <label for="telegram" class="block text-lg font-medium">Telegram Number</label>
                        <InputText id="telegram" v-model="form.telegram" class="w-full" placeholder="Enter Telegram Number" />
                    </div>
                    <div>
                        <label for="website" class="block text-lg font-medium">Website</label>
                        <InputText id="website" v-model="form.website" class="w-full" placeholder="Enter Website" />
                    </div>
                    <div>
                        <label for="bank_name" class="block text-lg font-medium">Bank Name</label>
                        <InputText id="bank_name" v-model="form.bank_name" class="w-full" placeholder="Enter Bank Name" />
                    </div>
                    <div>
                        <label for="bank_address" class="block text-lg font-medium">Bank Address</label>
                        <InputText id="bank_address" v-model="form.bank_address" class="w-full" placeholder="Enter Bank Address" />
                    </div>
                    <div>
                        <label for="bank_account_name" class="block text-lg font-medium">Bank Account Name</label>
                        <InputText id="bank_account_name" v-model="form.bank_account_name" class="w-full" placeholder="Enter Bank Account Name" />
                    </div>
                    <div>
                        <label for="bank_account_number" class="block text-lg font-medium">Bank Account Number</label>
                        <InputText id="bank_account_number" v-model="form.bank_account_number" class="w-full" placeholder="Enter Bank Account Number" />
                    </div>
                    <div>
                        <label for="bank_swift" class="block text-lg font-medium">Bank Swift</label>
                        <InputText id="bank_swift" v-model="form.bank_swift" class="w-full" placeholder="Enter Bank Swift" />
                    </div>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>

<script>
import { ref } from "vue";
import { Head, Link } from '@inertiajs/vue3';
import { InputText, Button } from 'primevue';
import GuestLayout from '@/Layouts/GuestLayout.vue';

export default {
    components: {
        Head,
        Link,
        InputText,
        Button,
        GuestLayout
    },
    props: {
        customer: Object,
    },
    data() {
        return {
            // Form model initialized with existing customer data
            form: {
                name: this.customer.name || '',
                email: this.customer.email || '',
                address: this.customer.address || '',
                phone: this.customer.phone_number || '',
                telegram: this.customer.telegram_number || '',
                website: this.customer.website || '',
                bank_name: this.customer.bank_name || '',
                bank_address: this.customer.bank_address || '',
                bank_account_name: this.customer.bank_account_name || '',
                bank_account_number: this.customer.bank_account_number || '',
                bank_swift: this.customer.bank_swift || '',
            },
        };
    },
    methods: {
        submit() {
            // Call a method to handle the update of the customer data
            this.$inertia.put(route('customers.update', this.customer.id), this.form, {
                onSuccess: () => {
                    // Optionally handle success (e.g., redirect to customer list page)
                    this.$inertia.visit(route('customers.index'));
                },
                onError: (error) => {
                    console.error('Error updating customer:', error);
                }
            });
        }
    }
};
</script>

<style scoped>
/* Customize the style for input boxes */
.p-inputtext {
    background-color: #f4f4f4;
    border-radius: 0.25rem;
}
</style>
