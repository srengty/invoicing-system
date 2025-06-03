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
            <h1 class="mx-auto text-3xl font-bold mb-6 text-center">Receipt</h1>

            <div class="grid grid-cols-1 gap-6">
                <div class="flex items-center gap-56">
                    <p><strong>Receipt No:</strong> {{ receipt.receipt_no }}</p>
                    <p><strong>Invoice No:</strong> {{ receipt.invoice_no }}</p>
                </div>
                <div class="grid gap-5">
                    <p>
                        <strong>Date:</strong>
                        {{ receipt.receipt_date }}
                    </p>
                    <p>
                        <strong>Customer/Organization:</strong>
                        {{ receipt.customer ? receipt.customer.name : "N/A" }}
                    </p>
                    <p>
                        <strong>Purpose:</strong> {{ receipt.purpose || "N/A" }}
                    </p>
                    <p>
                        <strong>Amount Paid:</strong>
                        {{ formatCurrency(receipt.paid_amount) }}
                    </p>
                    <p>
                        <strong>Amount in Words:</strong>
                        {{ receipt.amount_in_words || "Not available" }}
                    </p>
                </div>
            </div>
            <!-- Signature Section -->
            <div class="mt-6 text-sm">
                <div class="flex gap-52 mt-10">
                    <div class="grid gap-10 items-start justify-between">
                        <p><strong>Customer Signature and name:</strong></p>
                        <div class="mt-4 signature-line"></div>
                    </div>
                    <div class="grid gap-10 items-start">
                        <p><strong>Authorized Signature and name:</strong></p>
                        <div class="mt-4 signature-line"></div>
                    </div>
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
const formatCurrency = (value) => {
    if (value === null || value === undefined || value === "") return "0.00";
    const numValue =
        typeof value === "string"
            ? parseFloat(value.replace(/,/g, ""))
            : Number(value);

    return numValue.toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
};
</script>

<style>
@media print {
    body {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    button {
        display: none !important;
    }

    .signature-line {
        border-top: 1px solid #000 !important;
        width: 160px !important;
        margin-top: 16px !important;
        margin-bottom: 30px !important;
    }

    /* Ensure all elements are visible when printing */
    * {
        visibility: visible !important;
    }
}

.signature-line {
    width: 160px;
    border-top: 1px solid #000;
    margin-top: 16px;
    margin-bottom: 30px;
}

@page {
    size: A4;
    margin: 20mm;
    margin-bottom: 30mm; /* Extra space for signatures */
}
</style>
