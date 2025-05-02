<template>
    <Head title="Invoice Printing" />
    <div class="flex justify-start items-center gap-4 ml-10">
        <!-- Toggle Currency -->
        <div class="flex items-center gap-3 mt-6">
            <p class="text-sm font-semibold">{{ toggleLabel }}</p>
            <ToggleSwitch
                v-model="isKhmer"
                :onLabel="'KH/៛'"
                :offLabel="'EN/$'"
                class="w-20"
                @change="handleToggleChange"
            />
        </div>
        <Toast position="top-right" group="tr" />
        <!-- Print Button -->
        <div class="flex justify-center mt-6 mb-2">
            <Button
                label="Print Invoice"
                icon="pi pi-print"
                class="px-4 py-2"
                @click="generateAndDownloadPDF"
                size="small"
            />
        </div>
        <!-- Send Email Button -->
        <div class="flex justify-center mt-6 mb-2">
            <Button
                label="Send Invoice via Email"
                icon="pi pi-send"
                class="px-4 py-2"
                @click="showConfirmationDialog"
                size="small"
                :disabled="!isApproved"
            />
            <div
                v-if="!isApproved"
                class="absolute -bottom-8 left-1/2 transform -translate-x-1/2 bg-gray-700 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity"
            >
                Only approved invoices can be sent
            </div>
        </div>
    </div>

    <div
        ref="printArea"
        class="flex-col justify-center print-area a4-size text-sm"
    >
        <h1 class="flex flex-row justify-center text-3xl font-bold mb-6">
            Invoice
        </h1>
        <div class="flex flex-row justify-between mb-6">
            <div class="flex flex-col w-1/2 gap-4">
                <p><strong>Invoice No.:</strong> {{ invoice.invoice_no }}</p>
                <p>
                    <strong>Invoice Date:</strong>
                    {{ formatDate(invoice.invoice_date) }}
                </p>
                <p>
                    <strong>Customer Name:</strong>
                    {{ invoice.customer_name || "N/A" }}
                </p>
                <p><strong>Address:</strong> {{ invoice.address }}</p>
                <p><strong>Phone Number:</strong> {{ invoice.phone_number }}</p>
            </div>
            <div class=" flex flex-col w-1/2 items-end gap-4">
                <div class="right grid gap-4 mr-10">
                    <p>
                        <strong>Quotation No:</strong>
                        {{ invoice.quotation_no || "N/A" }}
                    </p>
                    <p>
                        <strong>Agreement No:</strong>
                        {{ invoice.agreement_no || "N/A" }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Invoice Section: Products Table -->
        <div ref="printTable" class="page-break">
            <div
                v-if="invoice.products && invoice.products.length"
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
                    v-for="(product, index) in invoice.products"
                    :key="product.product_id"
                    class="grid grid-cols-[70px_170px_110px_170px_150px] border-b py-2 px-4 text-start"
                >
                    <div class="text-start">{{ index + 1 }}</div>
                    <div>
                        <div class="font-medium">
                            {{
                                isUSD
                                    ? product.product_name
                                    : product.product_name_kh ||
                                      product.product_name
                            }}
                        </div>
                        <p class="text-gray-600 text-xs">
                            {{
                                isUSD
                                    ? product.desc
                                    : product.desc_kh || product.desc
                            }}
                        </p>
                    </div>
                    <div class="text-start">{{ product.quantity }}</div>
                    <div class="text-start font-semibold">
                        {{ formatCurrency(convertCurrency(product.price)) }}
                    </div>
                    <div class="text-start font-semibold">
                        {{
                            formatCurrency(
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
                        <!-- If isKhmer is true, show grand_total, else show calculated totalAmount -->
                        {{
                            isKhmer
                                ? invoice.grand_total
                                : formatCurrency(totalAmount)
                        }}
                    </p>
                </div>
                <div v-if="!isKhmer" class="pt-2 flex justify-end">
                    <p class="text-sm text-gray-600">
                        Exchange Rate: 1 USD = {{ exchangeRate }} KHR
                    </p>
                </div>
            </div>

            <!-- Terms and Conditions -->
            <div class="mt-8">
                <p class="font-bold mb-2">Terms and Conditions</p>
                <div
                    class="w-full border rounded-md p-3 text-sm text-gray-800 overflow-y-auto max-h-32"
                >
                    {{
                        invoice.terms ||
                        "Full payment is required upon invoice acceptance."
                    }}
                </div>
            </div>

            <!-- Customer Acceptance & Authorization -->
            <div class="mt-10 flex justify-between text-sm page-break-avoid">
                <div>
                    <p class="font-bold">Customer Acceptance</p>
                    <p class="text-xs italic">
                        I hereby accept the invoice and agree on Terms and
                        Conditions.
                    </p>
                    <div class="mt-5">
                        <p>
                            <strong>Date:</strong>
                            {{ invoice.acceptance_date || "___________" }}
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-bold">Authorized by</p>
                    <div class="mt-10">
                        <p>
                            <strong>Signature:</strong>
                            {{ invoice.authorized_by || "___________" }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <Dialog
            v-model:visible="isSendDialogVisible"
            header="Confirm Send"
            footer="dialog-footer"
        >
            <div v-if="invoice" class="flex flex-col gap-4 ml-2 mr-2">
                <!-- Display Selected Invoice Info -->
                <div>
                    <strong>Invoice No:</strong>
                    <p>{{ invoice.invoice_no }}</p>
                </div>
                <div>
                    <strong>Customer Name:</strong>
                    <p>{{ invoice.customer_name || "N/A" }}</p>
                </div>

                <!-- Email Checkbox -->
                <div class="flex items-center">
                    <input
                        type="checkbox"
                        id="emailCheckbox"
                        v-model="sendForm.emailChecked"
                        class="mr-2"
                    />
                    <label for="emailCheckbox" class="font-bold"
                        >Email: {{ invoice.email || "N/A" }}</label
                    >
                </div>

                <!-- Telegram Checkbox -->
                <div class="flex items-center">
                    <input
                        type="checkbox"
                        id="telegramCheckbox"
                        v-model="sendForm.telegramChecked"
                        class="mr-2"
                    />
                    <label for="telegramCheckbox" class="font-bold"
                        >Telegram: {{ invoice.phone_number || "N/A" }}</label
                    >
                </div>
            </div>

            <!-- Dialog Footer -->
            <template #footer>
                <Button
                    label="Cancel"
                    severity="secondary"
                    @click="cancelSend"
                />
                <Button
                    label="Send"
                    severity="success"
                    @click="generateAndSendPDF"
                    :loading="isSending"
                />
            </template>
        </Dialog>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { usePage } from "@inertiajs/vue3";
import Button from "primevue/button";
import html2pdf from "html2pdf.js";
import { PDFDocument } from "pdf-lib";
import ToggleSwitch from "primevue/toggleswitch";
import { useToast } from "primevue/usetoast";
import Dialog from "primevue/dialog";
import { Head } from "@inertiajs/vue3";
import Toast from "primevue/toast";
import { useForm } from "@inertiajs/vue3";

const toast = useToast();

// Reactive form data and states
const sendForm = ref({
    emailChecked: false,
    telegramChecked: false,
});

const sendMail = useForm({
    invoice_id: null,
    send_email: false,
    send_telegram: false,
    pdf_file: null,
});

// Other reactive state variables
const isSendDialogVisible = ref(false);
const isSending = ref(false);
const printArea = ref(null);

const { props } = usePage();
const invoice = ref({
    ...props.invoice,
    grand_total: props.invoice?.grand_total || 0,
    total_usd: props.invoice?.total_usd || 0,
    exchange_rate: props.invoice?.exchange_rate || 4100,
});

const isApproved = computed(() => {
    return invoice.value.status?.toLowerCase() === "approved";
});

const cancelSend = () => {
    isSendDialogVisible.value = false;
};

const showConfirmationDialog = () => {
    if (!isApproved.value) {
        toast.add({
            severity: "warn",
            summary: "Invoice Not Approved",
            detail: "Only approved invoices can be sent.",
            life: 3000,
            group: "tr",
        });
        return;
    }
    isSendDialogVisible.value = true;
};

// State for toggling between currencies
const isUSD = ref(false);
const isKhmer = ref(true);
const toggleLabel = computed(() =>
    isKhmer.value ? "USD/Dollar($)" : "KHR/Reil(៛)"
);

// Exchange rate and currency labels
const exchangeRate = computed(() => invoice.value.exchange_rate);
const currencyLabel = computed(() => (isUSD.value ? "USD" : "KHR"));

// Functions for converting and formatting currency
const convertCurrency = (amount) => {
    if (!amount) return 0;
    return isUSD.value ? amount / exchangeRate.value : amount;
};

const formatCurrency = (value) => {
    if (!value) return isUSD.value ? "$0.00" : "៛0";
    const formatted = new Intl.NumberFormat(isUSD.value ? "en-US" : "km-KH", {
        minimumFractionDigits: isUSD.value ? 2 : 0,
        maximumFractionDigits: isUSD.value ? 2 : 0,
    }).format(value);
    return isUSD.value ? `$${formatted}` : `៛${formatted}`;
};

const formatDate = (dateString) => {
    if (!dateString) return "";
    const date = new Date(dateString);
    return date.toLocaleDateString("en-GB", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
    });
};

const handleToggleChange = () => {
    isUSD.value = !isKhmer.value;
    if (
        isUSD.value &&
        (!invoice.value.total_usd || invoice.value.total_usd <= 0)
    ) {
        toast.add({
            severity: "warn",
            summary: "Missing USD Information",
            detail: "This invoice doesn't have USD amount specified.",
            group: "tr",
            life: 5000,
        });
    }
};

// Compute total amount
const totalAmount = computed(() => {
    const totalKHR =
        invoice.value.total ||
        invoice.value.products?.reduce(
            (sum, product) =>
                sum +
                (product.pivot?.quantity || 0) * (product.pivot?.price || 0),
            0
        ) ||
        0;
    return isUSD.value
        ? invoice.value.total_usd || totalKHR / exchangeRate.value
        : totalKHR;
});

// Format product details
const formattedProducts = computed(() => {
    if (!invoice.value.products) return [];
    return invoice.value.products.map((product) => ({
        id: product.id,
        name: product.name || "No English name",
        name_kh: product.name_kh || product.name || "No Khmer name",
        desc: product.description || product.desc || "",
        desc_kh: product.description_kh || product.desc_kh || "",
        remark: product.remark || "",
        remark_kh: product.remark_kh || "",
        quantity: product.pivot?.quantity ?? 0,
        price: product.pivot?.price ?? 0,
        include_catalog: product.pivot?.include_catalog ?? 0,
        pdf_url: product.pdf_url || null,
    }));
});

const generatePDF = (element) => {
    const opt = {
        margin: 10,
        filename: `invoice_${invoice.value.invoice_no}.pdf`,
        image: { type: "jpeg", quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: "mm", format: "a4", orientation: "portrait" },
    };

    return new Promise((resolve) => {
        html2pdf()
            .set(opt)
            .from(element)
            .toPdf()
            .outputPdf("blob")
            .then(resolve);
    });
};

const generateAndDownloadPDF = async () => {
    try {
        if (!printArea.value) {
            console.error("Print area is not available");
            return;
        }

        const invoicePDF = await generatePDF(printArea.value);
        const catalogPDFs = await generateCatalogPDFs(formattedProducts.value);
        const mergedPDF = await mergePDFs([invoicePDF, ...catalogPDFs]);
        const filename = `invoice_${invoice.value.invoice_no}.pdf`;
        downloadPDF(mergedPDF, filename);
    } catch (error) {
        console.error("Error generating PDF:", error);
        toast.add({
            severity: "error",
            summary: "PDF Generation Failed",
            detail: "Could not generate PDF file.",
            group: "tr",
            life: 5000,
        });
    }
};

const generateCatalogPDFs = async (products) => {
    const catalogPDFs = await Promise.all(
        products.map(async (product) => {
            if (product.include_catalog && product.pdf_url) {
                try {
                    const response = await fetch(product.pdf_url);
                    if (!response.ok) throw new Error("Failed to fetch PDF");
                    return await response.blob();
                } catch (error) {
                    console.error(
                        `Error fetching catalog for product ${product.id}:`,
                        error
                    );
                    return null;
                }
            }
            return null;
        })
    );
    return catalogPDFs.filter((pdf) => pdf !== null);
};

const mergePDFs = async (pdfBlobs) => {
    try {
        const pdfDoc = await PDFDocument.create();

        for (const pdfBlob of pdfBlobs) {
            if (!pdfBlob) continue;
            const pdf = await PDFDocument.load(await pdfBlob.arrayBuffer());
            const pages = await pdfDoc.copyPages(pdf, pdf.getPageIndices());
            pages.forEach((page) => pdfDoc.addPage(page));
        }

        return await pdfDoc.save();
    } catch (error) {
        console.error("Error merging PDFs:", error);
        throw error;
    }
};

const downloadPDF = (pdfBytes, filename) => {
    const blob = new Blob([pdfBytes], { type: "application/pdf" });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};

const generateAndSendPDF = async () => {
    isSending.value = true;
    try {
        if (!printArea.value) {
            throw new Error("Print area not available");
        }

        // Generate the invoice PDF
        const invoicePDF = await generatePDF(printArea.value);

        // If you have catalog PDFs, generate and merge them
        const catalogPDFs = await generateCatalogPDFs(formattedProducts.value);
        const mergedPDF = await mergePDFs([invoicePDF, ...catalogPDFs]);

        // Create a Blob from the merged PDF
        const pdfBlob = new Blob([mergedPDF], { type: "application/pdf" });
        const filename = `invoice_${invoice.value.invoice_no}.pdf`; // Set the filename to invoice_no

        // Send the PDF with the filename to the backend via email
        await sendPDFViaEmail(pdfBlob, filename);

        toast.add({
            severity: "success",
            summary: "Invoice Sent",
            detail: "The invoice has been sent successfully.",
            group: "tr",
            life: 5000,
        });

        isSendDialogVisible.value = false; // Close the dialog after sending
    } catch (error) {
        console.error("Error sending invoice:", error);
        toast.add({
            severity: "error",
            summary: "Sending Failed",
            detail: "Could not send the invoice.",
            group: "tr",
            life: 5000,
        });
    } finally {
        isSending.value = false;
    }
};

const sendPDFViaEmail = async (pdfBlob, filename) => {
    isSending.value = true;

    try {
        // Create a new FormData instance to send the PDF with the correct filename
        const formData = new FormData();
        formData.append("invoice_id", invoice.value.id);
        formData.append("send_email", sendForm.value.emailChecked);
        formData.append("send_telegram", sendForm.value.telegramChecked);
        formData.append("pdf_file", pdfBlob, filename); // Attach the PDF Blob with the filename

        // POST the form data to the backend for sending the email
        await axios.post("/invoices/send", formData, {
            headers: {
                "Content-Type": "multipart/form-data", // Ensure we use multipart for file uploads
            },
        });

        window.location.href = route("invoices.list"); // Redirect after successful submission
    } catch (error) {
        console.error("Unexpected error:", error);
        toast.add({
            severity: "error",
            summary: "Error",
            detail: "Failed to send the invoice.",
            group: "tr",
            life: 5000,
        });
    } finally {
        isSending.value = false;
    }
};

onMounted(() => {
    // Initialize currency display based on invoice data
    if (invoice.value.total_usd && invoice.value.total_usd > 0) {
        isKhmer.value = false;
        isUSD.value = true;
    }
});
</script>

<style scoped>
.p-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.print-area {
    width: 210mm;
    padding: 10mm;
    margin: 0 auto;
    background: white;
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
.right{
        margin-left: 30px;
    }
.page-break {
    page-break-inside: avoid;
}

.page-break-avoid {
    page-break-inside: avoid;
}
</style>
