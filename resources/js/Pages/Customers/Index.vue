<template>
    <Head title="Customers/Organization Name" />
    <ConfirmDialog />
    <Toast position="top-center" group="tc" />
    <GuestLayout>
        <BodyLayout>
            <div class="customers">
                <!-- Search and Header Section -->
                <div class="flex justify-between items-center pb-4">
                    <div class="flex items-center gap-2">
                        <img src="/User.png" alt="Item Icon" class="h-8 w-8" />
                        <h1 class="text-xl text-green-600">
                            Customers/Organization Name
                        </h1>
                    </div>
                    <div class="flex items-center gap-2">
                        <!-- Search Input -->
                        <InputText
                            v-model="searchTerm"
                            placeholder="Search"
                            class="w-64"
                            size="small"
                        />

                        <!-- Search Type Dropdown -->
                        <Dropdown
                            v-model="searchType"
                            :options="searchOptions"
                            optionLabel="label"
                            optionValue="value"
                            class="w-48 text-sm"
                            placeholder="Search by"
                        />

                        <!-- New Customer Button -->
                        <Button
                            icon="pi pi-plus"
                            label="New Customer"
                            @click="isCreateCustomerVisible = true"
                            size="small"
                        />
                    </div>
                </div>

                <!-- DataTable -->
                <DataTable
                    :value="filteredCustomers"
                    paginator
                    :rows="5"
                    :rowsPerPageOptions="[5, 10, 20, 50]"
                    class="text-sm"
                >
                    <!-- Dynamic Columns -->
                    <Column
                        v-for="col of showColumns"
                        :key="col.field"
                        :field="col.field"
                        :header="col.header"
                    ></Column>

                    <!-- Actions Column -->
                    <Column header="Actions">
                        <template #body="slotProps">
                            <div class="flex gap-2">
                                <!-- View Button -->
                                <Button
                                    icon="pi pi-eye"
                                    class="p-button-info"
                                    aria-label="View"
                                    size="small"
                                    @click="viewCustomer(slotProps.data.id)"
                                    outlined
                                />

                                <!-- Edit Button -->
                                <Button
                                    icon="pi pi-pencil"
                                    class="p-button-warning"
                                    aria-label="Edit"
                                    size="small"
                                    @click="editCustomer(slotProps.data.id)"
                                    outlined
                                />

                                <!-- Toggle Active Status Button -->
                                <Button
                                    :icon="
                                        slotProps.data.active
                                            ? 'pi pi-lock'
                                            : 'pi pi-unlock'
                                    "
                                    :label="
                                        slotProps.data.active
                                            ? 'Deactivate'
                                            : 'Activate'
                                    "
                                    :class="{
                                        'p-button-danger':
                                            slotProps.data.active,
                                        'p-button-success':
                                            !slotProps.data.active,
                                        'w-28 h-8 flex items-center justify-center': true,
                                    }"
                                    aria-label="Toggle Active Status"
                                    size="small"
                                    @click="toggleActive(slotProps.data)"
                                    outlined
                                />
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>

            <!-- Create Customer Dialog -->
            <Dialog
                v-model:visible="isCreateCustomerVisible"
                modal
                header="Add Customer"
                class="w-2/3"
            >
                <template #header>
                    <div class="flex items-center gap-2">
                        <img
                            src="/User.png"
                            alt="Item Customer"
                            class="h-8 w-8 ml-2"
                        />
                        <span class="text-xl font-semibold bor"
                            >Add Customer</span
                        >
                    </div>
                </template>
                <Customers
                    :customerCategories="customerCategories"
                    redirect_route="customers.index"
                    :mode="'create'"
                    @close="isCreateCustomerVisible = false"
                />
            </Dialog>

            <!-- Edit Customer Dialog -->
            <Dialog
                v-model:visible="isEditCustomerVisible"
                modal
                header="Edit Customer"
                class="w-2/3"
            >
                <template #header>
                    <div class="flex items-center gap-2">
                        <img
                            src="/Item.png"
                            alt="Item Customer"
                            class="h-8 w-8 ml-2"
                        />
                        <span class="text-xl font-semibold bor"
                            >Edit Customer</span
                        >
                    </div>
                </template>
                <Customers
                    :customerCategories="customerCategories"
                    redirect_route="customers.index"
                    :mode="'edit'"
                    :customer="selectedCustomer"
                    @close="isEditCustomerVisible = false"
                />
            </Dialog>

            <!-- View Customer Dialog -->
            <Dialog
                v-model:visible="isViewCustomerVisible"
                modal
                header="View Customer"
                class="w-2/3"
            >
                <template #header>
                    <div class="flex items-center gap-2">
                        <img
                            src="/Item.png"
                            alt="Item Customer"
                            class="h-8 w-8 ml-2"
                        />
                        <span class="text-xl font-semibold bor"
                            >Customer Details</span
                        >
                    </div>
                </template>
                <Customers
                    :customerCategories="customerCategories"
                    redirect_route="customers.index"
                    :mode="'view'"
                    :customer="selectedCustomer"
                    @close="isViewCustomerVisible = false"
                />
            </Dialog>
        </BodyLayout>
    </GuestLayout>
</template>

<script setup>
import { computed, ref } from "vue";
import { Head } from "@inertiajs/vue3";
import { DataTable, Column, Button, Dialog, InputText, Dropdown } from "primevue";
import { Inertia } from "@inertiajs/inertia";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import BodyLayout from "@/Layouts/BodyLayout.vue";
import Customers from "@/Components/Customers.vue";
import { useToast } from "primevue/usetoast";
import Toast from "primevue/toast";
import ConfirmDialog from "primevue/confirmdialog";
import { useConfirm } from "primevue/useconfirm";
import { router } from "@inertiajs/vue3";

const toast = useToast();
const confirm = useConfirm();

// Props
const props = defineProps({
    customers: Array,
    customerCategories: Array,
});

// Reactive State
const searchTerm = ref("");
const searchType = ref("name");
const isCreateCustomerVisible = ref(false);
const isEditCustomerVisible = ref(false);
const isViewCustomerVisible = ref(false);
const selectedCustomer = ref(null);

// Columns for DataTable
const columns = [
    { field: "name", header: "Name", style: { width: "5%" } },
    { field: "code", header: "Code", style: { width: "5%" } },
    { field: "credit_period", header: "Credit Period", style: { width: "10%" } },
    { field: "address", header: "Address", style: { width: "15%" } },
    { field: "website", header: "Website", style: { width: "5%" } },
    { field: "phone_number", header: "Phone", style: { width: "5%" } },
];

const showColumns = ref(columns);

// Search Options
const searchOptions = [
    { label: "Name", value: "name" },
    { label: "Code", value: "code" },
    { label: "Phone", value: "phone_number" },
    { label: "Email", value: "email" },
    { label: "Address", value: "address" },
    { label: "Credit Period", value: "credit_period" }, // Added
    { label: "Status", value: "active" }, // Added
];

// Filtered Customers
const filteredCustomers = computed(() => {
    const term = searchTerm.value.toLowerCase();
    if (!term) return props.customers; // If no search term, return all customers

    return props.customers.filter((customer) => {
        if (searchType.value === "name") {
            return customer.name.toLowerCase().includes(term);
        } else if (searchType.value === "code") {
            return customer.code.toLowerCase().includes(term);
        } else if (searchType.value === "phone_number") {
            return customer.phone_number.toLowerCase().includes(term);
        } else if (searchType.value === "email") {
            return customer.email.toLowerCase().includes(term);
        } else if (searchType.value === "address") {
            return customer.address.toLowerCase().includes(term);
        } else if (searchType.value === "credit_period") {
            return customer.credit_period.toString().includes(term);
        } else if (searchType.value === "active") {
            // Handle filtering by status (activate/deactivate)
            const status = customer.active.toLowerCase(); // Convert to lowercase
            return status.includes(term);
        }
        return false;
    });
});

// Edit Customer
const editCustomer = (id) => {
    const customer = props.customers.find((cust) => cust.id === id);
    selectedCustomer.value = customer;
    isEditCustomerVisible.value = true;
};

// View Customer
const viewCustomer = (id) => {
    const customer = props.customers.find((cust) => cust.id === id);
    selectedCustomer.value = customer;
    isViewCustomerVisible.value = true;
};

// Toggle Active Status
const toggleActive = (customer) => {
    const action = customer.active ? "deactivate" : "activate";

    confirm.require({
        message: `Are you sure you want to ${action} this customer?`,
        header: "Confirmation",
        icon: "pi pi-exclamation-triangle",
        accept: () => {
            router.put(
                route("customers.toggleActive", customer.id),
                { active: !customer.active },
                {
                    onSuccess: () => {
                        showToast(
                            "success",
                            "Success",
                            `Customer ${action}d successfully!`
                        );
                        // Refresh the page to reflect changes
                        router.reload({ preserveScroll: true });
                    },
                    onError: (error) => {
                        showToast(
                            "error",
                            "Error",
                            `Error ${action}ing customer.`
                        );
                        console.error("Error toggling active status:", error);
                    },
                }
            );
        },
        reject: () => {
            showToast("info", "Cancelled", "No changes were made.");
        },
    });
};

// Show Toast
const showToast = (severity, summary, detail, duration = 4000) => {
    toast.add({
        group: "tc",
        severity,
        summary,
        detail,
        life: duration,
        className: "my-custom-toast",
    });
};
</script>

<style scoped>
.my-custom-toast {
    background-color: #e0f7fa;
    border: 1px solid #4dd0e1;
    color: #006064;
}
</style>