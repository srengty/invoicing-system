<template>
    <div class="create-customer text-sm">
        <form @submit.prevent="submit" class="">
            <div class="p-3 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 ml-4 mr-4">
                <!-- Customer Category -->
                <div>
                    <label for="code" class="block text-sm font-medium required">
                        Customer category
                    </label>
                    <Select
                        v-model="form.customer_category_id"
                        :options="customerCategories"
                        optionLabel="category_name_english"
                        optionValue="id"
                        placeholder="Select customer category"
                        class="w-full"
                        :disabled="mode === 'view'"
                    />
                    <Message
                        v-if="form.errors.customer_category_id"
                        severity="error"
                        size="small"
                        variant="simple"
                        class="col-span-2"
                    >
                        {{ form.errors.customer_category_id }}
                    </Message>
                </div>

                <!-- Customer/Organization Name -->
                <div>
                    <label for="name" class="block text-sm font-medium required">
                        Customer/Organization name
                    </label>
                    <InputText
                        id="name"
                        v-model="form.name"
                        class="w-full"
                        size="small"
                        placeholder="Enter Customer Name"
                        :disabled="mode === 'view'"
                    />
                    <Message
                        v-if="form.errors.name"
                        severity="error"
                        size="small"
                        variant="simple"
                        class="col-span-2"
                    >
                        {{ form.errors.name }}
                    </Message>
                </div>

                <!-- Customer Code -->
                <div>
                    <label for="code" class="block text-sm font-medium required">
                        Customer code
                    </label>
                    <InputText
                        id="code"
                        v-model="form.code"
                        class="w-full"
                        size="small"
                        placeholder="Enter code"
                        :disabled="mode === 'view'"
                    />
                    <Message
                        v-if="form.errors.code"
                        severity="error"
                        size="small"
                        variant="simple"
                        class="col-span-2"
                    >
                        {{ form.errors.code }}
                    </Message>
                </div>

                <!-- Credit Period -->
                <div>
                    <label for="credit_period" class="block text-sm font-medium required">
                        Credit period (days)
                    </label>
                    <InputText
                        class="w-full"
                        size="small"
                        v-model="form.credit_period"
                        placeholder="Enter credit period"
                        :disabled="mode === 'view'"
                    />
                    <Message
                        v-if="form.errors.credit_period"
                        severity="error"
                        size="small"
                        variant="simple"
                        class="col-span-2"
                    >
                        {{ form.errors.credit_period }}
                    </Message>
                </div>

                <!-- Address -->
                <div>
                    <label for="address" class="block text-sm font-medium required">
                        Address
                    </label>
                    <InputText
                        id="address"
                        v-model="form.address"
                        class="w-full"
                        size="small"
                        placeholder="Enter address"
                        :disabled="mode === 'view'"
                    />
                    <Message
                        v-if="form.errors.address"
                        severity="error"
                        size="small"
                        variant="simple"
                        class="col-span-2"
                    >
                        {{ form.errors.address }}
                    </Message>
                </div>

                <!-- Website -->
                <div>
                    <label for="website" class="block text-sm font-medium required">
                        Website
                    </label>
                    <InputText
                        id="website"
                        v-model="form.website"
                        class="w-full"
                        size="small"
                        placeholder="Enter website"
                        :disabled="mode === 'view'"
                    />
                    <Message
                        v-if="form.errors.website"
                        severity="error"
                        size="small"
                        variant="simple"
                        class="col-span-2"
                    >
                        {{ form.errors.website }}
                    </Message>
                </div>

                <div class="col-span-1 sm:col-span-2 md:col-span-3">
                    <hr />
                </div>

                <!-- Contact Person Name -->
                <div>
                    <label for="contact_person" class="block text-sm font-medium required">
                        Contact person name
                    </label>
                    <InputText
                        id="contact_person"
                        v-model="form.contact_person"
                        class="w-full"
                        size="small"
                        placeholder="Enter contact person name"
                        :disabled="mode === 'view'"
                    />
                    <Message
                        v-if="form.errors.contact_person"
                        severity="error"
                        size="small"
                        variant="simple"
                        class="col-span-2"
                    >
                        {{ form.errors.contact_person }}
                    </Message>
                </div>

                <!-- Phone Number -->
                <div>
                    <label for="phone_number" class="block text-sm font-medium required">
                        Phone
                    </label>
                    <InputText
                        id="phone_number"
                        v-model="form.phone_number"
                        class="w-full"
                        size="small"
                        placeholder="Enter phone number"
                        :disabled="mode === 'view'"
                    />
                    <Message
                        v-if="form.errors.phone_number"
                        severity="error"
                        size="small"
                        variant="simple"
                        class="col-span-2"
                    >
                        {{ form.errors.phone_number }}
                    </Message>
                </div>

                <!-- Telegram Number -->
                <div>
                    <label for="telegram_number" class="block text-sm font-medium required">
                        Telegram Number
                    </label>
                    <InputText
                        id="telegram_number"
                        v-model="form.telegram_number"
                        class="w-full"
                        size="small"
                        placeholder="Enter telegram number"
                        :disabled="mode === 'view'"
                    />
                    <Message
                        v-if="form.errors.telegram_number"
                        severity="error"
                        size="small"
                        variant="simple"
                        class="col-span-2"
                    >
                        {{ form.errors.telegram_number }}
                    </Message>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium required">
                        Email
                    </label>
                    <InputText
                        id="email"
                        v-model="form.email"
                        class="w-full"
                        size="small"
                        placeholder="Enter email"
                        :disabled="mode === 'view'"
                    />
                    <Message
                        v-if="form.errors.email"
                        severity="error"
                        size="small"
                        variant="simple"
                        class="col-span-2"
                    >
                        {{ form.errors.email }}
                    </Message>
                </div>

                <div class="col-span-1 sm:col-span-2 md:col-span-3">
                    <hr />
                </div>

                <!-- Bank Name -->
                <div>
                    <label for="bank_name" class="block text-sm font-medium required">
                        Bank name
                    </label>
                    <InputText
                        id="bank_name"
                        v-model="form.bank_name"
                        class="w-full"
                        size="small"
                        placeholder="Enter bank name"
                        :disabled="mode === 'view'"
                    />
                    <Message
                        v-if="form.errors.bank_name"
                        severity="error"
                        size="small"
                        variant="simple"
                        class="col-span-2"
                    >
                        {{ form.errors.bank_name }}
                    </Message>
                </div>

                <!-- Bank Address -->
                <div>
                    <label for="bank_address" class="block text-sm font-medium required">
                        Bank address
                    </label>
                    <InputText
                        id="bank_address"
                        v-model="form.bank_address"
                        class="w-full"
                        size="small"
                        placeholder="Enter bank address"
                        :disabled="mode === 'view'"
                    />
                    <Message
                        v-if="form.errors.bank_address"
                        severity="error"
                        size="small"
                        variant="simple"
                        class="col-span-2"
                    >
                        {{ form.errors.bank_address }}
                    </Message>
                </div>

                <!-- Bank Account Name -->
                <div>
                    <label for="bank_account_name" class="block text-sm font-medium required">
                        Bank account name
                    </label>
                    <InputText
                        id="bank_account_name"
                        v-model="form.bank_account_name"
                        class="w-full"
                        size="small"
                        placeholder="Enter bank account name"
                        :disabled="mode === 'view'"
                    />
                    <Message
                        v-if="form.errors.bank_account_name"
                        severity="error"
                        size="small"
                        variant="simple"
                        class="col-span-2"
                    >
                        {{ form.errors.bank_account_name }}
                    </Message>
                </div>

                <!-- Bank Account Number -->
                <div>
                    <label for="bank_account_number" class="block text-sm font-medium required">
                        Bank account number
                    </label>
                    <InputText
                        id="bank_account_number"
                        v-model="form.bank_account_number"
                        class="w-full"
                        size="small"
                        placeholder="Enter bank account number"
                        :disabled="mode === 'view'"
                    />
                    <Message
                        v-if="form.errors.bank_account_number"
                        severity="error"
                        size="small"
                        variant="simple"
                        class="col-span-2"
                    >
                        {{ form.errors.bank_account_number }}
                    </Message>
                </div>

                <!-- Bank Swift -->
                <div>
                    <label for="bank_swift" class="block text-sm font-medium required">
                        Bank swift
                    </label>
                    <InputText
                        id="bank_swift"
                        v-model="form.bank_swift"
                        class="w-full"
                        size="small"
                        placeholder="Enter bank swift"
                        :disabled="mode === 'view'"
                    />
                    <Message
                        v-if="form.errors.bank_swift"
                        severity="error"
                        size="small"
                        variant="simple"
                        class="col-span-2"
                    >
                        {{ form.errors.bank_swift }}
                    </Message>
                </div>
            </div>

            <!-- Submit Button (Hidden in View Mode) -->
            <div v-if="mode !== 'view'" class="flex justify-end gap-2 mr-4 mb-2">
                <Button
                    :label="mode === 'create' ? 'Create' : 'Update'"
                    class="p-button-primary w-38"
                    type="submit"
                    outlined
                />
            </div>
        </form>
    </div>
</template>

<script setup>
import { InputText, Button, Message, Select } from "primevue";
import { useForm } from "@inertiajs/vue3";
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
    mode: {
        type: String,
        required: true,
        validator: (value) => ["create", "edit", "view"].includes(value),
    },
    customer: {
        type: Object,
        default: () => ({}),
    },
    errors: Object,
    redirect_route: String,
    customerCategories: Array,
});

const emit = defineEmits(['close']);

const form = useForm({
    name: props.customer.name || "",
    code: props.customer.code || "",
    address: props.customer.address || "",
    email: props.customer.email || "",
    phone_number: props.customer.phone_number || "",
    contact_person: props.customer.contact_person || "",
    telegram_number: props.customer.telegram_number || "",
    credit_period: props.customer.credit_period || "",
    website: props.customer.website || "",
    bank_name: props.customer.bank_name || "",
    bank_address: props.customer.bank_address || "",
    bank_account_name: props.customer.bank_account_name || "",
    bank_account_number: props.customer.bank_account_number || "",
    bank_swift: props.customer.bank_swift || "",
    redirect_route: props.redirect_route || "customers.index",
    customer_category_id: props.customer.customer_category_id || "",
});

const submit = () => {
    if (!form.name || !form.code || !form.customer_category_id) {
        alert("Please fill in all required fields.");
        return;
    }

    if (props.mode === "create") {
        form.post(route("customers.store"), {
            onSuccess: () => {
                alert("Customer created successfully!");
                emit('close'); // Close the dialog
                Inertia.visit(route(props.redirect_route)); // Redirect to index
            },
            onError: (errors) => {
                console.error(errors);
            },
        });
    } else if (props.mode === "edit") {
        form.put(route("customers.update", props.customer.id), {
            onSuccess: () => {
                alert("Customer updated successfully!");
                emit('close'); // Close the dialog
                Inertia.visit(route(props.redirect_route)); // Redirect to index
            },
            onError: (errors) => {
                console.error(errors);
            },
        });
    }
};
</script>