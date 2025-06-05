<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { Link } from "@inertiajs/vue3";
import MainNavTree from "./MainNavTree.vue";
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-gray-100 sm:justify-center dark:bg-gray-100"
    >
        <div
            class="w-full h-screen overflow-y-auto bg-white shadow-md sm:rounded-lg dark:bg-gray-800 grid grid-cols-1 md:grid-cols-6"
        >
            <div
                class="row-span-2 bg-slate-100 dark:bg-slate-100 sticky top-0 h-screen overflow-auto"
            >
                <div
                    class="flex flex-col items-center justify-center py-2 pb-5 mb-3"
                >
                    <Link href="/dashboard">
                        <img src="/logo.png" alt="Logo" class="h-20 w-20" />
                    </Link>
                    <div class="text-sm">
                        <p>Institute of Technology of Cambodia</p>
                    </div>
                </div>
                <MainNavTree />
            </div>

            <div class="md:col-span-5">
                <!-- <div class="bg-slate-100 dark:bg-slate-100 min-h-10">
                    &emsp;
                </div> -->
                <slot />
            </div>
        </div>
    </div>
</template>
