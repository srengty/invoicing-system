<template>
    <div class="Show-invoice">
        <div class="flex justify-between items-center p-3 mr-4">
            <h1 class="text-2xl">Show Invoice</h1>
            <div>
                <Button label="Print" icon="pi pi-print" class="p-button-success" @click="printInvoice" rounded />
            </div>
        </div>
        <div class="p-3 grid grid-cols-1 md:grid-cols-3 gap-4 ml-4 mr-4">
            <div>
                <label class="block text-lg font-medium">Invoice No</label>
                <p class="text-gray-700">{{ invoice.invoice_no }}</p>
            </div>
            <div>
                <label class="block text-lg font-medium">Agreement No</label>
                <p class="text-gray-700">{{ invoice.agreement_no }}</p>
            </div>
            <div>
                <label class="block text-lg font-medium">Quotation No</label>
                <p class="text-gray-700">{{ invoice.quotation_no }}</p>
            </div>
            <div>
                <label class="block text-lg font-medium">Customer/Organization Name</label>
                <p class="text-gray-700">{{ invoice.customer_name }}</p>
            </div>
            <div>
                <label class="block text-lg font-medium">Address</label>
                <p class="text-gray-700">{{ invoice.address }}</p>
            </div>
            <div>
                <label class="block text-lg font-medium">Phone</label>
                <p class="text-gray-700">{{ invoice.phone }}</p>
            </div>
            <div>
                <label class="block text-lg font-medium">Start Date</label>
                <p class="text-gray-700">{{ invoice.start_date }}</p>
            </div>
            <div>
                <label class="block text-lg font-medium">End Date</label>
                <p class="text-gray-700">{{ invoice.end_date }}</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Button } from 'primevue';

const invoice = {
    invoice_no: 'INV001',
    agreement_no: 'AGR001',
    quotation_no: 'QTN001',
    customer_name: 'Customer A',
    address: '123 Main Street, City, Country',
    phone: '+1234567890',
    start_date: '2025-01-01',
    end_date: '2025-01-10',
};

// Print Invoice
const printInvoice = () => {
    console.log('Printing invoice:', invoice.invoice_no);
    alert('Print functionality triggered!');
};
</script>
