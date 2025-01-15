<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex min-h-screen flex-col items-center bg-gray-100 sm:justify-center dark:bg-gray-900">
        <div class="w-full h-screen overflow-hidden bg-white shadow-md sm:rounded-lg dark:bg-gray-800 grid grid-cols-1 md:grid-cols-6">
            <div class="row-span-2 bg-slate-100">
                <div class="flex items-center justify-center">
                    <Link href="/">
                    <!-- <ApplicationLogo class="h-20 w-20 fill-current text-gray-500" /> -->
                        <img src="/logo.png" alt="Logo" class="h-20 w-20" />
                    </Link>
                </div>
            </div>
            <div class="md:col-span-5">
                <div class="bg-slate-100 min-h-10">&emsp;</div>
                <slot />
            </div>
        </div>
    </div>
</template>
