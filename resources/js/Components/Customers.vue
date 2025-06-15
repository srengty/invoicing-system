<template>
    <div class="create-customer text-sm">
        <form @submit.prevent="submit" class="">
            <input type="hidden" name="_token" :value="page.props.csrf_token" />
            <div
                class="p-3 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 ml-4 mr-4"
            >
                <!-- Customer Category -->
                <div>
                    <label
                        for="code"
                        class="block text-sm font-medium required"
                    >
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
                    <label
                        for="name"
                        class="block text-sm font-medium required"
                    >
                        Customer/Organization name
                    </label>
                    <InputText
                        id="name"
                        v-model="form.name"
                        class="w-full"
                        size="small"
                        placeholder="Enter Customer/Organization name"
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
                    <label
                        for="code"
                        class="block text-sm font-medium required"
                    >
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
                    <label
                        for="credit_period"
                        class="block text-sm font-medium"
                    >
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
                    <label
                        for="address"
                        class="block text-sm font-medium required"
                    >
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
                    <label for="website" class="block text-sm font-medium">
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
                </div>

                <div class="col-span-1 sm:col-span-2 md:col-span-3">
                    <hr />
                </div>

                <!-- Contact Person Name -->
                <div>
                    <label
                        for="contact_person"
                        class="block text-sm font-medium"
                    >
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
                </div>

                <!-- Phone Number -->
                <div>
                    <label
                        for="phone_number"
                        class="block text-sm font-medium required"
                    >
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
                    <label
                        for="telegram_number"
                        class="block text-sm font-medium"
                    >
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
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium">
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
                </div>

                <div class="col-span-1 sm:col-span-2 md:col-span-3">
                    <hr />
                </div>

                <!-- Bank Name -->
                <div>
                    <label for="bank_name" class="block text-sm font-medium">
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
                </div>

                <!-- Bank Address -->
                <div>
                    <label for="bank_address" class="block text-sm font-medium">
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
                </div>

                <!-- Bank Account Name -->
                <div>
                    <label
                        for="bank_account_name"
                        class="block text-sm font-medium"
                    >
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
                </div>

                <!-- Bank Account Number -->
                <div>
                    <label
                        for="bank_account_number"
                        class="block text-sm font-medium"
                    >
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
                </div>

                <!-- Bank Swift -->
                <div>
                    <label for="bank_swift" class="block text-sm font-medium">
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
                </div>
            </div>

            <!-- Submit Button (Hidden in View Mode) -->
            <div
                v-if="mode !== 'view'"
                class="flex justify-end gap-2 mr-4 mb-2"
            >
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
import { ref } from "vue";
import { useToast } from "primevue/usetoast";
import { InputText, Button, Message, Select } from "primevue";
import { Inertia } from "@inertiajs/inertia";
import { router, useForm } from "@inertiajs/vue3";
import Toast from "primevue/toast";
import { useConfirm } from "primevue/useconfirm";
import { usePage } from "@inertiajs/vue3";

const confirm = useConfirm();
const toast = useToast();
const page = usePage();
const csrfToken = page.props.csrf_token;

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

const emit = defineEmits(["close"]);

const form = useForm({
    name: props.customer.name || "",
    code: props.customer.code || "",
    address: props.customer.address || "",
    email: props.customer.email || "",
    phone_number: props.customer.phone_number || "",
    contact_person: props.customer.contact_person || "",
    telegram_number: props.customer.telegram_number || "",
    credit_period: props.customer.credit_period || "15",
    website: props.customer.website || "",
    bank_name: props.customer.bank_name || "",
    bank_address: props.customer.bank_address || "",
    bank_account_name: props.customer.bank_account_name || "",
    bank_account_number: props.customer.bank_account_number || "",
    bank_swift: props.customer.bank_swift || "",
    redirect_route: props.redirect_route || "customers.index",
    customer_category_id: props.customer.customer_category_id || "",
});

const showToast = (severity, summary, detail, duration = 4000) => {
    toast.add({
        group: "tc",
        severity,
        summary,
        detail,
        life: duration,
        className: "my-custom-toast",
    });
};

// Validation functions using regular expressions
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validateWebsite(url) {
    if (!url) return false; // reject empty string
    const pattern = new RegExp(
        "^(https?:\\/\\/)?" + // protocol is optional
            "((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|localhost|((\\d{1,3}\\.){3}\\d{1,3}))" + // domain name, localhost, or IP (v4)
            "(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*" + // optional port and path
            "(\\?[;&a-z\\d%_.~+=-]*)?" + // optional query string
            "(\\#[-a-z\\d_]*)?$", // optional fragment locator
        "i"
    );
    return pattern.test(url);
}

const validateForm = () => {
    let isValid = true;
    if (!validateEmail(form.email)) {
        form.errors.email = "Please enter a valid email address.";
        isValid = false;
    } else {
        form.errors.email = "";
    }
    if (!validateWebsite(form.website)) {
        form.errors.website = "Please enter a valid website URL.";
        isValid = false;
    } else {
        form.errors.website = "";
    }
    return isValid;
};

const submit = () => {
    if (!form) {
        toast.add({
            severity: "error",
            summary: "Error",
            detail: "Please fill in all required fields.",
            life: 3000,
        });
        showToast(
            "warn",
            "Validation Error",
            "Please fill in all required fields."
        );
        return;
    }

    // Check if the credit period is greater than 30 and prompt the user for confirmation
    if (parseInt(form.credit_period) > 30) {
        confirm.require({
            message:
                "The credit period is over 30 days. Are you sure you want to proceed?",
            header: "Credit Period Confirmation",
            icon: "pi pi-exclamation-triangle",
            accept: () => {
                // Proceed with form submission if the user accepts
                handleFormSubmission();
            },
            reject: () => {
                // Don't submit the form if the user rejects
                toast.add({
                    severity: "info",
                    summary: "Action Cancelled",
                    detail: "The submission has been cancelled.",
                    life: 3000,
                });
            },
        });
    } else {
        // Proceed with form submission if the credit period is 30 or less
        handleFormSubmission();
    }
};

// Function to handle the form submission logic
const handleFormSubmission = () => {
    if (props.mode === "create") {
        form.post(route("customers.store"), {
            headers: {
                "X-CSRF-TOKEN": page.props.csrf_token,
                "X-Requested-With": "XMLHttpRequest",
            },
            onSuccess: () => {
                toast.add({
                    severity: "success",
                    summary: "Success",
                    detail: "Customer created successfully!",
                    life: 3000,
                });
                setTimeout(() => {
                    emit("close");
                    Inertia.visit(route(props.redirect_route));
                }, 1000); // Delay to allow toast to show
                showToast(
                    "success",
                    "Customer Created",
                    "Your new customer has been successfully created!",
                    4000
                );
                setTimeout(() => {
                    emit("close");
                    router.push(route(props.redirect_route));
                }, 1000);
                // Inertia.visit(route(props.redirect_route));
            },
            onError: (errors) => {
                showToast(
                    "error",
                    "Error",
                    "There was an error creating the customer."
                );
                console.error(errors);
            },
        });
    } else if (props.mode === "edit") {
        form.put(route("customers.update", props.customer.id), {
            onSuccess: () => {
                toast.add({
                    severity: "success",
                    summary: "Success",
                    detail: "Customer updated successfully!",
                    life: 3000,
                });

                setTimeout(() => {
                    emit("close");
                    Inertia.visit(route(props.redirect_route));
                }, 1000); // Delay for toast display
                showToast(
                    "success",
                    "Success",
                    "Customer updated successfully!"
                );
                setTimeout(() => {
                    emit("close");
                    router.push(route(props.redirect_route));
                }, 1000);
                // Inertia.visit(route(props.redirect_route));
            },
            onError: (errors) => {
                showToast(
                    "error",
                    "Error",
                    "There was an error updating the customer."
                );
                console.error(errors);
            },
        });
    }
};
</script>

<style scoped>
.my-custom-toast {
    /* Example custom styles */
    background-color: #e0f7fa;
    border: 1px solid #4dd0e1;
    color: #006064;
}
</style>
