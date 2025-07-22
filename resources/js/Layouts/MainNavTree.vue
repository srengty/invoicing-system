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
                        <!-- <Badge
                            v-if="item.key === 2"
                            :value="
                                page.props.pendingQuotationsCount > 0
                                    ? page.props.pendingQuotationsCount
                                    : '0'
                            "
                            :severity="
                                page.props.pendingQuotationsCount > 0
                                    ? 'danger'
                                    : ''
                            "
                            class="ml-auto"
                        /> -->
                        <Badge
                            v-if="
                                item.key === 2 &&
                                userPermissions.canAlterQuotations &&
                                page.props.pendingQuotationsCount > 0
                            "
                            :value="page.props.pendingQuotationsCount"
                            severity="danger"
                            class="ml-auto"
                        />
                        <Badge
                            v-if="
                                item.key === 3 &&
                                userPermissions.canAlterAgreements &&
                                page.props.dueAgreementsCount > 0
                            "
                            :value="page.props.dueAgreementsCount"
                            severity="danger"
                            class="ml-auto"
                        />
                        <Badge
                            v-if="item.key === 4 && invoiceBadgeCount > 0"
                            :value="invoiceBadgeCount"
                            severity="danger"
                            class="ml-auto"
                        />
                        <Badge
                            v-if="
                                item.href === '/settings/products' &&
                                userPermissions.canAlterItems &&
                                page.props.pendingItemsCount > 0
                            "
                            :value="page.props.pendingItemsCount"
                            severity="danger"
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
const userPermissions = computed(() => {
    const roles = page.props.userRoles || [];
    return {
        canCreateQuotations: roles.some((role) =>
            role.toLowerCase().includes("division staff")
        ),
        canCreateAgreements: roles.some((role) =>
            role.toLowerCase().includes("chef department")
        ),
        canCreateInvoices: roles.some((role) =>
            role.toLowerCase().includes("division staff")
        ),
        canSeenReceipts: roles.some((role) =>
            role.toLowerCase().includes("revenue manager")
        ),
        canSeenQuotations: roles.some(
            (role) =>
                role.toLowerCase().includes("chef department") ||
                role.toLowerCase().includes("division staff")
        ),
        canSeenAgreements: roles.some(
            (role) =>
                role.toLowerCase().includes("chef department") ||
                role.toLowerCase().includes("director")
        ),
        canSeenManagements: roles.some(
            (role) =>
                role.toLowerCase().includes("chef department") ||
                role.toLowerCase().includes("director") ||
                role.toLowerCase().includes("revenue manager") ||
                role.toLowerCase().includes("division staff")
        ),
        canSeenItems: roles.some(
            (role) =>
                role.toLowerCase().includes("chef department") ||
                role.toLowerCase().includes("director")
        ),
        canSeenCustomerCategories: roles.some(
            (role) =>
                role.toLowerCase().includes("chef department") ||
                role.toLowerCase().includes("director")
        ),
        canSeenItemCategories: roles.some(
            (role) =>
                role.toLowerCase().includes("chef department") ||
                role.toLowerCase().includes("director")
        ),

        canAlterQuotations: roles.some(
            (role) =>
                role.toLowerCase().includes("division staff") ||
                role.toLowerCase().includes("chef department")
        ),
        canAlterAgreements: roles.some(
            (role) =>
                role.toLowerCase().includes("chef department") ||
                role.toLowerCase().includes("division staff")
        ),
        canAlterItems: roles.some(
            (role) =>
                role.toLowerCase().includes("chef department") ||
                role.toLowerCase().includes("division staff") ||
                role.toLowerCase().includes("director")
        ),
        canAlterHDStatus: roles.some((role) =>
            role.toLowerCase().includes("chef department")
        ),
        canAlterRMStatus: roles.some((role) =>
            role.toLowerCase().includes("revenue manager")
        ),
        canAlterDirectorStatus: roles.some((role) =>
            role.toLowerCase().includes("director")
        ),
    };
});

const invoiceBadgeCount = computed(() => {
    if (userPermissions.value.canAlterHDStatus) {
        return page.props.hdPendingCount;
    }
    if (userPermissions.value.canAlterRMStatus) {
        return page.props.rmPendingCount;
    }
    if (userPermissions.value.canAlterDirectorStatus) {
        return page.props.directorPendingCount;
    }
    return 0;
});

const items = ref([
    {
        key: 1,
        label: "Dashboard",
        href: "/dashboard",
        icon: "pi pi-home",
        shortcut: ">",
        items: [
            // {
            //     label: "Compose",
            //     href: "/compose",
            //     icon: "pi pi-file-edit",
            //     shortcut: "⌘+N",
            // },
        ],
    },
    ...(userPermissions.value.canSeenQuotations
        ? [
              {
                  key: 2,
                  label: "Quotations",
                  href: "/quotations",
                  icon: "pi pi-chart-bar",
                  shortcut: "⌘+R",
                  items: [
                      ...(userPermissions.value.canCreateQuotations &&
                      userPermissions.value.canAlterQuotations
                          ? [
                                {
                                    label: "New quotation",
                                    href: "/quotations/create",
                                    icon: "pi pi-chart-line",
                                    // shortcut: "⌘+O",
                                },
                            ]
                          : []),
                  ],
              },
          ]
        : []),
    ...(userPermissions.value.canSeenAgreements
        ? [
              {
                  key: 3,
                  label: "Agreements",
                  href: "/agreements",
                  icon: "pi pi-user",
                  shortcut: ">",
                  items: [
                      ...(userPermissions.value.canCreateAgreements &&
                      userPermissions.value.canAlterAgreements
                          ? [
                                {
                                    label: "Create Agreement",
                                    href: "/agreements/create",
                                    icon: "pi pi-plus",
                                    // shortcut: "⌘+O",
                                },
                            ]
                          : []),
                  ],
              },
          ]
        : []),

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
                // shortcut: "⌘+O",
            },
            ...(userPermissions.value.canCreateInvoices
                ? [
                      {
                          label: "Create Invoice",
                          href: "/invoices/create",
                          icon: "pi pi-cog",
                          //   shortcut: "⌘+O",
                      },
                  ]
                : []),
        ],
    },
    ...(userPermissions.value.canSeenReceipts
        ? [
              {
                  key: 5,
                  label: "Receipts",
                  href: "/receipts",
                  icon: "pi pi-receipt",
                  shortcut: "⌘+W",
                  items: [],
              },
          ]
        : []),
    ...(userPermissions.value.canSeenManagements
        ? [
              {
                  key: 7,
                  label: "Management",
                  href: "/settings",
                  icon: "pi pi-cog",
                  shortcut: "⌘+W",
                  items: [
                      {
                          label: "Customers",
                          href: "/settings/customers",
                          icon: "pi pi-user",
                          //   shortcut: "⌘+W",
                      },
                      ...(userPermissions.value.canSeenItems
                          ? [
                                {
                                    label: "Items",
                                    href: "/settings/products",
                                    icon: "pi pi-box",
                                    // shortcut: "⌘+P",
                                },
                            ]
                          : []),
                      ...(userPermissions.value.canSeenCustomerCategories
                          ? [
                                {
                                    label: "Customer Categories",
                                    href: "/settings/customer-categories",
                                    icon: "pi pi-box",
                                    // shortcut: "⌘+P",
                                },
                            ]
                          : []),
                      ...(userPermissions.value.canSeenItemCategories
                          ? [
                                {
                                    label: "Product Categories",
                                    href: "/settings/product-categories",
                                    icon: "pi pi-box",
                                    // shortcut: "⌘+P",
                                },
                            ]
                          : []),
                  ],
              },
          ]
        : []),
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
    /* animation: pulse 2s infinite; */
}

.p-badge.p-badge-info {
    background: #3b82f6;
    color: white;
}

.p-badge.p-badge-warning {
    background: #f59e0b;
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
