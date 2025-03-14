<template>
    <Head title="Customers/Organization Name" />
    <ConfirmDialog />
    <Toast position="top-center" group="tc" />
    <GuestLayout>
        <BodyLayout>
            <div class="customers">
                <div class="flex justify-between items-center pb-4">
                    <div class="flex items-center gap-2">
                        <img src="/User.png" alt="Item Icon" class="h-8 w-8" />
                        <h1 class="text-xl text-green-600">
                            Customers/Organization Name
                        </h1>
                    </div>
                    <div class="flex items-center gap-2">
                        <InputText
                            v-model="searchTerm"
                            placeholder="Search"
                            class="w-64"
                            size="small"
                        />

                        <Button
                            v-model="searchType"
                            :class="{
                                'p-button-primary': searchType === 'name',
                                'p-button-outlined': searchType !== 'name',
                            }"
                            label="Search by Name"
                            @click="searchType = 'name'"
                            size="small"
                        />
                        <Button
                            v-model="searchType"
                            :class="{
                                'p-button-primary': searchType === 'code',
                                'p-button-outlined': searchType !== 'code',
                            }"
                            label="Search by Code"
                            @click="searchType = 'code'"
                            size="small"
                        />

                        <!-- Search Input -->

                        <Button
                            icon="pi pi-plus"
                            label="New Customer"
                            @click="isCreateCustomerVisible = true"
                            size="small"
                        />

                        <!-- <ChooseColumns
                            :columns="columns"
                            v-model="selectedColumns"
                            @apply="updateColumns"
                            outlined
                            size="small"
                        /> -->
                    </div>
                </div>
                <!-- <div class="flex justify-end items-center pb-4">
                    <InputText
                        v-model="searchTerm"
                        placeholder="Search by Name or Code"
                        class="w-1/4"
                    />
                </div> -->
                <DataTable
                    :value="filteredCustomers"
                    paginator
                    :rows="5"
                    :rowsPerPageOptions="[5, 10, 20, 50]"
                    class="text-sm"
                >
                    <Column
                        field="customer_category_name"
                        header="Category"
                        style="width: 5%"
                    ></Column>

                    <!-- Loop through other columns -->
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
                                <Button
                                    icon="pi pi-eye"
                                    class="p-button-info"
                                    aria-label="View"
                                    size="small"
                                    @click="viewCustomer(slotProps.data.id)"
                                    outlined
                                />
                                <Button
                                    icon="pi pi-pencil"
                                    class="p-button-warning"
                                    aria-label="Edit"
                                    size="small"
                                    @click="editCustomer(slotProps.data.id)"
                                    outlined
                                />
                                <Button
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
                                        'p-button-success':
                                            slotProps.data.active,
                                        'p-button-danger':
                                            !slotProps.data.active,
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
import { DataTable, Column, Button, Dialog, InputText } from "primevue";
import { Inertia } from "@inertiajs/inertia";
import ChooseColumns from "@/Components/ChooseColumns.vue";
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
const searchType = ref("name");

const props = defineProps({
    customers: Array,
    customerCategories: Array,
});

const columns = [
    // { field: "id", header: "ID", style: { width: "5%" } },
    { field: "name", header: "Name", style: { width: "5%" } },
    { field: "code", header: "Code", style: { width: "5%" } },
    {
        field: "credit_period",
        header: "Credit Period",
        style: { width: "5%" },
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

const updateColumns = () => {
    showColumns.value = selectedColumns.value;
};

const filteredCustomers = computed(() => {
    const filtered = props.customers
        .map((cust) => {
            const categoryName = getCategoryNameById(cust.customer_category_id); // Fetch category name
            const updatedCustomer = {
                ...cust,
                customer_category_name: categoryName, // Ensure category name is added here
            };
            return updatedCustomer;
        })
        .filter((cust) => {
            const term = searchTerm.value.toLowerCase();
            if (searchType.value === "name") {
                return cust.name.toLowerCase().includes(term);
            } else if (searchType.value === "code") {
                return cust.code.toLowerCase().includes(term);
            }
            return false;
        });

    return filtered;
});

const getCategoryNameById = (categoryId) => {
    const category = props.customerCategories.find(
        (category) => category.id === categoryId
    );
    console.log(category); // This will show the correct category object with `category_name_english`
    return category ? category.category_name_english : "Unknown Category"; // Make sure to use category_name_english here
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
        active: typeof cust.active !== "undefined" ? cust.active : true, // default true if missing
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
