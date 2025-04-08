<template>
    <Head title="Products" />
    <ConfirmDialog />
    <Toast position="top-center" group="tc" />
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
        <!-- <BodyLayout> -->
        <Toast position="top-center" group="tc" />
        <div class="Items text-sm p-4">
            <div class="flex justify-between items-center pb-4">
                <div class="flex items-center gap-2">
                    <img src="/Item.png" alt="Item Icon" class="h-8 w-8 ml-4" />
                    <h1 class="text-xl text-green-600">Manage Items</h1>
                </div>
                <div class="flex items-center gap-2">
                    <Dropdown
                        v-model="searchType"
                        :options="searchOptions"
                        optionLabel="label"
                        optionValue="value"
                        class="w-48 text-sm"
                    />
                    <!-- Search Input -->
                    <InputText
                        v-model="searchTerm"
                        placeholder="Search"
                        class="w-64"
                        size="small"
                    />
                    <Button
                        icon="pi pi-plus"
                        raised
                        label="New Item"
                        size="small"
                        class="custom-button-create"
                        severity="warning"
                        @click="openForm()"
                    />
                </div>
            </div>

            <!-- DataTable to display items -->
            <DataTable
                :value="filteredProducts"
                paginator
                :rows="5"
                :rowsPerPageOptions="[5, 10, 20, 50]"
                striped
            >
                <Column header="Division">
                    <template #body="{ data }">
                        {{
                            getDivisionDisplay(data.division_id) || "Short Text"
                        }}
                    </template>
                </Column>
                <Column header="Category">
                    <template #body="{ data }">
                        {{ getCategoryName(data.category_id) }}
                    </template>
                </Column>
                <Column
                    v-for="col of columns"
                    :key="col.field"
                    :field="col.field"
                    :header="col.header"
                    sortable
                />

                <!-- ✅ Status Button Column -->
                <Column header="Status">
                    <template #body="{ data }">
                        <div class="flex">
                            <Button
                                :icon="
                                    data.status === 'approved'
                                        ? 'pi pi-check'
                                        : data.status === 'rejected'
                                        ? 'pi pi-times'
                                        : 'pi pi-clock'
                                "
                                :label="
                                    data.status === 'approved'
                                        ? 'Approved'
                                        : data.status === 'rejected'
                                        ? 'Rejected'
                                        : 'Pending'
                                "
                                :class="
                                    data.status === 'approved'
                                        ? 'p-button-success'
                                        : data.status === 'rejected'
                                        ? 'p-button-danger'
                                        : 'p-button-warning'
                                "
                                size="small"
                                @click="toggleStatus(data)"
                                class="text-sm flex-grow"
                                outlined
                            />
                            <Button
                                v-if="data.comments && data.comments.length > 0"
                                icon="pi pi-comment"
                                class="p-button-info ml-2"
                                @click="viewComment(data.comments)"
                                outlined
                            />
                        </div>
                    </template>
                </Column>

                <Column header="Actions">
                    <template #body="slotProps">
                        <div class="flex gap-2">
                            <Button
                                class="custom-button"
                                icon="pi pi-eye"
                                severity="info"
                                size="small"
                                @click="viewProduct(slotProps.data)"
                                outlined
                            />
                            <Button
                                class="custom-button"
                                icon="pi pi-pencil"
                                severity="warning"
                                size="small"
                                @click="openForm(slotProps.data)"
                                outlined
                            />
                            <Button
                                class="custom-button"
                                icon="pi pi-trash"
                                severity="danger"
                                size="small"
                                @click="deleteProduct(slotProps.data.id)"
                                outlined
                            />
                        </div>
                    </template>
                </Column>
            </DataTable>

            <!-- View Product Dialog -->
            <Dialog
                v-model:visible="isViewDialogVisible"
                header="Product Details"
                :modal="true"
                class="flex rounded-lg shadow-lg w-auto"
            >
                <template #header>
                    <div class="flex items-center gap-2">
                        <img src="/Item.png" alt="Item Icon" class="h-8 ml-2" />
                        <span class="text-xl font-semibold">Item Details</span>
                    </div>
                </template>

                <div class="grid gap-4 mb-4">
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label
                                for="division"
                                class="block text-sm font-medium"
                                >Division</label
                            >
                            <InputText
                                id="division"
                                :value="
                                    getDivisionDisplay(
                                        selectedProduct?.division_id
                                    )
                                "
                                class="w-full text-sm"
                                disabled
                            />
                        </div>

                        <!-- Category -->
                        <div>
                            <label
                                for="category"
                                class="block text-sm font-medium"
                                >Category</label
                            >
                            <InputText
                                id="category"
                                :value="
                                    getCategoryName(
                                        selectedProduct?.category_id
                                    )
                                "
                                class="w-full text-sm"
                                disabled
                            />
                        </div>

                        <!-- Code -->
                        <div>
                            <label for="code" class="block text-sm font-medium"
                                >Code</label
                            >
                            <InputText
                                id="code"
                                :value="selectedProduct?.code || 'N/A'"
                                class="w-full text-sm"
                                disabled
                            />
                        </div>
                    </div>
                    <hr />
                    <div class="grid gap-4">
                        <div class="flex gap-4">
                            <div class="field w-1/3">
                                <label
                                    for="name"
                                    class="block text-sm font-medium w-1/3"
                                    >Name</label
                                >
                                <InputText
                                    id="name"
                                    :value="selectedProduct?.name || 'N/A'"
                                    class="w-full text-sm"
                                    disabled
                                />
                            </div>

                            <div class="field w-2/3">
                                <label
                                    for="desc"
                                    class="block text-sm font-medium"
                                    >Description</label
                                >
                                <InputText
                                    id="desc"
                                    :value="selectedProduct?.desc || 'N/A'"
                                    class="w-full text-sm"
                                    disabled
                                />
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="field w-1/3">
                                <label
                                    for="name_kh"
                                    class="block text-sm font-medium"
                                    >Name (KH)</label
                                >
                                <InputText
                                    id="name_kh"
                                    :value="selectedProduct?.name_kh || 'N/A'"
                                    class="w-full text-sm"
                                    disabled
                                />
                            </div>

                            <div class="field w-2/3">
                                <label
                                    for="desc_kh"
                                    class="block text-sm font-medium"
                                    >Description (KH)</label
                                >
                                <InputText
                                    id="desc_kh"
                                    :value="selectedProduct?.desc_kh || 'N/A'"
                                    class="w-full text-sm"
                                    disabled
                                />
                            </div>
                        </div>
                        <hr />
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label
                                    for="price"
                                    class="block text-sm font-medium"
                                    >Price in KHR</label
                                >
                                <InputText
                                    id="price"
                                    :value="
                                        selectedProduct?.price
                                            ? `${selectedProduct.price} KHR`
                                            : 'N/A'
                                    "
                                    class="w-full text-sm"
                                    disabled
                                />
                            </div>

                            <!-- Category -->
                            <div>
                                <label
                                    for="unit"
                                    class="block text-sm font-medium"
                                    >Unit</label
                                >
                                <InputText
                                    id="unit"
                                    :value="selectedProduct?.unit || 'N/A'"
                                    class="w-full text-sm"
                                    disabled
                                />
                            </div>

                            <!-- Code -->
                            <div>
                                <label
                                    for="acc_code"
                                    class="block text-sm font-medium"
                                    >Account Code</label
                                >
                                <InputText
                                    id="acc_code"
                                    :value="selectedProduct?.acc_code || 'N/A'"
                                    class="w-full text-sm"
                                    disabled
                                />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium"
                                >Catalog File</label
                            >
                            <div v-if="selectedProduct?.pdf_url">
                                <a
                                    :href="selectedProduct.pdf_url"
                                    target="_blank"
                                    class="text-blue-500 underline"
                                >
                                    View Catelog
                                </a>
                            </div>
                            <div v-else class="text-gray-500">
                                No file uploaded
                            </div>
                        </div>
                    </div>

                    <!-- PDF Catalog -->
                </div>

                <!-- Close Button -->
                <div class="flex justify-end gap-2 mr-4 mb-2">
                    <Button
                        label="Close"
                        class="p-button-secondary"
                        @click="closeViewDialog"
                    />
                </div>
            </Dialog>

            <!-- Product Form Dialog -->
            <Dialog
                v-model:visible="isFormVisible"
                :modal="true"
                class="text-sm bg-color-green-100"
                size="small"
                @hide="closeForm"
            >
                <template #header>
                    <div class="flex items-center gap-2">
                        <img
                            src="/Item.png"
                            alt="Item Icon"
                            class="h-8 w-8 ml-4"
                        />
                        <span class="text-xl font-semibold bor">{{
                            form.id ? "Edit Item" : "Create Item"
                        }}</span>
                    </div>
                </template>

                <!-- Form -->
                <form @submit.prevent="submitForm" class="ml-6 mr-6">
                    <div class="grid gap-4 mb-4">
                        <div class="grid grid-cols-3 gap-4">
                            <!-- Division -->
                            <div class="field">
                                <label for="division" class="required"
                                    >Division</label
                                >
                                <Dropdown
                                    v-model="form.division_id"
                                    :options="divisionOptions"
                                    optionLabel="displayName"
                                    optionValue="id"
                                    placeholder="Select a Division"
                                    :filter="true"
                                    filterPlaceholder="Search divisions..."
                                    class="w-full"
                                />
                                <Message
                                    v-if="form.errors.division_id"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                    class="col-span-2"
                                >
                                    {{ form.errors.division_id }}
                                </Message>
                            </div>

                            <!-- Category -->
                            <div class="field">
                                <label for="category" class="required"
                                    >Category</label
                                >
                                <Select
                                    id="category"
                                    v-model="form.category_id"
                                    :options="categoryOptions"
                                    optionLabel="name"
                                    optionValue="id"
                                    class="w-full"
                                    placeholder="Select Category"
                                />
                                <Message
                                    v-if="form.errors.division_id"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ form.errors.division_id }}
                                </Message>
                            </div>

                            <!-- Code -->
                            <div class="field">
                                <label for="code" class="required" unique
                                    >Code</label
                                >
                                <InputText
                                    id="code"
                                    v-model="form.code"
                                    class="w-full text-sm"
                                    placeholder="Enter Item Code"
                                />
                                <Message
                                    v-if="form.errors.code"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                    class="col-span-2"
                                >
                                    {{ form.errors.code }}
                                </Message>
                            </div>
                        </div>
                    </div>

                    <hr />

                    <div class="grid gap-4 mt-4 mb-4">
                        <div class="flex gap-4">
                            <div class="field w-1/3">
                                <label for="name" class="required">Name</label>
                                <InputText
                                    id="name"
                                    v-model="form.name"
                                    class="w-full text-sm"
                                    placeholder="Enter Item Name"
                                />
                                <Message
                                    v-if="form.errors.name"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                    class="col-span-2"
                                >
                                    {{ form.errors.name }}
                                </Message>
                            </div>

                            <div class="field w-2/3">
                                <label for="desc">Description</label>
                                <InputText
                                    id="desc"
                                    v-model="form.desc"
                                    class="w-full text-sm"
                                    placeholder="Enter Item Description"
                                />
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="field w-1/3">
                                <label for="name_kh" class="required"
                                    >Name (KH)</label
                                >
                                <InputText
                                    id="name_kh"
                                    v-model="form.name_kh"
                                    class="w-full text-sm"
                                    placeholder="Enter Khmer Name"
                                />
                                <Message
                                    v-if="form.errors.name_kh"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                    class="col-span-2"
                                >
                                    {{ form.errors.name_kh }}
                                </Message>
                            </div>

                            <div class="field w-2/3">
                                <label for="desc_kh">Description (KH)</label>
                                <InputText
                                    id="desc_kh"
                                    v-model="form.desc_kh"
                                    class="w-full text-sm"
                                    placeholder="Enter Khmer Description"
                                />
                            </div>
                        </div>
                    </div>
                    <hr />
                    <div class="grid gap-4 mt-4 mb-4 text-sm">
                        <div class="grid grid-cols-3 gap-4">
                            <div class="field">
                                <label for="price" class="required"
                                    >Price in KHR</label
                                >
                                <InputNumber
                                    id="price"
                                    v-model="form.price"
                                    class="w-full text-sm"
                                    size="small"
                                    placeholder="Enter Price in KHR"
                                />
                                <Message
                                    v-if="form.errors.price"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                    class="col-span-2"
                                >
                                    {{ form.errors.price }}
                                </Message>
                            </div>

                            <div class="field">
                                <label for="unit" class="required">Unit</label>
                                <InputText
                                    id="unit"
                                    v-model="form.unit"
                                    class="w-full text-sm"
                                    placeholder="Enter Unit (e.g., pcs, kg)"
                                />
                                <Message
                                    v-if="form.errors.unit"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                    class="col-span-2"
                                >
                                    {{ form.errors.unit }}
                                </Message>
                            </div>

                            <div class="field">
                                <label class="required">Account Code</label>
                                <InputText
                                    v-model="form.acc_code"
                                    class="w-full text-sm"
                                    placeholder="Enter Account Code"
                                />
                                <Message
                                    v-if="form.errors.acc_code"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                    class="col-span-2"
                                >
                                    {{ form.errors.acc_code }}
                                </Message>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-4">
                            <!-- File Upload -->
                            <div class="grid">
                                <label>Upload Catelog:</label>
                                <input
                                    type="file"
                                    accept="application/pdf"
                                    @change="handleFileUpload"
                                    class="border p-2 rounded"
                                />
                                <p
                                    v-if="form.pdf_url"
                                    class="text-xs text-gray-600"
                                >
                                    Current file:
                                    <a
                                        :href="form.pdf_url"
                                        target="_blank"
                                        class="text-blue-500"
                                        >{{ form.pdf_url.split("/").pop() }}</a
                                    >
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 mt-4">
                        <Button
                            label="Cancel"
                            class="p-button-secondary"
                            @click="closeForm"
                        />
                        <Button
                            :label="form.id ? 'Update' : 'Create'"
                            class="p-button-primary"
                            type="submit"
                            :loading="form.processing"
                        />
                    </div>
                </form>
            </Dialog>

            <Dialog
                v-model:visible="isStatusDialogVisible"
                header="Change Product Status"
                :modal="true"
                class="w-80"
            >
                <div class="p-2 pt-0">
                    <p class="text-lg text-center pb-2">
                        Do you want to approve or reject this product?
                    </p>
                    <textarea
                        v-model="commentText"
                        placeholder="Enter your comment..."
                        class="w-full p-2 border rounded"
                    ></textarea>

                    <div class="flex justify-center gap-4 mt-4 text-sm">
                        <Button
                            label="Reject"
                            icon="pi pi-times"
                            class="p-button-danger text-sm"
                            @click="changeStatus('rejected')"
                            size="small"
                            outlined
                        />
                        <Button
                            label="Approve"
                            icon="pi pi-check"
                            class="p-button-success text-sm"
                            @click="changeStatus('approved')"
                            size="small"
                            outlined
                        />
                    </div>
                </div>
            </Dialog>

            <Dialog
                v-model:visible="isCommentDialogVisible"
                header="Comment"
                :modal="true"
                class="w-80"
            >
                <div class="">
                    <p class="text-gray-700 rounded border p-2">
                        {{ selectedComment }}
                    </p>
                    <div class="flex justify-end mt-4">
                        <Button
                            label="Close"
                            class="p-button-secondary"
                            @click="isCommentDialogVisible = false"
                        />
                    </div>
                </div>
            </Dialog>
        </div>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import BodyLayout from "@/Layouts/BodyLayout.vue";
import NavbarLayout from "@/Layouts/NavbarLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { ref, computed, onMounted } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";
import { useConfirm } from "primevue/useconfirm";
import { router } from "@inertiajs/vue3";
import { getDepartment } from "../../data";
import { usePage } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import {
    DataTable,
    Column,
    Button,
    Dialog,
    InputText,
    InputNumber,
    Select,
    Message,
    Dropdown,
    Breadcrumb,
    Toast,
} from "primevue";

const confirm = useConfirm();
const toast = useToast();
const divisionOptions = ref([]);

// The Breadcrumb Quotations
const page = usePage();
const items = computed(() => [
    {
        label: "",
        to: "/",
        icon: "pi pi-home",
    },
    { label: page.props.title || "Items", to: route("products.index") },
]);

const searchOptions = [
    { label: "All Fields", value: "all" },
    { label: "Name (KH)", value: "name_kh" },
    { label: "Division", value: "division.name" },
    { label: "Category", value: "category_id" },
    { label: "Code", value: "code" },
    { label: "Status", value: "status" },
    { label: "Unit", value: "unit" },
    { label: "Price", value: "price" },
];

const filteredProducts = computed(() => {
    const term = searchTerm.value.toLowerCase();
    return products.filter((product) => {
        if (!term) return true; // If search term is empty, show all products

        if (searchType.value === "all") {
            return Object.values(product).some((value) =>
                value?.toString().toLowerCase().includes(term)
            );
        } else if (searchType.value === "division.name") {
            // ✅ Filter by division code and name
            const division = divisionOptions.value.find(
                (d) => d.id === product.division_id
            );
            const divisionText = division
                ? `${division.code} - ${division.name}`.toLowerCase()
                : "";
            return divisionText.includes(term);
        } else if (searchType.value === "category_id") {
            // ✅ Filter by category name
            const categoryName = getCategoryName(
                product.category_id
            )?.toLowerCase();
            return categoryName?.includes(term);
        } else {
            // ✅ Filter by other fields (e.g., code, name_kh, etc.)
            return product[searchType.value]
                ?.toString()
                .toLowerCase()
                .includes(term);
        }
    });
});

onMounted(async () => {
    const response = await getDepartment();
    const data = response.data;

    // Filter departments with status === "service"
    const serviceDepartments = data.filter((dept) => dept.status === "service");

    if (Array.isArray(serviceDepartments) && serviceDepartments.length > 0) {
        divisionOptions.value = serviceDepartments.map((dept) => ({
            name: dept.name,
            id: dept.id,
            code: dept.code, // Assuming `code` exists in API response
            displayName: `${dept.code} - ${dept.name}`, // Add this for Dropdown
        }));
    } else {
        console.warn("No service departments found.");
        divisionOptions.value = [];
    }
});

const reloadData = () => {
    router.visit(window.location.href, {
        preserveScroll: true,
        preserveState: false,
    });
};
const setFormData = (product) => {
    form.id = product.id;
    form.division_id = product.division_id;
    form.category_id = product.category_id;
    form.code = product.code;
    form.name = product.name;
    form.name_kh = product.name_kh;
    form.unit = product.unit;
    form.price = product.price;
    form.desc = product.desc || "";
    form.desc_kh = product.desc_kh || "";
    form.acc_code = product.acc_code || "73048 ផលពីសេវាផ្សេងៗ";
    form.pdf_url = product.pdf_url || "";
    form.remark = product.remark || "";
    form.pdf = null; // Always reset file
};

const showToast = (operation, status) => {
    const toastMessages = {
        create: {
            success: {
                group: "tc",
                severity: "success",
                summary: "Product Created",
                detail: "Product created successfully!",
                life: 5000,
            },
            error: {
                group: "tc",
                severity: "error",
                summary: "Creation Failed",
                detail: "Failed to create product. Please check the form fields!",
                life: 3000,
            },
        },
        update: {
            success: {
                group: "tc",
                severity: "success",
                summary: "Product Updated",
                detail: "Product updated successfully!",
                life: 3000,
            },
            error: {
                group: "tc",
                severity: "error",
                summary: "Update Failed",
                detail: "Failed to update product. Please try again!",
                life: 3000,
            },
        },
        delete: {
            success: {
                group: "tc",
                severity: "success",
                summary: "Product Deleted",
                detail: "Product deleted successfully!",
                life: 3000,
            },
            error: {
                group: "tc",
                severity: "error",
                summary: "Deletion Failed",
                detail: "Failed to delete product!",
                life: 3000,
            },
        },
        // New cancel action
        cancel: {
            info: {
                group: "tc",
                severity: "info",
                summary: "Action Cancelled",
                detail: "Operation was cancelled.",
                life: 3000,
            },
        },
    };
    const options = toastMessages[operation][status];
    toast.add(options);
};

// Close the form dialog and notify the user
const closeForm = () => {
    // showToast("cancel", "info");
    isFormVisible.value = false;
    form.errors = {};
    form.reset();
};

// Close the view dialog and notify the user
const closeViewDialog = () => {
    showToast("cancel", "info");
    isViewDialogVisible.value = false;
};

// ✅ Use usePage().props to get data
const { products, categories } = usePage().props;

// ✅ Computed properties to map dropdown options
const categoryOptions = computed(
    () =>
        categories?.map((category) => ({
            name:
                category.category_name_english || category.category_name_khmer,
            id: category.id,
        })) || []
);

const getCategoryName = (categoryId) => {
    const category = categories.find((cat) => cat.id === categoryId);
    return category
        ? category.category_name_english || category.category_name_khmer
        : "Unknown";
};

const getDivisionDisplay = (divisionId) => {
    const division = divisionOptions.value.find((div) => div.id === divisionId);
    return division ? `${division.code} - ${division.name}` : "Unknown";
};

// Define columns for DataTable
const columns = [
    { field: "code", header: "Item Code" },
    { field: "name_kh", header: "Name (KH)" },
    { field: "unit", header: "Unit" },
    { field: "price", header: "Price" },
];

// State for form and view dialogs
const isFormVisible = ref(false);
const isViewDialogVisible = ref(false);
const selectedProduct = ref(null);
const selectedComment = ref("");
const commentText = ref("");
const isCommentDialogVisible = ref(false);

const selectedProductForStatus = ref(null); // ✅ Define this variable
const isStatusDialogVisible = ref(false);

const viewComment = (comments) => {
    selectedComment.value = comments.map((c) => c.comment).join("\n"); // Combine all comments into a single string
    isCommentDialogVisible.value = true;
};

const form = useForm({
    id: null,
    division_id: "",
    code: "",
    acc_code: "73048 ផលពីសេវាផ្សេងៗ",
    name: "",
    name_kh: "",
    desc: null,
    desc_kh: null,
    unit: "",
    price: null,
    category_id: "",
    pdf_url: "",
    remark: "",
    pdf: null, // Add this line to handle file uploads
});

// Open product form
const openForm = (product = null) => {
    form.reset();
    form.clearErrors();

    if (product) {
        setFormData(product);
    } else {
        form.acc_code = "73048 ផលពីសេវាផ្សេងៗ"; // Default for new
    }

    isFormVisible.value = true;
};

const isEditMode = ref(false);

isEditMode.value = true;

const handleFileUpload = (event) => {
    form.pdf = event.target.files[0];
};

const viewProduct = (product) => {
    selectedProduct.value = { ...product };

    // ✅ Always use the correct new PDF URL
    selectedProduct.value.pdf_url = product.pdf_url || null;

    isViewDialogVisible.value = true;
};

const changeStatus = (newStatus) => {
    if (!selectedProductForStatus.value) return;
    if (!commentText.value.trim()) {
        toast.add({
            group: "tc",
            severity: "error",
            summary: "Comment Required",
            detail: "Please enter a comment before proceeding.",
            life: 3000,
        });
        return;
    }

    router.put(
        route("products.toggleStatus", {
            product: selectedProductForStatus.value.id,
        }),
        { status: newStatus, comment: commentText.value.trim() },
        {
            onSuccess: () => {
                toast.add({
                    group: "tc",
                    severity: "success",
                    summary: "Status Updated",
                    detail: `Product marked as ${newStatus.toUpperCase()} with comment.`,
                    life: 3000,
                });
                // router.reload();
                selectedProductForStatus.value.status = newStatus;
                isStatusDialogVisible.value = false;
                reloadData();
            },
            onError: () => {
                toast.add({
                    group: "tc",
                    severity: "error",
                    summary: "Update Failed",
                    detail: "Could not update product status!",
                    life: 3000,
                });
            },
        }
    );
};

const toggleStatus = (product) => {
    selectedProductForStatus.value = product;
    isStatusDialogVisible.value = true;
};

const searchTerm = ref(""); // The search term input
const searchType = ref("name_kh"); // Default search type is 'name'

const submitForm = () => {
    console.log("Form data being submitted:", form.data());
    if (
        !form.division_id ||
        !form.category_id ||
        !form.code ||
        !form.name ||
        !form.name_kh
    ) {
        toast.add({
            group: "tc",
            severity: "error",
            summary: "Validation Error",
            detail: "Please fill in all required fields!",
            life: 4000,
        });
        return;
    }

    const formData = new FormData();
    const rawData = form.data();

    Object.entries(rawData).forEach(([key, value]) => {
        if (value !== null && value !== undefined && key !== "pdf") {
            formData.append(key, value);
        }
    });

    if (form.pdf instanceof File) {
        formData.append("pdf", form.pdf);
    }
    if (form.id) {
        form.transform((data) => ({
            ...data,
            _method: "PUT",
        })).post(route("products.update", form.id), {
            onSuccess: () => {
                setTimeout(() => {
                    showToast("update", "success");
                    isFormVisible.value = false;
                }, 100);
                reloadData();
            },
            onError: (errors) => {
                setTimeout(() => showToast("update", "error"), 100);
                console.error("Update errors:", errors);
            },
        });
    } else {
        form.post(route("products.store"), {
            forceFormData: true,
            onSuccess: () => {
                console.log("Create success");
                setTimeout(() => {
                    showToast("create", "success");
                    isFormVisible.value = false;
                }, 100);
                reloadData();
            },
            onError: (errors) => {
                console.error("Validation Errors:", errors);
                console.error("Creation errors:", errors);
                setTimeout(() => showToast("create", "error"), 100);
            },
        });
    }
};

// Delete product
const deleteProduct = (id) => {
    confirm.require({
        message: "Are you sure you want to delete this product?",
        header: "Delete Confirmation",
        icon: "pi pi-exclamation-triangle",
        accept: () => {
            router.delete(route("products.destroy", id), {
                onSuccess: () => {
                    setTimeout(() => {
                        toast.add({
                            group: "tc",
                            severity: "success",
                            summary: "Deleted",
                            detail: "Product deleted successfully!",
                            life: 3000,
                        });
                    }, 50);
                    reloadData();
                },
                onError: () => {
                    setTimeout(() => {
                        toast.add({
                            group: "tc",
                            severity: "error",
                            summary: "Error",
                            detail: "Failed to delete product!",
                            life: 3000,
                        });
                    }, 50);
                },
            });
        },
        reject: () => {
            setTimeout(() => {
                toast.add({
                    group: "tc",
                    severity: "info",
                    summary: "Cancelled",
                    detail: "Product deletion cancelled.",
                    life: 3000,
                });
            }, 50);
        },
    });
};
</script>
