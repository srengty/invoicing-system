<template>
    <Head title="Create Agreement" />
    <GuestLayout>
        <Toast />
        <BodyLayout>
            <div>
                <div class="flex justify-between items-center">
                    <h1 class="text-xl font-semibold text-gray-800">Products Categories</h1>
                    <Button type="button" size="small" class="p-button-success" @click="openModal">Add New</Button>
                </div>
                <div class="mt-4 text-sm">
                    <DataTable :value="productCategories" :paginator="true" :rows="10" :rowsPerPageOptions="[5, 10, 20]">
                        <Column field="category_name_khmer" header="Name"></Column>
                        <Column field="category_name_english" header="Name En"></Column>
                        <Column field="description_khmer" header="Description"></Column>
                        <Column field="description_english" header="Description En"></Column>
                        <Column field="created_at" header="Created At">
                            <template #body="slotProps">
                                {{ new Date(slotProps.data['created_at']).toLocaleDateString() }}
                            </template>
                        </Column>
                        <Column header="Actions" class="flex gap-2" >
                            <template #body="slotProps">
                                <Button icon="pi pi-eye" size="small" severity="info" @click="openDetailModal(slotProps.data)" outlined />
                                <Button icon="pi pi-pencil" size="small" @click="openEditModal(slotProps.data)" outlined />
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
            
            <Dialog v-model:visible="displayDetailModal" header="Category Details" modal>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <strong>Name:</strong> {{ selectedCategory.category_name_khmer }}
                    </div>
                    <div>
                        <strong>Name En:</strong> {{ selectedCategory.category_name_english }}
                    </div>
                    <div>
                        <strong>Description:</strong> {{ selectedCategory.description_khmer }}
                    </div>
                    <div>
                        <strong>Description En:</strong> {{ selectedCategory.description_english }}
                    </div>
                    <div>
                        <strong>Created At:</strong> {{ new Date(selectedCategory.created_at).toLocaleDateString() }}
                    </div>
                </div>
            </Dialog>
            
            <Dialog v-model:visible="displayEditModal" header="Edit Customer Category" modal>
                <form @submit.prevent="handleEditSubmit">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label for="edit-name" class="block text-lg font-medium">Name</label>
                            <InputText id="edit-name" v-model="form.category_name_khmer" class="w-full" placeholder="Enter Customer Category Name" />
                        </div>
                        <div>
                            <label for="edit-name-en" class="block text-lg font-medium">Name En</label>
                            <InputText id="edit-name-en" v-model="form.category_name_english" class="w-full" placeholder="Enter Customer Category Name in English" />
                        </div>
                        <div>
                            <label for="edit-description" class="block text-lg font-medium">Description</label>
                            <InputText id="edit-description" v-model="form.description_khmer" class="w-full" placeholder="Enter description" />
                        </div>
                        <div>
                            <label for="edit-description-en" class="block text-lg font-medium">Description En</label>
                            <InputText id="edit-description-en" v-model="form.description_english" class="w-full" placeholder="Enter description in English" />
                        </div>
                        <div>
                            <Button type="submit" class="p-button-success">Save</Button>
                        </div>
                    </div>
                </form>
            </Dialog>
        </BodyLayout>
    </GuestLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import { DataTable, Column, Dialog, Button, InputText, Toast } from "primevue";
import { Head, usePage } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import BodyLayout from '@/Layouts/BodyLayout.vue';

const productCategories = computed(() => usePage().props.productCategories || []);
const displayDetailModal = ref(false);
const displayEditModal = ref(false);
const selectedCategory = ref({});
const form = ref({ category_name_khmer: "", category_name_english: "", description_khmer: "", description_english: "", id: null });

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
    Toast.add({ severity: 'success', summary: 'Success', detail: 'Category updated', life: 3000 });
    displayEditModal.value = false;
};
</script>

<style lang="scss" scoped>
</style>
