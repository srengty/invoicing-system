<template>
    <Head title="Quotations Printing" />
    <Toast position="top-right" group="tr" />
    <div
        class="flex justify-start items-center gap-4 ml-10 mt-5 print:ml-0 print:gap-2"
    >
        <div class="flex items-center gap-3">
            <p class="text-sm font-semibold">{{ toggleLabel }}</p>
            <ToggleSwitch
                v-model="isKhmer"
                class="w-20"
                @change="handleToggleChange"
            />
        </div>
        <div class="">
            <Button
                label="Print Quotation"
                icon="pi pi-print"
                class="px-4 py-2"
                @click="generateAndDownloadPDF"
                size="small"
            />
        </div>
        <div class="">
            <Button
                label="Send Quotation"
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
                Only approved quotations can be sent
            </div>
        </div>
        <div class="">
            <Button
                label="Back"
                icon="pi pi-arrow-left"
                class="px-4 py-2"
                size="small"
                @click="goBack"
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

        <Dialog
            v-model:visible="isSendDialogVisible"
            header="Confirm Send"
            footer="dialog-footer"
            :modal="true"
            style="width: 350px"
        >
            <div v-if="quotation" class="flex flex-col gap-4 ml-2 mr-2">
                <div class="bg-blue-50 p-4 rounded-md">
                    <p class="font-semibold">
                        Quotation #{{ quotation.quotation_no || "Draft" }}
                    </p>
                    <p>Customer: {{ quotation.customer_name }}</p>
                </div>

                <!-- Email Checkbox -->
                <div class="flex items-start gap-3 p-3 border rounded-md">
                    <Checkbox
                        v-model="sendForm.emailChecked"
                        :binary="true"
                        :disabled="!hasCustomerEmail"
                    />
                    <div>
                        <label class="font-medium block">Email</label>
                        <p class="text-sm">
                            {{ quotation.email || "No email available" }}
                        </p>
                    </div>
                </div>

                <!-- Telegram Checkbox -->
                <div class="flex items-start gap-3 p-3 border rounded-md">
                    <Checkbox
                        v-model="sendForm.telegramChecked"
                        :binary="true"
                        :disabled="!hasTelegram"
                    />
                    <div>
                        <label class="font-medium block">Telegram</label>
                        <p
                            class="text-sm"
                            :class="{ 'text-gray-500': !hasTelegram }"
                        >
                            {{
                                quotation.telegram_chat_id
                                    ? "Telegram account linked"
                                    : "No Telegram account linked"
                            }}
                        </p>
                    </div>
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
import { ref, computed, watch, onMounted, nextTick } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import { PDFDocument } from "pdf-lib";
import { useToast } from "primevue/usetoast";
import { Head } from "@inertiajs/vue3";
import html2pdf from "html2pdf.js";
import { useForm } from "@inertiajs/vue3";
import { ToggleSwitch, Dialog, Button, Checkbox, Toast } from "primevue";

const toast = useToast();
const showSuccessToast = () => {
    toast.add({
        severity: "success",
        summary: "Success Message",
        detail: "Message Content",
        life: 3000,
    });
};
const showErrorToast = (detail) => {
    toast.add({
        severity: "error",
        summary: "Error",
        detail: detail || "An error occurred",
        life: 5000,
    });
};
const hasCustomerEmail = ref(true);
const hasTelegram = ref(true);

const sendForm = ref({
    emailChecked: false,
    telegramChecked: false,
});

const sendMail = useForm({
    quotation_id: null,
    send_email: false,
    send_telegram: false,
    pdf_file: null,
});
onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search);
    const isDownload = urlParams.get("download") === "1";

    if (isDownload) {
        setTimeout(() => {
            generateAndDownloadPDF();
        }, 1000);
    }
});
const isSendDialogVisible = ref(false);
const selectedQuotation = ref(null);
const isSending = ref(false);
const isApproved = computed(() => {
    return quotation.value.status?.toLowerCase() === "approved";
});

const cancelSend = () => {
    isSendDialogVisible.value = false;
};

const showConfirmationDialog = () => {
    if (!isApproved.value) {
        toast.add({
            severity: "warn",
            summary: "Quotation Not Approved",
            detail: "Only approved quotations can be sent.",
            life: 3000,
        });
        return;
    }
    selectedQuotation.value = quotation;
    isSendDialogVisible.value = true;
};

// Refs and Data Setup
const { props } = usePage();
const quotation = ref({
    ...props.quotation,
    total: props.quotation?.total || 0,
    total_usd: props.quotation?.total_usd || 0,
    exchange_rate: props.quotation?.exchange_rate || 4100,
});
defineProps({
    quotation: {
        type: Object,
        required: true,
    },
});

const printArea = ref(null);
const isUSD = ref(false);
const isKhmer = ref(true);
const toggleLabel = computed(() =>
    isKhmer.value ? "USD/Dollar($)" : "KHR/Reil(៛)"
);

// Currency conversion and formatting functions
const handleToggleChange = () => {
    if (
        !isKhmer.value &&
        (!quotation.value.total_usd || !quotation.value.exchange_rate)
    ) {
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
const currencyLabel = computed(() => (isUSD.value ? "KHR" : "USD"));

// Functions for converting and formatting currency
const convertCurrency = (amount) =>
    isUSD.value ? amount / exchangeRate.value : amount;
const formatNumber = (value) => {
    if (!value) return "0.00";
    const formatted = new Intl.NumberFormat("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(value);
    return isUSD.value ? `$${formatted}` : `៛${formatted}`;
};

// Compute total amount
const totalAmount = computed(() => {
    const totalKHR =
        quotation.value.products?.reduce(
            (sum, product) =>
                sum +
                (product.pivot?.quantity || 0) * (product.pivot?.price || 0),
            0
        ) || 0;
    return isUSD.value ? totalKHR / exchangeRate.value : totalKHR;
});

// Format product details
const formattedProducts = computed(() => {
    if (!quotation.value.products) return [];
    return quotation.value.products.map((product) => ({
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

const generatePDF = (element) => {
    return new Promise((resolve) => {
        html2pdf().from(element).toPdf().outputPdf("blob").then(resolve);
    });
};
const includeCatalog = ref(props.includeCatalog);
const generateAndDownloadPDF = async () => {
    try {
        await nextTick();
        setTimeout(async () => {
            const element = printArea.value;
            const quotationPDF = await generatePDF(element);
            const catalogPDFs = await generateCatalogPDFs(
                formattedProducts.value
            );
            const mergedPDF = await mergePDFs([quotationPDF, ...catalogPDFs]);
            const filename = `Quotation_${
                quotation.value.customer_name ||
                quotation.value.customer?.name ||
                "Customer"
            }_${quotation.value.quotation_no}.pdf`;
            downloadPDF(mergedPDF, filename);
            router.get(route("quotations.print"), {}, { preserveScroll: true });
        }, 1000);
    } catch (error) {
        console.error("Error generating PDF:", error);
    }
};

// Function to handle sending the PDF via email
const generateAndSendPDF = async () => {
    try {
        if (!printArea.value) {
            console.error("Print area is not available");
            return;
        }

        const quotationPDF = await generatePDF(printArea.value);
        const catalogPDFs = await generateCatalogPDFs(formattedProducts.value);
        const mergedPDF = await mergePDFs([quotationPDF, ...catalogPDFs]);

        const filename = `Quotation_${quotation.value.customer_name}_${quotation.value.quotation_no}.pdf`;
        sendPDFViaEmail(mergedPDF, filename);
        isSendDialogVisible.value = false;
        window.location.href = route("quotations.print");
    } catch (error) {
        console.error("Error generating PDFs:", error);
    }
};

// Function to generate catalog PDFs (if available)
const generateCatalogPDFs = async (products) => {
    const catalogPDFs = await Promise.all(
        products.map(async (product) => {
            if (product.pdf_url && product.include_catalog === 1) {
                try {
                    const response = await fetch(product.pdf_url);
                    if (!response.ok) {
                        console.warn(
                            `Failed to fetch catalog PDF for product ${product.id}`
                        );
                        return null;
                    }
                    return await response.blob();
                } catch (error) {
                    console.warn(
                        `Error fetching catalog PDF for product ${product.id}:`,
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

// Merge multiple PDFs
const mergePDFs = async (pdfBlobs) => {
    const pdfDoc = await PDFDocument.create();
    for (const pdfBlob of pdfBlobs) {
        const pdf = await PDFDocument.load(await pdfBlob.arrayBuffer());
        const pages = await pdfDoc.copyPages(pdf, pdf.getPageIndices());
        pages.forEach((page) => pdfDoc.addPage(page));
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

const sendPDFViaEmail = async (pdfBytes, filename) => {
    isSending.value = true;

    try {
        const sendEmail = sendForm.value.emailChecked;
        const sendTelegram = sendForm.value.telegramChecked;

        const blob = new Blob([pdfBytes], { type: "application/pdf" });
        const formData = new FormData();
        formData.append("quotation_id", quotation.value.id);
        formData.append("pdf_file", blob, filename);
        formData.append("update_status", "Pending");

        if (sendEmail) formData.append("send_email", true);
        if (sendTelegram) formData.append("send_telegram", true);

        const response = await fetch("/quotations/send", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf_token"]')
                    .getAttribute("content"),
            },
            body: formData,
        });

        const data = await response.json();

        if (data.success) {
            if (sendEmail) {
                toast.add({
                    severity: "success",
                    summary: "Email Sent",
                    detail: "Quotation sent via email successfully.",
                    life: 5000,
                    group: "tr",
                });
            }

            if (sendTelegram) {
                toast.add({
                    severity: "success",
                    summary: "Telegram Sent",
                    detail: "Quotation sent via Telegram successfully.",
                    life: 5000,
                    group: "tr",
                });
            }

            isSendDialogVisible.value = false;
        } else {
            throw new Error("Server returned an error.");
        }
    } catch (error) {
        console.error("Error sending quotation:", error);
        toast.add({
            severity: "error",
            summary: "Sending Failed",
            detail: "Could not send the quotation.",
            group: "tr",
            life: 5000,
        });
    } finally {
        isSending.value = false;
    }
};
const goBack = () => {
    window.history.back();
};
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
.p-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>
