<template>
    <div class="card">
        <Toolbar class="p-4">
            <template #start>
                <div class="flex items-center gap-8">
                    <h1 class="text-xl font-bold m-0">
                        {{ currentRouteTitle }}
                    </h1>
                    <IconField>
                        <InputIcon>
                            <i class="pi pi-search" />
                        </InputIcon>
                        <InputText
                            size="small"
                            class="w-80"
                            placeholder="Find invoices, clients, and more"
                        />
                    </IconField>
                </div>
            </template>
            <template #end>
                <SplitButton
                    :label="userName"
                    :model="items"
                    class="mr-3"
                    size="small"
                    outlined=""
                ></SplitButton>
                <Avatar
                    :image="userAvatar"
                    style="width: 32px; height: 32px"
                    size="large"
                    shape="circle"
                />
            </template>
        </Toolbar>
    </div>
</template>

<script setup>
import { computed, ref } from "vue";
import { usePage } from "@inertiajs/vue3";
import IconField from "primevue/iconfield";
import InputText from "primevue/inputtext";
import InputIcon from "primevue/inputicon";
import Toolbar from "primevue/toolbar";
import SplitButton from "primevue/splitbutton";
import Avatar from "primevue/avatar";
import Button from "primevue/button";

const page = usePage();
const routeTitles = {
    "/": {
        title: "Dashboard",
    },
    "/quotations": {
        title: "Quotation",
    },
    "/quotations/create": {
        title: "Create Quotation",
    },
    "/agreements": {
        title: "Agreement",
    },
    "/agreements/create": {
        title: "Create Agreement",
    },
    "/invoices": {
        title: "Invoices",
    },
    "/invoices/create": {
        title: "Create Invoices",
    },
    "/settings": {
        title: "Setting",
    },
    "/settings/customers": {
        title: "Customers",
    },
    "/settings/products": {
        title: "Items",
    },
    "/settings/product-categories": {
        title: "Product Categories",
    },
    "/settings/customer-categories": {
        title: "Customer Categories",
    },
};

const currentRouteTitle = computed(() => {
    return routeTitles[page.url]?.title || "Dashboard";
});

const userName = ref("Manager");
const userAvatar = ref(
    "https://primefaces.org/cdn/primevue/images/avatar/amyelsner.png"
);

const items = ref([
    {
        label: "Accounts",
        icon: "pi pi-user",
    },
    {
        label: "Logout",
        icon: "pi pi-sign-out",
    },
]);
</script>

<style scoped>
/* Custom styles */
.p-toolbar {
    padding: 1rem;
    background: white;
    border: none;
    border-radius: 0%;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
}

.text-xl {
    font-size: 1.25rem;
    line-height: 1.75rem;
}
</style>
