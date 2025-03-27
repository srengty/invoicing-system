<template>
    <Head title="Quotations Printing" />

    <div class="flex justify-start items-center gap-4 ml-10">
        <!-- Toggle Currency -->
        <div class="flex items-center gap-3 mt-6">
            <p class="text-sm font-semibold">{{ toggleLabel }}</p>
            <ToggleSwitch
                v-model="isKhmer"
                onLabel="KH/៛"
                offLabel="EN/$"
                class="w-20"
                @change="handleToggleChange"
            />
        </div>
        <Toast position="top-right" group="tr" />
        <!-- Print Button -->
        <div class="flex justify-center mt-6 mb-2">
            <Button
                label="Print Quotation"
                icon="pi pi-print"
                class="px-4 py-2"
                @click="generateAndDownloadPDF"
                size="small"
            />
        </div>
        <!-- Send Email Button -->
        <div class="flex justify-center mt-6 mb-2">
            <Button
                label="Send Quotation via Email"
                icon="pi pi-send"
                class="px-4 py-2"
                @click="showConfirmationDialog"
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
                    <!-- <p v-if="exchangeRate">
                        <strong>Exchange Rate:</strong>
                        1 USD = {{ exchangeRate }} KHR
                    </p> -->
                </div>
            </div>
        </div>

        <!-- Print Section: Products Table -->
        <div ref="printTable" class="page-break">
            <div
                v-if="quotation.products && quotation.products.length"
                class="table-container"
            >
                <!-- Table Header -->
                <div
                    class="grid grid-cols-[70px_170px_110px_170px_150px] bg-gray-200 py-2 px-4 font-bold text-center border-b"
                >
                    <div class="text-start">No.</div>
                    <div class="text-start">ITEM</div>
                    <div class="text-start">QTY</div>
                    <div class="text-start">Unit Price</div>
                    <div class="text-start">Sub-Total</div>
                </div>

                <!-- Table Body -->
                <div
                    v-for="(product, index) in formattedProducts"
                    :key="product.id"
                    class="grid grid-cols-[70px_170px_110px_170px_150px] border-b py-2 px-4 text-start"
                >
                    <div class="text-start">{{ index + 1 }}</div>
                    <div>
                        <div class="font-medium">
                            {{
                                isKhmer
                                    ? product.name_kh || "No English name"
                                    : product.name || "No Khmer name"
                            }}
                        </div>
                        <p class="text-gray-600 text-xs">
                            {{ isUSD ? product.desc : product.desc_kh }}
                        </p>
                        <p class="text-gray-500 italic text-xs">
                            {{ isUSD ? product.remark : product.remark_kh }}
                        </p>
                    </div>
                    <div class="text-start">{{ product.quantity }}</div>
                    <div class="text-start font-semibold">
                        {{ formatNumber(convertCurrency(product.price)) }}
                    </div>
                    <div class="text-start font-semibold">
                        {{
                            formatNumber(
                                convertCurrency(
                                    product.price * product.quantity
                                )
                            )
                        }}
                    </div>
                </div>
            </div>

            <!-- Total Amount Section -->
            <div class="pt-6">
                <div class="pt-6 flex justify-end">
                    <p class="font-bold">
                        Total ({{ currencyLabel }}):
                        {{ formatNumber(totalAmount) }}
                    </p>
                    <!-- <p class="text-sm text-gray-600">
                        Equivalent {{ isUSD ? "KHR" : "USD" }}:
                        {{ formatNumber(alternateTotal) }}
                    </p> -->
                </div>
                <!-- <div class="text-sm">
                    <p>Exchange Rate: 1 USD = {{ exchangeRate }} KHR</p>
                </div> -->
            </div>

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

            <Dialog v-model:visible="isSendDialogVisible" header="Confirm Send" footer="dialog-footer">
            <div v-if="quotation" class="flex flex-col gap-4 ml-2 mr-2">
                <!-- Display Selected Quotation Info -->
                <div>
                    <strong>Quotation No:</strong>
                    <p>{{ quotation.quotation_no }}</p>
                </div>
                <div>
                    <strong>Customer Name:</strong>
                    <p>{{ quotation.customer_name || "N/A" }}</p>
                </div>

                <!-- Email Checkbox -->
                <div class="flex items-center">
                    <input
                        type="checkbox"
                        id="emailCheckbox"
                        v-model="sendForm.emailChecked"
                        class="mr-2"
                    />
                    <label for="emailCheckbox" class="font-bold">Email: {{ quotation.email || "N/A" }}</label>
                </div>

                <!-- Telegram Checkbox -->
                <div class="flex items-center">
                    <input
                        type="checkbox"
                        id="telegramCheckbox"
                        v-model="sendForm.telegramChecked"
                        class="mr-2"
                    />
                    <label for="telegramCheckbox" class="font-bold">Telegram: {{ quotation.phone_number || "N/A" }}</label>
                </div>
            </div>

            <!-- Dialog Footer -->
            <template #footer>
                <Button label="Cancel" severity="secondary" @click="cancelSend" />
                <Button label="Send" severity="success" @click="generateAndSendPDF" :loading="isSending" />
            </template>
        </Dialog>
    </div>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import { usePage, router } from '@inertiajs/vue3';
import Button from "primevue/button";
import html2pdf from "html2pdf.js";
import { PDFDocument } from "pdf-lib";
import ToggleSwitch from "primevue/toggleswitch";
import { useToast } from "primevue/usetoast";
import Dialog from "primevue/dialog";
import { Head } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia'; // <-- Add this import


const toast = useToast();

// Form state to track email and telegram sending options
const sendForm = ref({
    emailChecked: false,
    telegramChecked: false
});

// Other reactive state variables
const isSendDialogVisible = ref(false);
const selectedQuotation = ref(null);  // Make sure this is populated with your quotation data
const isSending = ref(false);

const cancelSend = () => {
    isSendDialogVisible.value = false;  // Close the dialog
};

const showConfirmationDialog = () => {
    selectedQuotation.value = quotation;
    isSendDialogVisible.value = true;
};

// Example function to simulate the sending process (replace with your actual send function)
const sendQuotationRequest = async (quotation, formData) => {
    return new Promise((resolve, reject) => {
        setTimeout(() => {
            // Simulate successful email sending after 2 seconds
            resolve();
        }, 2000);
    });
};

// Refs and Data Setup
const { props } = usePage();
const quotation = ref({
    ...props.quotation,
    total: props.quotation?.total || 0,
    total_usd: props.quotation?.total_usd || 0,
    exchange_rate: props.quotation?.exchange_rate || 4100,
});

// Create reference for print area (ensure it's properly linked to the DOM)
const printArea = ref(null);

// State for toggling between currencies
const isUSD = ref(false);
const isKhmer = ref(true);
const toggleLabel = computed(() => isKhmer.value ? "USD/Dollar($)" : "KHR/Reil(៛)");

// Currency conversion and formatting functions
const handleToggleChange = () => {
    if (!isKhmer.value && (!quotation.value.total_usd || !quotation.value.exchange_rate)) {
        toast.add({
            severity: "warn",
            summary: "Missing Information",
            detail: "Please edit this quotation to add USD total and exchange rate before switching to USD view.",
            group: "tr",
            life: 5000,
        });
        isKhmer.value = true;
        return;
    }
    isUSD.value = !isKhmer.value;
};

// Exchange rate and currency labels
const exchangeRate = computed(() => quotation.value.exchange_rate || 0);
const currencyLabel = computed(() => isUSD.value ? "KHR" : "USD");

// Functions for converting and formatting currency
const convertCurrency = (amount) => isUSD.value ? amount / exchangeRate.value : amount;
const formatNumber = (value) => {
    if (!value) return "0.00";
    const formatted = new Intl.NumberFormat("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value);
    return isUSD.value ? `$${formatted}` : `៛${formatted}`;
};

// Compute total amount
const totalAmount = computed(() => {
    const totalKHR = quotation.value.products?.reduce((sum, product) => sum + (product.pivot?.quantity || 0) * (product.pivot?.price || 0), 0) || 0;
    return isUSD.value ? totalKHR / exchangeRate.value : totalKHR;
});

// Format product details
const formattedProducts = computed(() => {
    if (!quotation.value.products) return [];
    return quotation.value.products.map(product => ({
        id: product.id,
        name: product.name || "No English name",
        name_kh: product.name_kh || "No Khmer name",
        desc: product.desc || "",
        desc_kh: product.desc_kh || "",
        remark: product.remark || "",
        remark_kh: product.remark_kh || "",
        quantity: product.pivot?.quantity ?? 0,
        price: product.pivot?.price ?? 0,
        include_catalog: product.pivot?.include_catalog ?? 0,
        pdf_url: product.pdf_url || null,
    }));
});

// Function to generate PDF from the print area
const generatePDF = (element) => {
    return new Promise((resolve) => {
        html2pdf().from(element).toPdf().outputPdf("blob").then(resolve);
    });
};

// Function to handle printing the PDF (Download)
const generateAndDownloadPDF = async () => {
    try {
        if (!printArea.value) {
            console.error('Print area is not available');
            return;
        }

        // Step 1: Generate the PDF for the quotation section
        const quotationPDF = await generatePDF(printArea.value);

        // Step 2: Generate any catalog PDFs for products with valid URLs
        const catalogPDFs = await generateCatalogPDFs(formattedProducts.value);

        // Step 3: Merge the quotation PDF with the catalog PDFs (if any)
        const mergedPDF = await mergePDFs([quotationPDF, ...catalogPDFs]);

        // Step 4: Download the merged PDF
        const filename = `quotation_${quotation.value.quotation_no}.pdf`;
        downloadPDF(mergedPDF, filename);
        Inertia.visit(route('quotations.list'));
    } catch (error) {
        console.error("Error generating PDFs:", error);
    }
};

// Function to handle sending the PDF via email
const generateAndSendPDF = async () => {
    try {
        if (!printArea.value) {
            console.error('Print area is not available');
            return;
        }

        const quotationPDF = await generatePDF(printArea.value);
        const catalogPDFs = await generateCatalogPDFs(formattedProducts.value);
        const mergedPDF = await mergePDFs([quotationPDF, ...catalogPDFs]);

        const filename = `quotation_${quotation.value.quotation_no}.pdf`;
        sendPDFViaEmail(mergedPDF, filename);
        isSendDialogVisible.value=false;
        Inertia.visit(route('quotations.list'));
    } catch (error) {
        console.error("Error generating PDFs:", error);
    }
};

// Function to generate catalog PDFs (if available)
const generateCatalogPDFs = async (products) => {
    const catalogPDFs = await Promise.all(products.map(async (product) => {
        if (product.pdf_url && product.include_catalog === 1) {
            try {
                const response = await fetch(product.pdf_url);
                if (!response.ok) {
                    console.warn(`Failed to fetch catalog PDF for product ${product.id}`);
                    return null;
                }
                return await response.blob();
            } catch (error) {
                console.warn(`Error fetching catalog PDF for product ${product.id}:`, error);
                return null;
            }
        }
        return null;
    }));
    return catalogPDFs.filter(pdf => pdf !== null);
};

// Merge multiple PDFs
const mergePDFs = async (pdfBlobs) => {
    const pdfDoc = await PDFDocument.create();
    for (const pdfBlob of pdfBlobs) {
        const pdf = await PDFDocument.load(await pdfBlob.arrayBuffer());
        const pages = await pdfDoc.copyPages(pdf, pdf.getPageIndices());
        pages.forEach(page => pdfDoc.addPage(page));
    }
    return pdfDoc.save();
};

// Download the PDF
const downloadPDF = (pdfBytes, filename) => {
    const blob = new Blob([pdfBytes], { type: "application/pdf" });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};

// Send the generated PDF via email
const sendPDFViaEmail = (pdfBytes, filename) => {
    const blob = new Blob([pdfBytes], { type: "application/pdf" });
    const formData = new FormData();
    formData.append("quotation_id", quotation.value.id);
    formData.append("send_email", "send bro");
    formData.append("pdf_file", blob, filename);
    formData.append("update_status", "Pending");

    fetch('/quotations/send', {
        method: 'POST',
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf_token"]').getAttribute('content'),
        },
        body: formData,
    })
    .then((response) => response.json())
    .then((data) => {
        if (data.success) {
            console.log("PDF sent via email successfully!");
        } else {
            console.error("Error sending PDF via email.");
        }
    })
    .catch((error) => {
        console.error("Error sending PDF to server:", error);
    });
};

</script>


<style scoped>
.print-area {
    width: 210mm;
    padding: 10mm;
    margin: 0 auto;
    background: white;
    /* box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2); */
    page-break-inside: avoid;
}

.table-container {
    width: 100%;
    font-size: 12px;
    border-collapse: collapse;
}

.table-container,
.table-container div {
    page-break-inside: avoid;
}

.toggle-container {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.toggle-label {
    font-size: 0.875rem;
    font-weight: 600;
}

.toggle-switch {
    position: relative;
    display: inline-block;
    width: 3.5rem;
    height: 1.75rem;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: 0.4s;
    border-radius: 1.75rem;
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 1.25rem;
    width: 1.25rem;
    left: 0.25rem;
    bottom: 0.25rem;
    background-color: white;
    transition: 0.4s;
    border-radius: 50%;
}

input:checked + .toggle-slider {
    background-color: #10b981;
}

input:checked + .toggle-slider:before {
    transform: translateX(1.75rem);
}

.toggle-labels {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 100%;
    display: flex;
    justify-content: space-between;
    padding: 0 0.5rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: white;
    pointer-events: none;
}

@media print {
    input[type="checkbox"],
    .buttons,
    .non-printable,
    .mt-6 {
        display: none !important;
    }

    .print-area {
        margin: 0;
        padding: 5mm;
        width: 100%;
        height: auto;
        box-shadow: none;
        page-break-after: avoid;
    }

    .table-container {
        page-break-before: auto;
        page-break-after: avoid;
        page-break-inside: avoid;
    }

    .table-container tr {
        page-break-inside: avoid;
        break-inside: avoid-column;
    }

    .table-container th,
    .table-container td {
        font-size: 11px !important;
        padding: 4px;
    }

    .catalog-area {
        page-break-before: always;
    }

    .catalog-area img {
        max-width: 100%;
        height: auto;
    }
}
</style>
