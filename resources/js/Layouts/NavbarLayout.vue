<template>
    <div class="min-h-screen flex flex-col">
        <!-- NAVBAR -->
        <nav
            class="fixed top-0 w-full z-50 bg-white border-b-4 border-gray-200 px-4 py-2 flex items-center justify-between"
        >
            <!-- Left Section (e.g. Brand or Dashboard link) -->
            <div class="flex items-center space-x-4">
                <!-- If you have a route named 'dashboard', you can use route() helper in Blade -->
                <inertia-link
                    :href="route('dashboard')"
                    class="text-xl font-bold"
                >
                    Dashboard
                </inertia-link>
            </div>

            <!-- Middle Section (Search form) -->
            <div class="mx-4 flex-1">
                <form @submit.prevent="handleSearch" class="flex">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Find movies, actors, and more"
                        class="border border-gray-300 px-3 py-1 rounded-l focus:outline-none"
                    />
                    <button
                        type="submit"
                        class="bg-[#10B981] text-white px-4 py-1 rounded-r hover:bg-green-600"
                    >
                        Go!
                    </button>
                </form>
            </div>

            <!-- Right Section (e.g. Button, Profile, or Notifications) -->dd
            <div>
                <button
                    class="bg-[#10B981] text-white px-4 py-2 rounded hover:bg-green-600"
                >
                    WorkPro
                </button>
            </div>
        </nav>

        <!-- MAIN CONTENT -->
        <main class="flex-1 mt-4">
            <ProductTable />
            <slot />
        </main>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { Inertia } from "@inertiajs/inertia";
import ProductTable from "../Components/ProductTable.vue";

const searchQuery = ref("");

function handleSearch() {
    // Example of sending a GET request with Inertia:
    // Inertia.get('/search', { query: searchQuery.value })

    alert(`Searching for: ${searchQuery.value}`);
}
</script>

<style scoped>
/* Add custom styling if desired */
</style>
