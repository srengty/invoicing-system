<template>
    <Head title="Create Invoice" />
    <GuestLayout>
        <Invoices></Invoices>
        <Payment></Payment>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Invoices from '@/Components/Invoices.vue';
import { Head } from '@inertiajs/vue3';
import Payment from '@/Components/Payment.vue';
</script>

<style scoped>

</style>
