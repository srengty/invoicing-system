<template>
    <div class="card flex justify-center dark:bg-white"></div>

    <div class="card flex justify-center pr-1"></div>

    <div class="card flex justify-center dark:bg-white"></div>
    <div class="card flex justify-center pr-1">
        <PanelMenu
            v-model:expandedKeys="expandedKeys"
            :model="items"
            class="w-full md:w-80 text-xs"
            :pt="{ panel: { class: 'border-0 bg-transparent p-0' } }"
            multiple
        >
            <template #item="{ item }">
                <div
                    :class="[
                        'flex items-center cursor-pointer group',
                        { active: page.url == item.href },
                    ]"
                >
                    <Link
                        :href="item.href"
                        class="flex items-center px-4 py-2 group"
                    >
                        <span
                            :class="[
                                'rounded-full me-2 w-8 h-8 bg-green-300 flex items-center justify-center',
                                { hidden: !item.id },
                            ]"
                            >{{ item.id }}</span
                        >
                        <span
                            :class="[
                                item.icon,
                                'text-primary group-hover:text-inherit',
                            ]"
                        />
                        <span
                            :class="['ml-2', { 'font-semibold': item.items }]"
                            >{{ item.label }}</span
                        >
                    </Link>
                    <Badge
                        v-if="item.badge"
                        class="ml-auto"
                        :value="item.badge"
                    />
                    <span
                        v-if="item.shortcut"
                        class="ml-auto rounded text-muted-color text-xl p-1 w-8"
                        ><span class="pi pi-angle-right"
                    /></span>
                </div>
            </template>
        </PanelMenu>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { PanelMenu, Badge } from "primevue";
import { Link, usePage } from "@inertiajs/vue3";
const page = usePage();
const items = ref([
    {
        key: 1,
        label: "Dashboard",
        href: "/",
        icon: "pi pi-home",
        shortcut: ">",
        badge: null,
        items: [
            {
                label: "Compose",
                href: "/compose",
                icon: "pi pi-file-edit",
                shortcut: "⌘+N",
            },
        ],
    },
    {
        key: 2,
        label: "Quotations",
        href: "/quotations",
        icon: "pi pi-chart-bar",
        shortcut: "⌘+R",
        items: [
            {
                label: "New quotation",
                href: "/quotations/create",
                icon: "pi pi-chart-line",
                badge: 0,
            },
            // {
            //     label: 'Products',
            //     href: '/products',
            //     icon: 'pi pi-box',
            //     shortcut: '⌘+P'
            // }
        ],
    },
    {
        key: 3,
        label: "Agreements",
        href: "/agreements",
        icon: "pi pi-user",
        shortcut: ">",
        items: [
            {
                label: "Create Agreement",
                href: "/agreements/create",
                icon: "pi pi-plus",
                shortcut: "⌘+O",
            },
        ],
    },
    {
        key: 4,
        label: "Invoices",
        href: "/invoices",
        icon: "pi pi-money-bill",
        shortcut: "⌘+W",
        items: [
            {
                label: "List Invoice",
                href: "/invoices/list",
                icon: "pi pi-list",
                shortcut: "⌘+O",
            },
            {
                label: "Create Invoice",
                href: "/invoices/create",
                icon: "pi pi-cog",
                shortcut: "⌘+O",
            },
        ],
    },
    {
        key: 5,
        label: "Receipts",
        href: "/receipts",
        icon: "pi pi-receipt",
        shortcut: "⌘+W",
        items: [
            // {
            //     label: "Create Receipt",
            //     href: "/receipts/create",
            //     icon: "pi pi-plus",
            //     shortcut: "⌘+O",
            // },
        ],
    },
    {
        key: 6,
        label: "Settings",
        href: "/settings",
        icon: "pi pi-cog",
        shortcut: "⌘+W",
        items: [
            {
                label: "Customers",
                href: "/settings/customers",
                icon: "pi pi-user",
                shortcut: "⌘+W",
            },
            {
                label: "Items",
                href: "/settings/products",
                icon: "pi pi-box",
                shortcut: "⌘+P",
            },
            {
                label: "Customer Categories",
                href: "/settings/customer-categories",
                icon: "pi pi-box",
                shortcut: "⌘+P",
            },
            {
                label: "Product Categories",
                href: "/settings/product-categories",
                icon: "pi pi-box",
                shortcut: "⌘+P",
            },
        ],
    },
]);
const expandedKeys = ref(
    items.value.reduce((acc, item) => {
        if (page.url.startsWith(item.href) && item.href !== "/") {
            acc[item.key] = true;
        }
        return acc;
    }, {})
);
</script>
<style>
:root {
    --p-panelmenu-panel-background: transparent;
    --p-panelmenu-panel-border-color: transparent;
    --p-panelmenu-item-focus-background: #fff;
    --p-panelmenu-panel-padding: 0 !important;
}

.active {
    background-color: #d4f4dd; /* Equivalent to bg-green-100 */
}

.card {
    position: sticky;
    top: 0;
    z-index: 10;
    /* Ensure it's above other content */
}
</style>
