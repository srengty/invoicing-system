<template>
    <Head title="Create Agreement" />
    <GuestLayout>
        <NavbarLayout />
        <!-- PrimeVue Breadcrumb -->
        <div class="py-3">
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
                    <!-- Customer Categories -->
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
                        icon="pi pi-plus"
                        label="New Customer Categories"
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
                        style="width: 15%; font-size: 12px"
                    ></Column>
                    <Column
                        field="category_name_english"
                        header="Name En"
                        style="width: 15%; font-size: 12px"
                    ></Column>
                    <Column
                        field="description_khmer"
                        header="Description"
                        style="width: 15%; font-size: 12px"
                    ></Column>
                    <Column
                        field="description_english"
                        header="Description En"
                        style="width: 15%; font-size: 12px"
                    ></Column>
                    <Column
                        field="created_at"
                        header="Created At"
                        style="width: 15%; font-size: 12px"
                    >
                        <template #body="slotProps">
                            {{
                                new Date(
                                    slotProps.data["created_at"]
                                ).toLocaleDateString()
                            }}
                        </template>
                    </Column>
                    <Column
                        field="action"
                        header="Action"
                        style="width: 15%; font-size: 12px"
                    >
                        <template #body="slotProps">
                            <!-- <Button severity="warn" size="small" @click="router.get(route('categories.edit',{'id':slotProps.data.id}))">Edit</Button> -->
                            <Button
                                class="custom-button"
                                icon="pi pi-pencil"
                                severity="warning"
                                size="small"
                                style="width: 30px; height: 30px"
                                @click="
                                    router.get(
                                        route('categories.edit', {
                                            id: slotProps.data.id,
                                        })
                                    )
                                "
                                outlined
                            />
                        </template>
                    </Column>
                </DataTable>
            </div>
            <Dialog
                v-model="displayModal"
                header="Add New Customer Category"
                :visible="displayModal"
                @update:visible="displayModal = $event"
                modal
            >
                <form @submit="handleSubmit">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label for="name" class="block text-lg font-medium"
                                >Name</label
                            >
                            <InputText
                                id="name"
                                v-model="form.name"
                                class="w-full"
                                placeholder="Enter Customer Category Name"
                            />
                        </div>
                        <div>
                            <label
                                for="description"
                                class="block text-lg font-medium"
                                >Description</label
                            >
                            <InputText
                                id="description"
                                v-model="form.description"
                                class="w-full"
                                placeholder="Enter description"
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
            <Dialog
                v-model="displayEditModal"
                header="Edit Customer Category"
                :visible="displayEditModal"
                @update:visible="displayEditModal = $event"
                modal
            >
                <form @submit="handleEditSubmit">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label for="name" class="block text-lg font-medium"
                                >Name</label
                            >
                            <InputText
                                id="name"
                                v-model="form.name"
                                class="w-full"
                                placeholder="Enter Customer Category Name"
                            />
                        </div>
                        <div>
                            <label
                                for="description"
                                class="block text-lg font-medium"
                                >Description</label
                            >
                            <InputText
                                id="description"
                                v-model="form.description"
                                class="w-full"
                                placeholder="Enter description"
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
        </div>
        <!-- </BodyLayout> -->
    </GuestLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import { useToast } from "primevue/usetoast";
import {
    Button,
    Dialog,
    DataTable,
    Column,
    InputText,
    Toast,
    Dropdown,
} from "primevue";
import { Head, Link, router } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import BodyLayout from "@/Layouts/BodyLayout.vue";
import NavbarLayout from "@/Layouts/NavbarLayout.vue";
import Breadcrumb from "primevue/breadcrumb";
import { usePage } from "@inertiajs/vue3";

// The Breadcrumb Quotations
const page = usePage();
const items = computed(() => [
    {
        label: "",
        to: "/dashboard",
        icon: "pi pi-home",
    },
    {
        label: page.props.title || "Customer Categories",
        to: route("customerCategory.index"),
    },
]);
const toast = useToast();
const { customerCategories } = usePage().props;
const displayModal = ref(false);
const displayEditModal = ref(false);
const form = ref({
    name: "",
    description: "",
});
const openModal = () => {
    displayModal.value = true;
};
const hideModal = () => {
    displayModal.value = false;
};
const hideEditModal = () => {
    displayEditModal.value = false;
};
const searchType = ref("");
const searchTerm = ref("");
const searchOptions = [
    { label: "Name (Khmer)", value: "category_name_khmer" },
    { label: "Name (English)", value: "category_name_english" },
    { label: "Description (Khmer)", value: "description_khmer" },
    { label: "Description (English)", value: "description_english" },
];
const clearFilters = () => {
    searchType.value = "";
    searchTerm.value = "";
};
const filteredCategories = computed(() => {
    if (!searchType.value || !searchTerm.value.trim()) {
        return customerCategories || [];
    }

    const term = searchTerm.value.toLowerCase();
    return (customerCategories || []).filter((cat) => {
        const fieldValue = String(cat[searchType.value] ?? "").toLowerCase();
        return fieldValue.includes(term);
    });
});
</script>

<style lang="scss" scoped></style>
