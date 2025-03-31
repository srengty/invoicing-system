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
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import IconField from "primevue/iconfield";
import InputText from "primevue/inputtext";
import InputIcon from "primevue/inputicon";
import Toolbar from "primevue/toolbar";
import SplitButton from "primevue/splitbutton";
import Avatar from "primevue/avatar";

const page = usePage();

const routeTitles = {
    "/": "Dashboard",
    "/quotations": "Quotations",
    "/quotations/create": "Create Quotation",
    "/agreements": "Agreements",
    "/agreements/create": "Create Agreement",
    "/invoices": "Invoices",
    "/invoices/create": "Create Invoice",
    "/settings": "Settings",
    "/settings/customers": "Customers",
    "/settings/products": "Items",
    "/settings/product-categories": "Product Categories",
    "/settings/customer-categories": "Customer Categories",
};

const currentRouteTitle = computed(() => {
    // Check if we're editing a quotation by component name
    if (page.component === 'Quotations/Edit') {
        return `Edit Quotation ${page.props.quotation?.id || ''}`;
    }

    // Check if URL matches edit pattern (supports both /edit/{id} and /{id}/edit)
    if (page.url.match(/\/quotations\/(edit\/|)\d+/)) {
        return `Edit Quotation ${page.props.quotation?.id || ''}`;
    }

    // Check create route
    if (page.url === '/quotations/create') {
        return "Create Quotation";
    }

    // Default to mapped titles
    return routeTitles[page.url] || "Edit Quotation";
});

const userName = "Manager";
const userAvatar = "https://primefaces.org/cdn/primevue/images/avatar/amyelsner.png";

const items = [
    {
        label: "Accounts",
        icon: "pi pi-user",
    },
    {
        label: "Logout",
        icon: "pi pi-sign-out",
    },
];
</script>

<style scoped>
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
