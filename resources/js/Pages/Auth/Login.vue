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

            <!-- If the backend sets a “status” flash (e.g. “Password reset link sent”), show it -->
            <!-- <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
                {{ status }}
            </div> -->

            <form @submit.prevent="handleSubmit" class="space-y-4">
                <!-- EMAIL FIELD -->
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

                <!-- PASSWORD FIELD -->
                <div>
                    <InputLabel for="password" value="Password" />
                    <Password
                        id="password"
                        v-model="form.password"
                        class="mt-1 w-full"
                        :feedback="false"
                        toggleMask
                        placeholder="Enter Password"
                        :inputClass="{
                            'w-full': true,
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

                <!-- REMEMBER ME CHECKBOX -->
                <!-- <div class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                        Remember me
                    </span>
                </div> -->

                <!-- SUBMIT BUTTON -->
                <PrimaryButton
                    type="submit"
                    class="w-full mt-4 flex justify-center"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Log in
                </PrimaryButton>
            </form>
        </div>
    </div>
</template>

<script setup>
import { usePage, useForm } from "@inertiajs/vue3";
import { computed, onMounted } from "vue";
import { route } from "ziggy-js";
import Checkbox from "@/Components/Checkbox.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import Message from "primevue/message";
import Password from "primevue/password";
import loginBg from "/public/assets/images/login.jpg";

const page = usePage();
const rolesArray = computed(() => page.props.userRoles || []);
const rolesString = computed(() => {
    return rolesArray.value
        .map((r) => r.charAt(0).toUpperCase() + r.slice(1))
        .join(", ");
});
const userRoles = computed(() => page.props.userRoles || []);

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const validateForm = () => {
    let valid = true;
    if (!form.email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
        form.errors.email = "Please enter a valid email address.";
        valid = false;
    } else {
        form.errors.email = "";
    }
    if (!form.password || form.password.length < 6) {
        form.errors.password = "Please enter a password (min 6 characters).";
        valid = false;
    } else {
        form.errors.password = "";
    }
    return valid;
};

const handleSubmit = () => {
    if (!validateForm()) return;

    form.post(route("login"), {
        // preserveScroll: true,
        onSuccess: (inertiaPage) => {
            const rolesAfterLogin = inertiaPage.props.roles || [];
            console.log("Logged-in user’s roles:", rolesAfterLogin);
        },
        onError: (errors) => {
            form.errors.email = errors.email
                ? errors.email
                : "Login failed. Please try again.";
        },
    });
};

onMounted(() => {
    console.log("Logged‐in user’s roles:", rolesArray.value);
});
</script>

<style scoped>
/* … */
</style>

<style scoped>
/* … */
</style>
