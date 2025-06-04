<template>
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
                        { active: page.url === item.href },
                    ]"
                >
                    <Link
                        :href="item.href"
                        class="flex items-center px-4 py-2 group"
                    >
                        <span
                            v-if="item.id"
                            class="rounded-full me-2 w-8 h-8 bg-green-300 flex items-center justify-center"
                        >
                            {{ item.id }}
                        </span>
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

                    <div class="ml-auto flex items-center gap-2">
                        <Badge
                            v-if="item.key === 3"
                            :value="
                                dueAgreementsCount > 0
                                    ? dueAgreementsCount
                                    : '0'
                            "
                            :severity="
                                dueAgreementsCount > 0 ? 'danger' : 'info'
                            "
                            class="ml-auto"
                        />
                        <span
                            v-if="item.shortcut"
                            class="rounded text-muted-color text-xl p-1 w-8"
                        >
                            <span class="pi pi-angle-right" />
                        </span>
                    </div>
                </div>
            </template>
        </PanelMenu>
    </div>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import { PanelMenu, Badge } from "primevue";
import moment from "moment";

const page = usePage();
const agreementsData = ref(page.props.agreements || []);

watch(
    () => page.props.agreements,
    (newAgreements) => {
        agreementsData.value = newAgreements || [];
    },
    { immediate: true }
);

const getPaymentStatus = (schedule) => {
    if (schedule.status === "PAID" || schedule.paid_amount >= schedule.amount) {
        return "PAID";
    }
    if (schedule.paid_amount > 0) {
        return "PARTIALLY_PAID";
    }

    const today = moment();
    const dueDate = moment(
        schedule.due_date,
        ["YYYY-MM-DD", "DD/MM/YYYY", moment.ISO_8601],
        true
    );
    if (!dueDate.isValid()) {
        return schedule.status || "UPCOMING";
    }
    if (dueDate.isBefore(today, "day")) return "PAST DUE";
    if (dueDate.diff(today, "days") <= 13) return "DUE SOON";
    return "UPCOMING";
};

const dueAgreementsCount = computed(() => {
    return agreementsData.value.filter((agreement) =>
        (agreement.payment_schedules || []).some((schedule) => {
            const status = getPaymentStatus(schedule);
            return status === "DUE SOON" || status === "PAST DUE";
        })
    ).length;
});

const items = ref([
    {
        key: 1,
        label: "Dashboard",
        href: "/dashboard",
        icon: "pi pi-home",
        shortcut: ">",
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
            },
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
        items: [],
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

// Update badge count reactively on dueAgreementsCount changes
watch(
    dueAgreementsCount,
    (newCount) => {
        const agreementsItem = items.value.find((item) => item.key === 3);
        if (agreementsItem) {
            agreementsItem.badge = newCount;
        }
    },
    { immediate: true }
);

const expandedKeys = ref(
    items.value.reduce((acc, item) => {
        if (page.url.startsWith(item.href) && item.href !== "/") {
            acc[item.key] = true;
        }
        return acc;
    }, {})
);
</script>

<style scoped>
:root {
    --p-panelmenu-panel-background: transparent;
    --p-panelmenu-panel-border-color: transparent;
    --p-panelmenu-item-focus-background: #fff;
    --p-panelmenu-panel-padding: 0 !important;
}

.active {
    background-color: #d4f4dd; /* bg-green-100 equivalent */
}

.card {
    position: sticky;
    top: 0;
    z-index: 10; /* above other content */
}

.p-badge {
    font-size: 0.75rem;
    min-width: 1.5rem;
    height: 1.5rem;
    line-height: 1.5rem;
    display: inline-flex !important;
}

.p-badge.p-badge-danger {
    background: #ef4444;
    color: white;
    animation: pulse 2s infinite;
}

.p-badge.p-badge-info {
    background: #3b82f6;
    color: white;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(239, 68, 68, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
    }
}
</style>
