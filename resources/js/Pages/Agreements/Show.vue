<template>
    <Head title="Agreement Printing" />
    <div class="flex flex-col">
        <div
            class="grid grid-cols-2 md:grid-cols-4 gap-3 print:hidden mb-5 fixed top-6 left-6"
        >
            <Select
                :options="currencies"
                option-value="value"
                option-label="name"
                v-model="form.currency"
                @value-change="changeCurrency()"
                class="w-full md:w-28 h-10"
            ></Select>
            <Button @click="doPrint" class="md:col-start-2 md:w-28" size="small"
                >Print {{ form.currency }}</Button
            >
            <Button @click="back()" class="w-full md:w-28 h-10"
                >Back to list</Button
            >
        </div>
        <div
            class="flex flex-col items-stretch justify-stretch mx-auto aspect-1/1.414 shadow-lg p-20 min-h-svh border print:border-0 print:shadow-none print:p-0 print:mx-0 print:aspect-none print:w-full print:print-container"
        >
            <!-- <div><img src="/logo.png" alt="ITC logo" class="w-28" /></div> -->
            <div class="w-full"></div>
            <h1 class="mx-auto text-2xl">Agreement</h1>
            <div class="grid grid-cols-2">
                <div>
                    <p>
                        <strong>Quotation No:</strong> {{ form.quotation_no }}
                    </p>
                    <p>
                        <strong>Agreement No:</strong> {{ form.agreement_no }}
                    </p>
                    <p>
                        <strong>Customer/Organization:</strong>
                        {{ agreement.customer.name }}
                    </p>
                    <p><strong>Date:</strong> {{ agreement.agreement_date }}</p>
                    <p><strong>Address:</strong> {{ form.address }}</p>
                    <p class="print:hidden">
                        <strong>Agreement Doc:</strong>
                        <a :href="form.agreement_doc" target="_blank"
                            >View doc</a
                        >
                    </p>
                </div>
                <div class="text-right">
                    <p><strong>Agreement summary</strong></p>
                    <p>
                        <strong>Start Date:</strong> {{ agreement.start_date }}
                    </p>
                    <p><strong>End Date:</strong> {{ agreement.end_date }}</p>
                    <p>
                        <strong>Agreement amount:</strong>
                        {{ form.amount_no_tax }}
                    </p>
                    <p class="print:hidden">
                        <strong>Status:</strong> {{ form.status }}
                    </p>
                </div>
            </div>
            <PaymentSchedule
                class="mt-2"
                v-model="form.payment_schedules"
                :currency="form.currency"
                readonly
            />
        </div>
    </div>
</template>

<script setup>
import { Button, Select } from "primevue";
import { reactive, ref, defineProps, onMounted } from "vue";
import PaymentSchedule from "./PaymentSchedule.vue";
import { currencies } from "@/constants";
import moment from "moment";
import { Head } from "@inertiajs/vue3";
const props = defineProps({
    agreement: Object,
});
const exchangeRate = ref(4100);
const doPrint = () => {
    window.print();
};
const changeCurrency = () => {
    form.payment_schedules = form.payment_schedules.map((v) => {
        const rate =
            form.currency == "KHR" && v.currency == "USD"
                ? exchangeRate.value
                : form.currency == "USD" && v.currency == "KHR"
                ? 1 / exchangeRate.value
                : 1;
        return {
            ...v,
            currency: form.currency,
            amount: v.amount * rate,
        };
    });
};
const back = () => {
    window.history.back();
};
const form = reactive({
    ...props.agreement,
    rate: props.agreement.currency == "KHR" ? 4100 : 1,
    payment_schedules: props.agreement?.payment_schedules?.map((v) => {
        return {
            ...v,
            due_date: moment(v.due_date, "DD/MM/YYYY").toDate(),
        };
    }) ?? [
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
    ],
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
