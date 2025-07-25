<template>
    <div class="create-customer">
        <div class="flex justify-between items-center p-3 mr-4">
            <h1 class="text-2xl">
                {{ form.id ? "Edit Customer" : "Create Customer" }}
            </h1>
            <div class="flex gap-2">
                <!-- Back Button (Navigation to the Customers List page) -->
                <Link :href="route('customers.index')">
                    <Button
                        label="Back"
                        icon="pi pi-times"
                        class="p-button"
                        rounded
                    />
                </Link>

                <!-- Save Button to Trigger Form Submission -->
                <Button
                    label="Save"
                    icon="pi pi-check"
                    class="p-button"
                    @click="submit"
                    rounded
                />
            </div>
        </div>

        <form @submit.prevent="submit">
            <input type="hidden" name="_token" :value="page.props.csrf_token" />
            <div class="p-3 grid grid-cols-1 md:grid-cols-3 gap-4 ml-4 mr-4">
                <div>
                    <label for="code" class="block text-lg font-medium required"
                        >Customer category</label
                    >
                    <Select
                        v-model="form.customer_category_id"
                        :options="customerCategories"
                        option-value="id"
                        option-label="category_name_english"
                        class="w-full"
                    />
                </div>
                <div>
                    <label for="name" class="block text-lg font-medium"
                        >Name</label
                    >
                    <InputText
                        id="name"
                        v-model="form.name"
                        class="w-full"
                        placeholder="Enter Customer Name"
                    />
                </div>
                <div>
                    <label for="code" class="block text-lg font-medium"
                        >Code</label
                    >
                    <InputText
                        id="code"
                        v-model="form.code"
                        class="w-full"
                        placeholder="Enter code"
                    />
                </div>
                <div>
                    <label for="code" class="block text-lg font-medium"
                        >Credit period (days)</label
                    >
                    <InputText class="w-full" v-model="form.credit_period" />
                </div>
                <div>
                    <label for="address" class="block text-lg font-medium"
                        >Address</label
                    >
                    <InputText
                        id="address"
                        v-model="form.address"
                        class="w-full"
                        placeholder="Enter address"
                    />
                </div>
                <div>
                    <label for="email" class="block text-lg font-medium"
                        >Email</label
                    >
                    <InputText
                        id="email"
                        v-model="form.email"
                        class="w-full"
                        placeholder="Enter email"
                    />
                </div>
                <div>
                    <label for="phone" class="block text-lg font-medium"
                        >Phone</label
                    >
                    <InputText
                        id="phone"
                        v-model="form.phone_number"
                        class="w-full"
                        placeholder="Enter phone number"
                    />
                </div>
                <div>
                    <label for="telegram" class="block text-lg font-medium"
                        >Bank Telegram Number</label
                    >
                    <InputText
                        id="telegram"
                        v-model="form.telegram_number"
                        class="w-full"
                        placeholder="Enter telegram number"
                    />
                </div>
                <div>
                    <label for="website" class="block text-lg font-medium"
                        >Website</label
                    >
                    <InputText
                        id="website"
                        v-model="form.website"
                        class="w-full"
                        placeholder="Enter website"
                    />
                </div>
                <div>
                    <label for="bank_name" class="block text-lg font-medium"
                        >Bank Name</label
                    >
                    <InputText
                        id="bank_name"
                        v-model="form.bank_name"
                        class="w-full"
                        placeholder="Enter bank name"
                    />
                </div>
                <div>
                    <label for="bank_address" class="block text-lg font-medium"
                        >Bank Address</label
                    >
                    <InputText
                        id="bank_address"
                        v-model="form.bank_address"
                        class="w-full"
                        placeholder="Enter bank address"
                    />
                </div>
                <div>
                    <label
                        for="bank_account_name"
                        class="block text-lg font-medium"
                        >Bank Account Name</label
                    >
                    <InputText
                        id="bank_account_name"
                        v-model="form.bank_account_name"
                        class="w-full"
                        placeholder="Enter bank account name"
                    />
                </div>
                <div>
                    <label
                        for="bank_account_number"
                        class="block text-lg font-medium"
                        >Bank Account Number</label
                    >
                    <InputText
                        id="bank_account_number"
                        v-model="form.bank_account_number"
                        class="w-full"
                        placeholder="Enter bank account number"
                    />
                </div>
                <div>
                    <label for="bank_swift" class="block text-lg font-medium"
                        >Bank Swift</label
                    >
                    <InputText
                        id="bank_swift"
                        v-model="form.bank_swift"
                        class="w-full"
                        placeholder="Enter bank swift"
                    />
                </div>
            </div>
        </form>
    </div>
</template>

<script setup>
import { InputText, Button, Select } from "primevue";
import { useForm } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";
import { Inertia } from "@inertiajs/inertia";
import { usePage } from "@inertiajs/vue3";

// Prop to receive customer data
const { customer, customerCategories } = usePage().props;

// Initialize form with customer data if editing, or empty for new customer
const form = useForm({
    id: customer?.id || "",
    name: customer?.name || "",
    code: customer?.code || "",
    credit_period: customer?.credit_period || "",
    address: customer?.address || "",
    email: customer?.email || "",
    phone_number: customer?.phone_number || "",
    telegram_number: customer?.telegram_number || "",
    website: customer?.website || "",
    bank_name: customer?.bank_name || "",
    bank_address: customer?.bank_address || "",
    bank_account_name: customer?.bank_account_name || "",
    bank_account_number: customer?.bank_account_number || "",
    bank_swift: customer?.bank_swift || "",
    customer_category_id: customer?.customer_category_id || "",
    _token: page.props.csrf_token,
});

// Submit function to either create or update the customer
const submit = () => {
    if (form.id) {
        // If customer ID exists, it's an update
        form.put(route("customers.update", form.id), {
            headers: {
                "X-CSRF-TOKEN": page.props.csrf_token,
                "X-Requested-With": "XMLHttpRequest",
            },
            onSuccess: () => {
                Inertia.visit(route("customers.index")); // Redirect to the customers list after success
                alert("Customer updated successfully!");
            },
            onError: (errors) => {
                console.error(errors);
            },
        });
    } else {
        // If no ID, it's a new customer creation
        form.post(route("customers.store"), {
            headers: {
                "X-CSRF-TOKEN": page.props.csrf_token,
                "X-Requested-With": "XMLHttpRequest",
            },
            onSuccess: () => {
                Inertia.visit(route("customers.index")); // Redirect to the customers list after success
                alert("Customer created successfully!");
            },
            onError: (errors) => {
                console.error(errors);
            },
        });
    }
};
</script>
