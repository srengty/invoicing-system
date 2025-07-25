<template>
    <Head title="Customers/Organization Name" />
    <ConfirmDialog
        :draggable="false"
        :resizable="false"
        :position="'center'"
        :closeOnEscape="false"
    />
    <Toast position="top-center" group="tc" />
    <GuestLayout>
        <NavbarLayout class="fixed top-0 z-50 w-5/6" />
        <!-- PrimeVue Breadcrumb -->
        <div class="py-3 mt-16">
            <Breadcrumb :model="items" class="border-none bg-transparent p-0">
                <template #item="{ item }">
                    <Link
                        :href="item.to"
                        class="text-sm hover:text-primary flex items-start justify-center gap-1"
                    >
                        <i v-if="item.icon" :class="item.icon"></i>
                        {{ item.label }}
                    </Link>
                </template>
            </Breadcrumb>
        </div>
        <!-- <BodyLayout> -->
        <div class="customers px-4">
            <div class="flex justify-between items-center pb-4">
                <div class="flex items-center gap-2">
                    <!-- <img src="/User.png" alt="Item Icon" class="h-8 w-8 ml-4" />
                    <h1 class="text-xl text-green-600">
                        Customers/Organization Name
                    </h1> -->
                </div>
                <div class="flex items-center gap-2">
                    <Dropdown
                        v-model="searchType"
                        :options="searchOptions"
                        optionLabel="label"
                        optionValue="value"
                        class="w-48 text-sm"
                        placeholder="Select field to search"
                    />
                    <InputText
                        v-model="searchTerm"
                        placeholder="Search"
                        class="w-64"
                        size="small"
                    />
                    <Button
                        label="Clear"
                        @click="clearFilters"
                        class="p-button-secondary w-24"
                        icon="pi pi-times"
                        size="small"
                        severity="success"
                        variant="outlined"
                    />
                    <Button
                        v-if="userPermissions.canCreateCustomer"
                        icon="pi pi-plus"
                        label="New Customer"
                        @click="isCreateCustomerVisible = true"
                        size="small"
                    />
                </div>
            </div>

            <DataTable
                :value="filteredCustomers"
                paginator
                :rows="5"
                :rowsPerPageOptions="[5, 10, 20, 50]"
                class="text-sm"
                :showGridlines="true"
            >
                <Column
                    field="customer_category_name"
                    header="Category"
                    style="width: 5%; font-size: 12px"
                >
                    <template #body="{ data }">
                        {{ getCategoryNameById(data.customer_category_id) }}
                    </template>
                </Column>

                <Column
                    v-for="col of showColumns"
                    style="width: 10%; font-size: 12px"
                    :key="col.field"
                    :field="col.field"
                    :header="col.header"
                    :sortable="col.sortable"
                ></Column>

                <Column header="Actions" style="width: 5%; font-size: 12px">
                    <template #body="slotProps">
                        <div class="flex gap-2">
                            <Button
                                icon="pi pi-eye"
                                class="p-button-info"
                                aria-label="View"
                                size="small"
                                style="width: 30px; height: 30px"
                                @click="viewCustomer(slotProps.data.id)"
                                outlined
                                :disabled="!slotProps.data.active"
                            />
                            <Button
                                v-if="userPermissions.canAction"
                                icon="pi pi-pencil"
                                class="p-button-warning"
                                aria-label="Edit"
                                size="small"
                                style="width: 30px; height: 30px"
                                @click="editCustomer(slotProps.data.id)"
                                outlined
                                :disabled="!slotProps.data.active"
                            />
                            <Button
                                v-if="userPermissions.canAction"
                                :icon="
                                    slotProps.data.active
                                        ? 'pi pi-unlock'
                                        : 'pi pi-lock'
                                "
                                :label="
                                    slotProps.data.active
                                        ? 'Activate'
                                        : 'Deactivate'
                                "
                                :class="{
                                    'p-button-success': slotProps.data.active,
                                    'p-button-danger': !slotProps.data.active,
                                    ' w-28 h-8 flex items-center justify-center': true,
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

        <!-- Dialogs for Create, Edit, and View -->
        <Dialog
            v-model:visible="isCreateCustomerVisible"
            modal
            header="Add Customer"
            class="w-2/3"
            :draggable="false"
            :resizable="false"
            :position="'center'"
            :closeOnEscape="false"
        >
            <template #header>
                <div class="flex items-center gap-2">
                    <img
                        src="/User.png"
                        alt="Item Customer"
                        class="h-8 w-8 ml-2"
                    />
                    <span class="text-xl font-semibold bor">Add Customer</span>
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
            :draggable="false"
            :resizable="false"
            :position="'center'"
            :closeOnEscape="false"
        >
            <template #header>
                <div class="flex items-center gap-2">
                    <img
                        src="/Item.png"
                        alt="Item Customer"
                        class="h-8 w-8 ml-2"
                    />
                    <span class="text-xl font-semibold bor">Edit Customer</span>
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
            :draggable="false"
            :resizable="false"
            :position="'center'"
            :closeOnEscape="false"
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
        <!-- </BodyLayout> -->
    </GuestLayout>
</template>

<script setup>
import { computed, ref } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import {
    DataTable,
    Column,
    Button,
    Dialog,
    InputText,
    Dropdown,
} from "primevue";
import { Inertia } from "@inertiajs/inertia";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import BodyLayout from "@/Layouts/BodyLayout.vue";
import Customers from "@/Components/Customers.vue";
import { useToast } from "primevue/usetoast";
import Toast from "primevue/toast";
import ConfirmDialog from "primevue/confirmdialog";
import { useConfirm } from "primevue/useconfirm";
import { router } from "@inertiajs/vue3";
import NavbarLayout from "@/Layouts/NavbarLayout.vue";
import Breadcrumb from "primevue/breadcrumb";
import { usePage } from "@inertiajs/vue3";
import { route } from "ziggy-js";

const toast = useToast();
const confirm = useConfirm();
const searchType = ref("");

const props = defineProps({
    customers: Array,
    customerCategories: Array,
});
// The Breadcrumb Quotations
const page = usePage();
const userPermissions = computed(() => {
    const roles = page.props.userRoles || [];
    return {
        canCreateCustomer: roles.some(
            (role) =>
                role.toLowerCase().includes("division staff") ||
                role.toLowerCase().includes("chef department") ||
                role.toLowerCase().includes("revenue manager") ||
                role.toLowerCase().includes("director")
        ),
        canAction: roles.some(
            (role) =>
                role.toLowerCase().includes("division staff") ||
                role.toLowerCase().includes("chef department") ||
                role.toLowerCase().includes("revenue manager") ||
                role.toLowerCase().includes("director")
        ),
    };
});
const items = computed(() => [
    {
        label: "",
        to: "/dashboard",
        icon: "pi pi-home",
    },
    { label: page.props.title || "Customers", to: route("customers.index") },
]);

const columns = [
    { field: "name", header: "Name", style: { width: "5%" } },
    { field: "code", header: "Code", style: { width: "5%" }, sortable: true },
    {
        field: "credit_period",
        header: "Credit Period",
        style: { width: "10%" },
    },
    { field: "address", header: "Address", style: { width: "5%" } },
    { field: "website", header: "Website", style: { width: "5%" } },
    { field: "phone_number", header: "Phone", style: { width: "5%" } },
];

const selectedColumns = ref(columns);
const showColumns = ref(columns);

const isCreateCustomerVisible = ref(false);
const isEditCustomerVisible = ref(false);
const isViewCustomerVisible = ref(false);
const selectedCustomer = ref(null);
const searchTerm = ref("");

const searchOptions = [
    { label: "Name", value: "name" },
    { label: "Code", value: "code" },
    { label: "Phone", value: "phone_number" },
    { label: "Email", value: "email" },
    { label: "Address", value: "address" },
    { label: "Credit Period", value: "credit_period" },
    { label: "Category", value: "customer_category_name" },
];
const clearFilters = () => {
    // reset search dropdown + text
    searchType.value = "";
    searchTerm.value = "";
    // reset date pickers
    startDateFilter.value = null;
    endDateFilter.value = null;
};

const filteredCustomers = computed(() => {
    const term = searchTerm.value.toLowerCase();
    if (!term) return props.customers;

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
            const status = customer.active.toLowerCase();
            return status.includes(term);
        } else if (searchType.value === "customer_category_name") {
            const categoryName = getCategoryNameById(
                customer.customer_category_id
            ).toLowerCase();
            return categoryName.includes(term);
        }
        return false;
    });
});

const getCategoryNameById = (categoryId) => {
    const category = props.customerCategories.find(
        (category) => category.id === categoryId
    );
    return category ? category.category_name_english : "Unknown Category";
};

const editCustomer = (id) => {
    const customer = props.customers.find((cust) => cust.id === id);
    selectedCustomer.value = customer;
    isEditCustomerVisible.value = true;
};

const viewCustomer = (id) => {
    const customer = props.customers.find((cust) => cust.id === id);
    selectedCustomer.value = customer;
    isViewCustomerVisible.value = true;
};

const localCustomers = ref(
    props.customers.map((cust) => ({
        ...cust,
        active: cust.active || "deactivate", // Default to "deactivate" if missing
    }))
);

const indexedCustomers = computed(() => {
    return localCustomers.value.map((customer, index) => ({
        ...customer,
        index: index + 1,
    }));
});

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

const toggleActive = (customer) => {
    const action = customer.active ? "deactivate" : "activate"; // Clarify action message

    // Show confirmation dialog
    confirm.require({
        message: `Are you sure you want to ${action} this customer?`,
        header: "Confirmation",
        icon: "pi pi-exclamation-triangle",
        rejectProps: {
            label: "Cancel",
            icon: "pi pi-times",
            outlined: true,
            size: "small",
        },
        acceptProps: {
            label: "Save",
            icon: "pi pi-check",
            size: "small",
        },
        accept: () => {
            // Call API to toggle the active status
            router.put(
                route("customers.toggleActive", customer.id),
                { active: !customer.active }, // Toggle the active status
                {
                    onSuccess: () => {
                        showToast(
                            "success",
                            "Success",
                            `Customer successfully ${action}d!`
                        );
                        // Update local state
                        localCustomers.value = localCustomers.value.map(
                            (cust) =>
                                cust.id === customer.id
                                    ? { ...cust, active: !cust.active } // Update the status locally
                                    : cust
                        );
                        // Optionally reload the page if you want to refresh data from the server
                        router.reload({ preserveScroll: true });
                    },
                    onError: (error) => {
                        // Display error message
                        showToast(
                            "error",
                            "Error",
                            `There was an error ${action}ing the customer. Please try again later.`
                        );
                        console.error("Error toggling active status:", error);
                    },
                }
            );
        },
        reject: () => {
            // Toast notification on rejection
            showToast("info", "Cancelled", "No changes were made.");
        },
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
