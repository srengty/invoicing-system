<template>
    <Head title="Quotations" />
    <GuestLayout>
        <Toast position="top-center" group="tc" />
        <Toast position="top-right" group="tr" />

        <div class="quotations text-sm">
            <div class="flex justify-between items-center p-4">
                <h1 class="text-2xl">Quotations list</h1>
            </div>
            <div class="flex justify-between items-center p-4">
                Quotations are proposed to customer to bid for a project.
            </div>
            <div class="flex justify-end p-4 gap-4">
                <div>
                    <Link :href="route('quotations.create')"
                        ><Button
                            icon="pi pi-plus"
                            label="Issue Quotation"
                            rounded
                    /></Link>
                </div>
                <Link :href="route('invoices.create')"
                    ><Button icon="pi pi-plus" label="Issue Invoice" rounded
                /></Link>
                <Link :href="route('agreements.create')"
                    ><Button icon="pi pi-plus" label="Record Agreement" rounded
                /></Link>
            </div>

            <div>
                <DataTable
                    :value="quotations"
                    paginator
                    :rows="5"
                    :rowsPerPageOptions="[5, 10, 20, 50]"
                    tableStyle="min-width: 50rem"
                >
                    <Column
                        field="quotation_no"
                        header="No."
                        style="width: 10%"
                    />
                    <Column
                        field="customer.name"
                        header="Customer/Organization Name"
                        style="width: 25%"
                    />
                    <Column field="total" header="Total" style="width: 10%" />
                    <Column field="status" header="Status" style="width: 10%">
                        <template #body="slotProps">
                            <Dropdown
                                v-model="slotProps.data.status"
                                :options="StatusOptions"
                                optionLabel="name"
                                optionValue="code"
                                placeholder="Select Status"
                                class="w-full md:w-14rem"
                                @change="updateQuotationStatus(slotProps.data)"
                            />

                        </template>
                    </Column>

                    <Column
                        field="customer_status"
                        header="Customer Status"
                        style="width: 20%"
                    />
                    <Column header="View / Print-out" style="width: 20%">
                        <template #body="slotProps">
                            <div class="flex gap-4">
                                <Button
                                    icon="pi pi-eye"
                                    aria-label="View"
                                    severity="info"
                                    rounded
                                    size="small"
                                    class="custom-button"
                                    @click="viewQuotation(slotProps.data)"
                                />
                                <Button
                                    icon="pi pi-print"
                                    aria-label="Print out"
                                    size="small"
                                    class="custom-button"
                                    @click="
                                        printQuotation(
                                            slotProps.data.quotation_no
                                        )
                                    "
                                    rounded
                                />
                            </div>
                        </template>
                    </Column>
                </DataTable>

                <!-- View Dialog display -->
                <Dialog
                    v-model:visible="isViewDialogVisible"
                    header="Quotation Details"
                    modal
                    :style="{ width: '40rem' }"
                    class="text-sm"
                >
                    <div
                        v-if="selectedQuotation"
                        class="flex flex-col gap-2 text-sm pl-6"
                    >
                        <p><strong>ID:</strong> {{ selectedQuotation.id }}</p>
                        <p>
                            <strong>Quotation No.:</strong>
                            {{ selectedQuotation.quotation_no }}
                        </p>
                        <p>
                            <strong>Quotation Date:</strong>
                            {{ selectedQuotation.quotation_date }}
                        </p>
                        <p>
                            <strong>Customer ID:</strong>
                            {{ selectedQuotation.customer_id }}
                        </p>
                        <p>
                            <strong>Customer Name:</strong>
                            {{ selectedQuotation.customer?.name || "N/A" }}
                        </p>
                        <p>
                            <strong>Address:</strong>
                            {{ selectedQuotation.address }}
                        </p>
                        <p>
                            <strong>Phone Number:</strong>
                            {{ selectedQuotation.phone_number }}
                        </p>

                        <!-- Items Section -->
                        <span class="font-bold block mb-2 text-center"
                            >Items</span
                        >
                        <div v-if="selectedQuotation.products?.length">
                            <DataTable
                                :value="selectedQuotation.products"
                                responsiveLayout="scroll"
                            >
                                <Column field="name" header="Item"></Column>
                                <Column
                                    field="pivot.quantity"
                                    header="QTY"
                                ></Column>
                                <Column
                                    field="pivot.price"
                                    header="Unit Price"
                                ></Column>
                            </DataTable>
                        </div>

                        <br />
                        <p>
                            <strong>Total:</strong>
                            {{ selectedQuotation.total }}
                        </p>
                    </div>

                    <template #footer>
                        <Button
                            label="Approve"
                            severity="success"

                        />
                        <Button
                            label="Revice"
                            severity="danger"
                        />
                        <Button
                            label="Close"
                            severity="secondary"
                            @click="isViewDialogVisible = false"
                        />
                    </template>
                </Dialog>
            </div>
        </div>
    </GuestLayout>
</template>

<script setup>
``;
import ChooseColumns from "@/Components/ChooseColumns.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { ref } from "vue";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Button from "primevue/button";
import Dialog from "primevue/dialog";
import { useForm } from "@inertiajs/vue3";
import VirtualScroller from "primevue/virtualscroller";
import moment from "moment";
import Dropdown from "primevue/dropdown";
import { router } from "@inertiajs/vue3"; // for printing
import { useToast } from "primevue/usetoast";

const toast = useToast();
const isViewDialogVisible = ref(false);
const selectedQuotation = ref([]);
const selectedQuo_customer = ref([]);
const userRole = ref("manager");

const props = defineProps({
    customers: Array,
    products: Array,
    quotations: Array,
});

const form = useForm({
    quotation_no: "",
    quotation_date: "",
    address: "",
    phone_number: "",
    customer_id: "",
    total: 0,
    tax: 0,
    // grand_total: 0,
    products: [],
});

const openForm = (quotations = null) => {
    if (quotations) {
        form.quotation_no = quotations.id;
        form.quotation_date = quotations.quotation_date;
        form.address = quotations.address;
        form.phone_number = quotations.phone_number;
        form.customer_id = quotations.customer_id;
        // form.grand_total = quotations.grand_total;
    } else {
        form.reset();
    }
    isFormVisible.value = true;
};

const closeForm = () => {
    isFormVisible.value = false;
    form.reset();
};

// Open view quotation dialog
const viewQuotation = (quotations) => {
    selectedQuotation.value = quotations;
    isViewDialogVisible.value = true;

    console.log(selectedQuotation);
};

const columns = [
    { field: "quotation_no", header: "Quotation No." },
    { field: "quotation_date", header: "Quotation Date" },
    { field: "customer.name", header: "Customer/Organization Name" },
    { field: "address", header: "Address" },
    { field: "phone_number", header: "Phone Number" },
    { field: "terms", header: "Terms" },
    { field: "total", header: "Total" },
    { field: "tax", header: "Tax" },
    // { field: 'grand_total', header: 'Grand Total' },
    { field: "status", header: "Status" },
    { field: "customer_status", header: "Customer Status" },
];

// Printing quotations
const printQuotation = (quotation_no) => {
    const quotUrl = `/quotations/${quotation_no}`;
    const printWindow = window.open(quotUrl, "_blank"); //create new tab

    printWindow.onload = () => {
        printWindow.print();
    };
    // if (printWindow) {
    //     printWindow.onload = () => {
    //         setTimeout(() => {
    //             printWindow.print();
    //             printWindow.close();
    //         }, 1000); // Add delay to ensure full load
    //     };
    // } else {
    //     alert('Popup blocked! Please allow popups for this website.');
    // }
};
const selectedColumns = ref(columns);
const showColumns = ref(columns);
const updateColumns = (columns) => {
    showColumns.value = selectedColumns.value;
};

// quotations status
const selectedStatus = ref();
const StatusOptions = ref([
    { name: "Pending", code: "Pending" },
    { name: "Approved", code: "Approved" },
    { name: "Revise", code: "Revise" },
]);

const updateQuotationStatus = (quotation) => {
    router.put(
        `/quotations/${quotation.id}/update-status`,
        {
            status: quotation.status, // Send selected status to backend
        },
        {
            preserveScroll: true,
            onSuccess: (response) => {
                if (response.props.flash.success) {
                    // Ensure a success response exists
                    toast.add({
                        severity: "success",
                        summary: "Success",
                        detail: "Quotation status updated successfully!",
                        life: 3000,
                    });
                }
            },
            onError: (err) => {
                toast.add({
                    severity: "error",
                    summary: "Error",
                    detail: "Failed to update quotation status!",
                    life: 3000,
                });
                console.error("Error updating quotation status:", err);
            },
        }
    );
};
</script>

<style>
.custom-button {
    padding: 4px 4px !important; /* Smaller padding */
    font-size: 12px !important; /* Smaller icon size */
    min-width: 30px !important; /* Reduce button width */
}
</style>
