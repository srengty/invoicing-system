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
        <div class="flex justify-center mt-6 mb-2">
            <Button
                label="Print Quotation"
                icon="pi pi-print"
                class="px-4 py-2"
                @click="generateAndMergePDFs"
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
                        <p class="font-medium">
                            {{ isUSD ? product.name : product.name_kh }}
                        </p>
                        <p class="text-gray-600 text-xs">
                            {{ isUSD ? product.desc : product.desc_kh }}
                        </p>
                        <p class="text-gray-500 italic text-xs">
                            {{ isUSD ? product.remark : product.remark_kh }}
                        </p>
                    </div>
                    <div class="text-start">{{ product.quantity }}</div>
                    <div class="text-start font-semibold">
                        ៛{{ formatNumber(convertCurrency(product.price)) }}
                    </div>
                    <div class="text-start font-semibold">
                        ៛{{
                            formatNumber(
                                convertCurrency(
                                    product.price * product.quantity
                                )
                            )
                        }}
                    </div>
                </div>
            </div>

            <!-- Total Amount -->
            <p class="pt-6 flex justify-end">
                Total ({{ currencyLabel }}): {{ currencySymbol
                }}{{ formatNumber(convertCurrency(totalAmount)) }}
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
import html2pdf from "html2pdf.js";
import { PDFDocument } from "pdf-lib";

const { props } = usePage();
const quotation = ref(props.quotation);
const printArea = ref(null);
const catalogArea = ref(null);

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

const totalAmount = computed(() => {
    return (
        quotation.value.products?.reduce((sum, product) => {
            const quantity = product.pivot?.quantity || 0;
            const price = product.pivot?.price || 0;
            return sum + quantity * price;
        }, 0) || 0
    );
});

const formattedProducts = computed(() => {
    if (!quotation.value.products) return [];

    const products = quotation.value.products.map((product) => ({
        id: product.id,
        name: product.name || "Unknown",
        name_kh: product.name_kh || "Unknown",
        desc: product.desc || "",
        desc_kh: product.desc_kh || "",
        remark: product.remark || "",
        remark_kh: product.remark_kh || "",
        quantity: product.pivot?.quantity ?? 0,
        price: product.pivot?.price ?? 0,
        include_catalog: product.pivot?.include_catalog ?? 0,
        pdf_url: product.pdf_url || null,
    }));

    console.log("Formatted Products:", products);
    return products;
});

const generateAndMergePDFs = async () => {
    try {
        const quotationPDF = await generatePDF(printArea.value);
        const productsWithPDF = formattedProducts.value.filter((product) => {
            const include = product.pdf_url && product.include_catalog === 1;
            console.log(
                `Product ${product.id}: include_catalog=${product.include_catalog}, pdf_url=${product.pdf_url}, include=${include}`
            );
            return include;
        });
        const catalogPDFs = await Promise.all(
            productsWithPDF.map(async (product) => {
                try {
                    console.log(
                        `Fetching catalog PDF for product ${product.id} from ${product.pdf_url}...`
                    );
                    const response = await fetch(product.pdf_url);
                    if (!response.ok) {
                        console.warn(
                            `Failed to fetch catalog PDF for product ${product.id}: ${response.status} ${response.statusText}`
                        );
                        return null;
                    }
                    const blob = await response.blob();
                    return blob;
                } catch (error) {
                    console.warn(
                        `Error fetching catalog PDF for product ${product.id}:`,
                        error
                    );
                    return null;
                }
            })
        );
        const validCatalogPDFs = catalogPDFs.filter((pdf) => pdf !== null);
        const mergedPDFBytes = await mergePDFs([
            quotationPDF,
            ...validCatalogPDFs,
        ]);
        displayMergedPDF(mergedPDFBytes);
    } catch (error) {
        console.error("Error generating PDFs:", error);
    }
};

const generatePDF = (element) => {
    return new Promise((resolve) => {
        html2pdf().from(element).toPdf().outputPdf("blob").then(resolve);
    });
};

const mergePDFs = async (pdfBlobs) => {
    const pdfDoc = await PDFDocument.create();

    for (const pdfBlob of pdfBlobs) {
        const pdf = await PDFDocument.load(await pdfBlob.arrayBuffer());
        const pages = await pdfDoc.copyPages(pdf, pdf.getPageIndices());
        pages.forEach((page) => pdfDoc.addPage(page));
    }

    return pdfDoc.save();
};

const displayMergedPDF = (pdfBytes) => {
    const blob = new Blob([pdfBytes], { type: "application/pdf" });
    const url = URL.createObjectURL(blob);
    window.open(url, "_blank");
};

const downloadPDF = (pdfBytes, filename) => {
    const blob = new Blob([pdfBytes], { type: "application/pdf" });
    downloadBlob(blob, filename);
};

const downloadBlob = (blob, filename) => {
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};
</script>

<style scoped>
.print-area {
    width: 210mm;
    padding: 10mm;
    margin: 0 auto;
    background: white;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
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
</style>
