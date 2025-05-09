<template>
    <Head title="Forgot Password" />

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
                Forgot Password
            </h2>

            <div
                v-if="status"
                class="mb-4 text-sm font-medium text-green-600 dark:text-green-400"
            >
                {{ status }}
            </div>

            <form @submit.prevent="submit">
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

                    <!-- <InputError class="mt-2" :message="form.errors.email" /> -->
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

                <div class="mt-4 flex items-center justify-end">
                    <PrimaryButton
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Email Password Reset Link
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import loginBg from "../../../../public/assets/images/login.jpg";
import Message from "primevue/message";

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: "",
    errors: {
        email: "",
    },
});

const submit = () => {
    form.post(route("password.email"));
};
</script>
