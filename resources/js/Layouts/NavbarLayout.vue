<template>
    <div class="card  dark:bg-[#1d1d1d] dark:text-gray-100">
        <Toolbar class="p-4 dark:bg-[#1d1d1d] dark:border-b dark:border-gray-700">
            <template #start>
                <div class="flex items-center gap-8">
                    <h1 class="text-xl font-bold m-0">
                        {{ pageTitle }}
                    </h1>
                    <IconField>
                        <InputIcon>
                            <i class="pi pi-search" />
                        </InputIcon>
                        <InputText
                            size="small"
                            class="w-80 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700"
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
import IconField from "primevue/iconfield";
import InputText from "primevue/inputtext";
import InputIcon from "primevue/inputicon";
import Toolbar from "primevue/toolbar";
import SplitButton from "primevue/splitbutton";
import Avatar from "primevue/avatar";

const page = usePage();

const userName = "Manager";
const userAvatar =
    "https://primefaces.org/cdn/primevue/images/avatar/amyelsner.png";

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

// Computed property for page title
const pageTitle = computed(() => {
    const routePath = page.url;

    // Map of static routes
    const routeTitles = {
        "/": "Dashboard",
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

<style scoped>
.p-toolbar {
  padding: 1rem;
  background: white;
  border: none;
  border-radius: 0%;
  box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
}

/* Dark mode overrides */
.dark .p-toolbar {
  background: #1d1d1d;
  border-bottom: 1px solid #374151; /* Tailwind gray-700 */
  box-shadow: none;
}

.text-xl {
  font-size: 1.25rem;
  line-height: 1.75rem;
}

/* InputText dark mode overrides */
.dark .p-inputtext {
  background-color: #1f2937;
  border-color: #374151;
  color: #e5e7eb;
}

/* SplitButton dark mode */
.dark .p-splitbutton {
  border-color: #4b5563; /* Tailwind gray-600 */
  color: #e5e7eb;
}
</style>

