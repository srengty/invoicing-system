<template>
    <Head title="Log in" />

    <div
        class="min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900 bg-cover bg-center"
        :style="{ backgroundImage: `url(${loginBg})` }"
    >
        <div
            class="w-full max-w-md bg-white/80 dark:bg-gray-800/80 p-8 rounded-md shadow-lg backdrop-blur-sm"
        >
            <div
                class="absolute -top-28 left-1/2 transform -translate-x-1/2 w-48 h-48 bg-green-700 rounded-full flex items-center justify-center shadow-lg"
            >
                <img src="/logo.png" alt="Logo" class="w-48" />
            </div>

            <h2
                class="text-2xl font-bold text-center text-gray-900 dark:text-gray-100 mt-16"
            >
                Log In
            </h2>

            <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
                {{ status }}
            </div>

            <form @submit.prevent="handleSubmit" class="space-y-4">
                <div>
                    <InputLabel for="email" value="Email" />
                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full"
                        v-model="form.email"
                        autofocus
                        autocomplete="username"
                        placeholder="Enter Email"
                        :class="{
                            'border-red-500 focus:border-red-500':
                                form.errors.email,
                        }"
                    />
                    <Message
                        v-if="form.errors.email"
                        severity="error"
                        :text="form.errors.email"
                        class="mt-2"
                        variant="simple"
                        size="small"
                    >
                        {{ form.errors.email }}
                    </Message>
                </div>

                <div>
                    <InputLabel for="password" value="Password" />
                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password"
                        autocomplete="current-password"
                        placeholder="Enter Password"
                        :class="{
                            'border-red-500 focus:border-red-500':
                                form.errors.password,
                        }"
                    />
                    <Message
                        v-if="form.errors.password"
                        severity="error"
                        :text="form.errors.password"
                        class="mt-2"
                        variant="simple"
                        size="small"
                    >
                        {{ form.errors.password }}
                    </Message>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <Checkbox
                            name="remember"
                            v-model:checked="form.remember"
                        />
                        <span
                            class="ms-2 text-sm text-gray-600 dark:text-gray-400"
                        >
                            Remember me
                        </span>
                    </div>
                    <div>
                        <Link
                            :href="route('password.request')"
                            class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
                        >
                            Forgot password?
                        </Link>
                    </div>
                </div>

                    <PrimaryButton
                        class="w-full mt-4 flex justify-center"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Log in
                    </PrimaryButton>
                <div class="flex items-center justify-center">
                    <Link
                        :href="route('canRegister')"
                        class="text-sm text-gray-600 underline hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
                    >
                        Don't have an account?
                    </Link>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import loginBg from "../../../../public/assets/images/login.jpg";
import Message from "primevue/message";

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: "",
    password: "",
    remember: false,
    errors: {
        email: "",
        password: "",
    },
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
function validateForm() {
    let valid = true;

    // EMAIL
    if (!form.email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
        form.errors.email = "Please enter a valid email address.";
        valid = false;
    } else {
        form.errors.email = "";
    }

    // PASSWORD (just required check here)
    if (!form.password || form.password.length < 6) {
        form.errors.password = "Please enter your password.";
        valid = false;
    } else {
        form.errors.password = "";
    }

    return valid;
}
const handleSubmit = () => {
    if (validateForm()) {
        submit();
    }
};
</script>
