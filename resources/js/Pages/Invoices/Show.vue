<template>
    <Head title="Show Invoice" />
    <GuestLayout>
        <InvoicesView></InvoicesView>
        <PaymentShow></PaymentShow>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head } from '@inertiajs/vue3';
import InvoicesView from '@/Components/InvoicesShow.vue';
import PaymentShow from '@/Components/PaymentShow.vue';
</script>

<style scoped>

</style>
