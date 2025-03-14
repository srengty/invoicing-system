<template>
    <Head title="Quotations Printing" />

    <div class="flex justify-start items-center gap-4 ml-10">
        <!-- Toggle Currency -->
        <div class="flex items-center gap-3 mt-6">
            <p class="text-sm font-semibold">Amount ({{ currencyLabel }})</p>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" v-model="isUSD" class="sr-only peer" />
                <div
                    class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#10B981] dark:peer-focus:ring-[#10B981] rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-6 peer-checked:after:border-white after:content-[''] after:absolute after:top-1 after:left-1 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#10B981]"
                ></div>
            </label>
        </div>

        <!-- Print Button -->
        <div class="flex justify-center mt-6">
            <Button
                label="Print Quotation"
                icon="pi pi-print"
                class="px-4 py-2"
                @click="printPage"
                size="small"
            />
        </div>
    </div>

    <div
        ref="printArea"
        class="flex-col justify-center print-area a4-size text-sm"
    >
        <h1 class="flex flex-row justify-center text-3xl font-bold mb-6">
            Quotation
        </h1>
        <div class="flex flex-row justify-between mb-6">
            <div class="flex flex-col w-1/2 gap-4">
                <p>
                    <strong>Customer Name:</strong>
                    {{ quotation.customer_name || "N/A" }}
                </p>
                <p><strong>Address:</strong> {{ quotation.address }}</p>
                <p>
                    <strong>Phone Number:</strong> {{ quotation.phone_number }}
                </p>
            </div>
            <div class="flex flex-col w-1/2 items-end gap-4">
                <div class="grid gap-4">
                    <p>
                        <strong>Quotation No.:</strong>
                        {{ quotation.quotation_no }}
                    </p>
                    <p>
                        <strong>Quotation Date:</strong>
                        {{ quotation.quotation_date }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Print Section: Products Table -->
        <div ref="printTable" class="page-break">
            <div v-if="quotation.products?.length" class="table-container">
                <!-- Table Header -->
                <div
                    class="grid grid-cols-5 bg-gray-200 py-2 px-4 font-bold text-center border-b"
                >
                    <div class="w-1/5">No.</div>
                    <div class="w-1/5">ITEM</div>
                    <div class="w-1/5">QTY</div>
                    <div class="w-3/5">Unit Price</div>
                    <div class="w-3/5">Sub-Total</div>
                </div>

                <!-- Table Body -->
                <div
                    v-for="(product, index) in quotation.products"
                    :key="product.id"
                    class="grid grid-cols-5 border-b py-2 px-4 text-start"
                >
                    <div>{{ index + 1 }}</div>
                    <div>
                        <p>{{ isUSD ? product.name : product.name_kh }}</p>
                        <p>{{ isUSD ? product.desc : product.desc_kh }}</p>
                        <p>{{ isUSD ? product.remark : product.remark_kh }}</p>
                    </div>
                    <div>{{ product.pivot.quantity }}</div>
                    <div>
                        <div class="flex flex-col">
                            <span class="font-semibold"
                                >៛{{
                                    formatNumber(
                                        convertCurrency(product.pivot.price)
                                    )
                                }}</span
                            >
                        </div>
                    </div>
                    <div class="font-semibold">
                        ៛{{
                            formatNumber(
                                convertCurrency(
                                    product.pivot.price * product.pivot.quantity
                                ).toFixed(2)
                            )
                        }}
                    </div>
                </div>
            </div>

            <!-- Total Amount -->
            <p class="pt-6 flex justify-end">
                Total ({{ currencyLabel }}): {{ currencySymbol
                }}{{ formatNumber(convertCurrency(quotation.total)) }}
            </p>

            <!-- Terms and Conditions -->
            <div class="mt-8">
                <p class="font-bold mb-2">Terms and Conditions</p>
                <div
                    class="w-full border rounded-md p-3 text-sm text-gray-800 overflow-y-auto max-h-32"
                >
                    {{ quotation.terms }}
                </div>
            </div>

            <!-- Customer Acceptance & Authorization -->
            <div class="mt-10 flex justify-between text-sm page-break-avoid">
                <div>
                    <p class="font-bold">Customer Acceptance</p>
                    <p class="text-xs italic">
                        I hereby accept the quotation and agree on Terms and
                        Conditions.
                    </p>
                    <div class="mt-5">
                        <p>
                            <strong>Date:</strong>
                            {{ quotation.quotation_dates || "___________" }}
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-bold">Authorized by</p>
                    <div class="mt-10">
                        <p>
                            <strong>Signature:</strong>
                            {{ quotation.customer?.names || "___________" }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import { Head } from "@inertiajs/vue3";
import { usePage } from "@inertiajs/vue3";
import Button from "primevue/button";

const { props } = usePage();
const quotation = ref(props.quotation);
const printArea = ref(null);

const isUSD = ref(false);
const exchangeRate = ref(4100);

const currencySymbol = computed(() => (isUSD.value ? "$" : "៛"));
const currencyLabel = computed(() => (isUSD.value ? "USD" : "KHR"));

const convertCurrency = (amount) => {
    return isUSD.value ? amount / exchangeRate.value : amount;
};

const formatNumber = (value) => {
    if (!value) return "0.00";
    return new Intl.NumberFormat("en-US", { minimumFractionDigits: 2 }).format(
        value
    );
};

const printPage = () => {
    window.print();
};
</script>

<style scoped>
.print-area {
    width: 210mm;
    min-height: 297mm;
    padding: 20mm;
    background: white;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
    margin: auto;
}

/* Hide checkboxes in printed output if desired */
@media print {
    input[type="checkbox"] {
        display: none;
    }
    .mt-6 {
        display: none !important;
    }
    .print-area {
        margin: 0;
        padding: 0;
        width: 100%;
        height: auto;
        box-shadow: none;
        page-break-after: avoid;
    }
    .table-container {
        page-break-before: auto;
        page-break-after: auto;
        page-break-inside: auto;
    }
    .table-container div {
        page-break-inside: avoid;
    }
}
</style>
