<template>
    <Head title="Products" />
    <ConfirmDialog />
    <Toast position="top-center" group="tc" />
    <GuestLayout>
        <BodyLayout>
            <Toast position="top-center" group="tc" />
            <div class="Items text-sm">
                <div class="flex justify-between items-center pb-4">
                    <div class="flex items-center gap-2">
                        <img
                            src="/Item.png"
                            alt="Item Icon"
                            class="h-8 w-8 ml-4"
                        />
                        <h1 class="text-xl text-green-600">Manage Items</h1>
                    </div>

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

                <!-- DataTable to display items -->
                <DataTable
                    :value="products"
                    paginator
                    :rows="5"
                    :rowsPerPageOptions="[5, 10, 20, 50]"
                    striped
                >
                    <Column
                        v-for="col of columns"
                        :key="col.field"
                        :field="col.field"
                        :header="col.header"
                        sortable
                    />
                    <Column header="Category">
                        <template #body="{ data }">
                            {{ getCategoryName(data.category_id) }}
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
                    class="w-96 rounded-lg shadow-lg"
                >
                    <template #header>
                        <div class="flex items-center gap-2">
                            <img
                                src="/Item.png"
                                alt="Item Icon"
                                class="h-8 w-8 ml-2"
                            />
                            <span class="text-xl font-semibold bor"
                                >Item Details</span
                            >
                        </div>
                    </template>
                    <div
                        v-if="selectedProduct"
                        class="space-y-3 text-gray-700 pl-2 pr-2"
                    >
                        <div class="grid grid-cols-2 gap-x-4 gap-y-2">
                            <p><strong>Division:</strong></p>
                            <p
                                class="text-right"
                                :class="{
                                    'text-red-500':
                                        !selectedProduct?.division_id,
                                }"
                            >
                                {{
                                    getDivisionName(
                                        selectedProduct?.division_id
                                    ) ?? "Null"
                                }}
                            </p>
                            <p><strong>Category:</strong></p>
                            <p
                                class="text-right"
                                :class="{
                                    'text-red-500':
                                        !selectedProduct?.category_id,
                                }"
                            >
                                {{
                                    getCategoryName(
                                        selectedProduct?.category_id
                                    ) ?? "Null"
                                }}
                            </p>

                            <p><strong>Code:</strong></p>
                            <p
                                class="text-right"
                                :class="{
                                    'text-red-500': !selectedProduct?.code,
                                }"
                            >
                                {{ selectedProduct?.code ?? "Null" }}
                            </p>

                            <p><strong>Name:</strong></p>
                            <p
                                class="text-right"
                                :class="{
                                    'text-red-500': !selectedProduct?.name,
                                }"
                            >
                                {{ selectedProduct?.name ?? "Null" }}
                            </p>

                            <p><strong>Name (KH):</strong></p>
                            <p
                                class="text-right"
                                :class="{
                                    'text-red-500': !selectedProduct?.name_kh,
                                }"
                            >
                                {{ selectedProduct?.name_kh ?? "Null" }}
                            </p>

                            <p><strong>Description:</strong></p>
                            <p
                                class="text-right"
                                :class="{
                                    'text-red-500': !selectedProduct?.desc,
                                }"
                            >
                                {{ selectedProduct?.desc ?? "Null" }}
                            </p>

                            <p><strong>Description (KH):</strong></p>
                            <p
                                class="text-right"
                                :class="{
                                    'text-red-500': !selectedProduct?.desc_kh,
                                }"
                            >
                                {{ selectedProduct?.desc_kh ?? "Null" }}
                            </p>

                            <p><strong>Unit:</strong></p>
                            <p
                                class="text-right"
                                :class="{
                                    'text-red-500': !selectedProduct?.unit,
                                }"
                            >
                                {{ selectedProduct?.unit ?? "Null" }}
                            </p>

                            <p><strong>Quantity:</strong></p>
                            <p
                                class="text-right font-semibold"
                                :class="{
                                    'text-red-500': !selectedProduct?.quantity,
                                }"
                            >
                                {{ selectedProduct?.quantity ?? "Null" }}
                            </p>

                            <p><strong>Price in KHR:</strong></p>
                            <p
                                class="text-right font-semibold text-green-600"
                                :class="{
                                    'text-red-500': !selectedProduct?.price,
                                }"
                            >
                                {{ selectedProduct?.price ?? "Null" }}
                            </p>

                            <p><strong>Remark:</strong></p>
                            <p
                                class="text-right font-semibold"
                                :class="{
                                    'text-red-500': !selectedProduct?.remark,
                                }"
                            >
                                {{ selectedProduct?.remark ?? "Null" }}
                            </p>

                            <p><strong>Account code:</strong></p>
                            <p
                                class="text-right font-semibold"
                                :class="{
                                    'text-red-500': !selectedProduct?.acc_code,
                                }"
                            >
                                {{ selectedProduct?.acc_code ?? "Null" }}
                            </p>
                        </div>

                        <div v-if="selectedProduct.pdf_url" class="text-center">
                            <a
                                :href="`/pdfs/${selectedProduct.pdf_url
                                    .split('/')
                                    .pop()}`"
                                target="_blank"
                                class="text-blue-500 hover:text-blue-700 transition duration-200"
                            >
                                📄 View PDF
                            </a>
                        </div>
                        <p v-else class="text-center text-gray-400">
                            No PDF available
                        </p>
                    </div>

                    <div class="flex justify-end mt-2">
                        <Button
                            label="Close"
                            class="p-button-secondary px-4 py-2 rounded-lg text-sm hover:bg-gray-200 transition"
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
                                <label for="division" class="required">Division</label>
                                <Select
                                    id="division"
                                    v-model="form.division_id"
                                    :options="divisionOptions"
                                    optionLabel="name"
                                    optionValue="id"
                                    class="w-full"
                                    required
                                    placeholder="Select Division"
                                />
                                <Message v-if="form.errors.division_id" severity="error" size="small" variant="simple"
                                class="col-span-2">
                                    {{ form.errors.division_id }}
                                </Message>
                            </div>

                            <!-- Category -->
                            <div class="field">
                                <label for="category" class="required">Category</label>
                                <Select
                                    id="category"
                                    v-model="form.category_id"
                                    :options="categoryOptions"
                                    optionLabel="name"
                                    optionValue="id"
                                    class="w-full"
                                    placeholder="Select Category"
                                />
                                <Message v-if="form.errors.category_id" severity="error" size="small" variant="simple"
                                class="col-span-2">
                                    {{ form.errors.category_id }}
                                </Message>
                            </div>

                            <!-- Code -->
                            <div class="field">
                                <label for="code" class="required">Code</label>
                                <InputText
                                    id="code"
                                    v-model="form.code"
                                    class="w-full text-sm"
                                    placeholder="Enter Item Code"
                                />
                                <Message v-if="form.errors.code" severity="error" size="small" variant="simple"
                                class="col-span-2">
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
                                <Message v-if="form.errors.name" severity="error" size="small" variant="simple"
                                class="col-span-2">
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
                                <label for="name_kh" class="required">Name (KH)</label>
                                <InputText
                                    id="name_kh"
                                    v-model="form.name_kh"
                                    class="w-full text-sm"
                                    placeholder="Enter Khmer Name"
                                />
                                <Message v-if="form.errors.name_kh" severity="error" size="small" variant="simple"
                                class="col-span-2">
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
                                <label for="quantity" class="required">Quantity</label>
                                <InputNumber
                                    id="quantity"
                                    v-model="form.quantity"
                                    class="w-full text-sm"
                                    size="small"
                                    placeholder="Enter Quantity"
                                />
                                <Message v-if="form.errors.quantity" severity="error" size="small" variant="simple"
                                class="col-span-2">
                                    {{ form.errors.quantity }}
                                </Message>
                            </div>

                            <div class="field">
                                <label for="price" class="required">Price in KHR</label>
                                <InputNumber
                                    id="price"
                                    v-model="form.price"
                                    class="w-full text-sm"
                                    size="small"
                                    placeholder="Enter Price in KHR"
                                />
                                <Message v-if="form.errors.price" severity="error" size="small" variant="simple"
                                class="col-span-2">
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
                                <Message v-if="form.errors.unit" severity="error" size="small" variant="simple"
                                class="col-span-2">
                                    {{ form.errors.unit }}
                                </Message>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="field">
                                <label class="required">Remark</label>
                                <InputText
                                    v-model="form.remark"
                                    class="w-full text-sm"
                                    placeholder="Enter Remarks"
                                />
                                <Message v-if="form.errors.remark" severity="error" size="small" variant="simple"
                                class="col-span-2">
                                    {{ form.errors.remark }}
                                </Message>
                            </div>

                            <div class="field">
                                <label class="required">Account Code</label>
                                <InputText
                                    v-model="form.acc_code"
                                    class="w-full text-sm"
                                    placeholder="Enter Account Code"
                                />
                                <Message v-if="form.errors.acc_code" severity="error" size="small" variant="simple"
                                class="col-span-2">
                                    {{ form.errors.acc_code }}
                                </Message>
                            </div>

                            <!-- File Upload -->
                            <div class="grid">
                                <label>Upload PDF:</label>
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
                                    >{{
                                        form.pdf_url.split("/").pop()
                                    }}</a>
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
            </div>
        </BodyLayout>
    </GuestLayout>
</template>

<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import BodyLayout from "@/Layouts/BodyLayout.vue";
import {
    DataTable,
    Column,
    Button,
    Dialog,
    InputText,
    InputNumber,
    Select,
} from "primevue";
import Message from "primevue/message";
import { ref, computed, onMounted, onUnmounted, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { useToast } from "primevue/usetoast";
import Toast from "primevue/toast";
import { useConfirm } from "primevue/useconfirm";
import { router } from "@inertiajs/vue3";

const confirm = useConfirm();
const toast = useToast();

const reloadData = () => {
    router.visit(window.location.href, {
        preserveScroll: true,
        preserveState: false,
    });
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
    showToast("cancel", "info");
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
const { products, divisions, categories, errors } = usePage().props;

// ✅ Computed properties to map dropdown options
const categoryOptions = computed(
    () =>
        categories?.map((category) => ({
            name:
                category.category_name_english || category.category_name_khmer,
            id: category.id,
        })) || []
);

const divisionOptions = computed(
    () =>
        divisions?.map((division) => ({
            name: division.division_name_english || division.divison_name_khmer,
            id: division.id,
        })) || []
);

const getCategoryName = (categoryId) => {
    const category = categories.find((cat) => cat.id === categoryId);
    return category
        ? category.category_name_english || category.category_name_khmer
        : "Unknown";
};

const getDivisionName = (divisionId) => {
    const division = divisions.find((cat) => cat.id === divisionId);
    return division
        ? division.division_name_english || division.divison_name_khmer
        : "Unknown";
};

// Define columns for DataTable
const columns = [
    { field: "id", header: "ID" },
    { field: "code", header: "Code" },
    { field: "name", header: "Name" },
    { field: "name_kh", header: "Name (KH)" },
    { field: "unit", header: "Unit" },
    { field: "price", header: "Price" },
    { field: "quantity", header: "Quantity" },
];

// State for form and view dialogs
const isFormVisible = ref(false);
const isViewDialogVisible = ref(false);
const selectedProduct = ref(null);

// watch(isFormVisible,(value)=>{
//     if(value==false){
//         closeForm();
//     }
// })
// Create form using Inertia's `useForm`
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
    quantity: null,
    category_id: "",
    pdf_url: "",
    remark: "",
    pdf: null, // Add this line to handle file uploads
});


// Open product form
const openForm = (product = null) => {
    if (product) {
        form.id = product.id;
        form.code = product.code;
        form.name = product.name;
        form.name_kh = product.name_kh;
        form.unit = product.unit;
        form.price = product.price;
        form.quantity = product.quantity;
        form.category_id = product.category_id;
        form.division_id = Number(product.division_id);
        form.desc = product.desc || ""; // ✅ Ensure description is populated
        form.desc_kh = product.desc_kh || "";
        form.remark = product.remark || "";
        form.pdf_url = product.pdf_url || null; // ✅ Ensure PDF URL is correctly set
        form.pdf = null; // Reset file upload
    } else {
        form.reset();
    }
    isFormVisible.value = true;
};

const handleFileUpload = (event) => {
    form.pdf = event.target.files[0];
};

const viewProduct = (product) => {
    selectedProduct.value = { ...product };

    // ✅ Always use the correct new PDF URL
    selectedProduct.value.pdf_url = product.pdf_url || null;

    isViewDialogVisible.value = true;
};

const submitForm = () => {
    if (!form) {
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
    Object.entries(form).forEach(([key, value]) => {
        if (value !== null && key !== "pdf") {
            formData.append(key, value);
        }
    });
    if (form.pdf) {
        formData.append("pdf", form.pdf);
    }

    if (form.id) {
        form.post(route("products.update", form.id), {
            forceFormData: true,
            headers: { "Content-Type": "multipart/form-data" },
            onSuccess: () => {
                setTimeout(() => showToast("update", "success"), 50);
                isFormVisible.value = false;
                reloadData();
            },
            onError: (errors) => {
                setTimeout(() => showToast("update", "error"), 50);
                console.error("Update errors:", errors);
            },
        });
    } else {
        form.post(route("products.store"), {
            forceFormData: true,
            onSuccess: () => {
                setTimeout(() => showToast("create", "success"), 50);
                isFormVisible.value = false;
                reloadData();
            },
            onError: (errors) => {
                setTimeout(() => showToast("create", "error"), 50)
                console.log("Validation Errors:", errors);
                console.error("Creation errors:", errors);
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

