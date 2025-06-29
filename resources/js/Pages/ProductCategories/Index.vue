<template>
    <Head title="Create Agreement" />
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
        <Toast />
        <!-- <BodyLayout> -->
        <div class="px-4">
            <div class="flex justify-between items-center ml-4">
                <h1 class="text-xl font-semibold text-gray-800">
                    <!-- Products Categories -->
                </h1>
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
                        v-if="userPermissions.canCreateItemCategory"
                        icon="pi pi-plus"
                        label="New Item Categories"
                        @click="openModal"
                        size="small"
                    />
                </div>
            </div>
            <div class="mt-4 text-sm">
                <DataTable
                    :value="filteredCategories"
                    :paginator="true"
                    :rows="10"
                    :rowsPerPageOptions="[5, 10, 20]"
                    :showGridlines="true"
                >
                    <Column
                        field="category_name_khmer"
                        header="Name"
                        style="width: 10%; font-size: 12px"
                    ></Column>
                    <Column
                        field="category_name_english"
                        header="Name En"
                        style="width: 10%; font-size: 12px"
                    ></Column>
                    <Column
                        field="description_khmer"
                        header="Description"
                        style="width: 10%; font-size: 12px"
                    ></Column>
                    <Column
                        field="description_english"
                        header="Description En"
                        style="width: 10%; font-size: 12px"
                    ></Column>
                    <Column
                        field="created_at"
                        header="Created At"
                        style="width: 10%; font-size: 12px"
                    >
                        <template #body="slotProps">
                            {{
                                new Date(
                                    slotProps.data["created_at"]
                                ).toLocaleDateString()
                            }}
                        </template>
                    </Column>
                    <Column header="Actions" style="width: 5%; font-size: 12px">
                        <template #body="slotProps">
                            <div class="flex gap-2">
                                <Button
                                    icon="pi pi-eye"
                                    size="small"
                                    severity="info"
                                    style="width: 30px; height: 30px"
                                    @click="openDetailModal(slotProps.data)"
                                    outlined
                                />
                                <Button
                                    v-if="userPermissions.canAction"
                                    icon="pi pi-pencil"
                                    size="small"
                                    style="width: 30px; height: 30px"
                                    @click="openEditModal(slotProps.data)"
                                    outlined
                                />
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>

        <Dialog
            v-model:visible="displayDetailModal"
            header="Category Details"
            modal
        >
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <strong>Name:</strong>
                    {{ selectedCategory.category_name_khmer }}
                </div>
                <div>
                    <strong>Name En:</strong>
                    {{ selectedCategory.category_name_english }}
                </div>
                <div>
                    <strong>Description:</strong>
                    {{ selectedCategory.description_khmer }}
                </div>
                <div>
                    <strong>Description En:</strong>
                    {{ selectedCategory.description_english }}
                </div>
                <div>
                    <strong>Created At:</strong>
                    {{
                        new Date(
                            selectedCategory.created_at
                        ).toLocaleDateString()
                    }}
                </div>
            </div>
        </Dialog>

        <Dialog
            v-model:visible="displayEditModal"
            header="Edit Customer Category"
            modal
        >
            <form @submit.prevent="handleEditSubmit">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label for="edit-name" class="block text-lg font-medium"
                            >Name</label
                        >
                        <InputText
                            id="edit-name"
                            v-model="form.category_name_khmer"
                            class="w-full"
                            placeholder="Enter Customer Category Name"
                        />
                    </div>
                    <div>
                        <label
                            for="edit-name-en"
                            class="block text-lg font-medium"
                            >Name En</label
                        >
                        <InputText
                            id="edit-name-en"
                            v-model="form.category_name_english"
                            class="w-full"
                            placeholder="Enter Customer Category Name in English"
                        />
                    </div>
                    <div>
                        <label
                            for="edit-description"
                            class="block text-lg font-medium"
                            >Description</label
                        >
                        <InputText
                            id="edit-description"
                            v-model="form.description_khmer"
                            class="w-full"
                            placeholder="Enter description"
                        />
                    </div>
                    <div>
                        <label
                            for="edit-description-en"
                            class="block text-lg font-medium"
                            >Description En</label
                        >
                        <InputText
                            id="edit-description-en"
                            v-model="form.description_english"
                            class="w-full"
                            placeholder="Enter description in English"
                        />
                    </div>
                    <div>
                        <Button type="submit" class="p-button-success"
                            >Save</Button
                        >
                    </div>
                </div>
            </form>
        </Dialog>
        <!-- </BodyLayout> -->
    </GuestLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import {
    DataTable,
    Column,
    Dialog,
    Button,
    InputText,
    Toast,
    Dropdown,
} from "primevue";
import { Head, Link } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import BodyLayout from "@/Layouts/BodyLayout.vue";
import NavbarLayout from "@/Layouts/NavbarLayout.vue";
import Breadcrumb from "primevue/breadcrumb";
import { usePage } from "@inertiajs/vue3";

// The Breadcrumb Quotations
const page = usePage();
const userPermissions = computed(() => {
    const roles = page.props.userRoles || [];
    return {
        canCreateItemsCategory: roles.some((role) =>
            role.toLowerCase().includes("division staff")
        ),
        canAction: roles.some(
            (role) =>
                role.toLowerCase().includes("division staff") ||
                role.toLowerCase().includes("chef department")
        ),
    };
});
const items = computed(() => [
    {
        label: "",
        to: "/dashboard",
        icon: "pi pi-home",
    },
    {
        label: page.props.title || "Product Categories",
        to: route("customerCategory.index"),
    },
]);
const productCategories = computed(
    () => usePage().props.productCategories || []
);
const displayDetailModal = ref(false);
const displayEditModal = ref(false);
const selectedCategory = ref({});
const form = ref({
    category_name_khmer: "",
    category_name_english: "",
    description_khmer: "",
    description_english: "",
    id: null,
});
const searchType = ref("");
const searchTerm = ref("");
const searchOptions = [
    { label: "Name", value: "category_name_khmer" },
    { label: "Name En", value: "category_name_english" },
    { label: "Description", value: "description_khmer" },
    { label: "Description En", value: "description_english" },
];
const clearFilters = () => {
    searchType.value = "";
    searchTerm.value = "";
};
const filteredCategories = computed(() => {
    // If no searchType or no searchTerm, return everything
    if (!searchType.value || !searchTerm.value.trim()) {
        return productCategories.value;
    }

    const term = searchTerm.value.toString().toLowerCase();
    return productCategories.value.filter((cat) => {
        // Safely grab the field to search; convert to string + lowercase
        const fieldValue = String(cat[searchType.value] ?? "").toLowerCase();
        return fieldValue.includes(term);
    });
});

const openDetailModal = (category) => {
    selectedCategory.value = category;
    displayDetailModal.value = true;
};

const openEditModal = (category) => {
    form.value = { ...category };
    displayEditModal.value = true;
};

const handleEditSubmit = () => {
    // Handle edit submission logic
    Toast.add({
        severity: "success",
        summary: "Success",
        detail: "Category updated",
        life: 3000,
    });
    displayEditModal.value = false;
};
</script>

<style lang="scss" scoped></style>
