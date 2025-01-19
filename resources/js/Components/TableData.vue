<script setup>
defineProps({
    tabledata: {
        type: Array,
    },
});
</script>

<template>
    <div class="bg-white relative border rounded-lg">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="px-4 py-3">View Print-out</th>
                    <th class="px-4 py-3">NO.</th>
                    <th class="px-4 py-3">Customer/ Organization name</th>
                    <th class="px-4 py-3">TOTAL</th>
                    <th class="px-4 py-3">NO.</th>
                    <th class="px-4 py-3">Customer status</th>
                </tr>
            </thead>
            <tbody>
                <tr>

                </tr>
            </tbody>
        </table>
    </div>
</template>
