<script setup>
import ProductTable from "@/Components/ProductTable.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import NavbarLayout from "@/Layouts/NavbarLayout.vue";
import VerticalBar from "../Layouts/Charts/VerticalBar.vue";
import { Head, Link } from "@inertiajs/vue3";
import BaseLayout from "../Layouts/BaseLayout.vue";

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

function handleImageError() {
    document.getElementById("screenshot-container")?.classList.add("!hidden");
    document.getElementById("docs-card")?.classList.add("!row-span-1");
    document.getElementById("docs-card-content")?.classList.add("!flex-row");
    document.getElementById("background")?.classList.add("!hidden");
}
</script>

<template>
    <Head title="Welcome" />
    <GuestLayout>
        <NavbarLayout />
        <BaseLayout/>
        <ProductTable />
    </GuestLayout>
</template>
