<template>
    <div class="card">
        <Toolbar class="p-4">
            <template #start>
                <div class="flex items-center gap-8">
                    <h1 class="text-xl font-bold m-0">
                        {{ pageTitle }}
                    </h1>
                    <!-- <IconField>
                        <InputIcon>
                            <i class="pi pi-search" />
                        </InputIcon>
                        <InputText
                            size="small"
                            class="w-80 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700"
                            placeholder="Find invoices, clients, and more"
                        />
                    </IconField> -->
                </div>
            </template>
            <template #end>
                <SplitButton
                    :label="userName"
                    :model="items"
                    class="mr-3"
                    size="small"
                    outlined=""
                    :class="['dark:text-gray-100', 'dark:border-gray-600']"
                ></SplitButton>
                <Avatar
                    :image="userAvatar"
                    style="width: 32px; height: 32px"
                    size="large"
                    shape="circle"
                    class="dark:ring-2 dark:ring-gray-600"
                />
            </template>
        </Toolbar>
    </div>
</template>

<script setup>
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import { Inertia } from "@inertiajs/inertia";
import IconField from "primevue/iconfield";
import InputText from "primevue/inputtext";
import InputIcon from "primevue/inputicon";
import Toolbar from "primevue/toolbar";
import SplitButton from "primevue/splitbutton";
import Avatar from "primevue/avatar";

const page = usePage();
const userName = computed(() => rolesString.value || "No role assigned");
const userAvatar =
    "https://primefaces.org/cdn/primevue/images/avatar/amyelsner.png";

// 1) Read the raw roles array (lowercased) from Inertia props
const rolesArray = computed(() => page.props.roles || []);

// 2) Turn ["revenue manager"] → "Revenue Manager"
const rolesString = computed(() => {
    return rolesArray.value
        .map((r) => r.charAt(0).toUpperCase() + r.slice(1))
        .join(", ");
});

const items = [
    {
        label: "Accounts",
        icon: "pi pi-user",
        // add whatever logic you need here
    },
    {
        label: "Logout",
        icon: "pi pi-sign-out",
        command: () => {
            Inertia.post(route("logout"));
        },
    },
    // {
    //     // Show the capitalized roles right under “Logout”
    //     label: rolesString.value || "No role assigned",
    //     icon: "pi pi-tag",
    //     disabled: true,
    //     style: "cursor: default; opacity: 0.6; font-style: italic;",
    // },
];

// Computed property for page title
const pageTitle = computed(() => {
    const routePath = page.url;

    // Map of static routes
    const routeTitles = {
        "/dashboard": "Dashboard",
        "/quotations": "Quotations",
        "/quotations/create": "Create Quotation",
        "/agreements": "Agreements",
        "/agreements/create": "Create Agreement",
        "/invoices": "Invoices",
        "/invoices/create": "Create Invoice",
        "/receipts": "Receipts",
        "/settings": "Settings",
        "/settings/customers": "Customers",
        "/settings/products": "Items",
        "/settings/product-categories": "Product Categories",
        "/settings/customer-categories": "Customer Categories",
    };

    // Dynamic checks (Edit Agreement, Edit Quotation, Generate Invoice)
    if (routePath.match(/^\/agreements\/\d+\/edit$/)) {
        return "Edit Agreement";
    }
    if (routePath.match(/^\/quotations\/\d+\/update$/)) {
        const quotationNo = routePath.split("/")[2];
        return `Edit Quotation - ${quotationNo}`;
    }
    if (routePath.match(/^\/invoices\/generate\/\d+$/)) {
        return "Generate Invoice";
    }

    return routeTitles[routePath] || "Edit Quotation";
});
</script>


