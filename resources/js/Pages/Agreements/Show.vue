<template>
    <div class="flex flex-col">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 print:hidden mb-5 fixed top-0">
                <Button @click="doPrint" class="md:col-start-2">Print USD</Button>
                <Button @click="doPrintRiels">Print KHR</Button>
            </div>
        <div class="flex flex-col items-stretch justify-stretch mx-auto aspect-1/1.414 shadow-lg p-20 min-h-svh border print:border-0 print:shadow-none print:p-0 print:mx-0 print:aspect-none print:w-full print:print-container">
            <div><img src="/logo.png" alt="ITC logo" class="w-28" /></div>
            <div class="w-full"></div>
            <h1 class="mx-auto text-2xl">Agreement</h1>
            <div class="grid grid-cols-2">
                <div>
                    <p><strong>Quotation No:</strong> {{ agreement.quotation_no }}</p>
                    <p><strong>Agreement No:</strong> {{ agreement.agreement_no }}</p>
                    <p><strong>Customer/Organization:</strong> {{ agreement.customer.name }}</p>
                    <p><strong>Date:</strong> {{ agreement.agreement_date }}</p>
                    <p><strong>Address:</strong> {{ agreement.address }}</p>
                    <p class="print:hidden"><strong>Agreement Doc:</strong> <a :href="agreement.agreement_doc"
                            target="_blank">View doc</a></p>
                </div>
                <div class="text-right">
                    <p><strong>Agreement summary</strong></p>
                    <p><strong>Start Date:</strong> {{ agreement.start_date }}</p>
                    <p><strong>End Date:</strong> {{ agreement.end_date }}</p>
                    <p><strong>Agreement amount:</strong> {{ agreement.amount_no_tax }}</p>
                    <p v-if="agreement.tax != 0"><strong>Tax:</strong> {{ agreement.tax }}</p>
                    <p class="print:hidden"><strong>Status:</strong> {{ agreement.status }}</p>
                </div>
            </div>
            <PaymentSchedule class="mt-2" v-model="form.payment_schedule" :currency="currency"
                readonly />
        </div>
    </div>
</template>

<script setup>
import { Button } from 'primevue';
import { reactive, ref, defineProps } from 'vue';
import PaymentSchedule from './PaymentSchedule.vue';
defineProps({
    agreement: Object,
});
const currency = ref('$');
const doPrint = () => {
    currency.value = '$';
    window.print();
}
const doPrintRiels = () => {
    currency.value = '៛';
    window.print();
}
const form = reactive({
    payment_schedule: [
        {
            id: 1,
            due_date: "28/01/2025",
            short_description: "Item description",
            percentage: 10,
            remark: "Additional remark",
            amount: 2000,
        },
        {
            id: 2,
            due_date: "04/02/2025",
            short_description: "Item description",
            percentage: 20,
            remark: "Additional remark",
            amount: 5000,
        },
        {
            id: 3,
            due_date: "11/02/2025",
            short_description: "Item description",
            percentage: 30,
            remark: "Additional remark",
            amount: 3000,
        },
    ]
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