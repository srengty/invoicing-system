<template>

    <Head title="Create Agreement" />
    <GuestLayout>
        <Toast />
        <div>
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-800">Customer Categories</h1>
                <Button type="button" class="p-button-success" @click="openModal">Add New</Button>
            </div>
            <div class="mt-4">
                <DataTable :value="customerCategories" :paginator="true" :rows="10" :rowsPerPageOptions="[5, 10, 20]">
                    <Column field="category_name_khmer" header="Name"></Column>
                    <Column field="category_name_english" header="Name En"></Column>
                    <Column field="description_khmer" header="Description"></Column>
                    <Column field="description_english" header="Description En"></Column>
                    <Column field="created_at" header="Created At">
                        <template #body="slotProps">
                            {{ new Date(slotProps.data['created_at']).toLocaleDateString() }}
                        </template>
                    </Column>
                    <Column field="action" header="Action">
                        <template #body="slotProps">
                            <Button severity="warn" size="small" @click="router.get(route('categories.edit',{'id':slotProps.data.id}))">Edit</Button>
                        </template>
                    </Column>
                </DataTable>
            </div>
            <Dialog v-model="displayModal" header="Add New Customer Category" :visible="displayModal" @update:visible="displayModal = $event" modal>
                <form @submit="handleSubmit">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label for="name" class="block text-lg font-medium">Name</label>
                            <InputText id="name" v-model="form.name" class="w-full"
                                placeholder="Enter Customer Category Name" />
                        </div>
                        <div>
                            <label for="description" class="block text-lg font-medium">Description</label>
                            <InputText id="description" v-model="form.description" class="w-full"
                                placeholder="Enter description" />
                        </div>
                        <div>
                            <Button type="submit" class="p-button-success">Save</Button>
                        </div>
                    </div>
                </form>
            </Dialog>
            <Dialog v-model="displayEditModal" header="Edit Customer Category" :visible="displayEditModal"
                @update:visible="displayEditModal = $event" modal>
                <form @submit="handleEditSubmit">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label for="name" class="block text-lg font-medium">Name</label>
                            <InputText id="name" v-model="form.name" class="w-full"
                                placeholder="Enter Customer Category Name" />
                        </div>
                        <div>
                            <label for="description" class="block text-lg font-medium">Description</label>
                            <InputText id="description" v-model="form.description" class="w-full"
                                placeholder="Enter description" />
                        </div>
                        <div>
                            <Button type="submit" class="p-button-success">Save</Button>
                        </div>
                    </div>
                </form>
            </Dialog>
        </div>
    </GuestLayout>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useToast } from "primevue/usetoast";
import { Button, Dialog, DataTable, Column, InputText, Toast } from "primevue";
import { Head, usePage, router } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";
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
</script>

<style lang="scss" scoped></style>