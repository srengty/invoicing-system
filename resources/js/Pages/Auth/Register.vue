<template>
    <Head title="Register" />

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
                Register Form
            </h2>

            <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
                {{ status }}
            </div>
            <form @submit.prevent="submit">
                <div>
                    <InputLabel for="name" value="Name" />

                    <TextInput
                        id="name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.name"
                        autofocus
                        autocomplete="name"
                        :class="{
                            'border-red-500 focus:border-red-500':
                                form.errors.name,
                        }"
                    />
                    <Message
                        v-if="form.errors.name"
                        severity="error"
                        :text="form.errors.name"
                        class="mt-2"
                        variant="simple"
                        size="small"
                    >
                        {{ form.errors.name }}
                    </Message>
                    <!-- <InputError class="mt-2" :message="form.errors.name" /> -->
                </div>

                <div class="mt-4">
                    <InputLabel for="email" value="Email" />

                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full"
                        v-model="form.email"
                        autocomplete="username"
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
                    <!-- <InputError class="mt-2" :message="form.errors.email" /> -->
                </div>

                <div class="mt-4">
                    <InputLabel for="password" value="Password" />

                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password"
                        autocomplete="new-password"
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
                    <!-- <InputError class="mt-2" :message="form.errors.password" /> -->
                </div>

                <div class="mt-4">
                    <InputLabel
                        for="password_confirmation"
                        value="Confirm Password"
                    />

                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password_confirmation"
                        autocomplete="new-password"
                        :class="{
                            'border-red-500 focus:border-red-500':
                                form.errors.password_confirmation,
                        }"
                    />
                    <Message
                        v-if="form.errors.password_confirmation"
                        severity="error"
                        :text="form.errors.password_confirmation"
                        class="mt-2"
                        variant="simple"
                        size="small"
                    >
                        {{ form.errors.password_confirmation }}
                    </Message>
                    <!-- <InputError
                        class="mt-2"
                        :message="form.errors.password_confirmation"
                    /> -->
                </div>

                <div class="mt-4 flex items-center justify-end">
                    <Link
                        :href="route('login')"
                        class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800"
                    >
                        Already registered?
                    </Link>

                    <PrimaryButton
                        class="ms-4"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Register
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
import { Head, Link, useForm } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import loginBg from "../../../../public/assets/images/login.jpg";
import Message from "primevue/message";

defineProps({
    status: {
        type: String,
    },
});
const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    errors: {
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
    },
});

const submit = () => {
    form.post(route("register"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>
