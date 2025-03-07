<template>
    <Head title="Create Customer" />
    <GuestLayout>
        <Toast position="top-center" group="tc" />
        <Customers :customer-categories="customerCategories"></Customers>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import Customers from "@/Components/Customers.vue";
import { Head } from "@inertiajs/vue3";
import Toast from "primevue/toast";
defineProps({
    customerCategories: Array,
});
</script>

<style scoped></style>
