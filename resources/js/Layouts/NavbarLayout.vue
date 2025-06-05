<template>
    <div class="card">
        <Toolbar class="p-4">
            <template #start>
                <div class="flex items-center gap-8">
                    <h1 class="text-lg font-bold m-0">
                        {{ pageTitle }}
                    </h1>
                </div>
            </template>

            <template #end>
                <SplitButton
                    :label="userName"
                    :model="menuItems"
                    dropdownIcon="pi pi-cog"
                    class="p-button-rounded font-bold text-xs"
                    size="small"
                    outlined
                >
                    <template #icon>
                        <Avatar
                            :image="userAvatar"
                            style="width: 24px; height: 24px"
                            shape="circle"
                        />
                    </template>
                </SplitButton>
            </template>
        </Toolbar>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { usePage } from "@inertiajs/vue3";
import { Inertia } from "@inertiajs/inertia";
import Toolbar from "primevue/toolbar";
import SplitButton from "primevue/splitbutton";
import Avatar from "primevue/avatar";

const page = usePage();
const rolesArray = computed(() => page.props.roles || []);
const rolesString = computed(() =>
    rolesArray.value
        .map((r) => r.charAt(0).toUpperCase() + r.slice(1))
        .join(", ")
);
const userName = computed(() => rolesString.value || "No role assigned");
const userAvatar =
    "https://primefaces.org/cdn/primevue/images/avatar/amyelsner.png";

const theme = ref(localStorage.getItem("theme") || "system");

const applyTheme = (pref) => {
    if (pref === "light") {
        document.documentElement.classList.remove("dark");
        localStorage.setItem("theme", "light");
    } else if (pref === "dark") {
        document.documentElement.classList.add("dark");
        localStorage.setItem("theme", "dark");
    } else {
        localStorage.removeItem("theme");
        const isDark = window.matchMedia(
            "(prefers-color-scheme: dark)"
        ).matches;
        if (isDark) {
            document.documentElement.classList.add("dark");
        } else {
            document.documentElement.classList.remove("dark");
        }
    }
    theme.value = pref;
};

onMounted(() => {
    applyTheme(theme.value);

    window
        .matchMedia("(prefers-color-scheme: dark)")
        .addEventListener("change", (e) => {
            if (!localStorage.getItem("theme")) {
                if (e.matches) {
                    document.documentElement.classList.add("dark");
                } else {
                    document.documentElement.classList.remove("dark");
                }
            }
        });
});

const menuItems = [
    {
        label: rolesString.value || "No role assigned",
        icon: "pi pi-user",
        class: "font-bold text-sm ",
        command: () => {
            Inertia.get(route("profile.show"));
        },
    },
    { separator: true },
    {
        label: "Light Theme",
        icon: "pi pi-sun",
        class: "font-bold text-sm",
        command: () => {
            applyTheme("light");
        },
    },
    {
        label: "Dark Theme",
        icon: "pi pi-moon",
        class: "font-bold text-sm",
        command: () => {
            applyTheme("dark");
        },
    },
    {
        label: "System Theme",
        icon: "pi pi-desktop",
        class: "font-bold text-sm",
        command: () => {
            applyTheme("system");
        },
    },
    { separator: true },
    {
        label: "Sign Out",
        icon: "pi pi-sign-out",
        class: "font-bold text-sm",
        command: () => {
            Inertia.post(route("logout"));
        },
    },
];

const pageTitle = computed(() => {
    const routePath = page.url;

    const routeTitles = {
        "/dashboard": "Dashboard",
        "/quotations": "Quotations",
        "/quotations/create": "Create Quotation",
        "/agreements": "Agreements",
        "/agreements/create": "Create Agreement",
        "/invoices": "Invoices",
        "/invoices/create": "Create Invoice",
        "/invoices/list": "List Invoices",
        "/invoices/{invoices}/edit": "Edit Invoice",
        "/receipts": "Receipts",
        "/settings": "Settings",
        "/settings/customers": "Customers",
        "/settings/products": "Items",
        "/settings/product-categories": "Product Categories",
        "/settings/customer-categories": "Customer Categories",
    };

    if (routePath.match(/^\/invoices\/\d+\/edit$/)) {
        return "Edit Invoices";
    }
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

.text-xl {
    font-size: 1.25rem;
    line-height: 1.75rem;
}
</style>
