<template>
    <Head title="Receipt Printing" />
    <div class="flex flex-col">
        <div class="flex gap-4 mt-4 ml-4 print:hidden">
            <Button
                @click="doPrint"
                size="small"
                icon="pi pi-arrow-right-arrow-left"
                label="Print Receipt"
            ></Button>

            <Button
                label="Back to list"
                icon="pi pi-chevron-circle-left"
                @click="back()"
                size="small"
            />
        </div>
        <div
            class="flex flex-col items-stretch justify-stretch mx-auto aspect-1/1.414 shadow-lg p-20 min-h-svh border print:border-0 print:shadow-none print:p-0 print:mx-0 print:aspect-none print:w-full print:print-container"
        >
            <h1 class="mx-auto text-3xl font-bold mb-6">Receipt</h1>

            <div class="flex flex-row justify-between mb-6 text-sm">
                <!-- Left column for Receipt and Customer Info -->
                <div class="flex flex-col w-1/2 items-start gap-4">
                    <p><strong>Receipt No:</strong> {{ receipt.receipt_no }}</p>
                    <p><strong>Date:</strong> {{ receipt.receipt_date }}</p>
                    <p>
                        <strong>Customer/Organization:</strong>
                        {{ receipt.customer.name }}
                    </p>
                    <p><strong>Purpose:</strong> {{ receipt.purpose }}</p>
                    <p>
                        <strong>Amount Paid:</strong> {{ receipt.amount_paid }}
                    </p>
                    <p>
                        <strong>Amount in Words:</strong>
                        {{ receipt.amount_in_words }}
                    </p>
                </div>

                <!-- Right column for Invoice Info -->
                <div class="flex flex-col ml-28 items-start gap-4">
                    <p><strong>Invoice No:</strong> {{ receipt.invoice_no }}</p>
                </div>
            </div>

            <!-- Optional extra space for signatures -->
            <div class="flex flex-row justify-between mt-6 text-sm">
                <div class="flex flex-col w-1/2 items-start gap-4">
                    <p>
                        <strong>Customer Signature:</strong>
                        ____________________
                    </p>
                    <p>
                        <strong>Authorized Signature:</strong>
                        ____________________
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive, defineProps } from "vue";
import { Head } from "@inertiajs/vue3";
import { Button } from "primevue";

const props = defineProps({
    receipt: Object,
});

const doPrint = () => {
    window.print();
};

const back = () => {
    window.history.back();
};

const receipt = reactive({
    ...props.receipt,
});
</script>

<style>
@media print {
    button {
        display: none;
    }
}

@page {
    size: A4;
    margin: 20mm;
}
</style>
