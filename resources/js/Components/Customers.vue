<template>
    <div class="create-customer text-sm">
        <form @submit.prevent="submit" class="">
            <div class="p-3 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 ml-4 mr-4">
                <div>
                    <label for="code" class="block text-sm font-medium required"
                        >Customer category</label
                    >
                    <Select
                        v-model.number="form.customer_category_id"
                        :options="customerCategories"
                        option-value="id"
                        option-label="category_name_english"
                        class="w-full"
                        placeholder="Enter customer category"
                    />
                </div>
                <div>
                    <label for="name" class="block text-sm font-medium required"
                        >Customer/Organization name</label
                    >
                    <InputText
                        id="name"
                        v-model="form.name"
                        class="w-full"
                        size="small"
                        placeholder="Enter Customer Name"
                    />
                    <Message
                        v-if="form.errors.name"
                        severity="error"
                        size="small"
                        variant="simple"
                        class="col-span-2"
                        >{{ form.errors.name }}</Message
                    >
                </div>
                <div>
                    <label for="code" class="block text-sm font-medium required"
                        >Customer code</label
                    >
                    <InputText
                        id="code"
                        v-model="form.code"
                        class="w-full"
                        size="small"
                        placeholder="Enter code"
                    />
                </div>
                <div>
                    <label for="code" class="block text-sm font-medium required"
                        >Credit period (days)</label
                    >
                    <InputText
                        class="w-full"
                        size="small"
                        v-model="form.credit_period"
                        placeholder="Enter credit period"
                    />
                </div>
                <div>
                    <label
                        for="address"
                        class="block text-sm font-medium required"
                        >Address</label
                    >
                    <InputText
                        id="address"
                        v-model="form.address"
                        class="w-full"
                        size="small"
                        placeholder="Enter address"
                    />
                </div>
                <div>
                    <label
                        for="website"
                        class="block text-sm font-medium required"
                        >Website</label
                    >
                    <InputText
                        id="website"
                        v-model="form.website"
                        class="w-full"
                        size="small"
                        placeholder="Enter website"
                    />
                </div>
                <div class="col-span-1 sm:col-span-2 md:col-span-3"><hr /></div>
                <div>
                    <label
                        for="phone"
                        class="block text-sm font-medium required"
                        >Contact person name</label
                    >
                    <InputText
                        id="name"
                        v-model="form.contact_person"
                        class="w-full"
                        size="small"
                        placeholder="Enter contact person name"
                    />
                </div>
                <div>
                    <label
                        for="phone"
                        class="block text-sm font-medium required"
                        >Phone</label
                    >
                    <InputText
                        id="phone"
                        v-model="form.phone"
                        class="w-full"
                        size="small"
                        placeholder="Enter phone number"
                    />
                </div>
                <div>
                    <label
                        for="telegram"
                        class="block text-sm font-medium required"
                        >Telegram Number</label
                    >
                    <InputText
                        id="telegram"
                        v-model="form.telegram"
                        class="w-full"
                        size="small"
                        placeholder="Enter telegram number"
                    />
                </div>
                <div>
                    <label
                        for="email"
                        class="block text-sm font-medium required"
                        >Email</label
                    >
                    <InputText
                        id="email"
                        v-model="form.email"
                        class="w-full"
                        size="small"
                        placeholder="Enter email"
                    />
                </div>
                <div class="col-span-1 sm:col-span-2 md:col-span-3"><hr /></div>
                <div>
                    <label
                        for="bank_name"
                        class="block text-sm font-medium required"
                        >Bank name</label
                    >
                    <InputText
                        id="bank_name"
                        v-model="form.bank_name"
                        class="w-full"
                        size="small"
                        placeholder="Enter bank name"
                    />
                </div>
                <div>
                    <label
                        for="bank_address"
                        class="block text-sm font-medium required"
                        >Bank address</label
                    >
                    <InputText
                        id="bank_address"
                        v-model="form.bank_address"
                        class="w-full"
                        size="small"
                        placeholder="Enter bank address"
                    />
                </div>
                <div>
                    <label
                        for="bank_account_name"
                        class="block text-sm font-medium required"
                        >Bank account name</label
                    >
                    <InputText
                        id="bank_account_name"
                        v-model="form.bank_account_name"
                        class="w-full"
                        size="small"
                        placeholder="Enter bank account name"
                    />
                </div>
                <div>
                    <label
                        for="bank_account_number"
                        class="block text-sm font-medium required"
                        >Bank account number</label
                    >
                    <InputText
                        id="bank_account_number"
                        v-model="form.bank_account_number"
                        class="w-full"
                        size="small"
                        placeholder="Enter bank account number"
                    />
                </div>
                <div>
                    <label
                        for="bank_swift"
                        class="block text-sm font-medium required"
                        >Bank swift</label
                    >
                    <InputText
                        id="bank_swift"
                        v-model="form.bank_swift"
                        class="w-full"
                        size="small"
                        placeholder="Enter bank swift"
                    />
                </div>
            </div>
            <div class="flex justify-end gap-2 mr-4 mb-2">
                <Button label="Create" class="p-button-primary w-38" type="submit" @click="submit" outlined />
            </div>
        </form>
    </div>
</template>

<script setup>
import { InputText, Button, Message, Fieldset, Select } from "primevue";
import { router, useForm } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    errors: Object,
    redirect_route: String,
    customerCategories: Array,
});
const emit = defineEmits(["success"]);
// Create a form object using useForm hook from Inertia
const form = useForm({
    name: "",
    code: "",
    address: "",
    email: "",
    phone: "",
    contact_person: "",
    telegram: "",
    website: "",
    bank_name: "",
    bank_address: "",
    bank_account_name: "",
    bank_account_number: "",
    bank_swift: "",
    redirect_route: props.redirect_route ?? "customers.index",
    customer_category_id: "",
});

// Submit form to create a new customer
const submit = () => {
    form.post(route("customers.store"), {
        onSuccess: () => {
            // Optionally handle success, e.g., redirect to customers list page
            alert("Customer created successfully!");
            emit("success");
        },
        onError: (errors) => {
            // Handle validation errors, e.g., show error messages
            console.error(errors);
        },
    });
};



</script>
